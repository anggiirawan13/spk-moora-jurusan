@extends('layouts.app')

@section('title', 'Detail Nilai Alternatif')

@section('content')

    <div class="col-lg-12 order-lg-1">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h5 class="m-0 font-weight-bold text-primary">Detail Nilai Siswa (Alternatif)</h5>
            </div>
            <div class="card-body">

                <h6 class="mt-2 font-weight-bold text-info">Informasi Siswa</h6>
                <table class="table table-bordered mb-4">
                    <tr>
                        <th style="width: 200px;">Nama Siswa</th>
                        <td>{{ $alternative->student->name ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th>NIS/NISN</th>
                        <td>{{ $alternative->student->nis ?? $alternative->student->nisn ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th>Jurusan Pilihan Awal</th>
                        <td>{{ $alternative->student->major->name ?? '-' }}</td>
                    </tr>
                </table>
                <hr>

                <h5 class="mt-4 font-weight-bold text-info">Nilai Kriteria Berdasarkan Jurusan</h5>

                @forelse ($majorsWithValues as $major)
                    @php
                        $majorCollapseId = 'major-detail-' . $major->id;
                    @endphp

                    <div class="card shadow mb-3 border-left-success">

                        <a href="#{{ $majorCollapseId }}"
                            class="card-header py-3 d-flex justify-content-between align-items-center collapsed text-decoration-none"
                            data-toggle="collapse" aria-expanded="false" aria-controls="{{ $majorCollapseId }}">
                            <h6 class="m-0 font-weight-bold text-success">
                                <i class="fas fa-graduation-cap"></i> Jurusan:
                                <span class="font-weight-bold">{{ $major->name ?? 'N/A' }}</span>
                            </h6>
                        </a>

                        <div id="{{ $majorCollapseId }}" class="collapse">
                            <div class="card-body p-0">

                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped mb-0">
                                        <thead class="thead-light">
                                            <tr>
                                                <th style="width: 30%;">Mata Pelajaran (Kriteria)</th>
                                                <th style="width: 40%;">Sub-Kriteria Terpilih (Skala Nilai)</th>
                                                <th style="width: 15%;" class="text-center">Nilai SPK</th>
                                                <th style="width: 15%;" class="text-center">Atribut</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($major->criteria_groups as $valuesGroup)
                                                @php
                                                    $value = $valuesGroup->first();
                                                    $criteria = $value->subCriteria->criteria;
                                                @endphp
                                                <tr>
                                                    <td>
                                                        {{ $criteria->subject->code ?? '-' }} -
                                                        {{ $criteria->subject->name ?? '-' }}
                                                    </td>
                                                    <td>
                                                        <span class="font-weight-bold text-primary">
                                                            {{ $value->subCriteria->name ?? '-' }}
                                                        </span>
                                                    </td>
                                                    <td class="text-center">{{ $value->subCriteria->value ?? '-' }}</td>
                                                    <td class="text-center">
                                                        <span class="badge badge-secondary">
                                                            {{ ucwords($criteria->attribute_type ?? '-') }}
                                                        </span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                            </div>
                        </div>

                    </div>
                @empty
                    <div class="alert alert-warning text-center">
                        Alternatif ini belum memiliki nilai yang terekam berdasarkan Jurusan.
                    </div>
                @endforelse

                <hr class="mt-4">

                <div class="mt-3">
                    <x-button_back route="admin.alternative.index" />
                    @if (auth()->user()->is_admin == 1)
                        <x-button_edit route="admin.alternative.edit" :id="$alternative->id" />
                    @endif
                </div>

            </div>
        </div>
    </div>

@endsection