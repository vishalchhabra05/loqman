<?php
$user = auth()->user();

$user_full_name = $user->name;
$user_role = $user->role;
$roles_names_arr = Config::get('params.role_names');
$role_name = isset($roles_names_arr[$user_role]) ? $roles_names_arr[$user_role] : '';
$file_save_path = Config::get('params.file_save_path');
$admin_default_image = Config::get('params.admin_default_image');
$profile_pic = !empty($user->profile_image) ? asset('public/'.$user->profile_image): asset('public/'.$admin_default_image);
?>
<header class="main-header">
    <!-- Logo -->
    <a href="{!! route('admin.dashboard') !!}" class="logo">
        <!-- mini logo for sidebar mini 50x50 pixels -->
        
        <!-- logo for regular state and mobile devices -->
        <span class="logo-lg"><?= Config::get('params.site_title') ?></span>
    </a>
    <!-- Header Navbar: style can be found in header.less -->
    <nav class="navbar navbar-static-top">
        <!-- Sidebar toggle button-->
        <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
            <span class="sr-only">Toggle navigation</span>
        </a>

        <div class="navbar-custom-menu">
            <ul class="nav navbar-nav">
                <!-- User Account: style can be found in dropdown.less -->
                <li class="dropdown user user-menu">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                    @if(auth()->user()->profile_image)
                        <img src="{{asset('public/'.auth()->user()->profile_image)}}" class="user-image" alt="User Image">
                    @else
                        <img src="{{asset('public/images/profile-image.svg')}}" class="user-image" alt="User Image">
                    @endif
                        <span class="hidden-xs"><?= $user_full_name ?></span>
                    </a>
                    <ul class="dropdown-menu">
                        <!-- User image -->
                        <li class="user-header">
                        @if(auth()->user()->profile_image)
                            <img src="{{asset('public/'.auth()->user()->profile_image)}}" class="img-circle" alt="User Image">
                        @else
                            <img src="{{asset('public/images/profile-image.svg')}}" class="img-circle" alt="User Image">
                        @endif

                            <p>
                                <?= $user_full_name.' - '.$role_name ?>
                            </p>
                        </li>
                        <!-- Menu Body -->
                        <li class="user-body">
                            
                            <!-- /.row -->
                        </li>
                        <!-- Menu Footer-->
                        <li class="user-footer">
                            <div class="pull-left">
                                <a href="<?= route('admin.myprofile') ?>" class="btn btn-default btn-flat">Profile</a>
                            </div>
                            <div class="pull-right">
                                <a href="<?= route('admin.logout') ?>" class="btn btn-default btn-flat">Sign out</a>
                            </div>
                        </li>
                    </ul>
                </li>
                
            </ul>
        </div>
    </nav>
</header>