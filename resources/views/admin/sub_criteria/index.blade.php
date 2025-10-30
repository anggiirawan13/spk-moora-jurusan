@extends('layouts.app')

@section('title', 'Daftar Skala Konversi Nilai Rapor') {{-- Judul diubah agar lebih jelas --}}

@section('content')

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h5 class="m-0 font-weight-bold text-primary">Daftar Skala Konversi Nilai Rapor (Sub Kriteria) per Mata
                Pelajaran</h5>
            <p class="text-secondary mt-1 mb-0">Atur rentang nilai rapor yang akan dikonversi menjadi Nilai SPK (1-5).</p>
        </div>
    </div>

    <x-alert />

    @forelse ($majors as $major)
        @php
            $majorCollapseId = 'major-collapse-' . $major->id;
        @endphp

        <div class="card shadow mb-4 border-left-success">

            <a href="#{{ $majorCollapseId }}"
                class="card-header py-3 d-flex justify-content-between align-items-center collapsed text-decoration-none"
                data-toggle="collapse" aria-expanded="false" aria-controls="{{ $majorCollapseId }}">
                <h4 class="m-0 font-weight-bold text-success">
                    <i class="fas fa-graduation-cap"></i> <span class="font-weight-bold">{{ $major->name ?? 'N/A' }}
                        ({{ $major->code ?? 'N/A' }})</span>
                </h4>
            </a>

            <div id="{{ $majorCollapseId }}" class="collapse">
                <div class="card-body">

                    @forelse ($major->criteria as $item)
                        @php
                            $criteriaCollapseId = 'criteria-collapse-' . $item->id;
                        @endphp
                        <div class="card shadow mb-3 border-left-primary">

                            <div class="card-header py-3 d-flex justify-content-between align-items-center">

                                <a href="#{{ $criteriaCollapseId }}" data-toggle="collapse" aria-expanded="false"
                                    aria-controls="{{ $criteriaCollapseId }}"
                                    class="d-flex align-items-center text-decoration-none w-100 collapsed">
                                    <h6 class="m-0 font-weight-bold text-primary mr-auto">
                                        <i class="fas fa-book-open"></i>
                                        {{ $item->subject->name ?? 'N/A' }}
                                        ({{ $item->subject->code ?? 'N/A' }})
                                        ({{ $item->weight }})
                                    </h6>
                                </a>

                                {{-- Link Tambah Skala --}}
                                <a href="{{ route('admin.subcriteria.create', ['criteria_id' => $item->id]) }}"
                                    class="btn btn-success btn-sm ml-3">
                                    <i class="fas fa-plus"></i> Tambah
                                </a>
                            </div>

                            <div id="{{ $criteriaCollapseId }}" class="collapse">
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-hover">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th class="text-center" style="width: 50px;">No</th>
                                                    <th style="width: 250px;">Nama Skala</th>
                                                    {{-- KOLOM BARU --}}
                                                    <th class="text-center" style="width: 200px;">Rentang Nilai Rapor</th>
                                                    {{-- END KOLOM BARU --}}
                                                    <th class="text-center" style="width: 100px;">Nilai SPK</th>
                                                    <th class="text-center" style="width: 200px;">Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                {{-- Urutkan berdasarkan Nilai SPK (value) secara menurun agar Nilai Konversi 5 di
                                                atas --}}
                                                @forelse ($item->subCriteria->sortByDesc('value') as $sub)
                                                    <tr>
                                                        <td class="text-center">{{ $loop->iteration }}</td>
                                                        <td>{{ $sub->name }}</td>
                                                        {{-- TAMPILKAN DATA BARU --}}
                                                        <td class="text-center font-weight-bold text-info">
                                                            {{ $sub->min_value }} - {{ $sub->max_value }}
                                                        </td>
                                                        {{-- END TAMPILKAN DATA BARU --}}
                                                        <td class="text-center">
                                                            <span class="badge badge-success p-2">{{ $sub->value }}</span>
                                                        </td>
                                                        <td class="text-center">
                                                            <a href="{{ route('admin.subcriteria.show', $sub->id) }}"
                                                                class="btn btn-sm btn-info m-1">
                                                                <i class="fas fa-eye"></i>
                                                            </a>

                                                            @auth
                                                                @if (auth()->user()->is_admin == 1)
                                                                    <a href="{{ route('admin.subcriteria.edit', $sub->id) }}"
                                                                        class="btn btn-sm btn-primary m-1">
                                                                        <i class="fas fa-edit"></i>
                                                                    </a>

                                                                    <button type="button" class="btn btn-danger btn-sm m-1"
                                                                        onclick="confirmDelete('{{ route('admin.subcriteria.destroy', $sub->id) }}', '{{ $sub->name }}')">
                                                                        <i class="fas fa-trash"></i>
                                                                    </button>
                                                                @endif
                                                            @endauth
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="5" class="text-center text-muted"> {{-- colspan ditambah 1 --}}
                                                            Belum ada skala nilai konversi yang ditetapkan untuk kriteria ini.
                                                        </td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="alert alert-warning">
                            Jurusan {{ $major->name ?? 'ini' }} belum memiliki Kriteria Mata Pelajaran yang ditetapkan.
                        </div>
                    @endforelse

                </div>
            </div>

        </div>
    @empty
        <div class="alert alert-info">
            Belum ada Jurusan yang terdaftar.
        </div>
    @endforelse

    <form id="deleteForm" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            function confirmDelete(url, name) {
                Swal.fire({
                    title: 'Konfirmasi Hapus',
                    html: `Apakah Anda yakin ingin menghapus Skala Konversi <strong>${name}</strong>?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Hapus',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d'
                }).then((result) => {
                    if (result.isConfirmed) {
                        const form = document.getElementById('deleteForm');
                        form.action = url;
                        form.submit();
                    }
                });
            }
        </script>
    @endpush

@endsection