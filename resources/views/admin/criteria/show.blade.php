@extends('layouts.app')

@section('title', 'Kriteria')

@section('content')

    <div class="col-lg-12 order-lg-1">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h5 class="m-0 font-weight-bold text-primary">Detail Kriteria</h5>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <tr>
                        <th>Kode</th>
                        <td>{{ $criteria->code }}</td>
                    </tr>
                    <tr>
                        <th>Nama</th>
                        <td>{{ $criteria->name }}</td>
                    </tr>
                    <tr>
                        <th>Bobot</th>
                        <td>{{ $criteria->weight * 10 }}</td>
                    </tr>
                    <tr>
                        <th>Atribut</th>
                        <td>{{ ucwords(str_replace('_', ' ', $criteria->attribute_type)) }}</td>
                    </tr>
                </table>

                <div class="mt-3">
                    <a href="{{ route('admin.criteria.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i>
                        Kembali</a>
                    @if (auth()->user()->is_admin == 1)
                        <x-button_edit route="admin.criteria.edit" :id="$criteria->id" />
                    @endif
                </div>
            </div>
        </div>
    </div>

@endsection
