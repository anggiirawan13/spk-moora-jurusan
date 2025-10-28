<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Reset Password - {{ config('app.name') }}</title>
    <style>
        body {
            font-family: 'Nunito', sans-serif;
            background-color: #f4f6f9;
            color: #343a40;
            padding: 40px 0;
        }

        .container {
            max-width: 600px;
            background-color: #fff;
            border-radius: 8px;
            margin: auto;
            padding: 40px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        .logo {
            text-align: center;
            margin-bottom: 30px;
        }

        .logo img {
            height: 60px;
        }

        h1 {
            color: #4e73df;
            font-size: 24px;
            margin-bottom: 10px;
        }

        p {
            font-size: 15px;
            line-height: 1.6;
            margin: 15px 0;
        }

        .btn {
            display: inline-block;
            background-color: #4e73df;
            color: #fff;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: bold;
            margin: 20px 0;
        }

        .footer {
            text-align: center;
            font-size: 13px;
            color: #6c757d;
            margin-top: 40px;
        }

        .url-preview {
            word-break: break-all;
            font-size: 13px;
            color: #6c757d;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>🔐 Reset Password</h1>

        <p>Halo {{ $user->name ?? 'Pengguna' }},</p>

        <p>Kami menerima permintaan untuk mereset password akun Anda. Klik tombol di bawah untuk mengatur ulang password
            Anda:</p>

        <p style="text-align: center;">
            <a href="{{ $url }}" class="btn" style="color: white;">Reset Password</a>
        </p>

        <p><strong>Catatan:</strong> Link ini hanya berlaku selama <strong>5 menit</strong>. Setelah itu, Anda perlu
            mengajukan permintaan baru.</p>

        <p>Jika Anda tidak meminta reset password, abaikan saja email ini. Akun Anda akan tetap aman.</p>

        <p>Salam hangat,<br><strong>{{ config('app.name') }}</strong></p>

        <hr style="margin-top: 40px; border: none; border-top: 1px solid #e3e6f0;">

        <div class="footer">
            &copy; {{ date('Y') }} {{ config('app.name') }}. Semua hak dilindungi.
        </div>
    </div>
</body>

</html>
