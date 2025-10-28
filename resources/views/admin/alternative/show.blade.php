@extends('layouts.app')

@section('title', 'Alternatif')

@section('content')

    <div class="col-lg-12 order-lg-1">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h5 class="m-0 font-weight-bold text-primary">Detail Alternatif</h5>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <tr>
                        <th>Nama Alternatif</th>
                        <td>{{ $alternative->car?->name }}</td>
                    </tr>
                </table>

                <h6 class="mt-4 font-weight-bold">Nilai Kriteria</h6>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Kode</th>
                            <th>Kriteria</th>
                            <th>Sub-Kriteria Terpilih</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($alternative->values as $value)
                            <tr>
                                <td>{{ $value->subCriteria->criteria->code ?? '-' }}</td>
                                <td>{{ $value->subCriteria->criteria->name ?? '-' }}</td>
                                <td>{{ $value->subCriteria->name ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="mt-3">
                    <x-button_back route="admin.alternative.index" />
                    @if (auth()->user()->is_admin == 1)
                        <x-button_edit route="admin.alternative.edit" :id="$alternative->id" />
                    @endif
                </div>
            </div>
        </div>
    </div>

@endsection
