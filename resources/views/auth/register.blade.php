<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>SPK Moora - Register</title>

    <!-- Custom fonts for this template-->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">

    <style>
        ul#passwordRequirements {
            list-style-type: none;
            padding-left: 0;
        }
    </style>
</head>

<body class="bg-gradient-primary">

    <div class="container">
        <div class="card o-hidden border-0 shadow-lg my-5">
            <div class="card-body p-0">
                <!-- Nested Row within Card Body -->
                <div class="row">
                    <div class="col-lg-5 d-none d-lg-block bg-register-image"></div>
                    <div class="col-lg-7">
                        <div class="p-5">
                            <div class="text-center">
                                <h1 class="h4 text-gray-900 mb-4">Create an Account!</h1>
                            </div>

                            <x-alert />

                            <form action="{{ route('register') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="form-group">
                                    <img id="imagePreview" class="img-fluid mt-2"
                                        style="max-width: 300px; display: none;" />
                                    <label for="image_name">Foto Profil</label>
                                    <input type="file" name="image_name" id="image_name" class="form-control"
                                        accept="image/*" onchange="previewImage(event)" />
                                </div>

                                <div class="form-group">
                                    <label for="name">Nama Lengkap <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control"
                                        placeholder="Masukkan nama lengkap" value="{{ old('name') }}" required />
                                </div>

                                <div class="form-group">
                                    <label for="email">Email <span class="text-danger">*</span></label>
                                    <input type="email" name="email" class="form-control"
                                        placeholder="Masukkan email" value="{{ old('email') }}" required />
                                </div>

                                <div class="form-group">
                                    <label for="password">Password <span class="text-danger">*</span></label>
                                    <input type="password" name="password"
                                        class="form-control @error('password') is-invalid @enderror"
                                        placeholder="Masukkan password" required />
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="password_confirmation">Konfirmasi Password <span
                                            class="text-danger">*</span></label>
                                    <input type="password" name="password_confirmation"
                                        class="form-control @error('password_confirmation') is-invalid @enderror"
                                        placeholder="Ulangi password" required />
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

                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Simpan
                                    </button>
                                </div>
                            </form>

                            <hr>
                            <div class="text-center">
                                <a class="small" href="{{ route('password.request') }}">Forgot Password?</a>
                            </div>
                            <div class="text-center">
                                <a class="small" href="{{ route('login') }}">Already have an account? Login!</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>

    <script src="js/sb-admin-2.min.js"></script>

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
</body>

</html>
