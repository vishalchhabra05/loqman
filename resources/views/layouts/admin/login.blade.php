<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <?php
        $site_title = Session::get('settings.site_title');
        $page_title = isset($title_page) ? "$site_title - $title_page" : $site_title;
        ?>
        <title><?= $page_title ?></title>
        <!-- Tell the browser to be responsive to screen width -->
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
        <!-- Bootstrap 3.3.7 -->
        <link rel="stylesheet" href="{{asset('public/assets/admin/bootstrap.min.css')}}">
        <!-- Font Awesome -->
        <link rel="stylesheet" href="{{asset('public/assets/admin/font-awesome/css/font-awesome.min.css')}}">
        <!-- Ionicons -->

        <!-- Theme style -->
        <link rel="stylesheet" href="{{asset('public/assets/admin/AdminLTE.min.css')}}">
        <link rel="stylesheet" href="{{asset('public/assets/admin/style.css')}}">
        <!-- iCheck -->


        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->

        <!-- Google Font -->
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
        <link rel="apple-touch-icon" sizes="57x57" href="{{asset('public/favicon/apple-icon-57x57.png')}}">
        <link rel="apple-touch-icon" sizes="60x60" href="{{asset('public/favicon/apple-icon-60x60.png')}}">
        <link rel="apple-touch-icon" sizes="72x72" href="{{asset('public/favicon/apple-icon-72x72.png')}}">
        <link rel="apple-touch-icon" sizes="76x76" href="{{asset('public/favicon/apple-icon-76x76.png')}}">
        <link rel="apple-touch-icon" sizes="114x114" href="{{asset('public/favicon/apple-icon-114x114.png')}}">
        <link rel="apple-touch-icon" sizes="120x120" href="{{asset('public/favicon/apple-icon-120x120.png')}}">
        <link rel="apple-touch-icon" sizes="144x144" href="{{asset('public/favicon/apple-icon-144x144.png')}}">
        <link rel="apple-touch-icon" sizes="152x152" href="{{asset('public/favicon/apple-icon-152x152.png')}}">
        <link rel="apple-touch-icon" sizes="180x180" href="{{asset('public/favicon/apple-icon-180x180.png')}}">
        <link rel="icon" type="image/png" sizes="192x192"  href="{{asset('public/favicon/android-icon-192x192.png')}}">
        <link rel="icon" type="image/png" sizes="32x32" href="{{asset('public/favicon/favicon-32x32.png')}}">
        <link rel="icon" type="image/png" sizes="96x96" href="{{asset('public/favicon/favicon-96x96.png')}}">
        <link rel="icon" type="image/png" sizes="16x16" href="{{asset('public/favicon/favicon-16x16.png')}}">
        <link rel="manifest" href="/manifest.json">
        <meta name="msapplication-TileColor" content="#ffffff">
        <meta name="msapplication-TileImage" content="/ms-icon-144x144.png">
        <meta name="theme-color" content="#ffffff">
    </head>
    <body class="hold-transition login-page">

        <!-- /.login-box -->
        @yield('content')

        <!-- jQuery 3 -->
        <script src="{{asset('public/assets/admin/js/jquery.min.js')}}"></script>
        <!-- Bootstrap 3.3.7 -->
        <script src="{{asset('public/assets/admin/js/bootstrap.min.js')}}"></script>
        <!-- iCheck -->
        <script src="{{asset('public/assets/admin/notify.min.js')}}"></script>
        <?php
        $msg_type = (session()->has('success') ? 'success' : ( session()->has('error') ? 'error' : ( session()->has('warning') ? 'warning' : '')));
        $message = ''; 
        if($msg_type) {
            $message = session()->get($msg_type);
        }
        ?>
        <script>
            var msg_type = "<?= $msg_type ?>";
            var message = "<?= $message ?>";
            if(msg_type) {
                $.notify(message, msg_type);
            }
        </script>
        
    </body>
</html>
