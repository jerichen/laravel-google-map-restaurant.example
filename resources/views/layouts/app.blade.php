<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>google map example</title>
    <link rel="stylesheet" href="{{ secure_asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ secure_asset('css/jquery.ui.css') }}">
    <link rel="stylesheet" href="{{ secure_asset('css/custom.css') }}">
    <script src="{{ secure_asset('js/jquery.min.js') }}"></script>
    <script src="{{ secure_asset('js/jquery.ui.js') }}"></script>
    <script src="{{ secure_asset('js/bootstrap.min.js') }}"></script>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <style>
        #map {
            height: 90%;
        }
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
        }
    </style>
</head>
<body scroll="no" style="overflow: hidden">
@yield('content')
<div class="panel panel-warning" id="layer">
    <!--start header-->
    @include('vendor.menu')
    <!--end header-->
    @include('vendor.panel')
</div>
@include('vendor.review')
@include('vendor.detail')
@include('layouts.footer')
@include('layouts.js-map')
<script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLEAPIKEY') }}&callback=initialize"
        async defer></script>
</body>
</html>
