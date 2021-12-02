<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>

    <title>{{ config('app.name', 'Backend') }} | {{ !empty($custom_title) ? $custom_title : 'Admin Panel' }} </title>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" />
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/pages/login/login-4.min.css') }}" />

    @include('admin.auth.layouts.includes.css')
    @stack('extra-css-styles')

    @include('admin.auth.layouts.includes.favicon')
</head>
<body id="kt_body" class="header-fixed header-mobile-fixed subheader-enabled subheader-fixed aside-enabled aside-fixed aside-minimize-hoverable page-loading">
        @yield('content')
    <script src="{{ asset('js/app.js') }}"></script>
    <script src="{{ asset('admin/plugins/jquery-validation/js/jquery.validate.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('admin/plugins/jquery-validation/js/additional-methods.js') }}" type="text/javascript"></script>
    @stack('extra-js-scripts')
</body>
</html>
