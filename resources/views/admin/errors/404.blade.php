<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Error')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Tambahkan CSS dasar (misal Bootstrap) -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f8f9fc;
            color: #5a5c69;
        }

        .error {
            font-size: 120px;
            font-weight: 700;
            color: #e74a3b;
        }

        .lead {
            font-size: 24px;
            font-weight: 500;
        }
    </style>
</head>

<body>
    <div class="text-center">
        <div class="error mx-auto" data-text="404">404</div>
        <p class="lead text-gray-800 mb-5">Page Not Found</p>
        <p class="text-gray-500 mb-0">It looks like you found a glitch in the matrix...</p>
        <a href="{{ route('student.index') }}">&larr; Back</a>
    </div>
</body>

</html>
