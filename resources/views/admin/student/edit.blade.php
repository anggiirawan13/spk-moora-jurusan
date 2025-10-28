@extends('layouts.app')

@section('title', 'Data Siswa')

@section('content')

    <x-alert />

    <div class="card">
        <div class="card-header py-3">
            <h5 class="m-0 font-weight-bold text-primary">Ubah Data Siswa: {{ $student->name }}</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.student.update', $student->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="nis">NIS (Nomor Induk Siswa)</label>
                    <input required type="text" name="nis" class="form-control" placeholder="Masukkan NIS"
                        value="{{ old('nis', $student->nis) }}" />
                </div>

                <div class="form-group">
                    <label for="name">Nama Siswa</label>
                    <input required type="text" name="name" class="form-control" placeholder="Masukkan nama siswa"
                        value="{{ old('name', $student->name) }}" />
                </div>
                
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" name="email" class="form-control" placeholder="Masukkan email siswa"
                        value="{{ old('email', $student->email) }}" />
                </div>
                
                <div class="form-group">
                    <label for="profile_picture">Foto Profil</label>
                    <img id="currentImage" src="{{ $student->profile_picture ? asset('storage/student/' . $student->profile_picture) : asset('img/default-profile.png') }}" 
                         width="100" alt="Foto Profil" class="mb-2 ml-2">
                    <input type="file" name="profile_picture" class="form-control" onchange="previewUpdateImage(event)" />
                    <img id="imagePreview" class="img-fluid mt-2" style="max-width: 300px; display: none;" />
                </div>
                
                <div class="form-group">
                    <label for="grade_level">Tingkat Kelas</label>
                    <input required type="number" name="grade_level" class="form-control" placeholder="Masukkan tingkat kelas (10-12)"
                        value="{{ old('grade_level', $student->grade_level) }}" min="10" max="12" />
                </div>
                
                <div class="form-group">
                    <label for="major_id">Jurusan Saat Ini</label>
                    <select class="form-control" name="major_id" id="major_id">
                        <option value="">Belum memiliki jurusan</option>
                        @foreach ($majors as $major)
                            <option value="{{ $major->id }}" 
                                {{ old('major_id', $student->major_id) == $major->id ? 'selected' : '' }}>
                                {{ $major->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="description">Keterangan Tambahan / Alamat</label>
                    <textarea class="form-control" name="description" placeholder="Masukkan keterangan tambahan atau alamat" id="description">{{ old('description', $student->description) }}</textarea>
                </div>

                <div class="form-group">
                    <label for="is_active">Status Keaktifan</label>
                    <select required class="form-control" name="is_active" id="is_active">
                        <option value="" hidden>Pilih status keaktifan</option>
                        <option {{ old('is_active', $student->is_active) == 0 ? 'selected' : '' }} value="0">Tidak Aktif</option>
                        <option {{ old('is_active', $student->is_active) == 1 ? 'selected' : '' }} value="1">Aktif</option>
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
        function previewUpdateImage(event) {
            var input = event.target;
            var reader = new FileReader();
            
            document.getElementById('currentImage').style.display = 'none';

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
