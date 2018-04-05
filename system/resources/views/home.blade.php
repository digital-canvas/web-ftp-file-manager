@extends('layout.app')

@section('content')

    <file-browser message="{{ $message }}" data-directory="{{ $directory }}" :max-size="{{ $maxsize }}"></file-browser>

@endsection
