<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @include('partials.styles')
    @yield('styles')
    <title>Kriss Crm</title>
</head>

<body>
@include('partials.header')
<div class="page-content">
    @include('partials.sidebar')
    @yield('content')
</div>
@include('partials.scripts')
@yield('scripts')
</body>

</html>
