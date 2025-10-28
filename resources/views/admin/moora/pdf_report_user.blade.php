@php
    $getValue = function ($alternative, $criteriaId) {
        foreach ($alternative->values as $val) {
            if ($val->subCriteria && $val->subCriteria->criteria_id === $criteriaId) {
                return $val->subCriteria->value;
            }
        }
        return 0;
    };
@endphp

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan MOORA</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11px;
            color: #222;
            margin: 20px;
        }

        h1 {
            font-size: 20px;
            margin-bottom: 10px;
        }

        .meta {
            font-size: 10px;
            margin-bottom: 20px;
        }

        .meta p {
            margin: 2px 0;
        }

        .section-title {
            background-color: #3f51b5;
            color: white;
            padding: 6px 10px;
            font-size: 12px;
            font-weight: bold;
            margin-top: 25px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0 20px;
            font-size: 10px;
        }

        th,
        td {
            border: 1px solid #999;
            padding: 5px;
            text-align: center;
        }

        th {
            background-color: #e0e0e0;
        }

        h4 {
            margin: 12px 0 6px;
            font-size: 11px;
        }

        .footer {
            margin-top: 40px;
            font-size: 10px;
            text-align: center;
            color: #777;
        }

        .small {
            font-size: 9px;
        }
    </style>
</head>

<body>

    <h1>Laporan Perhitungan MOORA</h1>

    <div class="meta">
        <p><strong>Pengguna:</strong> {{ auth()->user()->name }}</p>
        <p><strong>Tanggal:</strong> {{ \Carbon\Carbon::now()->format('d-m-Y') }}</p>
        <p><strong>Waktu:</strong> {{ \Carbon\Carbon::now()->format('H:i:s') }}</p>
    </div>

    @if ($originalAlternativeCount === 0)
        <div class="alert alert-warning">
            <strong>Data tidak ditemukan sesuai filter.</strong><br>
            Menampilkan alternatif yang mendekati kriteria Anda.
        </div>
    @endif

    @if (!empty(request()->input('criteria')))
        <div class="section-title">Filter Kriteria yang Dipilih</div>
        <ul>
            @foreach ($criteria as $c)
                @php $chosen = request()->input("criteria.{$c->id}"); @endphp
                @if ($chosen)
                    <li>{{ $c->name }}:
                        {{ optional($c->subCriteria->firstWhere('id', $chosen))->name ?? '-' }}
                    </li>
                @endif
            @endforeach
        </ul>
    @endif

    {{-- Step 1: Nilai Alternatif --}}
    <div class="section-title">Step 1: Nilai Alternatif & Kuadrat</div>
    <p class="small">Rumus: ∑(x<sub>ij</sub>)² = x<sub>1j</sub>² + x<sub>2j</sub>² + ...</p>
    <table>
        <thead>
            <tr>
                <th>Alternatif</th>
                @foreach ($criteria as $c)
                    <th>{{ $c->name }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach ($alternatives as $a)
                <tr>
                    <td>{{ optional($a->car)->name ?? $a->name }}</td>
                    @foreach ($criteria as $c)
                        @php
                            $val = $getValue($a, $c->id);
                        @endphp
                        <td>{{ number_format($getValue($a, $c->id), 5) }}</td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td><strong>∑x<sub>ij</sub>²</strong></td>
                @foreach ($criteria as $c)
                    @php
                        $sumSquare = $alternatives->sum(function ($a) use ($c, $getValue) {
                            $v = $getValue($a, $c->id);
                            return pow($v, 2);
                        });
                    @endphp
                    <td>{{ number_format($sumSquare, 5) }}</td>
                @endforeach
            </tr>
        </tfoot>
    </table>

    {{-- Step 2: Akar Normalisasi --}}
    <div class="section-title">Step 2: Akar Total Tiap Kriteria</div>
    <p class="small">Rumus: √(∑x<sub>ij</sub>²)</p>
    <table>
        <thead>
            <tr>
                @foreach ($criteria as $c)
                    <th>{{ $c->name }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            <tr>
                @foreach ($criteria as $c)
                    <td>{{ number_format($normDivisor[$c->id], 5) }}</td>
                @endforeach
            </tr>
        </tbody>
    </table>

    {{-- Step 3: Normalisasi --}}
    <div class="section-title">Step 3: Normalisasi Nilai Alternatif</div>
    <p class="small">Rumus: r<sub>ij</sub> = x<sub>ij</sub> / √(∑x<sub>ij</sub>²)</p>
    <table>
        <thead>
            <tr>
                <th>Alternatif</th>
                @foreach ($criteria as $c)
                    <th>{{ $c->name }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach ($alternatives as $a)
                <tr>
                    <td>{{ optional($a->car)->name ?? $a->name }}</td>
                    @foreach ($criteria as $c)
                        @php
                            $raw = $getValue($a, $c->id);
                            $norm = $raw / ($normDivisor[$c->id] ?: 1);
                        @endphp
                        <td>{{ number_format($norm, 5) }}</td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Step 4: Dikali Bobot --}}
    <div class="section-title">Step 4: Nilai Normalisasi × Bobot</div>
    <p class="small">Rumus: y<sub>ij</sub> = r<sub>ij</sub> × w<sub>j</sub></p>
    <table>
        <thead>
            <tr>
                <th>Alternatif</th>
                @foreach ($criteria as $c)
                    <th>{{ $c->name }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach ($alternatives as $a)
                <tr>
                    <td>{{ optional($a->car)->name ?? $a->name }}</td>
                    @foreach ($criteria as $c)
                        <td>{{ number_format($normalization[$a->id][$c->id] ?? 0, 5) }}</td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Step 5 & 6: MOORA --}}
    <div class="section-title">Step 5–6: Nilai Akhir Yi dan Peringkat</div>
    <p class="small">Rumus: Yi = Σ(W<sub>j</sub> × r<sub>ij</sub>) benefit − Σ(W<sub>j</sub> × r<sub>ij</sub>) cost</p>
    <table>
        <thead>
            <tr>
                <th>Peringkat</th>
                <th>Alternatif</th>
                <th>Total Benefit</th>
                <th>Total Cost</th>
                <th>Yi</th>
            </tr>
        </thead>
        <tbody>
            @php
                $rank = 0;
                $prevYi = null;
            @endphp

            @foreach ($valueMoora as $id => $yi)
                @php
                    if ($yi !== $prevYi) {
                        $rank++;
                        $prevYi = $yi;
                    }

                    $alt = $alternatives->firstWhere('id', $id);
                    $benefit = $cost = 0;
                    foreach ($criteria as $c) {
                        $value = $normalization[$id][$c->id] ?? 0;
                        if (strtolower($c->attribute_type) === 'benefit') {
                            $benefit += $value;
                        } else {
                            $cost += $value;
                        }
                    }
                @endphp

                <tr>
                    <td>{{ $rank }}</td>
                    <td>{{ optional($alt->car)->name ?? ($alt->name ?? '—') }}</td>
                    <td>{{ number_format($benefit, 5) }}</td>
                    <td>{{ number_format($cost, 5) }}</td>
                    <td class="font-weight-bold">{{ number_format($yi, 5) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Dicetak oleh {{ auth()->user()->name }} pada {{ \Carbon\Carbon::now()->format('d-m-Y H:i:s') }}
    </div>

</body>

</html>
