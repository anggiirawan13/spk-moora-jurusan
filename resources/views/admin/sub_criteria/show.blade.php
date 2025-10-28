@extends('layouts.app')

@section('title', 'Sub Kriteria')

@section('content')

    <div class="col-lg-12 order-lg-1">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h5 class="m-0 font-weight-bold text-primary">Detail Kriteria</h5>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <tr>
                        <th>Kriteria</th>
                        <td>{{ $subCriteria->criteria->code }} - {{ $subCriteria->criteria->name }}</td>
                    </tr>
                    <tr>
                        <th>Nama Sub Kriteria</th>
                        <td>{{ $subCriteria->name }}</td>
                    </tr>
                    <tr>
                        <th>Nilai</th>
                        <td>{{ $subCriteria->value }}</td>
                    </tr>
                </table>

                <div class="mt-3">
                    <a href="{{ route('admin.subcriteria.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i>
                        Kembali</a>
                    @if (auth()->user()->is_admin == 1)
                        <x-button_edit route="admin.subcriteria.edit" :id="$subCriteria->id" />
                    @endif
                </div>
            </div>
        </div>
    </div>

@endsection
