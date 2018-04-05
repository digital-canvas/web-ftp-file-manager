@extends('layout.app')
@section('content')

    <div class="w-full max-w-xs mx-auto">

        <form class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4" method="post" action="{{ url('login') }}">
            <h1 class="font-serif text-grey-darker mb-4">Log In</h1>
            @if($messages)
                <div class="bg-red-lightest border border-red-light text-red-dark px-4 py-3 mb-4 rounded relative"
                     role="alert">
                    @foreach($messages as $message)
                        <div>{{ $message }}</div>
                    @endforeach
                </div>
            @endif

            @if(config('ftp.name'))
                <div class="mb-4">
                    <div class="block text-grey-darker text-sm font-bold mb-2">Server</div>
                    <div>{{ config('ftp.name') }}</div>
                </div>
            @endif

            <div class="mb-4">
                <label for="login-username" class="block text-grey-darker text-sm font-bold mb-2">Username</label>
                <input id="login-username" class="shadow appearance-none border w-full py-2 px-3 text-grey-darker"
                       type="text" name="username" value="" required>
            </div>
            <div class="mb-4">
                <label for="login-password" class="block text-grey-darker text-sm font-bold mb-2">Password</label>
                <input id="login-password" class="shadow appearance-none border w-full py-2 px-3 text-grey-darker"
                       type="password" name="password" value="" required>
            </div>
            <div class="mb-4">
                <button type="submit" class="bg-blue hover:bg-blue-dark text-white font-bold py-2 px-4">Log In</button>
            </div>
            {{ csrf_field() }}
        </form>
    </div>

@endsection
