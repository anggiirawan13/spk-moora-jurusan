@extends('layouts.app')

@section('title', 'Tambah Skala Nilai (Sub Kriteria)')

@section('content')

    <x-alert />

    <div class="col-lg-12 order-lg-1">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h5 class="m-0 font-weight-bold text-primary"><i class="fas fa-plus-circle"></i> Tambah Skala Nilai (Sub Kriteria)</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.subcriteria.store') }}" method="POST">
                    @csrf

                    <input type="hidden" name="criteria_id" value="{{ $criteria->id }}">

                    {{-- INFORMASI KRITERIA INDUK --}}
                    <div class="form-group">
                        <label for="criteria_display">Kriteria Induk (Mata Pelajaran)</label>
                        <input type="text" id="criteria_display" class="form-control font-weight-bold"
                            value="Jurusan: {{ $criteria->major->name ?? 'N/A' }} | Mapel: {{ $criteria->subject->name ?? 'N/A' }} (Bobot: {{ $criteria->weight }} | Atribut: {{ ucwords($criteria->attribute_type) }})"
                            disabled>
                    </div>

                    <hr>
                    <h6 class="font-weight-bold text-info mt-4 mb-3">
                        <i class="fas fa-list-alt"></i> Pilih Skala Nilai yang Akan Digunakan:
                    </h6>
                    
                    @php
                        // Data sub kriteria hardcode yang tersedia
                        $fixedSubCriteria = [
                            ['name' => 'Sangat Baik', 'value' => 5],
                            ['name' => 'Baik', 'value' => 4],
                            ['name' => 'Cukup', 'value' => 3],
                            ['name' => 'Kurang', 'value' => 2],
                            ['name' => 'Sangat Kurang', 'value' => 1],
                        ];

                        // Ambil nilai SPK yang sudah ada untuk kriteria ini
                        $existingValues = $criteria->subCriteria->pluck('value')->toArray();
                    @endphp

                    <div class="row">
                        @foreach ($fixedSubCriteria as $index => $item)
                            @php
                                $isDisabled = in_array($item['value'], $existingValues);
                                $statusClass = $isDisabled ? 'text-muted bg-light' : 'text-dark';
                                $badgeClass = $isDisabled ? 'badge-secondary' : 'badge-primary';
                            @endphp
                            <div class="col-md-6 col-lg-4 mb-3">
                                <div class="card p-3 h-100 border-left-{{ $isDisabled ? 'secondary' : 'success' }} shadow-sm">
                                    <div class="custom-control custom-checkbox {{ $statusClass }}">
                                        
                                        {{-- Checkbox untuk memilih sub kriteria --}}
                                        <input type="checkbox" class="custom-control-input" 
                                            id="sub_criteria_{{ $item['value'] }}" 
                                            name="sub_criteria_to_add[{{ $item['value'] }}]" 
                                            value="1" 
                                            {{ $isDisabled ? 'disabled' : '' }}
                                            {{ old('sub_criteria_to_add.'.$item['value']) ? 'checked' : '' }}
                                            >
                                        
                                        <label class="custom-control-label font-weight-bold" for="sub_criteria_{{ $item['value'] }}">
                                            {{ $item['name'] }}
                                            @if ($isDisabled)
                                                <span class="badge badge-warning ml-1">Sudah Ada</span>
                                            @endif
                                        </label>
                                        
                                        <p class="mb-0 mt-1">
                                            Nilai SPK: <span class="badge {{ $badgeClass }} p-2">{{ $item['value'] }}</span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endforeach

                        {{-- Menampilkan error jika tidak ada yang dipilih --}}
                        @if ($errors->has('sub_criteria_to_add') || $errors->has('criteria_id') && old('sub_criteria_to_add') == null)
                            <div class="col-12 mt-3">
                                <div class="alert alert-danger">Pilih minimal satu Skala Nilai untuk ditambahkan.</div>
                            </div>
                        @endif
                    </div>
                    
                    <hr class="mt-4">

                    <a href="{{ route('admin.subcriteria.index', $criteria->id) }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Kembali ke Daftar Sub Kriteria
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Simpan Pilihan
                    </button>
                </form>
            </div>
        </div>
    </div>

@endsection