<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#2563eb">
    <title>Masuk — MIE AYAM WENGI'57</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <main class="login-page">
        <section class="login-layout">
            <aside class="login-visual login-gradient">
                <div class="login-pattern"></div>
                <div class="login-visual-content">
                    <a class="login-visual-brand" href="{{ route('login') }}"><span class="brand-mark">M</span><span>MIE AYAM WENGI'57</span></a>
                    <div class="login-quote">
                        <span class="login-eyebrow">SEJAK XXXX, RASANYA TAK TERGANTIKAN</span>
                        <h2>Mie ayam legendaris dengan cita rasa autentik.</h2>
                        <p>Kelola warung mie ayam wengi'57 dengan mudah. Biar kami yang urus mie-nya.</p>
                    </div>
                </div>
            </aside>
            <div class="login-form-side">
                <section class="login-card">
                    <div class="login-brand">
                        <span class="brand-name">MIE AYAM WENGI'57</span>
                        <span class="brand-subtitle">Warung Makan Management System</span>
                    </div>
                    <h1>Selamat datang kembali</h1>
                    <p class="login-intro">Masuk untuk melanjutkan pengelolaan restoran Anda.</p>
                    @if(session('status'))<p class="form-alert success">{{ session('status') }}</p>@endif
                    @if($errors->any())<p class="form-alert error">{{ $errors->first() }}</p>@endif
                    <form class="login-form" action="{{ route('login.store') }}" method="post">
                        @csrf
                        <div class="form-group"><label class="field-label" for="email">Email</label><input class="input-control" id="email" name="email" type="email" value="{{ old('email') }}" placeholder="nama@email.com" autocomplete="email" required></div>
                        <div class="form-group">
                            <div class="login-label-row"><label class="field-label" for="password">Password</label><a href="{{ route('password.request') }}">Lupa password?</a></div>
                            <div class="password-field">
                                <input class="input-control" id="password" name="password" type="password" placeholder="Masukkan password" autocomplete="current-password" required data-password-input>
                                <button class="password-toggle" type="button" aria-label="Tampilkan password" aria-pressed="false" data-password-toggle>
                                    <x-icon name="eye" />
                                </button>
                            </div>
                        </div>
                        <label class="login-remember"><input type="checkbox" name="remember" value="1" @checked(old('remember'))> Ingat saya</label>
                        <button type="submit" class="btn btn-primary login-submit">Masuk ke dashboard</button>
                    </form>
                    @if($registrationAvailable)
                        <p class="login-signup">Pertama kali menggunakan aplikasi? <a href="{{ route('register') }}">Buat akun administrator</a></p>
                    @else
                        <p class="login-signup">Pendaftaran di halaman ini hanya untuk administrator pertama.</p>
                    @endif
                </section>
            </div>
        </section>
    </main>
</body>
</html>
