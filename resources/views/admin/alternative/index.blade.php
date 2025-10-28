@extends('layouts.app')

@section('title', 'Alternatif')

@section('content')

    <x-alert />

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            @auth
                @if (auth()->user()->is_admin == 1)
                    <a href="{{ route('admin.alternative.create') }}" class="btn btn-primary float-right">
                        <i class="fas fa-fw fa-plus-circle"></i> Tambah Data
                    </a>
                @endif
            @endauth
            <h5 class="m-0 font-weight-bold text-primary">Daftar Alternatif</h5>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" id="dataTable" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            @foreach ($criterias as $criteria)
                                <th>{{ $criteria->name }}</th>
                            @endforeach
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($alternatives as $index => $item)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $item['name'] }}</td>
                                @foreach ($criterias as $criteria)
                                    <td>{{ $item[$criteria->id] ?? '-' }}</td>
                                @endforeach
                                <td class="d-flex gap-1">
                                    <a href="{{ route('admin.alternative.show', $item['id']) }}"
                                        class="btn btn-sm btn-info m-1">
                                        <i class="fas fa-eye"></i>
                                    </a>

                                    @auth
                                        @if (auth()->user()->is_admin == 1)
                                            <a href="{{ route('admin.alternative.edit', $item['id']) }}"
                                                class="btn btn-sm btn-primary m-1">
                                                <i class="fas fa-edit"></i>
                                            </a>

                                            <button type="button" class="btn btn-danger btn-sm m-1"
                                                onclick="confirmDelete('{{ route('admin.alternative.destroy', $item['id']) }}', '{{ $item['name'] }}')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        @endif
                                    @endauth
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ count($criterias) + 3 }}" class="text-center">Data Kosong</td>
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
                    const form = document.getElementById('deleteForm');
                    form.action = url;
                    form.submit();
                }
            });
        }
    </script>

@endsection
