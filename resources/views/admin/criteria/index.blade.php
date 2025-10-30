@extends('layouts.app')

@section('title', 'Daftar Kriteria Penjurusan')

@section('content')

    <x-alert />

    <div class="d-flex justify-content-end mb-3">
        <a href="{{ route('admin.criteria.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Tambah Kriteria
        </a>
    </div>

    @forelse ($majors as $major)
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h5 class="m-0 font-weight-bold text-primary">
                    Kriteria untuk Jurusan: {{ $major->name }}
                </h5>
            </div>
            <div class="card-body">
                @if ($major->criteria->isEmpty())
                    <div class="alert alert-warning">
                        Belum ada kriteria yang ditetapkan untuk Jurusan {{ $major->name }}.
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th width="5%">No.</th>
                                    <th>Kode Mapel</th>
                                    <th>Nama Kriteria (Mata Pelajaran)</th>
                                    <th>Bobot (W)</th>
                                    <th>Atribut</th>
                                    <th width="15%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($major->criteria as $index => $criteria)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        {{-- Kode diambil dari relasi Subject --}}
                                        <td>{{ $criteria->subject->code ?? 'N/A' }}</td>
                                        {{-- Nama diambil dari relasi Subject --}}
                                        <td>{{ $criteria->subject->name ?? 'Mata Pelajaran Tidak Ditemukan' }}</td>
                                        <td>{{ $criteria->weight }}</td>
                                        {{-- Format tipe atribut --}}
                                        <td>{{ ucwords($criteria->attribute_type) }}</td>
                                        <td>
                                            {{-- Tombol Aksi --}}
                                            <a href="{{ route('admin.criteria.show', $criteria->id) }}" class="btn btn-sm btn-info m-1">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.criteria.edit', $criteria->id) }}" class="btn btn-sm btn-primary m-1">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('admin.criteria.destroy', $criteria->id) }}" method="POST"
                                                class="d-inline delete-form">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger m-1"
                                                    onclick="return confirm('Apakah Anda yakin ingin menghapus kriteria ini?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    @empty
        <div class="alert alert-info">
            Belum ada data Jurusan yang tersedia. Silakan tambahkan data Jurusan terlebih dahulu.
        </div>
    @endforelse

@endsection