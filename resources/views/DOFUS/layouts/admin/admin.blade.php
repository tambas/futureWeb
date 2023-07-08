<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Azote Admin">
    <title>{{ config('dofus.title') }} Admin - @yield('title')</title>
    <link rel="icon" type="image/png" href="{{ URL::asset('imgs/favicon.png') }}" />

    <!-- STYLES (Elixir) -->
    {!! Html::style('css/bootstrap.min.css') !!}
    {!! Html::style('css/vendor_admin.min.css') !!}
    {!! Html::style('css/app_admin.min.css') !!}

    <style>
        .topbar {
            z-index: 0 !important;
        }
    </style>

    <!-- HTML5 Shiv and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->

     <!-- JQUERY & JQUERY-UI -->
    {!! Html::script('js/jquery-2.1.4.min.js') !!}
    {!! Html::script('js/jquery-ui.min.js') !!}
    <!-- MODERNIZR (Elixir) -->
    {!! Html::script('js/admin/modernizr.min.js') !!}

    @yield('header')
</head>
<body class="fixed-left">

    <!-- Begin page -->
    <div id="wrapper">
    @include('includes.admin.topbar')

        @include('includes.admin.leftsidebar')

        <div class="content-page">
        @yield('content')
            @include('includes.admin.footer')
        </div>
    </div>
    <!-- END wrapper -->

<script>
        var resizefunc = [];
</script>
        <!-- App (Elixir) -->
        {!! Html::script('js/admin/vendor_admin.min.js') !!}
        {!! Html::script('js/admin/app_admin.min.js') !!}
        @yield('bottom')
        <!-- Toastr (Elixir) -->
        {!! Html::style('css/toastr.min.css') !!}
        {!! Html::script('js/admin/toastr.min.js') !!}
        {!! Toastr::render() !!}
</body>
</html>
