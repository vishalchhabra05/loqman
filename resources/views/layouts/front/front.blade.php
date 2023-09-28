<!DOCTYPE html>

<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1, minimum-scale=1, user-scalable=0" />
    <title> Home - Blinkers </title>
    <link rel="shortcut icon" href="{{ asset('public/assets/front/images/favicon-32x32.png') }}" type="image/x-icon">
    <link rel="shortcut icon" href="{{ asset('public/assets/front/images/favicon-16x16.png') }}" type="image/x-icon">

    <!--- 3 Party Css ---->

    <link rel="stylesheet" href="{{asset('public/assets/front/css/bootstrap.min.css')}}">
    <link rel="stylesheet" href="{{asset('public/assets/front/css/all.min.css')}}">
    <link rel="stylesheet" href="{{asset('public/assets/front/css/animate.css')}}">

    <link rel="stylesheet" href="{{asset('public/assets/front/css/tempusdominus-bootstrap-4.css')}}">

    <link rel="stylesheet" href="{{asset('public/assets/front/css/select2.min.css')}}">
    <link rel="stylesheet" href="{{asset('public/assets/front/css/jquery.mCustomScrollbar.css')}}">
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

    <link rel="stylesheet" href="{{asset('public/assets/front/css/slick.css')}}">
    <link rel="stylesheet" href="{{asset('public/assets/front/css/lightgallery.min.css')}}">
    

    <link rel="stylesheet" href="{{asset('public/assets/front/css/style.css')}}">
    <link rel="stylesheet" href="{{asset('public/assets/front/css/responsive.css')}}">

    

    

    <!--- Custom Style ---->

</head>
<body id="mainBody">

@include('front.includes.header')
@yield('content')    
@include('front.includes.footer')
@yield('uniquepagescript')
</body>
</html>