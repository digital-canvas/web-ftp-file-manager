@extends('layout.app')
@section('content')

    <div class="w-full max-w-xs mx-auto">

        <form class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4" method="post" action="{{ url('login') }}">
            <h1 class="text-xl font-serif text-gray-700 mb-4">Log In</h1>
            @if($messages)
                <div class="bg-red-300 border border-red-400 text-red-700 px-4 py-3 mb-4 rounded relative"
                     role="alert">
                    @foreach($messages as $message)
                        <div>{{ $message }}</div>
                    @endforeach
                </div>
            @endif

            @if(config('ftp.name'))
                <div class="mb-4">
                    <div class="block text-gray-700 text-sm font-bold mb-2">Server</div>
                    <div>{{ config('ftp.name') }}</div>
                </div>
            @endif

            <div class="mb-4">
                <label for="login-username" class="block text-gray-700 text-sm font-bold mb-2">Username</label>
                <input id="login-username" class="shadow appearance-none border w-full py-2 px-3 text-gray-700"
                       type="text" name="username" value="" required>
            </div>
            <div class="mb-4">
                <label for="login-password" class="block text-gray-700 text-sm font-bold mb-2">Password</label>
                <input id="login-password" class="shadow appearance-none border w-full py-2 px-3 text-gray-700"
                       type="password" name="password" value="" required>
            </div>
            <div class="mb-4">
                <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4">Log In</button>
            </div>
            {{ csrf_field() }}
        </form>
    </div>

@endsection
