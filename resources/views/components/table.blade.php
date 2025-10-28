<x-alert />

<div class="card shadow mb-4">
    <div class="card-header py-3">
        @auth
            @if (auth()->user()->is_admin == 1)
                <a href="{{ route($createRoute) }}" class="btn btn-primary float-right">
                    <i class="fas fa-fw fa-plus-circle"></i> Tambah Data
                </a>
            @endif
        @endauth
        <h5 class="m-0 font-weight-bold text-primary">{{ $title }}</h5>
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" width="100%" id="dataTable" cellspacing="0">
                <thead>
                    <tr>
                        <th>No.</th>
                        @foreach ($columns as $column)
                            <th>{{ $column['label'] }}</th>
                        @endforeach
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th>No</th>
                        @foreach ($columns as $column)
                            <th>{{ $column['label'] }}</th>
                        @endforeach
                        <th>Aksi</th>
                    </tr>
                </tfoot>
                <tbody>
                    @forelse ($data as $key => $item)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            @foreach ($columns as $column)
                                <td>
                                    @if ($column['field'] === 'is_admin')
                                        <span class="badge {{ $item['is_admin'] ? 'badge-danger' : 'badge-primary' }}">
                                            {{ $item['is_admin'] ? 'Admin' : 'User' }}
                                        </span>
                                    @elseif ($column['field'] === 'created_at')
                                        {{ \Carbon\Carbon::parse($item['created_at'])->format('d M Y') }}
                                    @else
                                        @if (!empty($column['html']) && $column['html'])
                                            {!! $item[$column['field']] !!}
                                        @elseif (!empty($column['php']) && $column['php'])
                                            {{ $column['field'] }}
                                        @else
                                            {{ $item[$column['field']] }}
                                        @endif
                                    @endif
                                </td>
                            @endforeach
                            <td class="d-flex gap-1">
                                <a href="{{ route($showRoute, $item['id']) }}" class="btn btn-sm btn-info m-1">
                                    <i class="fas fa-eye"></i>
                                </a>

                                @auth
                                    @if (auth()->user()->is_admin == 1)
                                        <a href="{{ route($editRoute, $item['id']) }}" class="btn btn-sm btn-primary m-1">
                                            <i class="fas fa-edit"></i>
                                        </a>

                                        <button type="button" class="btn btn-danger btn-sm m-1"
                                            onclick="confirmDelete('{{ route($deleteRoute, $item['id']) }}', '{{ $item[$columns[0]['field']] }}')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    @endif
                                @endauth
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ count($columns) + 2 }}" class="text-center">Data Kosong</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

        </div>
    </div>
</div>

<script>
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
