<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'PPDB') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Styles -->
    <link rel="stylesheet" href="{{ asset('dist/css/tabler.min.css') }}">
    <link rel="stylesheet" href="{{ asset('dist/css/tabler-flags.min.css') }}">
    <link rel="stylesheet" href="{{ asset('dist/css/tabler-payments.min.css') }}">
    <link rel="stylesheet" href="{{ asset('dist/css/tabler-vendors.min.css') }}">
    
    <!-- Custom CSS -->
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: #f1f5f9;
        }
        .auth-card {
            max-width: 400px;
            margin: 2rem auto;
            border-radius: 10px;
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);
        }
    </style>
</head>
<body>
    <div class="page page-center">
        <div class="container container-tight py-4">
            <div class="text-center mb-4">
                <a href="/" class="navbar-brand navbar-brand-autodark">
                    <img src="{{ asset('static/logo.svg') }}" height="36" alt="">
                </a>
            </div>
            
            {{ $slot }}
            
            @if(session('status'))
            <div class="alert alert-success mt-3">
                {{ session('status') }}
            </div>
            @endif
        </div>
    </div>

    <!-- Scripts -->
    <script src="{{ asset('dist/js/tabler.min.js') }}" defer></script>
</body>
</html>
