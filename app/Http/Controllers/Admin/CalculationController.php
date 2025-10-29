<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Criteria;
use App\Models\Alternative;
use App\Models\Major;

class CalculationController extends Controller
{
    public function calculation(Request $request)
    {
        // 1. Ambil semua Data Dasar, dengan relasi yang diperlukan
        $majors = Major::with('criteria.subCriteria')->get();
        $alternatives = Alternative::with(['values.subCriteria', 'student', 'major'])->get();

        // Matriks untuk menyimpan hasil MOORA Y_i per Alternatif per Jurusan
        $finalResults = collect();

        // Matriks untuk menyimpan semua hasil perhitungan (untuk tampilan Step 1-4)
        $allCalculations = [];

        // 2. Loop setiap Jurusan dan jalankan MOORA
        foreach ($majors as $major) {
            $criteria = $major->criteria; // Kriteria hanya yang dimiliki Jurusan ini

            // Lewati jika Jurusan tidak memiliki kriteria
            if ($criteria->isEmpty()) {
                continue;
            }

            // --- TAHAP PERHITUNGAN MOORA UNTUK JURUSAN: {{ $major->name }} ---

            // 2a. Normalisasi Bobot Kriteria Jurusan
            $totalWeight = $criteria->sum('weight') ?: 1;
            $weight = $criteria->pluck('weight', 'id')->map(fn($w) => $w / $totalWeight);

            // 2b. Bentuk Matriks Awal (x_ij)
            $altValues = [];
            foreach ($alternatives as $alt) {
                foreach ($criteria as $c) {
                    $altValues[$alt->id][$c->id] = 0;

                    // Cari nilai sub-kriteria yang sesuai dengan kriteria Jurusan ini
                    $matchingValue = $alt->values->first(function ($val) use ($c) {
                        return $val->subCriteria && $val->subCriteria->criteria_id === $c->id;
                    });

                    if ($matchingValue) {
                        $altValues[$alt->id][$c->id] = $matchingValue->subCriteria->value;
                    }
                }
            }

            // 2c. Normalisasi Vektor (D_j)
            $normDivisor = [];
            foreach ($criteria as $c) {
                $sumSquares = 0;
                foreach ($alternatives as $alt) {
                    $val = $altValues[$alt->id][$c->id] ?? 0;
                    $sumSquares += pow($val, 2);
                }
                $normDivisor[$c->id] = sqrt($sumSquares) ?: 1;
            }

            // 2d. Matriks Normalisasi Berbobot (y_ij) dan Nilai Akhir (Y_i)
            $normalization = [];
            $valueMoora = [];

            foreach ($alternatives as $alt) {
                $benefit = 0;
                $cost = 0;

                foreach ($criteria as $c) {
                    $raw = $altValues[$alt->id][$c->id] ?? 0;
                    $norm = $raw / $normDivisor[$c->id];
                    $weighted = $norm * $weight[$c->id];

                    $normalization[$alt->id][$c->id] = $weighted;

                    if (strtolower($c->attribute_type) === 'benefit') {
                        $benefit += $weighted;
                    } else {
                        $cost += $weighted;
                    }
                }

                $yi = $benefit - $cost;
                $valueMoora[$alt->id] = $yi;

                // Simpan hasil Y_i per Alternatif terhadap Jurusan saat ini
                $finalResults->push([
                    'major_id' => $major->id,
                    'alternative_id' => $alt->id,
                    'yi_value' => $yi,
                ]);
            }

            // Simpan semua matriks perhitungan Jurusan ini (untuk Step 1-4 di tampilan)
            $allCalculations[$major->id] = [
                'criteria' => $criteria,
                'alternatives' => $alternatives,
                'altValues' => $altValues,
                'normDivisor' => $normDivisor,
                'weight' => $weight,
                'normalization' => $normalization,
                'valueMoora' => $valueMoora,
            ];
        }

        // 3. Tentukan Rekomendasi Terbaik untuk Setiap Alternatif
        $recommendations = $alternatives->map(function ($alt) use ($finalResults, $majors) {
            // Filter semua hasil Y_i untuk Alternatif ini, terlepas dari Jurusan
            $altResults = $finalResults->where('alternative_id', $alt->id);

            // Cari hasil Y_i tertinggi (Jurusan yang paling direkomendasikan)
            $bestResult = $altResults->sortByDesc('yi_value')->first();

            // Ambil Jurusan saat ini (dari relasi model)
            $currentMajor = optional($alt->major)->name ?? 'Belum Ditentukan';

            // Ambil Jurusan Rekomendasi
            $recommendedMajor = 'N/A';
            if ($bestResult) {
                $recommendedMajor = optional($majors->firstWhere('id', $bestResult['major_id']))->name ?? 'N/A';
            }

            return [
                'alternative' => $alt,
                'current_major' => $currentMajor,
                'recommended_major' => $recommendedMajor,
                'best_yi_value' => $bestResult['yi_value'] ?? 0,
                'all_yi' => $altResults->pluck('yi_value', 'major_id')->toArray() // Nilai Y_i per semua Jurusan
            ];
        })->sortByDesc('best_yi_value')->values(); // Urutkan siswa berdasarkan Y_i terbaik yang mereka dapatkan


        return view('admin.moora.calculation', compact(
            'alternatives',
            'majors',
            'recommendations', // Data hasil akhir dan ranking rekomendasi
            'allCalculations' // Data untuk menampilkan Step 1-4 per Jurusan
        ));
    }

