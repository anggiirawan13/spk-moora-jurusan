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
                <form action="{{ route('admin.criteria.store') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label>Kode</label>
                        <input required type="text" class="form-control" name="code">
                    </div>
                    <div class="form-group">
                        <label>Nama</label>
                        <input required type="text" class="form-control" name="name">
                    </div>
                    <div class="form-group">
                        <label>Bobot</label>
                        <input required type="number" class="form-control" name="weight" min="0.01" step="0.01">
                    </div>
                    <div class="form-group">
                        <label for="attribute_type">Atribut</label>
                        <select required class="form-control" name="attribute_type" id="attribute_type">
                            <option hidden>Pilih atribut</option>
                            <option value="Cost">Cost</option>
                            <option value="Benefit">Benefit</option>
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
