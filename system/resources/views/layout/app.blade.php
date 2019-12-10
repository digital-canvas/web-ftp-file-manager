<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="profile" href="http://gmpg.org/xfn/11">
    <title>@section('title'){{ config('app.name', 'Web FTP Manager') }}@show</title>

    <link rel="stylesheet" href="{{ asset('app.css') }}">
    @yield('metatags')
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script defer src="https://use.fontawesome.com/releases/v5.0.9/js/all.js" integrity="sha384-8iPTk2s/jMVj81dnzb/iFR2sdA7u06vHJyyLlAd4snFpCl/SnyUjRrbdJsw1pGIl" crossorigin="anonymous"></script>
    @stack('head')
</head>
<body>
<div id="app" class="flex flex-col min-h-screen">
    <header class="bg-teal-600 text-white py-4">
        <div class="container mx-auto flex justify-between items-center">
            <div class="text-5xl font-bold">{{ config('app.name', 'Web FTP Manager') }}</div>

            @if(auth()->check())
                <a class="text-white no-underline" href="#" onclick="document.getElementById('form-logout').submit();">Log Out</a>
                <form id="form-logout" class="hidden" method="post" action="{{ route('logout') }}">
                    {{ csrf_field() }}
                </form>
            @endif
        </div>
    </header>
    <div class="container mx-auto flex-1 py-4">
        @yield('content')
    </div>
    <footer class="bg-black text-white py-3 text-center">
        <div class="container mx-auto">
            &copy; {{ date('Y') }} <a class="text-white" rel="nofollow" target="_blank" href="https://www.digitalcanvas.com">Digital Canvas</a>
        </div>
    </footer>

</div>
<script src="{{ asset('runtime.js') }}"></script>
<script src="{{ asset('app.js') }}"></script>
@stack('footer')
</body>

</html>
