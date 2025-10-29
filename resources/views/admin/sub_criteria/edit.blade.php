@extends('layouts.app')

@section('title', 'Ubah Sub Kriteria')

@section('content')

    <x-alert />

    <div class="col-lg-12 order-lg-1">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h5 class="m-0 font-weight-bold text-primary">Ubah Skala Nilai (Sub Kriteria)</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.subcriteria.update', $subCriteria->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <input type="hidden" name="criteria_id" value="{{ $subCriteria->criteria->id }}">

                    <div class="form-group">
                        <label for="criteria_display">Kriteria Induk (Mata Pelajaran)</label>
                        <input type="text" id="criteria_display" class="form-control"
                            value="Jurusan: {{ $subCriteria->criteria->major->name ?? 'N/A' }} | Mapel: {{ $subCriteria->criteria->subject->name ?? 'N/A' }} ({{ $subCriteria->criteria->weight }} | {{ ucwords($subCriteria->criteria->attribute_type) }})"
                            disabled>
                    </div>

                    <div class="form-group">
                        <label for="name">Nama Skala / Sub Kriteria</label>
                        <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror"
                            value="{{ old('name', $subCriteria->name) }}" placeholder="Contoh: Sangat Baik, Cukup, Rendah"
                            required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="value">Nilai Numerik (untuk perhitungan SPK)</label>
                        <input type="number" name="value" id="value"
                            class="form-control @error('value') is-invalid @enderror"
                            value="{{ old('value', $subCriteria->value) }}" placeholder="Contoh: 5, 4, 3, 2, 1" min="1"
                            required>
                        @error('value')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <a href="{{ route('admin.subcriteria.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Batal
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Simpan Perubahan
                    </button>
                </form>
            </div>
        </div>
    </div>

@endsection