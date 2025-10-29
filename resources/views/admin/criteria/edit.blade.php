@extends('layouts.app')

@section('title', 'Ubah Kriteria Penjurusan')

@section('content')

    <x-alert />

    <div class="col-lg-12 order-lg-1">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h5 class="m-0 font-weight-bold text-primary">Ubah Data Kriteria: {{ $criteria->subject->name ?? 'N/A' }}
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.criteria.update', $criteria->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="form-group">
                        <label for="major_id">Jurusan Tujuan</label>
                        <select required class="form-control @error('major_id') is-invalid @enderror" name="major_id"
                            id="major_id">
                            <option hidden value="">Pilih Jurusan yang akan dinilai</option>
                            @foreach ($majors as $major)
                                <option value="{{ $major->id }}" {{ old('major_id', $criteria->major_id) == $major->id ? 'selected' : '' }}>
                                    {{ $major->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('major_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="subject_id">Mata Pelajaran (Kriteria)</label>
                        <select required class="form-control @error('subject_id') is-invalid @enderror" name="subject_id"
                            id="subject_id">
                            <option hidden value="">Pilih Mata Pelajaran sebagai Kriteria</option>
                            @foreach ($subjects as $subject)
                                <option value="{{ $subject->id }}" {{ old('subject_id', $criteria->subject_id) == $subject->id ? 'selected' : '' }}>
                                    {{ $subject->code }} - {{ $subject->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('subject_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="weight">Bobot (W)</label>
                        <input required type="number" class="form-control @error('weight') is-invalid @enderror"
                            name="weight" id="weight" placeholder="Contoh: 0.25 (Pastikan total bobot Jurusan = 1.0)"
                            value="{{ old('weight', $criteria->weight) }}" min="0.01" step="0.01" max="1">
                        @error('weight')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="attribute_type">Tipe Atribut</label>
                        <select required class="form-control @error('attribute_type') is-invalid @enderror"
                            name="attribute_type" id="attribute_type">
                            <option hidden value="">Pilih tipe atribut</option>

                            <option value="Benefit" {{ old('attribute_type', $criteria->attribute_type) == 'Benefit' ? 'selected' : '' }}>
                                Benefit (Lebih tinggi lebih baik)
                            </option>
                            <option value="Cost" {{ old('attribute_type', $criteria->attribute_type) == 'Cost' ? 'selected' : '' }}>
                                Cost (Lebih rendah lebih baik)
                            </option>
                        </select>
                        @error('attribute_type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <a href="{{ route('admin.criteria.index') }}" class="btn btn-secondary"><i
                                class="fas fa-arrow-left"></i>
                            Kembali</a>
                        <x-button_save />
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection