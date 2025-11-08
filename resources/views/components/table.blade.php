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
                        <tr class="transition-all">
                            <td class="text-center text-muted">{{ $key + 1 }}</td>
                            @foreach ($columns as $column)
                                <td>
                                    @if ($column['field'] === 'is_admin')
                                        <span class="badge badge-pill {{ $item['is_admin'] ? 'badge-danger' : 'badge-success' }}">
                                            <i class="fas {{ $item['is_admin'] ? 'fa-user-shield' : 'fa-user' }} mr-1"></i>
                                            {{ $item['is_admin'] ? 'Admin' : 'User' }}
                                        </span>
                                    @elseif ($column['field'] === 'is_active' || $column['field'] === 'is_available')
                                        <span class="badge badge-pill {{ $item[$column['field']] ? 'badge-success' : 'badge-secondary' }}">
                                            <i class="fas {{ $item[$column['field']] ? 'fa-check' : 'fa-times' }} mr-1"></i>
                                            {{ $item[$column['field']] ? 'Aktif' : 'Nonaktif' }}
                                        </span>
                                    @elseif (in_array($column['field'], ['created_at', 'updated_at']))
                                        <span class="text-muted small">
                                            <i class="far fa-clock mr-1"></i>
                                            {{ \Carbon\Carbon::parse($item[$column['field']])->format('d M Y H:i') }}
                                        </span>
                                    @elseif (strpos($column['field'], 'score') !== false)
                                        <div class="d-flex align-items-center">
                                            <div class="progress flex-grow-1 mr-2" style="height: 6px;">
                                                <div class="progress-bar bg-success" 
                                                     role="progressbar" 
                                                     style="width: {{ $item[$column['field']] }}%"
                                                     aria-valuenow="{{ $item[$column['field']] }}" 
                                                     aria-valuemin="0" 
                                                     aria-valuemax="100">
                                                </div>
                                            </div>
                                            <span class="badge badge-light">{{ $item[$column['field']] }}</span>
                                        </div>
                                    @else
                                        @if (!empty($column['html']) && $column['html'])
                                            {!! $item[$column['field']] !!}
                                        @elseif (!empty($column['php']) && $column['php'])
                                            {{ $column['field'] }}
                                        @else
                                            {{ $item[$column['field']] ?? '-' }}
                                        @endif
                                    @endif
                                </td>
                            @endforeach
                            <td>
                                <div class="d-flex justify-content-center gap-1">
                                    <a href="{{ route($showRoute, $item['id']) }}" 
                                       class="btn btn-info btn-sm btn-action m-1"
                                       data-toggle="tooltip" title="Lihat Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>

                                    @auth
                                        @if (auth()->user()->is_admin == 1)
                                            <a href="{{ route($editRoute, $item['id']) }}" 
                                               class="btn btn-primary btn-sm btn- m-1"
                                               data-toggle="tooltip" title="Edit Data">
                                                <i class="fas fa-edit"></i>
                                            </a>

                                            <button type="button" 
                                                    class="btn btn-danger btn-sm btn-action m-1"
                                                    onclick="confirmDelete('{{ route($deleteRoute, $item['id']) }}', '{{ $item[$columns[0]['field']] ?? $item['name'] ?? 'Data' }}')"
                                                    data-toggle="tooltip" title="Hapus Data">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        @endif
                                    @endauth
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ count($columns) + 2 }}" class="text-center py-4">
                                <div class="text-muted">
                                    <i class="fas fa-inbox fa-3x mb-3"></i>
                                    <p class="mb-0">Tidak ada data ditemukan</p>
                                </div>
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