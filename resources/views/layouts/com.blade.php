<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name') }}</title>

    <!-- Scripts -->
    <!-- <script src="{{ asset('js/app.js') }}" defer></script> -->

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/output.css') }}" rel="stylesheet">
</head>
<body class="font-sans antialiased bg-white">
    <div class="min-h-screen bg-white">
        <div class="container mx-auto h-screen">
            <nav class="navbar navbar-expand-lg navbar-light navbar-laravel">
                <div class="container">
                    <a class="navbar-brand" href="{{ url('/chat') }}">
                        {{ config('app.name') }}
                    </a>
                </div>
            </nav>
            <main>
                @yield('content')
            </main>
        </div>
    </div>
    @yield('js')
</body>
</html>
