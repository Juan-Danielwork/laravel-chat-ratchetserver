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

                    <button id="toggle_float" type="button" class="-my-1 -mr-1 ml-6 flex h-8 w-8 items-center justify-center lg:hidden"><span class="sr-only">Open navigation</span><svg viewBox="0 0 24 24" class="h-6 w-6 stroke-slate-900"><path d="M3.75 12h16.5M3.75 6.75h16.5M3.75 17.25h16.5" fill="none" stroke-width="1.5" stroke-linecap="round"></path></svg></button>

                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        <!-- Left Side Of Navbar -->
                        <ul class="navbar-nav mr-auto">

                        </ul>

                        <!-- Right Side Of Navbar -->
                        <ul class="navbar-nav ml-auto">
                            <!-- Authentication Links -->
                            @guest
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                                </li>
                            @else
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('chat.index') }}">Chat Box</a>
                                </li>
                            @endguest
                        </ul>
                    </div>
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
