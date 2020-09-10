<!doctype html>
@inject('config', 'app.config')
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>{{ config('app.name') }}</title>
    <link rel="stylesheet" href="{{ asset('layui/css/layui.css') }}">
    <link rel="stylesheet" href="{{ mix('css/app.css') }}">
    <script src="{{ mix('js/common.js') }}"></script>
    <script src="{{ asset('layui/layui.js') }}"></script>
    @stack('head')
</head>
<body class="@yield('body-class')">
    @section('body')
    @show
    @stack('script')
</body>
@stack('after-body')
</html>
