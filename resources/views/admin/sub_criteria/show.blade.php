@extends('layouts.app')

@section('title', 'Detail Sub Kriteria')

@section('content')

    <div class="col-lg-12 order-lg-1">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h5 class="m-0 font-weight-bold text-primary">Detail Sub Kriteria: {{ $subCriteria->name }}</h5>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <tr>
                        <th>Jurusan</th>
                        <td>{{ $subCriteria->criteria->major->name ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th>Kriteria Induk (Mata Pelajaran)</th>
                        <td>
                            {{ $subCriteria->criteria->subject->code ?? 'N/A' }} -
                            {{ $subCriteria->criteria->subject->name ?? 'N/A' }}
                        </td>
                    </tr>

                    <tr>
                        <th>Nama Skala / Sub Kriteria</th>
                        <td>{{ $subCriteria->name }}</td>
                    </tr>
                    <tr>
                        <th>Nilai Numerik (untuk SPK)</th>
                        <td>{{ $subCriteria->value }}</td>
                    </tr>
                </table>

                <div class="mt-3">
                    <a href="{{ route('admin.subcriteria.index') }}" class="btn btn-secondary"><i
                            class="fas fa-arrow-left"></i>
                        Kembali</a>
                    @if (auth()->user()->is_admin == 1)
                        <x-button_edit route="admin.subcriteria.edit" :id="$subCriteria->id" />
                    @endif
                </div>
            </div>
        </div>
    </div>

@endsection