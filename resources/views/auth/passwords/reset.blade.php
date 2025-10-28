<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ganti Password</title>

    <!-- SB Admin 2 CSS -->
    <link href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/sb-admin-2.min.css') }}" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body class="bg-gradient-primary">

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-6 col-lg-6 col-md-9">
                <div class="card o-hidden border-0 shadow-lg my-5">
                    <div class="card-body p-5">
                        <div class="text-center">
                            <h1 class="h4 text-gray-900 mb-4">Reset Password</h1>
                        </div>

                        <form method="POST" action="{{ route('password.update') }}" class="user">
                            @csrf
                            <input type="hidden" name="token" value="{{ $token }}">
                            <input type="hidden" name="email" value="{{ request()->email }}">

                            <div class="form-group">
                                <input type="password" name="password" class="form-control form-control-user"
                                    placeholder="Password Baru" required>
                            </div>

                            <div class="form-group">
                                <input type="password" name="password_confirmation"
                                    class="form-control form-control-user" placeholder="Konfirmasi Password" required>
                            </div>

                            <button type="submit" class="btn btn-primary btn-user btn-block">
                                Ganti Password
                            </button>
                        </form>

                        <hr>
                        <div class="text-center">
                            <a class="small" href="{{ route('login') }}">Kembali ke Login</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- JS SB Admin 2 -->
    <script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('vendor/jquery-easing/jquery.easing.min.js') }}"></script>
    <script src="{{ asset('js/sb-admin-2.min.js') }}"></script>

    <!-- SweetAlert2 -->
    <script>
        @if ($errors->any())
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                html: `{!! implode('<br>', $errors->all()) !!}`
            });
        @endif
    </script>

</body>

</html>
