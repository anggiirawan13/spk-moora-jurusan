{{-- File: admin/rapor/index.blade.php (View ini tetap bekerja) --}}
@extends('layouts.app')

@section('title', 'Input Nilai Rapor Siswa')

@section('content')
    {{-- ... (Bagian header card) ... --}}

    {{-- $majorsData kini berisi SEMUA Jurusan dan kelompok 'Belum Dijuruskan' --}}
    @forelse ($majorsData as $majorGroup)
        @php
            // ... (Kode PHP untuk menentukan ID, nama, total siswa, dan warna card)
            $majorCollapseId = 'major-collapse-' . $majorGroup['major_id'];
            $majorName = $majorGroup['major_name'];
            $studentsCollection = collect($majorGroup['students']); // Ubah array students menjadi Collection
            $totalStudents = $studentsCollection->count();

            // Tentukan warna card (Logic penentuan warna tetap sama)
            $cardClass = ($majorGroup['major_id'] == 'null_major') ? 'border-left-warning' : 'border-left-success';
            $textColor = ($majorGroup['major_id'] == 'null_major') ? 'text-warning' : 'text-success';
        @endphp

        <div class="card shadow mb-4 {{ $cardClass }}">

            {{-- HEADER CARD (Collapsible) --}}
            <a href="#{{ $majorCollapseId }}"
                class="card-header py-3 d-flex justify-content-between align-items-center collapsed text-decoration-none"
                data-toggle="collapse" aria-expanded="false" aria-controls="{{ $majorCollapseId }}">
                <h4 class="m-0 font-weight-bold {{ $textColor }}">
                    <i class="fas fa-graduation-cap"></i> <span class="font-weight-bold">{{ $majorName }}</span>
                </h4>
                <span class="badge badge-secondary p-2">{{ $totalStudents }} Siswa</span>
            </a>

            {{-- BODY CARD (Isi Daftar Siswa) --}}
            <div id="{{ $majorCollapseId }}" class="collapse">
                <div class="card-body">

                    @if ($totalStudents > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                                <thead class="thead-light">
                                    <tr>
                                        <th class="text-center" style="width: 50px;">No</th>
                                        <th>NIS</th>
                                        <th>Nama Siswa</th>
                                        <th style="width: 150px;" class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($studentsCollection as $student)
                                        <tr>
                                            <td class="text-center">{{ $loop->iteration }}</td>
                                            <td>{{ $student['nis'] }}</td>
                                            <td>{{ $student['name'] }}</td>
                                            <td class="text-center">
                                                {{-- KONDISI BARU DITAMBAHKAN DI SINI --}}
                                                @if ($majorGroup['major_id'] != 'null_major')
                                                    <a href="{{ route('admin.rapor.show', $student['id']) }}" class="btn btn-sm btn-info">
                                                        <i class="fas fa-pencil-alt"></i> Input/Edit Nilai
                                                    </a>
                                                @else
                                                    {{-- Tampilkan tombol yang tidak bisa diklik atau pesan --}}
                                                    <button class="btn btn-sm btn-secondary disabled" disabled>
                                                        <i class="fas fa-ban"></i> Belum Dijuruskan
                                                    </button>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info mb-0">
                            Tidak ada siswa yang terdaftar untuk Jurusan {{ $majorName }}.
                        </div>
                    @endif

                </div>
            </div>

        </div>
    @empty
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i> Belum ada Jurusan yang terdaftar dalam sistem.
        </div>
    @endforelse

@endsection