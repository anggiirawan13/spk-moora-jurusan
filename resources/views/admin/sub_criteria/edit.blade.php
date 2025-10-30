@extends('layouts.app')

@section('title', 'Ubah Skala Konversi Nilai Rapor') {{-- Judul diubah --}}

@section('content')

    <x-alert />

    <div class="col-lg-12 order-lg-1">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h5 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-edit"></i> Ubah Skala Konversi Nilai Rapor
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.subcriteria.update', $subCriteria->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    {{-- INFORMASI KRITERIA INDUK --}}
                    <h6 class="font-weight-bold text-info mb-3">
                        <i class="fas fa-info-circle"></i> Kriteria Induk yang Sedang Dikonfigurasi
                    </h6>
                    <table class="table table-bordered mb-4 bg-light">
                        <tr>
                            <th style="width: 250px;">Jurusan (Major)</th>
                            <td class="font-weight-bold">{{ $subCriteria->criteria->major->name ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Mata Pelajaran (Subject)</th>
                            <td class="font-weight-bold text-primary">{{ $subCriteria->criteria->subject->name ?? 'N/A' }}
                            </td>
                        </tr>
                        <tr>
                            <th>Bobot</th>
                            <td>
                                <span class="font-weight-bold text-warning p-2">{{ $subCriteria->criteria->weight }}</span>
                            </td>
                        </tr>
                        <tr>
                            <th>Atribut</th>
                            <td>
                                <span
                                    class="font-weight-bold text-secondary p-2">{{ ucwords($subCriteria->criteria->attribute_type) }}</span>
                            </td>
                        </tr>
                    </table>

                    <hr>

                    {{-- FORM INPUT RENTANG KONVERSI --}}
                    <h6 class="font-weight-bold text-info mt-4 mb-3">
                        <i class="fas fa-sliders-h"></i> Detail Skala Konversi
                    </h6>

                    <div class="form-group">
                        <label for="name">Nama Skala (Opsional, cth: A, Baik Sekali)</label>
                        <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror"
                            value="{{ old('name', $subCriteria->name) }}" placeholder="Cth: Sangat Baik atau A">
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="value">Nilai SPK Konversi (Value C)</label>
                        <input type="number" id="value" name="value"
                            class="form-control @error('value') is-invalid @enderror"
                            value="{{ old('value', $subCriteria->value) }}" placeholder="Contoh: 5 (Angka 1 sampai 5)"
                            min="1" max="5">
                        <small class="form-text text-muted">Nilai ini adalah nilai $X_{ij}$ yang akan digunakan dalam
                            perhitungan MOORA (biasanya 1 sampai 5).</small>
                        @error('value')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="min_value">Nilai Rapor Minimum (Angka)</label>
                                <input type="number" step="0.1" id="min_value" name="min_value"
                                    class="form-control @error('min_value') is-invalid @enderror"
                                    value="{{ old('min_value', $subCriteria->min_value) }}" placeholder="Contoh: 90" min="0"
                                    max="100">
                                @error('min_value')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="max_value">Nilai Rapor Maksimum (Angka)</label>
                                <input type="number" step="0.1" id="max_value" name="max_value"
                                    class="form-control @error('max_value') is-invalid @enderror"
                                    value="{{ old('max_value', $subCriteria->max_value) }}" placeholder="Contoh: 100"
                                    min="0" max="100">
                                @error('max_value')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <p class="text-danger mt-1 mb-4">
                        <i class="fas fa-exclamation-triangle"></i> Pastikan Rentang Nilai Rapor (Minimum -
                        Maksimum) tidak tumpang tindih dengan rentang lain untuk kriteria ini.
                    </p>

                    <hr class="mt-4">

                    <a href="{{ route('admin.subcriteria.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Batal
                    </a>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save"></i> Simpan Perubahan
                    </button>
                </form>
            </div>
        </div>
    </div>

@endsection