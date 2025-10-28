@extends('layouts.app')

@section('title', 'Mata Pelajaran')

@section('content')

    @if ($errors->any())
        @foreach ($errors->all() as $error)
            <div class="alert alert-danger">{{ $error }}</div>
        @endforeach
    @endif

    <div class="card">
        <div class="card-header py-3">
            <h5 class="m-0 font-weight-bold text-primary">Tambah Data Mata Pelajaran</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.subject.store') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label>Kode Mata Pelajaran</label>
                    <input type="text" class="form-control" name="code" required>
                </div>

                <div class="form-group">
                    <label>Nama Mata Pelajaran</label>
                    <input type="text" class="form-control" name="name" required>
                </div>
                
                <div class="form-group">
                    <x-button_back route="admin.subject.index" />
                    <x-button_save />
                </div>
            </form>
        </div>
    </div>

@endsection
