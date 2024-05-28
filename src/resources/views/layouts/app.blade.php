<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Laravel Terminal Beta</title>

    <!-- Styles -->
    <link href="{{ route('laravel-terminal.assets.serve', 'xterm.css') }}" rel="stylesheet">
    @livewireStyles
</head>
<body style="background-color: {{ config('laravel-terminal.terminal.colors.background') }};">
    {{ $slot }}
    <!-- Scripts -->
    @livewireScripts
    <script src="{{ route('laravel-terminal.assets.serve', 'xterm.js') }}"></script>
</body>
</html>
