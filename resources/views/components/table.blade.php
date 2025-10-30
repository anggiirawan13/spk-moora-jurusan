<x-alert />

<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h5 class="m-0 font-weight-bold text-primary">{{ $title }}</h5>
        {{-- Tombol Tambah hanya untuk Admin --}}
        @auth
            @if (auth()->user()->is_admin == 1)
                <a href="{{ route($createRoute) }}" class="btn btn-primary btn-icon-split">
                    <span class="icon text-white-50">
                        <i class="fas fa-plus-circle"></i>
                    </span>
                    <span class="text">Tambah</span>
                </a>
            @endif
        @endauth
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover" width="100%" id="dataTable" cellspacing="0">
                <thead class="thead-light">
                    <tr>
                        <th style="width: 50px;">No.</th>
                        @foreach ($columns as $column)
                            <th>{{ $column['label'] }}</th>
                        @endforeach
                        <th style="width: 120px;" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th>No</th>
                        @foreach ($columns as $column)
                            <th>{{ $column['label'] }}</th>
                        @endforeach
                        <th class="text-center">Aksi</th>
                    </tr>
                </tfoot>
                <tbody>
                    @forelse ($data as $key => $item)
                        {{-- Logika untuk menentukan apakah ini salah satu dari 5 baris terakhir --}}
                        @php
                            $isLastRows = $key >= count($data) - 5;
                        @endphp
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            @foreach ($columns as $column)
                                <td>
                                    {{-- Handle Kolom Spesial (is_admin, created_at, dll.) --}}
                                    @if ($column['field'] === 'is_admin')
                                        <span class="badge {{ $item['is_admin'] ? 'badge-danger' : 'badge-primary' }}">
                                            {{ $item['is_admin'] ? 'Admin' : 'User' }}
                                        </span>
                                    @elseif ($column['field'] === 'created_at')
                                        {{ \Carbon\Carbon::parse($item['created_at'])->format('d M Y') }}
                                    @else
                                        {{-- Tampilkan data umum --}}
                                        {{ $item[$column['field']] }}
                                    @endif
                                </td>
                            @endforeach

                            {{-- KOLOM AKSI (Menggunakan Dropdown) --}}
                            <td class="text-center">
                                {{-- KONDISI DROPUP BARU: Tambahkan 'dropup' jika berada di baris terakhir --}}
                                <div class="dropdown no-arrow {{ $isLastRows ? 'dropup' : '' }}">
                                    <button class="btn btn-sm btn-secondary dropdown-toggle" type="button"
                                        id="dropdownMenuButton_{{ $item['id'] }}" data-toggle="dropdown"
                                        aria-haspopup="true" aria-expanded="false">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    {{-- Hapus 'animated--grow-in' untuk menghilangkan glitch animasi --}}
                                    <div class="dropdown-menu dropdown-menu-right shadow"
                                        aria-labelledby="dropdownMenuButton_{{ $item['id'] }}">

                                        {{-- Aksi Lihat (Show) --}}
                                        <a href="{{ route($showRoute, $item['id']) }}" class="dropdown-item text-info">
                                            <i class="fas fa-fw fa-eye mr-2"></i> Lihat Detail
                                        </a>

                                        @auth
                                            @if (auth()->user()->is_admin == 1)
                                                <div class="dropdown-divider"></div>

                                                {{-- Aksi Edit --}}
                                                <a href="{{ route($editRoute, $item['id']) }}" class="dropdown-item text-primary">
                                                    <i class="fas fa-fw fa-edit mr-2"></i> Edit
                                                </a>

                                                {{-- Aksi Hapus --}}
                                                <button type="button" class="dropdown-item text-danger"
                                                    onclick="confirmDelete('{{ route($deleteRoute, $item['id']) }}', '{{ $item[$columns[0]['field']] }}')">
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
                            <td colspan="{{ count($columns) + 2 }}" class="text-center">
                                <i class="fas fa-info-circle"></i> Data Kosong
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

        </div>
    </div>
</div>

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