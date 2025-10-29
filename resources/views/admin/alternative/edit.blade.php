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

                    {{-- === BAGIAN 1: INFORMASI SISWA (TETAP) === --}}
                    <div class="form-group">
                        <label for="student_name">Siswa (Alternatif)</label>
                        <input type="text" id="student_name" class="form-control font-weight-bold" 
                            value="{{ $alternative->student->nis ?? 'N/A' }} - {{ $alternative->student->name ?? 'Siswa Tidak Ditemukan' }}" disabled>
                        
                        {{-- Kirim student_id yang lama --}}
                        <input type="hidden" name="student_id" value="{{ $alternative->student_id }}">
                    </div>

                    <hr>
                    
                    {{-- === BAGIAN 2: UBAH NILAI KRITERIA (LOOPING SEMUA KRITERIA SEMUA JURUSAN) === --}}
                    <h5 class="font-weight-bold text-info mt-4 mb-3">
                        <i class="fas fa-edit"></i> Ubah Nilai Rapor Berdasarkan Jurusan & Kriteria:
                    </h5>

                    <p class="text-secondary">
                        Pilih skala nilai (Sub Kriteria) yang baru atau pertahankan nilai yang sudah ada. Nilai yang tersimpan sebelumnya ditandai **(Nilai Terpilih)**.
                    </p>

                    {{-- LOOP UTAMA: JURUSAN (LAPISAN 1 - COLLAPSE JURUSAN) --}}
                    {{-- MENGGANTI: $uniqueCriteriaForInput dengan $majorsWithCriteria --}}
                    @forelse ($majorsWithCriteria as $major) 
                        @php
                            $majorCollapseId = 'major-input-edit-' . $major->id;
                        @endphp

                        <div class="card shadow mb-4 border-left-success">

                            {{-- TOMBOL TOGGLE JURUSAN --}}
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
                            
                            {{-- KONTEN COLLAPSIBLE JURUSAN --}}
                            <div id="{{ $majorCollapseId }}" class="collapse">
                                <div class="card-body">
                                    
                                    {{-- LOOP BERSARANG: KRITERIA (LAPISAN 2 - COLLAPSE KRITERIA) --}}
                                    @forelse ($major->criteria as $k)
                                        @php
                                            $criteriaCollapseId = 'criteria-input-edit-' . $k->id;
                                            
                                            // Ambil ID Sub Kriteria yang tersimpan untuk kriteria ini ($k->id)
                                            // $selectedSubs adalah array [criteria_id => sub_criteria_id]
                                            $currentSelectedId = $selectedSubs[$k->id] ?? null;
                                            
                                            // Gunakan old() jika ada error validasi, jika tidak gunakan nilai yang tersimpan ($currentSelectedId)
                                            // PENTING: Mengubah nama 'sub_criteria.' menjadi 'criteria.' agar konsisten dengan Controller
                                            $selectedValue = old('criteria.'.$k->id, $currentSelectedId);
                                        @endphp

                                        <div class="card mb-3 border-left-primary">
                                            
                                            {{-- HEADER KRITERIA (TOGGLE) --}}
                                            <a href="#{{ $criteriaCollapseId }}"
                                                class="card-header py-2 d-flex justify-content-between align-items-center collapsed text-decoration-none"
                                                data-toggle="collapse" aria-expanded="false"
                                                aria-controls="{{ $criteriaCollapseId }}">
                                                <h6 class="m-0 font-weight-bold text-primary">
                                                    <i class="fas fa-book-open"></i> {{ $k->subject->name ?? 'N/A' }} 
                                                    | Bobot: {{ $k->weight }}
                                                </h6>
                                            </a>
                                            
                                            {{-- KONTEN INPUT (COLLAPSIBLE) --}}
                                            <div id="{{ $criteriaCollapseId }}" class="collapse">
                                                <div class="card-body">
                                                    <div class="form-group">
                                                        <label for="criteria_{{ $k->id }}">Pilih Nilai Rapor (Skala Sub Kriteria)</label>

                                                        {{-- NAMA INPUT HARUS SAMA DENGAN VALIDASI DI CONTROLLER: name="criteria[{{ $k->id }}]" --}}
                                                        <select class="form-control @error('criteria.' . $k->id) is-invalid @enderror"
                                                            name="criteria[{{ $k->id }}]" id="criteria_{{ $k->id }}" required>
                                                            <option value="" disabled selected>-- Pilih Skala Nilai --</option>

                                                            @foreach ($k->subCriteria as $sub)
                                                                <option value="{{ $sub->id }}" 
                                                                        {{ $selectedValue == $sub->id ? 'selected' : '' }}>
                                                                    {{ $sub->name }} (Nilai SPK: {{ $sub->value }})
                                                                    {{-- Tanda untuk nilai yang sudah tersimpan --}}
                                                                    @if ($currentSelectedId == $sub->id)
                                                                        <span class="font-weight-bold text-success">(Nilai Terpilih)</span>
                                                                    @endif
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
                            </div> {{-- Akhir Konten Collapsible Jurusan --}}

                        </div>
                    @empty
                        <div class="alert alert-warning text-center">
                            Belum ada Jurusan yang terdaftar atau memiliki Kriteria.
                        </div>
                    @endforelse
                    {{-- === AKHIR BAGIAN UBAH NILAI KRITERIA === --}}


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