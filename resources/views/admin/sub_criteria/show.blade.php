@extends('layouts.app')

@section('title', 'Detail Skala Konversi Nilai Rapor')

@section('content')

    <div class="col-lg-12 order-lg-1">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h5 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-eye"></i> Detail Skala Konversi: {{ $subCriteria->name ?? 'N/A' }}
                </h5>
            </div>
            <div class="card-body">
                <h6 class="font-weight-bold text-info mb-3">
                    <i class="fas fa-info-circle"></i> Informasi Kriteria Induk
                </h6>
                <table class="table table-bordered mb-4">
                    <tr>
                        <th style="width: 250px;">Jurusan</th>
                        <td>{{ $subCriteria->criteria->major->name ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th>Kriteria Induk (Mata Pelajaran)</th>
                        <td>
                            <span class="font-weight-bold">{{ $subCriteria->criteria->subject->name ?? 'N/A' }}</span>
                            ({{ $subCriteria->criteria->subject->code ?? 'N/A' }})
                        </td>
                    </tr>
                    <tr>
                        <th>Bobot | Atribut</th>
                        <td>
                            <span class="badge badge-warning p-2">Bobot: {{ $subCriteria->criteria->weight }}</span>
                            <span class="badge badge-secondary p-2">Atribut:
                                {{ ucwords($subCriteria->criteria->attribute_type) }}</span>
                        </td>
                    </tr>
                </table>

                <hr>

                <h6 class="font-weight-bold text-info mt-4 mb-3">
                    <i class="fas fa-chart-bar"></i> Detail Skala Konversi
                </h6>
                <table class="table table-bordered">
                    <tr>
                        <th style="width: 250px;">Nama Skala / Sub Kriteria</th>
                        <td class="font-weight-bold">{{ $subCriteria->name }}</td>
                    </tr>

                    {{-- BARIS BARU: Rentang Nilai Rapor --}}
                    <tr>
                        <th>Rentang Nilai Rapor</th>
                        <td>
                            Dari {{ $subCriteria->min_value }} sampai {{ $subCriteria->max_value }}
                        </td>
                    </tr>

                    <tr>
                        <th>Nilai SPK (Value C)</th>
                        <td>
                            <span class="badge badge-success p-2 font-weight-bold">{{ $subCriteria->value }}</span>
                        </td>
                    </tr>
                </table>

                <div class="mt-4">
                    <a href="{{ route('admin.subcriteria.index') }}" class="btn btn-secondary"><i
                            class="fas fa-arrow-left"></i>
                        Kembali</a>

                    @auth
                        @if (auth()->user()->is_admin == 1)
                            <a href="{{ route('admin.subcriteria.edit', $subCriteria->id) }}" class="btn btn-primary">
                                <i class="fas fa-edit"></i> Simpan
                            </a>
                        @endif
                    @endauth
                </div>
            </div>
        </div>
    </div>

@endsection