@extends('layouts.app')

@section('title', 'Tambah Nilai Alternatif')

@section('content')

    <x-alert />

    <div class="col-lg-12 order-lg-1">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h5 class="m-0 font-weight-bold text-primary">Input Data Nilai Siswa (Alternatif Baru)</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.alternative.store') }}" method="POST">
                    @csrf

                    <div class="form-group">
                        <label for="student_id">Pilih Siswa (Alternatif)</label>
                        <select class="form-control @error('student_id') is-invalid @enderror" name="student_id"
                            id="student_id" required>
                            <option value="" hidden>Pilih Siswa yang belum dinilai</option>
                            @forelse ($students as $student)
                                <option value="{{ $student->id }}" {{ old('student_id') == $student->id ? 'selected' : '' }}>
                                    {{ $student->nis }} - {{ $student->name }}
                                </option>
                            @empty
                                <option value="" disabled>Semua siswa sudah terdaftar sebagai alternatif.</option>
                            @endforelse
                        </select>
                        @error('student_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <hr>
                    <h5 class="font-weight-bold text-info mt-4 mb-3">Input Nilai Rapor Berdasarkan Jurusan & Kriteria:</h5>

                    @forelse ($majorsWithCriteria as $major)
                        @php
                            $majorCollapseId = 'major-input-' . $major->id;
                        @endphp

                        <div class="card shadow mb-4 border-left-success">

                            <a href="#{{ $majorCollapseId }}" 
                               class="card-header py-3 d-flex justify-content-between align-items-center collapsed text-decoration-none" 
                               data-toggle="collapse" 
                               aria-expanded="false" 
                               aria-controls="{{ $majorCollapseId }}">
                                <h6 class="m-0 font-weight-bold text-success">
                                    <i class="fas fa-graduation-cap"></i> Jurusan: 
                                    <span class="font-weight-bold">{{ $major->name ?? 'N/A' }} ({{ $major->code ?? 'N/A' }})</span>
                                </h6>
                            </a>
                            
                            <div id="{{ $majorCollapseId }}" class="collapse">
                                <div class="card-body">
                                    
                                    @forelse ($major->criteria as $k)
                                        @php
                                            $criteriaCollapseId = 'criteria-input-' . $k->id;
                                        @endphp

                                        <div class="card mb-3 border-left-primary">
                                            
                                            <a href="#{{ $criteriaCollapseId }}"
                                                class="card-header py-2 d-flex justify-content-between align-items-center collapsed text-decoration-none"
                                                data-toggle="collapse" aria-expanded="false"
                                                aria-controls="{{ $criteriaCollapseId }}">
                                                <h6 class="m-0 font-weight-bold text-primary">
                                                    <i class="fas fa-book-open"></i> {{ $k->subject->name ?? 'N/A' }} 
                                                    | Bobot: {{ $k->weight }}
                                                </h6>
                                            </a>
                                            
                                            <div id="{{ $criteriaCollapseId }}" class="collapse">
                                                <div class="card-body">
                                                    <div class="form-group">
                                                        <label for="criteria_{{ $k->id }}">Pilih Nilai Rapor (Skala Sub Kriteria)</label>

                                                        <select class="form-control @error('criteria.' . $k->id) is-invalid @enderror"
                                                            name="criteria[{{ $k->id }}]" id="criteria_{{ $k->id }}" required>
                                                            <option value="" disabled selected>-- Pilih Skala Nilai --</option>

                                                            @foreach ($k->subCriteria as $sub)
                                                                <option value="{{ $sub->id }}" {{ old('criteria.' . $k->id) == $sub->id ? 'selected' : '' }}>
                                                                    {{ $sub->name }} (Nilai SPK: {{ $sub->value }})
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        @error('criteria.' . $k->id)
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror

                                                        @if($k->subCriteria->isEmpty())
                                                            <small class="text-danger">**Peringatan:** Kriteria ini belum memiliki skala nilai (Sub Kriteria)
                                                                yang ditetapkan!</small>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="alert alert-warning">
                                            Jurusan {{ $major->name ?? 'ini' }} belum memiliki Kriteria Mata Pelajaran yang ditetapkan.
                                        </div>
                                    @endforelse
                                    
                                </div>
                            </div>

                        </div>
                    @empty
                        <div class="alert alert-warning text-center">
                            Belum ada Jurusan yang terdaftar atau memiliki Kriteria.
                        </div>
                    @endforelse

                    <hr>

                    <div class="form-group mt-4">
                        <x-button_back route="admin.alternative.index" />
                        <x-button_save />
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection