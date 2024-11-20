<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body>
    <div class="flex flex-col min-h-screen justify-between bg-background">
        <header class="flex items-center h-[10vh] px-4 shadow-md shadow-bg-low">
            <nav class="flex-1 flex items-center justify-between flex-row w-full">
                <ul class="">
                    <li>
                        <a href="/">Home</a>
                    </li>
                </ul>
                <ul class="flex flex-row gap-2">
                    @guest
                    @if (Route::has('login'))
                        <li class="">
                            <a class="" href="{{ route('login') }}">{{ __('Login') }}</a>
                        </li>
                    @endif
                    @if (Route::has('register'))
                        <li class="">
                            <a class="" href="{{ route('register') }}">{{ __('Register') }}</a>
                        </li>
                    @endif
                    @else
                    <li>
                        <a id="navbarDropdown" class="" role="button">
                            Welcome <span class="text-accent border-r-[2px] pr-2 border-solid border-text">{{ Auth::user()->name }}</span>
                         </a>
                    </li>
                        <li class="">                            
                            <div aria-labelledby="navbarDropdown">
                                <a href="{{ route('logout') }}"
                                onclick="event.preventDefault();
                                                document.getElementById('logout-form').submit();">
                                    {{ __('Logout') }}
                                </a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            </div>
                        </li>
                    @endguest
                </ul>
            </nav>
        </header>
        <main class="flex flex-col flex-1">
            @yield('content')
        </main>
        <footer class="flex h-[10vh]">
        </footer>
    </div>
</body>
</html>
