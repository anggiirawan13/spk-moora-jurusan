@extends('layouts.app')

@section('title', 'Hasil Perhitungan MOORA')

@section('content')

    @php
        // Helper untuk mendapatkan Nilai Alternatif dari Sub Kriteria
        $getValue = function ($alternative, $criteriaId) {
            foreach ($alternative->values as $val) {
                if ($val->subCriteria && $val->subCriteria->criteria_id === $criteriaId) {
                    return $val->subCriteria->value;
                }
            }
            return 0;
        };
        // Helper untuk mendapatkan Nama Alternatif (Siswa)
        $getAltName = function ($alternative) {
            return optional($alternative->student)->name ?? ($alternative->name ?? '—');
        };
    @endphp

    <div class="container-fluid">
        <h1 class="h3 mb-4 text-gray-800">📊 Hasil Perhitungan MOORA (Per Jurusan)</h1>
        <hr>

        <a href="{{ route('admin.moora.download_pdf') }}" class="btn btn-success mb-4 shadow">
            <i class="fas fa-download"></i> Download Laporan PDF
        </a>

        <div class="card shadow mb-5 border-left-info">
            <div class="card-header bg-info text-white">
                <h6 class="m-0 font-weight-bold">Hasil Akhir: Rekomendasi Jurusan Terbaik</h6>
            </div>
            <div class="card-body">
                <p>
                    Perhitungan MOORA telah membandingkan setiap siswa terhadap semua kriteria jurusan yang tersedia. Peringkat didasarkan pada Nilai Y<sub>i</sub> Tertinggi yang didapatkan siswa dari jurusan manapun.
                </p>

                <table class="table table-striped table-bordered text-center">
                    <thead class="thead-dark">
                        <tr>
                            <th style="width: 5%;">Rank</th>
                            <th style="width: 25%;">Alternatif (Siswa)</th>
                            <th style="width: 20%;">Jurusan Saat Ini</th>
                            <th style="width: 25%;">Rekomendasi Jurusan Terbaik</th>
                            <th style="width: 25%;">Nilai Y<sub>i</sub> Tertinggi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $rank = 0;
                            $prevYi = null;
                        @endphp

                        @foreach ($recommendations as $data)
                            @php
                                $yi = $data['best_yi_value'];
                                $alt = $data['alternative'];
                                
                                // Perhitungan Rank
                                if ($yi !== $prevYi) {
                                    $rank++;
                                    $prevYi = $yi;
                                }
                                
                                $isCurrentMajorRecommended = ($data['current_major'] === $data['recommended_major']);
                            @endphp

                            <tr class="{{ $isCurrentMajorRecommended ? 'table-success font-weight-bold' : '' }}">
                                <td>
                                    @if($rank <= 3)
                                        <i class="fas fa-medal text-warning"></i> 
                                    @endif
                                    {{ $rank }}
                                </td>
                                <td class="text-left">{{ $getAltName($alt) }}</td>
                                
                                {{-- JURUSAN SAAT INI (diasumsikan sudah terisi dari relasi model) --}}
                                <td>{{ $data['current_major'] }}</td> 
                                
                                <td class="text-left">
                                    <span class="font-weight-bold {{ $isCurrentMajorRecommended ? 'text-success' : 'text-danger' }}">
                                        {{ $data['recommended_major'] }}
                                    </span>
                                    @if($isCurrentMajorRecommended)
                                        <br><small class="text-success">(Dianjurkan TETAP)</small>
                                    @else
                                        <br><small class="text-danger">(Dianjurkan PINDAH)</small>
                                    @endif
                                </td>
                                <td>{{ number_format($yi, 4) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <hr/>
        
        {{-- ========================================================================= --}}
        Detail Perhitungan MOORA per Jurusan
        {{-- ========================================================================= --}}

        @foreach ($allCalculations as $majorId => $calc)
            @php
                $major = $majors->firstWhere('id', $majorId);
                $majorName = optional($major)->name ?? 'Jurusan Tidak Dikenal';
                $criteria = $calc['criteria'];
                $alternatives = $calc['alternatives'];
            @endphp

            <div class="card shadow mb-5 border-left-primary">
                <div class="card-header bg-primary text-white">
                    <h6 class="m-0 font-weight-bold">Perhitungan untuk Jurusan: {{ $majorName }}</h6>
                </div>
                <div class="card-body">

                    {{-- 1. Matriks Keputusan Awal (x_ij) --}}
                    <h6>Step 1: Matriks Keputusan Awal (x<sub>ij</sub>)</h6>
                    <table class="table table-bordered table-sm text-center mb-4">
                        <thead class="thead-light">
                            <tr>
                                <th>Alternatif (Siswa)</th>
                                @foreach ($criteria as $c)
                                    <th>{{ $c->name }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($alternatives as $a)
                                <tr>
                                    <td class="text-left font-weight-bold">{{ $getAltName($a) }}</td>
                                    @foreach ($criteria as $c)
                                        <td>{{ number_format($calc['altValues'][$a->id][$c->id] ?? 0, 4) }}</td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="font-weight-bold bg-light">
                            <tr>
                                <td>∑(x<sub>ij</sub>)²</td>
                                @foreach ($criteria as $c)
                                    <td>
                                        @php
                                            $sumSquare = array_sum(array_map(fn($v) => pow($v, 2), array_column($calc['altValues'], $c->id)));
                                        @endphp
                                        {{ number_format($sumSquare, 4) }}
                                    </td>
                                @endforeach
                            </tr>
                        </tfoot>
                    </table>

                    {{-- 2. Vektor Normalisasi (D_j) --}}
                    <h6>Step 2: Vektor Normalisasi (D<sub>j</sub>)</h6>
                    <table class="table table-bordered table-sm text-center mb-4">
                        <thead class="thead-light">
                            <tr>
                                @foreach ($criteria as $c)
                                    <th>{{ $c->name }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="font-weight-bold bg-light">
                                @foreach ($criteria as $c)
                                    <td>
                                        {{ number_format($calc['normDivisor'][$c->id] ?? 0, 4) }}
                                    </td>
                                @endforeach
                            </tr>
                        </tbody>
                    </table>

                    {{-- 3 & 4. Matriks Normalisasi Berbobot (y_ij) --}}
                    <h6>Step 3 & 4: Matriks Normalisasi Berbobot (y<sub>ij</sub>)</h6>
                    <h6 class="mt-4">Detail Bobot Normalisasi (w<sub>j</sub>):</h6>
                    <table class="table table-sm table-bordered text-center mb-4">
                        <thead class="bg-warning">
                            <tr>
                                @foreach ($criteria as $c)
                                    <th>{{ $c->name }} <br> ({{ strtoupper($c->attribute_type) }})</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="font-weight-bold">
                                @foreach ($criteria as $c)
                                    <td>{{ number_format($calc['weight'][$c->id] ?? 0, 4) }}</td>
                                @endforeach
                            </tr>
                        </tbody>
                    </table>

                    <h6 class="mt-4">Matriks Berbobot (y<sub>ij</sub>):</h6>
                    <table class="table table-bordered table-sm text-center">
                        <thead class="thead-light">
                            <tr>
                                <th>Alternatif (Siswa)</th>
                                @foreach ($criteria as $c)
                                    <th>{{ $c->name }}</th>
                                @endforeach
                                <th>Y<sub>i</sub> Penuh</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($alternatives as $a)
                                <tr>
                                    <td class="text-left font-weight-bold">{{ $getAltName($a) }}</td>
                                    @foreach ($criteria as $c)
                                        <td>{{ number_format($calc['normalization'][$a->id][$c->id] ?? 0, 4) }}</td>
                                    @endforeach
                                    {{-- Kolom Y_i Penuh --}}
                                    <td>
                                        <span class="font-weight-bold text-success">
                                            {{ number_format($calc['valueMoora'][$a->id] ?? 0, 4) }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endforeach

    </div>

@endsection