    public function calculationUser(Request $request)
    {
        $showModal = !$request->has('filtered');

        $criteria = Criteria::with('subCriteria')->get();
        $selectedSubCriteria = $request->input('criteria', []);
        $alternatives = Alternative::with(['values.subCriteria', 'student'])->get();

        $filteredAlternatives = $alternatives;
        $suggestions = collect();

        if (!empty($selectedSubCriteria)) {
            $filteredAlternatives = $alternatives->filter(function ($alt) use ($selectedSubCriteria) {
                foreach ($selectedSubCriteria as $sub_id) {
                    if (!$sub_id) continue;
                    $match = $alt->values->firstWhere('sub_criteria_id', $sub_id);
                    if (!$match) return false;
                }
                return true;
            })->values();


            if ($filteredAlternatives->isEmpty()) {
                $suggestions = $alternatives->values();
            }
        }

        $originalAlternativeCount = $filteredAlternatives->count();

        // Gunakan suggestions jika AND logic kosong
        $alternatives = $originalAlternativeCount > 0 ? $filteredAlternatives : $suggestions;

        // Normalisasi bobot kriteria
        $totalWeight = $criteria->sum('weight') ?: 1;
        $weight = $criteria->pluck('weight', 'id')->map(fn($w) => $w / $totalWeight);

        // Ambil semua nilai alternatif berdasarkan sub_criterias.value
        $altValues = [];

        foreach ($alternatives as $alt) {
            foreach ($criteria as $c) {
                $altValues[$alt->id][$c->id] = 0;
                foreach ($alt->values as $val) {
                    $sub = $val->subCriteria;
                    if ($sub && $sub->criteria_id === $c->id) {
                        $altValues[$alt->id][$c->id] = $sub->value;
                        break;
                    }
                }
            }
        }

        // Normalisasi akar
        $normDivisor = [];
        foreach ($criteria as $c) {
            $sumSquares = 0;
            foreach ($alternatives as $alt) {
                $val = $altValues[$alt->id][$c->id] ?? 0;
                $sumSquares += pow($val, 2);
            }
            $normDivisor[$c->id] = sqrt($sumSquares) ?: 1;
        }

        // MOORA
        $normalization = [];
        $valueMoora = [];

        foreach ($alternatives as $alt) {
            $benefit = 0;
            $cost = 0;

            foreach ($criteria as $c) {
                $raw = $altValues[$alt->id][$c->id] ?? 0;
                $norm = $raw / ($normDivisor[$c->id] ?: 1);
                $weighted = $norm * $weight[$c->id];

                $normalization[$alt->id][$c->id] = $weighted;

                if (strtolower($c->attribute_type) === 'benefit') {
                    $benefit += $weighted;
                } else {
                    $cost += $weighted;
                }
            }

            $valueMoora[$alt->id] = $benefit - $cost;
        }

        arsort($valueMoora);

        return view('admin.moora.calculation_user', compact(
            'criteria',
            'alternatives',
            'normalization',
            'weight',
            'valueMoora',
            'normDivisor',
            'showModal',
            'suggestions',
            'originalAlternativeCount'
        ));
    }

