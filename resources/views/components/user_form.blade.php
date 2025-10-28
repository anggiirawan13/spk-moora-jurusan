@props([
    'id' => null,
    'route' => '',
    'method' => 'POST',
    'imageRequired' => false,
    'passwordRequired' => false,
    'isReadOnly' => false,
    'withRole' => false,
    'withBack' => false,
    'routeBack' => '',
    'name' => '',
    'email' => '',
    'image' => '',
    'role' => 0,
    'deletePhotoProfile' => false,
])

<x-alert />

<form action="{{ $id ? route($route, $id) : route($route) }}" enctype="multipart/form-data" method="POST">
    @csrf
    @method($method)

    <div class="form-group">
        <img id="imagePreview" class="img-fluid my-2"
            style="max-width: 300px; {{ $image ? 'display: block;' : 'display: none;' }}"
            src="{{ $image ? asset('storage/user/' . $image) : '' }}" />

        <label for="image_name">Foto Profil</label>
        <input type="file" name="image_name" id="image_name" class="form-control" accept="image/*"
            {{ $imageRequired ? 'required' : '' }} onchange="previewImage(event)" />

        @if ($image && $deletePhotoProfile)
            <button type="button" class="btn btn-danger btn-sm m-1"
                onclick="confirmDelete('{{ route('profile.delete_image', $id) }}')">
                <i class="fas fa-trash"></i> Hapus Foto Profil
            </button>
        @endif
    </div>
    <div class="form-group">
        <label for="name">Nama Lengkap <span class="text-danger">*</span></label>
        <input type="text" name="name" class="form-control" placeholder="Masukkan nama lengkap"
            value="{{ old('name', $name) }}" required />
    </div>
    <div class="form-group">
        <label for="email">Email <span class="text-danger">*</span></label>
        <input type="email" name="email" class="form-control" placeholder="Masukkan email"
            value="{{ old('email', $email) }}" required {{ $isReadOnly ? 'readOnly' : '' }} />
    </div>
    <div class="form-group">
        <label for="password">Password {!! $passwordRequired ? '<span class="text-danger">*</span>' : '' !!}</label>
        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
            placeholder="Masukkan password" {{ $passwordRequired ? 'required' : '' }} />
        @error('password')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="form-group">
        <label for="password_confirmation">Konfirmasi Password
            {!! $passwordRequired ? '<span class="text-danger">*</span>' : '' !!}</label>
        <input type="password" name="password_confirmation"
            class="form-control @error('password_confirmation') is-invalid @enderror" placeholder="Ulangi password"
            {{ $passwordRequired ? 'required' : '' }} />
        @error('password_confirmation')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div id="passwordMatch" class="mt-2 font-weight-bold text-danger">
        ❌ Password tidak cocok
    </div>

    <div class="form-group">
        <label><strong>Syarat Password:</strong></label>
        <ul id="passwordRequirements" class="text-sm pl-3">
            <li id="char" class="text-danger">❌ Minimal 8 karakter</li>
            <li id="upper" class="text-danger">❌ Minimal 1 huruf besar</li>
            <li id="lower" class="text-danger">❌ Minimal 1 huruf kecil</li>
            <li id="number" class="text-danger">❌ Minimal 1 angka</li>
            <li id="special" class="text-danger">❌ Minimal 1 karakter spesial</li>
        </ul>
    </div>
    @if ($withRole)
        <div class="form-group">
            <label for="role">Role</label>
            <select class="form-control" name="role" required>
                <option hidden>Pilih role</option>
                <option {{ $role == 1 ? 'selected' : '' }} value="1">Admin</option>
                <option {{ $role == 0 ? 'selected' : '' }} value="0">User</option>
            </select>
        </div>
    @endif
    <div class="form-group">
        @if ($withBack)
            <a href="{{ route($routeBack) }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i>
                Kembali</a>
        @endif
        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i>
            Simpan</button>
    </div>
</form>

<!-- Modal Konfirmasi Hapus -->
<div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="confirmDeleteLabel">Konfirmasi Hapus</h5>
                <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus foto profil?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <form id="deleteForm" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-danger">Ya, Hapus</button>
                </form>
            </div>
        </div>
    </div>
</div> <!-- End Modal -->

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

    function confirmDelete(url) {
        document.getElementById('deleteForm').action = url;

        var confirmModal = new bootstrap.Modal(document.getElementById('confirmDeleteModal'));
        confirmModal.show();
    }

    document.addEventListener('DOMContentLoaded', function() {
        const passwordInput = document.querySelector('input[name="password"]');
        const minChar = document.getElementById('char');
        const hasUpper = document.getElementById('upper');
        const hasLower = document.getElementById('lower');
        const hasNumber = document.getElementById('number');
        const hasSpecial = document.getElementById('special');

        passwordInput.addEventListener('input', function() {
            const val = passwordInput.value;

            if (val.length >= 8) {
                minChar.textContent = '✅ Minimal 8 karakter';
                minChar.classList.remove('text-danger');
                minChar.classList.add('text-success');
            } else {
                minChar.textContent = '❌ Minimal 8 karakter';
                minChar.classList.remove('text-success');
                minChar.classList.add('text-danger');
            }

            if (/[A-Z]/.test(val)) {
                hasUpper.textContent = '✅ Minimal 1 huruf besar';
                hasUpper.classList.remove('text-danger');
                hasUpper.classList.add('text-success');
            } else {
                hasUpper.textContent = '❌ Minimal 1 huruf besar';
                hasUpper.classList.remove('text-success');
                hasUpper.classList.add('text-danger');
            }

            if (/[a-z]/.test(val)) {
                hasLower.textContent = '✅ Minimal 1 huruf kecil';
                hasLower.classList.remove('text-danger');
                hasLower.classList.add('text-success');
            } else {
                hasLower.textContent = '❌ Minimal 1 huruf kecil';
                hasLower.classList.remove('text-success');
                hasLower.classList.add('text-danger');
            }

            if (/\d/.test(val)) {
                hasNumber.textContent = '✅ Minimal 1 angka';
                hasNumber.classList.remove('text-danger');
                hasNumber.classList.add('text-success');
            } else {
                hasNumber.textContent = '❌ Minimal 1 angka';
                hasNumber.classList.remove('text-success');
                hasNumber.classList.add('text-danger');
            }

            if (/[!@#$%^&*(),.?":{}|<>]/.test(val)) {
                hasSpecial.textContent = '✅ Minimal 1 karakter spesial';
                hasSpecial.classList.replace('text-danger', 'text-success');
            } else {
                hasSpecial.textContent = '❌ Minimal 1 karakter spesial (!@#$...)';
                hasSpecial.classList.replace('text-success', 'text-danger');
            }
        });

        const confirmInput = document.querySelector('input[name="password_confirmation"]');
        const matchText = document.getElementById('passwordMatch');

        function checkPasswordMatch() {
            const passwordVal = passwordInput.value;
            const confirmVal = confirmInput.value;

            if (confirmVal === '') {
                matchText.textContent = '';
                return;
            }

            if (passwordVal === confirmVal) {
                matchText.textContent = '✅ Password cocok';
                matchText.classList.remove('text-danger');
                matchText.classList.add('text-success');
            } else {
                matchText.textContent = '❌ Password tidak cocok';
                matchText.classList.remove('text-success');
                matchText.classList.add('text-danger');
            }
        }

        passwordInput.addEventListener('input', checkPasswordMatch);
        confirmInput.addEventListener('input', checkPasswordMatch);
    });
</script>
