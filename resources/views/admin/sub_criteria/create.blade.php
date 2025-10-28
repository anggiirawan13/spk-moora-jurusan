@extends('layouts.app')

@section('title', 'Kriteria')

@section('content')

    <x-alert />

    <div class="col-lg-12 order-lg-1">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h5 class="m-0 font-weight-bold text-primary">Tambah Data Kriteria</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.subcriteria.store') }}" method="POST">
                    @csrf

                    <input type="hidden" name="criteria_id" value="{{ $criteria->id }}">

                    <div class="form-group">
                        <label for="criteria_display">Kriteria</label>
                        <input type="text" id="criteria_display" class="form-control"
                            value="{{ $criteria->code }} - {{ $criteria->name }}" disabled>
                    </div>

                    <div class="form-group">
                        <label for="name">Nama Sub Kriteria</label>
                        <input type="text" name="name" id="name"
                            class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="value">Nilai</label>
                        <input type="number" name="value" id="value"
                            class="form-control @error('value') is-invalid @enderror" value="{{ old('value') }}"
                            min="1" required>
                        @error('value')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <a href="{{ route('admin.subcriteria.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Batal
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Simpan
                    </button>
                </form>
            </div>
        </div>
    </div>

@endsection