    public function downloadPDF()
    {
        $criteria = Criteria::with(['subCriteria'])->get();
        $alternatives = Alternative::with(['values.subCriteria', 'student'])->get();

        // Normalisasi bobot kriteria
        $totalWeight = $criteria->sum('weight') ?: 1;
        $weight = $criteria->pluck('weight', 'id')->map(fn($w) => $w / $totalWeight);

        // Ambil semua nilai alternatif berdasarkan sub_criterias.value
        $altValues = [];

        foreach ($alternatives as $alt) {
            foreach ($criteria as $c) {
                $altValues[$alt->id][$c->id] = 0;
                foreach ($alt->values as $val) {
                    $sub = $val->subCriteria;
                    if ($sub && $sub->criteria_id === $c->id) {
                        $altValues[$alt->id][$c->id] = $sub->value;
                        break;
                    }
                }
            }
        }

        // Normalisasi nilai alternatif per kriteria (sqrt(sum^2))
        $normDivisor = [];
        foreach ($criteria as $c) {
            $sumSquares = 0;
            foreach ($alternatives as $alt) {
                $val = $altValues[$alt->id][$c->id] ?? 0;
                $sumSquares += pow($val, 2);
            }
            $normDivisor[$c->id] = sqrt($sumSquares) ?: 1;
        }

        // Normalisasi dan perhitungan MOORA
        $normalization = [];
        $valueMoora = [];

        foreach ($alternatives as $alt) {
            $benefit = 0;
            $cost = 0;

            foreach ($criteria as $c) {
                $raw = $altValues[$alt->id][$c->id] ?? 0;
                $norm = $raw / $normDivisor[$c->id];
                $weighted = $norm * $weight[$c->id];

                $normalization[$alt->id][$c->id] = $weighted;

                if (strtolower($c->attribute_type) === 'benefit') {
                    $benefit += $weighted;
                } else {
                    $cost += $weighted;
                }
            }

            $valueMoora[$alt->id] = $benefit - $cost;
        }

        arsort($valueMoora);

        // Generate PDF
        $pdf = app('dompdf.wrapper');
        $pdf->loadView('admin.moora.pdf_report', compact(
            'alternatives',
            'criteria',
            'normalization',
            'weight',
            'valueMoora',
            'normDivisor'
        ));

        return $pdf->download('laporan_moora.pdf');
    }

    public function downloadPDFUser(Request $request)
    {
        $criteria = Criteria::with('subCriteria')->get();
        $selectedSubCriteria = $request->input('criteria', []);
        $alternatives = Alternative::with(['values.subCriteria', 'student'])->get();

        $filteredAlternatives = $alternatives;
        $suggestions = collect();

        if (!empty($selectedSubCriteria)) {
            $filteredAlternatives = $alternatives->filter(function ($alt) use ($selectedSubCriteria) {
                foreach ($selectedSubCriteria as $sub_id) {
                    if (!$sub_id) continue;
                    $match = $alt->values->firstWhere('sub_criteria_id', $sub_id);
                    if (!$match) return false;
                }
                return true;
            })->values();


            if ($filteredAlternatives->isEmpty()) {
                $suggestions = $alternatives->values();
            }
        }

        $originalAlternativeCount = $filteredAlternatives->count();

        // Gunakan suggestions jika AND logic kosong
        $alternatives = $originalAlternativeCount > 0 ? $filteredAlternatives : $suggestions;

        // Normalisasi bobot kriteria
        $totalWeight = $criteria->sum('weight') ?: 1;
        $weight = $criteria->pluck('weight', 'id')->map(fn($w) => $w / $totalWeight);

        // Ambil semua nilai alternatif berdasarkan sub_criterias.value
        $altValues = [];

        foreach ($alternatives as $alt) {
            foreach ($criteria as $c) {
                $altValues[$alt->id][$c->id] = 0;
                foreach ($alt->values as $val) {
                    $sub = $val->subCriteria;
                    if ($sub && $sub->criteria_id === $c->id) {
                        $altValues[$alt->id][$c->id] = $sub->value;
                        break;
                    }
                }
            }
        }

        // Normalisasi akar
        $normDivisor = [];
        foreach ($criteria as $c) {
            $sumSquares = 0;
            foreach ($alternatives as $alt) {
                $val = $altValues[$alt->id][$c->id] ?? 0;
                $sumSquares += pow($val, 2);
            }
            $normDivisor[$c->id] = sqrt($sumSquares) ?: 1;
        }

        // MOORA
        $normalization = [];
        $valueMoora = [];

        foreach ($alternatives as $alt) {
            $benefit = 0;
            $cost = 0;

            foreach ($criteria as $c) {
                $raw = $altValues[$alt->id][$c->id] ?? 0;
                $norm = $raw / ($normDivisor[$c->id] ?: 1);
                $weighted = $norm * $weight[$c->id];

                $normalization[$alt->id][$c->id] = $weighted;

                if (strtolower($c->attribute_type) === 'benefit') {
                    $benefit += $weighted;
                } else {
                    $cost += $weighted;
                }
            }

            $valueMoora[$alt->id] = $benefit - $cost;
        }

        arsort($valueMoora);

        // Generate PDF
        $pdf = app('dompdf.wrapper');
        $pdf->loadView('admin.moora.pdf_report_user', compact(
            'criteria',
            'alternatives',
            'normalization',
            'weight',
            'valueMoora',
            'normDivisor',
            'suggestions',
            'originalAlternativeCount',
            'selectedSubCriteria'
        ));

        return $pdf->download('laporan_moora.pdf');
    }
}
