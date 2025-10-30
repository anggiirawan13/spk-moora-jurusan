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
                                    @php
                                        $isLastRows = $index >= count($major->criteria) - 5;
                                    @endphp
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        {{-- Kode diambil dari relasi Subject --}}
                                        <td>{{ $criteria->subject->code ?? 'N/A' }}</td>
                                        {{-- Nama diambil dari relasi Subject --}}
                                        <td>{{ $criteria->subject->name ?? 'Mata Pelajaran Tidak Ditemukan' }}</td>
                                        <td>{{ $criteria->weight }}</td>
                                        {{-- Format tipe atribut --}}
                                        <td>{{ ucwords($criteria->attribute_type) }}</td>
                                        <td class="text-center">
                                            {{-- KONDISI DROPUP BARU: Tambahkan 'dropup' jika berada di baris terakhir --}}
                                            <div class="dropdown no-arrow {{ $isLastRows ? 'dropup' : '' }}">
                                                <button class="btn btn-sm btn-secondary dropdown-toggle" type="button"
                                                    id="dropdownMenuButton_{{ $criteria->id }}" data-toggle="dropdown"
                                                    aria-haspopup="true" aria-expanded="false">
                                                    <i class="fas fa-ellipsis-v"></i>
                                                </button>
                                                {{-- Hapus 'animated--grow-in' untuk menghilangkan glitch animasi --}}
                                                <div class="dropdown-menu dropdown-menu-right shadow"
                                                    aria-labelledby="dropdownMenuButton_{{ $criteria->id }}">

                                                    {{-- Aksi Lihat (Show) --}}
                                                    <a href="{{ route('admin.criteria.show', $criteria->id) }}"
                                                        class="dropdown-item text-info">
                                                        <i class="fas fa-fw fa-eye mr-2"></i> Lihat Detail
                                                    </a>

                                                    @auth
                                                        @if (auth()->user()->is_admin == 1)
                                                            <div class="dropdown-divider"></div>

                                                            {{-- Aksi Edit --}}
                                                            <a href="{{ route('admin.criteria.edit', $criteria->id) }}"
                                                                class="dropdown-item text-primary">
                                                                <i class="fas fa-fw fa-edit mr-2"></i> Edit
                                                            </a>

                                                            {{-- Aksi Hapus --}}
                                                            <button type="button" class="dropdown-item text-danger"
                                                            @php
                                                            @endphp
                                                                onclick="confirmDelete('{{ route('admin.criteria.destroy', $criteria->id) }}', '{{ $criteria->subject->name }}')">
                                                                <i class="fas fa-fw fa-trash mr-2"></i> Hapus
                                                            </button>
                                                        @endif
                                                    @endauth
                                                </div>
                                            </div>
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

<script>
    // Asumsi Swal.fire (SweetAlert2) sudah terinstal
    function confirmDelete(url, name) {
        Swal.fire({
            title: 'Konfirmasi Hapus',
            html: `Apakah Anda yakin ingin menghapus <strong>${name}</strong>?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d'
        }).then((result) => {
            if (result.isConfirmed) {
                // Buat dan submit form delete secara dinamis
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = url;

                // Ambil CSRF token dari meta tag (pastikan ada di layout utama)
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                const csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = '_token';
                csrfInput.value = csrfToken;

                const methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                methodInput.value = 'DELETE';

                form.appendChild(csrfInput);
                form.appendChild(methodInput);
                document.body.appendChild(form);
                form.submit();
            }
        });
    }
</script>