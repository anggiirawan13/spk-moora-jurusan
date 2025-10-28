@extends('layouts.app')

@section('title', 'Jurusan')

@section('content')

    @if ($errors->any())
        @foreach ($errors->all() as $error)
            <div class="alert alert-danger">{{ $error }}</div>
        @endforeach
    @endif

    <div class="card">
        <div class="card-header py-3">
            <h5 class="m-0 font-weight-bold text-primary">Ubah Data Jurusan</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.major.update', $major->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label>Kode Jurusan</label>
                    <input type="text" class="form-control" name="code" value="{{ $major->name }}" required>
                </div>

                <div class="form-group">
                    <label>Nama Jurusan</label>
                    <input type="text" class="form-control" name="name" value="{{ $major->name }}" required>
                </div>

                <div class="form-group">
                    <label>Deskripsi Jurusan</label>
                    <input type="text" class="form-control" name="description" value="{{ $major->name }}" required>
                </div>

                <div class="form-group">
                    <x-button_back route="admin.major.index" />
                    <x-button_save />
                </div>
            </form>
        </div>
    </div>

@endsection
