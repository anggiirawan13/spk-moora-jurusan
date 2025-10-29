@extends('layouts.app')

@section('title', 'Detail Nilai Alternatif')

@section('content')

    <div class="col-lg-12 order-lg-1">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h5 class="m-0 font-weight-bold text-primary">Detail Nilai Siswa (Alternatif)</h5>
            </div>
            <div class="card-body">

                {{-- === BAGIAN 1: INFORMASI SISWA (TETAP) === --}}
                <h6 class="mt-2 font-weight-bold text-info">Informasi Siswa</h6>
                <table class="table table-bordered mb-4">
                    <tr>
                        <th style="width: 200px;">Nama Siswa</th>
                        <td>{{ $alternative->student->name ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th>NIS</th>
                        <td>{{ $alternative->student->nis ?? $alternative->student->nisn ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th>Jurusan Pilihan Awal</th>
                        <td>{{ $alternative->student->major->name ?? '-' }}</td>
                    </tr>
                </table>
                <hr>
                
                {{-- === BAGIAN 2: NILAI KRITERIA UNIK (PERBAIKAN TAMPILAN) === --}}
                <h5 class="mt-4 font-weight-bold text-info">
                    <i class="fas fa-list-ol"></i> Nilai Kriteria yang Diinput (Unik Per Mata Pelajaran)
                </h5>

                <div class="table-responsive">
                    <table class="table table-bordered table-hover table-striped mb-0" id="criteriaDetailTable">
                        <thead class="thead-dark">
                            <tr>
                                <th style="width: 5%;">No</th>
                                <th style="width: 30%;">Mata Pelajaran (Kriteria)</th>
                                <th style="width: 40%;">Sub-Kriteria Terpilih (Skala Nilai Rapor)</th>
                                <th style="width: 15%;" class="text-center">Nilai SPK ($X_{ij}$)</th>
                                <th style="width: 10%;" class="text-center">Atribut</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($uniqueCriteriaValues as $index => $data)
                                @php
                                    $criteria = $data['criteria'];
                                @endphp
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        {{-- Menampilkan Nama Mata Pelajaran (Subject Name) sebagai kriteria utama --}}
                                        <span class="font-weight-bold">{{ $criteria->subject->name ?? 'Mata Pelajaran Tidak Ditemukan' }}</span> 
                                        
                                        {{-- Tampilkan Jurusan yang menaungi kriteria ini (jika ada) --}}
                                        @if($criteria->major)
                                            <br>
                                            <small class="text-secondary">Jurusan: {{ $criteria->major->code ?? '-' }}</small>
                                        @endif
                                        
                                        {{-- ID Kriteria dihapus karena tidak perlu ditampilkan ke pengguna --}}
                                    </td>
                                    <td>
                                        <span class="font-weight-bold text-primary">
                                            {{ $data['sub_criteria_name'] ?? '-' }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge badge-success p-2">
                                            {{ $data['value_spk'] ?? '-' }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge badge-secondary">
                                            {{ ucwords($criteria->attribute_type ?? '-') }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">
                                        Alternatif ini belum memiliki nilai kriteria yang terekam.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                {{-- === AKHIR NILAI KRITERIA UNIK === --}}

                <hr class="mt-4">

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