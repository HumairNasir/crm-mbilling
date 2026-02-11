<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @include('partials.styles')
    @yield('styles')
    <title>Kriss Crm</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script> -->

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>

    <script src="{{ asset('js/custom.js') }}"></script>

   

</head>
<div id="loader-overlay" style="display: none;">
        <div id="loader"></div>
    </div>

<body>
<div class="d-flex flex-column">
    @include('partials.header')
    <div class="page-content">
        @include('partials.sidebar')
        @yield('content')
    </div>
    @include('partials.footer')
</div>

@include('partials.scripts')
@include('partials.notification-scripts')
@yield('scripts')
</body>

</html>
