@extends('layouts.app')

@section('title', 'Transmisi')

@section('content')

    <div class="container">
        <div class="card">
            <div class="card-header">
                <h5 class="m-0 font-weight-bold text-primary">Detail Akun: {{ $user->name }}</h5>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <img id="imagePreview" class="img-fluid"
                        style="max-width: 300px; {{ $user->image_name ? 'display: block;' : 'display: none;' }}"
                        src="{{ $user->image_name ? asset('storage/user/' . $user->image_name) : '' }}" />
                </div>
                <table class="table table-bordered">
                    <tr>
                        <th>Nama</th>
                        <td>{{ $user->name }}</p>
                        </td>
                    </tr>

                    <tr>
                        <th>Email</th>
                        <td>{{ $user->email }}
                    </tr>

                    <tr>
                        <th>Role</th>
                        <td>{{ $user->is_admin == 1 ? 'Admin' : 'User' }}
                    </tr>

                    <tr>
                        <th>Dibuat Pada</th>
                        <td>{{ $user->created_at->format('d-m-Y H:i') }}</p>
                        </td>
                    </tr>
                    <tr>
                        <th>Diperbarui Pada</th>
                        <td>{{ $user->updated_at->format('d-m-Y H:i') }}</p>
                        </td>
                    </tr>
                </table>
            </div>
            <div class="card-footer">
                <a href="{{ route('admin.user.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i>
                    Kembali</a>
                @if (auth()->user()->is_admin == 1)
                    <x-button_edit route="admin.user.edit" :id="$user->id" />
                @endif
            </div>
        </div>
    </div>

@endsection
