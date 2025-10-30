@extends('layouts.app')

@section('title', 'Daftar Skala Konversi Nilai Rapor')

@section('content')

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h5 class="m-0 font-weight-bold text-primary">Daftar Skala Konversi Nilai Rapor (Sub Kriteria) per Mata
                Pelajaran</h5>
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
                                                    <th class="text-center" style="width: 20%;">Rentang Nilai Rapor</th>
                                                    {{-- END KOLOM BARU --}}
                                                    <th class="text-center" style="width: 7%;">Nilai SPK</th>
                                                    <th class="text-center" style="width: 11%;">Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                {{-- Urutkan berdasarkan Nilai SPK (value) secara menurun agar Nilai Konversi 5 di
                                                atas --}}
                                                @forelse ($item->subCriteria->sortByDesc('value') as $index => $sub)
                                                    @php
                                                        $isLastRows = $index >= count($item->subCriteria) - 5;
                                                    @endphp
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
                                                            {{-- KONDISI DROPUP BARU: Tambahkan 'dropup' jika berada di baris terakhir
                                                            --}}
                                                            <div class="dropdown no-arrow {{ $isLastRows ? 'dropup' : '' }}">
                                                                <button class="btn btn-sm btn-secondary dropdown-toggle" type="button"
                                                                    id="dropdownMenuButton_{{ $sub->id }}" data-toggle="dropdown"
                                                                    aria-haspopup="true" aria-expanded="false">
                                                                    <i class="fas fa-ellipsis-v"></i>
                                                                </button>
                                                                {{-- Hapus 'animated--grow-in' untuk menghilangkan glitch animasi --}}
                                                                <div class="dropdown-menu dropdown-menu-right shadow"
                                                                    aria-labelledby="dropdownMenuButton_{{ $sub->id }}">

                                                                    {{-- Aksi Lihat (Show) --}}
                                                                    <a href="{{ route('admin.subcriteria.show', $sub->id) }}"
                                                                        class="dropdown-item text-info">
                                                                        <i class="fas fa-fw fa-eye mr-2"></i> Lihat Detail
                                                                    </a>

                                                                    @auth
                                                                        @if (auth()->user()->is_admin == 1)
                                                                            <div class="dropdown-divider"></div>

                                                                            {{-- Aksi Edit --}}
                                                                            <a href="{{ route('admin.subcriteria.edit', $sub->id) }}"
                                                                                class="dropdown-item text-primary">
                                                                                <i class="fas fa-fw fa-edit mr-2"></i> Edit
                                                                            </a>

                                                                            {{-- Aksi Hapus --}}
                                                                            <button type="button" class="dropdown-item text-danger" @php
                                                                            @endphp
                                                                                onclick="confirmDelete('{{ route('admin.subcriteria.destroy', $sub->id) }}', '{{ $sub->name }}')">
                                                                                <i class="fas fa-fw fa-trash mr-2"></i> Hapus
                                                                            </button>
                                                                        @endif
                                                                    @endauth
                                                                </div>
                                                            </div>
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