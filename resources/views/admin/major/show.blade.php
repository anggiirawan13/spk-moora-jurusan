@extends('layouts.app')

@section('title', 'Jurusan')

@section('content')

    <div class="container">
        <div class="card">
            <div class="card-header">
                <h5 class="m-0 font-weight-bold text-primary">Detail Jurusan: {{ $major->name }}</h5>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <tr>
                        <th>Kode Jurusan</th>
                        <td>{{ $major->code }}</p>
                        </td>
                    </tr>

                    <tr>
                        <th>Nama Jurusan</th>
                        <td>{{ $major->name }}</p>
                        </td>
                    </tr>

                    <tr>
                        <th>Deskripsi Jurusan</th>
                        <td>{{ $major->description }}</p>
                        </td>
                    </tr>

                    <tr>
                        <th>Dibuat Pada</th>
                        <td>{{ $major->created_at->format('d-m-Y H:i') }}</p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th>Diperbarui Pada</th>
                        <td>{{ $major->updated_at->format('d-m-Y H:i') }}</p>
                        </td>
                    </tr>
                </table>
            </div>
            <div class="card-footer">
                <a href="{{ route('admin.major.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i>
                    Kembali</a>
                @if (auth()->user()->is_admin == 1)
                <x-button_edit route="admin.major.edit" :id="$major->id" />
                @endif
            </div>
        </div>
    </div>

@endsection
