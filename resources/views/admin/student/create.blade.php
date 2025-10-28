@extends('layouts.app')

@section('title', 'Data Siswa')

@section('content')

    <x-alert />

    <div class="card">
        <div class="card-header py-3">
            <h5 class="m-0 font-weight-bold text-primary">Tambah Data Siswa</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.student.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
 
                <div class="form-group">
                    <label for="nis">NIS (Nomor Induk Siswa)</label>
                    <input type="text" name="nis" class="form-control" placeholder="Masukkan NIS"
                        value="{{ old('nis') }}" required />
                </div>
                
                <div class="form-group">
                    <label for="name">Nama Siswa</label>
                    <input type="text" name="name" class="form-control" placeholder="Masukkan nama siswa"
                        value="{{ old('name') }}" required />
                </div>
                
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" name="email" class="form-control" placeholder="Masukkan email siswa (opsional)"
                        value="{{ old('email') }}" />
                </div>

                <div class="form-group">
                    <label for="profile_picture">Foto Profil</label>
                    <input type="file" name="profile_picture" id="profile_picture" class="form-control" accept="image/*" required
                        onchange="previewImage(event)" />
                    <img id="imagePreview" class="img-fluid mt-2" style="max-width: 300px; display: none;" />
                </div>
                
                <div class="form-group">
                    <label for="grade_level">Tingkat Kelas</label>
                    <input type="number" name="grade_level" class="form-control" placeholder="Masukkan tingkat kelas (misal: 10)"
                        value="{{ old('grade_level') }}" required min="10" max="12" />
                </div>
                
                <div class="form-group">
                    <label for="major_id">Jurusan Saat Ini (Jika ada)</label>
                    <select class="form-control" name="major_id" id="major_id">
                        <option value="" selected>Belum memiliki jurusan</option>
                        @foreach ($majors as $major)
                            <option value="{{ $major->id }}" {{ old('major_id') == $major->id ? 'selected' : '' }}>
                                {{ $major->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="description">Alamat / Keterangan Tambahan (Opsional)</label>
                    <textarea class="form-control" name="description" placeholder="Masukkan alamat lengkap atau keterangan tambahan" id="description">{{ old('description') }}</textarea>
                </div>

                <div class="form-group">
                    <label for="is_active">Status Keaktifan</label>
                    <select class="form-control" name="is_active" id="is_active" required>
                        <option value="" hidden>Pilih status keaktifan</option>
                        <option value="1" {{ old('is_active') === '1' ? 'selected' : '' }}>Aktif</option>
                        <option value="0" {{ old('is_active') === '0' ? 'selected' : '' }}>Tidak Aktif</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <a href="{{ route('admin.student.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i>
                        Kembali</a>
                    <x-button_save />
                </div>
            </form>
        </div>
    </div>

    <script>
        function previewImage(event) {
            var input = event.target;
            var reader = new FileReader();

            reader.onload = function() {
                var imgElement = document.getElementById('imagePreview');
                imgElement.src = reader.result;
                imgElement.style.display = 'block';
            };

            if (input.files && input.files[0]) {
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>

@endsection
