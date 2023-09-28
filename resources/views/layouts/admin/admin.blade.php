<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <?php
        $site_title = Config::get('params.site_title');
        $page_title = isset($title_page) ? "$site_title - $title_page" : $site_title;
        ?>
        <title>
        <?php echo $page_title?>
       </title>
        <!-- Tell the browser to be responsive to screen width -->
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
        <!-- Bootstrap 3.3.7 -->
        <link rel="stylesheet" href="{{asset('/public/assets/admin/bootstrap.min.css')}}">
        <!-- Font Awesome -->
        <link rel="stylesheet" href="{{asset('/public/assets/admin/font-awesome/css/font-awesome.min.css')}}">
        <!-- Ionicons -->
        <!-- Theme style -->
        <link rel="stylesheet" href="{{asset('/public/assets/admin/AdminLTE.min.css')}}">
        <link rel="stylesheet" href="{{asset('/public/assets/admin/custom.css')}}">
        <link rel="stylesheet" href="{{asset('/public/assets/admin/style.css')}}">
        <link rel="stylesheet" href="{{asset('/public/assets/admin/_all-skins.min.css')}}">
        <link rel="stylesheet" href="{{asset('/public/assets/admin/sweetalert.css')}}">  
        <link rel="stylesheet" href="{{asset('/public/assets/admin/bootstrap-material-datetimepicker.css')}}">  
        <!-- AdminLTE Skins. Choose a skin from the css/skins
             folder instead of downloading all of them to reduce the load. -->
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
        @yield('uniquepagestyle')
        <meta name="csrf-token" content="{{ csrf_token() }}">
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
    <body class="hold-transition skin-blue sidebar-mini">
        <div class="wrapper">

            @include('admin.includes.header')
            <!-- Left side column. contains the logo and sidebar -->
            @include('admin.includes.sidebar')

            <!-- Content Wrapper. Contains page content -->
            <div class="content-wrapper">
                @yield('content')
            </div>
            <!-- /.content-wrapper -->
            <?php // @include('admin.includes.footer') ?>

            <!-- Add the sidebar's background. This div must be placed
                 immediately after the control sidebar -->
            <div class="control-sidebar-bg"></div>
        </div>
        <!-- ./wrapper -->

        <!-- jQuery 3 -->
        <script src="{{asset('/public/assets/admin/js/jquery.min.js')}}"></script>
        <!-- jQuery UI 1.11.4 -->
        <script src="{{asset('/public/assets/admin/js/jquery-ui.min.js')}}"></script>
        <!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
        <script>
$.widget.bridge('uibutton', $.ui.button);
        </script>
        <!-- Bootstrap 3.3.7 -->
        <script src="{{asset('/public/assets/admin/js/bootstrap.min.js')}}"></script>
        <!-- Morris.js charts -->
        <script src="{{asset('/public/assets/admin/js/adminlte.min.js')}}"></script>
        <!-- AdminLTE dashboard demo (This is only for demo purposes) -->
        <script src="{{asset('/public/assets/admin/sweetalert.min.js')}}"></script>
        <script src="{{asset('/public/assets/admin/notify.min.js')}}"></script>
        <script src="{{asset('/public/assets/admin/bootstrap-material-datetimepicker.min.js')}}"></script>
        <script src="{{asset('/public/assets/admin/jquery.validate.min.js')}}"></script>
        <script src="{{asset('/public/ckeditor/ckeditor.js')}}"></script>
        <script src="{{asset('public/ckeditor/adapters/jquery.js')}}"></script>
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
            var admin_page_length = "<?= Config::get('params.admin_page_length') ?>";
            
            
            
            
function confirmDelete(event, obj_form) {
    event.preventDefault();
    swal({
        title: "Are you sure?",
        text: "You will not be able to recover this record!",
        type: "warning",
        showCancelButton: true,
        confirmButtonClass: "btn-danger",
        confirmButtonText: "Yes, delete it!",
        closeOnConfirm: false
    },
            function () {
                obj_form.submit();
                return true;
            });
}

CKEDITOR.replace('descritionss',
{
    customConfig : 'config.js',
    toolbar : 'simple'
});
        </script>
        @yield('uniquepagescript')
    </body>
</html>
