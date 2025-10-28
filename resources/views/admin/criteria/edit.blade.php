@extends('layouts.app')

@section('title', 'Kriteria')

@section('content')

    <x-alert />

    <div class="col-lg-12 order-lg-1">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h5 class="m-0 font-weight-bold text-primary">Ubah Data Kriteria</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.criteria.update', $criteria->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="form-group">
                        <label>Kode</label>
                        <input required type="text" class="form-control" name="code" value="{{ $criteria->code }}">
                    </div>
                    <div class="form-group">
                        <label>Nama</label>
                        <input required type="text" class="form-control" name="name" value="{{ $criteria->name }}">
                    </div>
                    <div class="form-group">
                        <label>Bobot</label>
                        <input required type="number" class="form-control" name="weight" value="{{ $criteria->weight }}"
                        min="0.01" step="0.01">
                    </div>
                    <div class="form-group">
                        <label for="attribute_type">Atribut</label>
                        <select required class="form-control" name="attribute_type" id="attribute_type">
                            <option hidden>Pilih atribut</option>
                            <option {{ strtolower($criteria->attribute_type) == 'cost' ? 'selected' : '' }} value="Cost">Cost</option>
                            <option {{ strtolower($criteria->attribute_type) == 'benefit' ? 'selected' : '' }} value="Benefit">Benefit</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <a href="{{ route('admin.criteria.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i>
                            Kembali</a>
                        <x-button_save />
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection
