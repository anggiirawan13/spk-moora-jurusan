@extends('layouts.app')

@section('title', 'Ubah Nilai Alternatif')

@section('content')

    <x-alert />

    <div class="col-lg-12 order-lg-1">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h5 class="m-0 font-weight-bold text-primary">Ubah Data Nilai Siswa (Alternatif)</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.alternative.update', $alternative->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="form-group">
                        <label for="student_name">Siswa (Alternatif)</label>
                        <input type="text" id="student_name" class="form-control" 
                            value="{{ $alternative->student->nis ?? 'N/A' }} - {{ $alternative->student->name ?? 'Siswa Tidak Ditemukan' }}" disabled>
                        
                        <input type="hidden" name="student_id" value="{{ $alternative->student_id }}">
                    </div>

                    <hr>
                    <h5 class="font-weight-bold text-info mt-4 mb-3">Ubah Nilai Rapor Berdasarkan Jurusan & Kriteria:</h5>

                    @forelse ($majorsWithCriteria as $major)
                        @php
                            $majorCollapseId = 'major-input-' . $major->id;
                            $hasSelectedCriteria = $major->criteria->contains(function($k) use ($selectedSubs) {
                                return array_key_exists($k->id, $selectedSubs);
                            });
                        @endphp

                        <div class="card shadow mb-4 border-left-success">
                            
                            <a href="#{{ $majorCollapseId }}" 
                               class="card-header py-3 d-flex justify-content-between align-items-center {{ $hasSelectedCriteria ? '' : 'collapsed' }} text-decoration-none" 
                               data-toggle="collapse" 
                               aria-expanded="{{ $hasSelectedCriteria ? 'true' : 'false' }}" 
                               aria-controls="{{ $majorCollapseId }}">
                                <h6 class="m-0 font-weight-bold text-success">
                                    <i class="fas fa-graduation-cap"></i> Jurusan: 
                                    <span class="font-weight-bold">{{ $major->name ?? 'N/A' }} ({{ $major->code ?? 'N/A' }})</span>
                                    @if($hasSelectedCriteria)
                                        <span class="badge badge-success ml-2">Nilai Terekam</span>
                                    @endif
                                </h6>
                            </a>
                            
                            <div id="{{ $majorCollapseId }}" class="collapse {{ $hasSelectedCriteria ? 'show' : '' }}">
                                <div class="card-body">
                                    
                                    @forelse ($major->criteria as $k)
                                        @php
                                            $criteriaCollapseId = 'criteria-input-' . $k->id;
                                            $currentSelectedId = $selectedSubs[$k->id] ?? null;
                                        @endphp

                                        <div class="card mb-3 border-left-primary">
                                            
                                            <a href="#{{ $criteriaCollapseId }}"
                                                class="card-header py-2 d-flex justify-content-between align-items-center {{ $currentSelectedId ? '' : 'collapsed' }} text-decoration-none"
                                                data-toggle="collapse" aria-expanded="{{ $currentSelectedId ? 'true' : 'false' }}"
                                                aria-controls="{{ $criteriaCollapseId }}">
                                                <h6 class="m-0 font-weight-bold text-primary">
                                                    <i class="fas fa-book-open"></i> {{ $k->subject->name ?? 'N/A' }} 
                                                    | Bobot: {{ $k->weight }}
                                                    @if($currentSelectedId)
                                                        <span class="badge badge-primary ml-2">Nilai Terpilih</span>
                                                    @endif
                                                </h6>
                                            </a>
                                            
                                            <div id="{{ $criteriaCollapseId }}" class="collapse {{ $currentSelectedId ? 'show' : '' }}">
                                                <div class="card-body">
                                                    <div class="form-group">
                                                        <label for="criteria_{{ $k->id }}">Pilih Nilai Rapor (Skala Sub Kriteria)</label>

                                                        <select class="form-control @error('criteria.' . $k->id) is-invalid @enderror"
                                                            name="criteria[{{ $k->id }}]" id="criteria_{{ $k->id }}" required>
                                                            <option value="" disabled>-- Pilih Skala Nilai --</option>

                                                            @foreach ($k->subCriteria as $sub)
                                                                <option value="{{ $sub->id }}" 
                                                                    {{ ($currentSelectedId == $sub->id) || (old('criteria.' . $k->id) == $sub->id) ? 'selected' : '' }}>
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
                        <x-button_save text="Simpan Perubahan" />
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection
