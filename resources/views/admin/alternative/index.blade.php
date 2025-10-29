@extends('layouts.app')

@section('title', 'Daftar Alternatif Siswa')

@section('content')

    <x-alert />

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h5 class="m-0 font-weight-bold text-primary">Daftar Alternatif (Siswa yang Sudah Dinilai)</h5>
            @auth
                @if (auth()->user()->is_admin == 1)
                    <a href="{{ route('admin.alternative.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-fw fa-plus-circle"></i> Tambah Nilai Siswa
                    </a>
                @endif
            @endauth
        </div>

        <div class="card-body">

            {{-- --- PENYESUAIAN DI SINI: MENAMPILKAN KRITERIA UNIK & DETAIL JURUSAN --- --}}
            <h6 class="font-weight-bold mb-3 text-secondary">
                <i class="fas fa-info-circle"></i> Kriteria & Jurusan yang Terlibat:
            </h6>
            <div class="mb-4 p-3 border rounded" style="max-height: 200px; overflow-y: auto;">
                
                @php
                    // Kelompokkan kriteria berdasarkan nama Mata Pelajaran (Subject) untuk melihat kriteria unik.
                    $uniqueCriterias = $criterias->groupBy('subject_id');
                @endphp
                
                <h6 class="font-weight-bold mb-2">Kriteria Mata Pelajaran (Dasar Penilaian):</h6>
                <p class="mb-3">
                    @forelse ($uniqueCriterias as $subjectId => $criteriaGroup)
                        @php
                            // Ambil kriteria pertama untuk mendapatkan nama mata pelajaran
                            $criteria = $criteriaGroup->first();
                            // Kumpulkan semua Jurusan yang menggunakan kriteria ini
                            $majorsUsing = $criteriaGroup->pluck('major.code')->filter()->unique()->implode(', ');
                        @endphp
                        
                        <span class="badge badge-info mr-2 mb-1 p-2"
                            title="Digunakan oleh Jurusan: {{ $majorsUsing }}">
                            {{ $criteria->subject->name ?? 'N/A' }}
                            ({{ $criteria->name }})
                        </span>
                    @empty
                        <em class="text-muted">Tidak ada Kriteria yang terdaftar.</em>
                    @endforelse
                </p>

                <h6 class="font-weight-bold mb-2 mt-3">Detail Bobot per Jurusan:</h6>
                <p class="mb-0">
                    @foreach ($criterias as $criteria)
                        <span class="badge badge-warning mr-2 mb-1 p-2"
                            title="Kriteria: {{ $criteria->subject->name ?? 'N/A' }}">
                            {{ $criteria->subject->code ?? 'N/A' }} 
                            <i class="fas fa-arrow-right"></i> {{ $criteria->major->code ?? 'N/A' }}
                            (Bobot: {{ $criteria->weight }})
                        </span>
                    @endforeach
                </p>
                
            </div>
            {{-- --- AKHIR PENYESUAIAN HEADER KRITERIA --- --}}

            <div class="table-responsive">
                <table class="table table-bordered" width="100%" id="dataTable" cellspacing="0">
                    <thead>
                        <tr>
                            <th style="width: 50px;">No</th>
                            <th>Nama Siswa (Alternatif)</th>
                            <th style="width: 150px;">NIS</th>
                            <th style="width: 200px;">Jurusan Pilihan (Awal)</th>
                            <th style="width: 150px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($alternatives as $index => $item)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    {{ $item['name'] ?? 'N/A' }}
                                </td>
                                <td>{{ $item['nis'] ?? '-' }}</td>
                                <td>{{ $item['major_name'] ?? '-' }}</td>

                                <td class="d-flex justify-content-center">
                                    <a href="{{ route('admin.alternative.show', $item['id']) }}" class="btn btn-sm btn-info m-1"
                                        title="Lihat Detail Nilai">
                                        <i class="fas fa-eye"></i>
                                    </a>

                                    @auth
                                        @if (auth()->user()->is_admin == 1)
                                            <a href="{{ route('admin.alternative.edit', $item['id']) }}"
                                                class="btn btn-sm btn-primary m-1" title="Ubah Nilai">
                                                <i class="fas fa-edit"></i>
                                            </a>

                                            <button type="button" class="btn btn-danger btn-sm m-1" title="Hapus Data"
                                                onclick="confirmDelete('{{ route('admin.alternative.destroy', $item['id']) }}', '{{ $item['name'] }}')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        @endif
                                    @endauth
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">
                                    Belum ada data Alternatif (Siswa yang sudah diinput nilainya).
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <form id="deleteForm" method="POST" style="display: none;">
                    @csrf
                    @method('DELETE')
                </form>

            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            function confirmDelete(url, name) {
                Swal.fire({
                    title: 'Konfirmasi Hapus',
                    html: `Apakah Anda yakin ingin menghapus data nilai siswa <strong>${name}</strong>?`,
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