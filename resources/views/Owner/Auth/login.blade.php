<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Login Owner — Golden Spice</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://fonts.googleapis.com/css2?family=Koulen&family=Questrial&display=swap" rel="stylesheet">
    <link rel="stylesheet"
          href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:wght@400;600&display=swap">

    <style>
        body{
            margin:0;
            min-height:100vh;
            display:grid;
            place-items:center;
            background:#f4f4f6;
            font-family:'Questrial',system-ui,-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif;
        }
        .auth-card{
            width:100%;
            max-width:420px;
            background:#fff;
            border-radius:24px;
            box-shadow:0 20px 60px rgba(0,0,0,.08);
            padding:28px 26px 24px;
        }
        .auth-title{
            font-family:'Koulen';
            font-size:26px;
            letter-spacing:.06em;
            margin-bottom:4px;
        }
        .muted{
            font-size:13px;
            color:#777;
            margin-bottom:20px;
        }
        .form-group{ margin-bottom:14px; font-size:13px; }
        label{ display:block; margin-bottom:4px; }
        input{
            width:100%;
            border-radius:999px;
            border:1px solid #ddd;
            padding:10px 14px;
            font-size:13px;
            outline:none;
        }
        input:focus{
            border-color:#D50505;
            box-shadow:0 0 0 1px rgba(213,5,5,.15);
        }
        .btn-primary{
            width:100%;
            border:none;
            border-radius:999px;
            padding:10px 16px;
            background:#D50505;
            color:#fff;
            font-weight:600;
            cursor:pointer;
            margin-top:4px;
        }
        .btn-primary:hover{ filter:brightness(1.05); }
        .bottom-text{
            margin-top:18px;
            font-size:13px;
            text-align:center;
        }
        .bottom-text a{
            color:#D50505;
            text-decoration:none;
            font-weight:600;
        }
        .error{
            font-size:12px;
            color:#b91c1c;
            margin-top:4px;
        }
        .alert{
            background:#fef2f2;
            border:1px solid #fecaca;
            padding:8px 10px;
            border-radius:12px;
            font-size:12px;
            color:#b91c1c;
            margin-bottom:10px;
        }
    </style>
</head>
<body>
<div class="auth-card">
    <div class="auth-title">OWNER LOGIN</div>
    <div class="muted">Masuk ke panel owner untuk mengelola menu Golden Spice.</div>

    @if($errors->any())
        <div class="alert">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('owner.login.submit') }}">
        @csrf
        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" value="{{ old('email') }}" required autofocus>
        </div>
        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" required>
        </div>
        <button class="btn-primary" type="submit">Masuk</button>
    </form>

    <div class="bottom-text">
    Akses hanya untuk pemilik Golden Spice.
</div>

</div>
</body>
</html>
