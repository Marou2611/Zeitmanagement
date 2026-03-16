<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'Zeitmanagement') }}</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet">
        <style>
            body { background: #f0f2f5; font-family: 'Figtree', sans-serif; }
            .login-card { border-radius: 12px; box-shadow: 0 4px 24px rgba(0,0,0,0.10); }
            .btn-primary { background: #4f46e5; border-color: #4f46e5; }
            .btn-primary:hover { background: #4338ca; border-color: #4338ca; }
        </style>
    </head>
    <body>
        <div class="min-vh-100 d-flex align-items-center justify-content-center">
            <div class="w-100" style="max-width: 420px; padding: 1rem;">
                <div class="text-center mb-4">
                    <a href="/">
                        <x-application-logo style="width:60px; height:60px;" />
                    </a>
                    <h4 class="mt-2 fw-bold text-dark">{{ config('app.name') }}</h4>
                </div>
                <div class="card login-card">
                    <div class="card-body p-4">
                        {{ $slot }}
                    </div>
                </div>
            </div>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>
