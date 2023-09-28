<?php
$user = auth()->user();
$route = Route::currentRouteAction();
$explode_route = explode('\\', $route);
$controller_ation = end($explode_route);
$controller_ation_ex = explode('@', $controller_ation);
$controller = isset($controller_ation_ex[0]) ? $controller_ation_ex[0] : '';
$action = isset($controller_ation_ex[1]) ? $controller_ation_ex[1] : '';
$user_full_name = $user->name;
$file_save_path = Config::get('params.file_save_path');
$admin_default_image = Config::get('params.admin_default_image');
$profile_pic = !empty($user->profile_image) ? asset('public/'.$user->profile_image) : asset('public/'.$admin_default_image);
$role_ids = Config::get('params.role_ids');
?>

<aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
        <!-- Sidebar user panel -->
        <div class="user-panel">
            <div class="pull-left image">
            @if(auth()->user()->profile_image)
                <img src="{{asset('public/'.auth()->user()->profile_image)}}" class="img-circle" alt="User Image">
            @else
                <img src="{{asset('public/images/profile-image.svg')}}" class="img-circle" alt="User Image">
            @endif
            </div>
            <div class="pull-left info">
                <p><?= $user_full_name ?></p>
                
            </div>
        </div>
        <!-- sidebar menu: : style can be found in sidebar.less -->
        <ul class="sidebar-menu" data-widget="tree">
            <li class="header">MAIN NAVIGATION</li>
            <?php
            $active = ($controller == 'UsersController' && $action == 'dashboard') ? 'active' : '';
            ?>
            <li class="<?= $active ?>"><a href="<?= route('admin.dashboard') ?>"><i class="fa fa-dashboard"></i> <span>Dashboard</span></a></li>
            
            
            <?php
            $active = ($controller == 'UsersController' && in_array($action, ['users', 'show', 'create', 'edit' ])) ? 'active' : '';
            ?>
            <li class="treeview <?= $active ?>">
                <a href="#"><i class="fa fa-users"></i><span> Users Managment</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">
                    
                    <li class="<?= $title_page == 'Users' ? 'active' : '' ?>"><a href="<?= route('admin.users', ['role'=>$role_ids['Users']]) ?>"><i class="fa fa-circle-o"></i> Users </a></li>

                    <li class="<?= $title_page == 'Guestuser' ? 'active' : '' ?>"><a href="<?= route('admin.users', ['role'=>$role_ids['Guestuser']]) ?>"><i class="fa fa-circle-o"></i> Guestuser </a></li>
                    
                    <li class="<?= $title_page == 'Expert' ? 'active' : '' ?>"><a href="<?= route('admin.users', ['role'=>$role_ids['Expert']]) ?>"><i class="fa fa-circle-o"></i> Expert </a></li>
                    
                </ul>
            </li>
            <?php
            
            $active = ($controller == 'CategoriesController') ? 'active' : '';
            ?>
            <li class="<?= $active ?>"><a href="{!! route('categories.index') !!}">
                <i class="fa fa-certificate"></i> <span>Categories</span></a></li>

            <?php
            $active = ($controller == 'BagesController') ? 'active' : '';
                ?>
            <li class="<?= $active ?>"><a href="{!! route('bages.index') !!}">
                    <i class="fa fa-certificate"></i> <span>Badge Management</span></a></li>
            
            
            
            
            <!-- <li class=""><a href="{!! route('tags.index') !!}">
                    <i class="fa fa-certificate"></i> <span>Tags Management</span></a></li> -->

                <?php
                   $active = ($controller == 'NotificationManagmentController') ? 'active' : '';
                ?>
            <li class="<?=$active ?>"><a href="{{route('notification.index')}}">
                    <i class="fa fa-certificate"></i> <span>Notifications Management</span></a></li>

                <?php
                   $active = ($controller == 'CmsController') ? 'active' : '';
                ?>
            <li class="treeview">
                <a href="#"><i class="fa fa-users"></i><span> Cms Managment</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">
                    
                    <li class="<?= $route == 'cms.index' ? 'active' : '' ?>"><a href="{{route('cms.index')}}"><i class="fa fa-circle-o"></i> Users Cms Managment </a></li>
                    <li class="#"><a href="{{route('admin.cms.exportcms')}}"><i class="fa fa-circle-o"></i> Expert Cms Managment</a></li>
                    
                </ul>
            </li>

            <?php
              $active = ($controller == 'ReportController') ? 'active' : '';
            ?>
            <li class="treeview">
                <a href="#"><i class="fa fa-users"></i><span> Report management</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">
                    
                    <li class="<?= $route == 'report.index' ? 'active' : '' ?>"><a href="{{route('report.index')}}"><i class="fa fa-circle-o"></i> Users Report Managment </a></li>
                    <li class="#"><a href="{{route('admin.expertreport')}}"><i class="fa fa-circle-o"></i> Expert Report Managment</a></li>
                    
                </ul>
            </li> 
            <?php
              $active = ($controller == 'RatingController') ? 'active' : '';
            ?>

            <li class="treeview">
                <a href="<?= $active ?>"><i class="fa fa-users"></i><span> Review & Rating Management</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">
                    
                    <li class="<?= $route == 'rating.index' ? 'active' : '' ?>"><a href="{{route('rating.index')}}"><i class="fa fa-circle-o"></i> Users Review & Rating Managment </a></li>
                    <li class="#"><a href="{{route('admin.rating_expert')}}"><i class="fa fa-circle-o"></i> Expert Review & Rating Managment</a></li>
                    
                </ul>
            </li> 

            <?php
              $active = ($controller == 'Feedbackcontroller') ? 'active' : '';
            ?>  

            <li class=""><a href="{{route('feedback.index')}}">
                <i class="fa fa-certificate"></i> <span>Feedback Management</span></a></li>  

            <?php
              $active = ($controller == 'ContactusController') ? 'active' : '';
            ?>
            <li class=""><a href="{{route('contact-us.index')}}">
                <i class="fa fa-certificate"></i> <span>Contact us</span></a></li>  
        </ul>
    </section>
    <!-- /.sidebar -->
</aside>


