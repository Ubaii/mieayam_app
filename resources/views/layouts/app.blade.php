<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#2563eb">
    <title>@yield('title', 'Dashboard') — MIE AYAM WENGI'57</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        
        @media (max-width: 1023.98px) {
            .app-main { margin-left: 0 !important; width: 100% !important; max-width: 100% !important; }
        }
        
        @media (min-width: 1024px) {
            .app-main { margin-left: 248px; }
        }
        .app-shell { overflow-x: hidden; }
    </style>
</head>
<body class="app-body">
    <div class="app-shell">
        <x-sidebar />
        <div class="app-main">
            <x-navbar />
            <main class="page-content">
                @if(session('status'))
                    <p class="form-alert success" role="status">{{ session('status') }}</p>
                @endif
                @if($errors->any())
                    <p class="form-alert error" role="alert">{{ $errors->first() }}</p>
                @endif
                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>