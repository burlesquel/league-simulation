<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

        <link href="{{asset('assets/css/bootstrap.min.css')}}" rel="stylesheet" crossorigin="anonymous">
        <link href="{{asset('assets/css/style.css')}}" rel="stylesheet" crossorigin="anonymous">
        
    </head>
    <div class="py-5 d-flex flex-row justify-content-center">
        <img style="width:12rem" src="{{asset('logos/league-logo.png')}}" alt="">
    </div>
    {{ $slot }}
    <script src="{{asset('assets/js/jquery.min.js')}}" crossorigin="anonymous"></script>
    <script src="{{asset('assets/js/bootstrap.bundle.js')}}" crossorigin="anonymous"></script>
    <script src="{{asset('assets/js/vue.js')}}" crossorigin="anonymous"></script>
    @stack('scripts')
</html>
