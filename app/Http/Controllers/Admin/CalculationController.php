<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Criteria;
use App\Models\Alternative;

class CalculationController extends Controller
{
    public function calculation(Request $request)
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

        return view('admin.moora.calculation', compact(
            'alternatives',
            'criteria',
            'normalization',
            'weight',
            'valueMoora',
            'normDivisor'
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
