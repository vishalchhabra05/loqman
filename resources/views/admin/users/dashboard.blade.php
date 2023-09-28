@extends('layouts.admin.admin')
@section('content')



<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        <?= $title_page ?>
    </h1>
    <ol class="breadcrumb">
        <li class="active"><i class="fa fa-dashboard"></i><?= $title_page  ?></li>
    </ol>
</section>

<!-- Main content -->
<section class="content">
    <!-- Small boxes (Stat box) -->
    <div class="row">
   
     
        <div class="col-lg-3 col-xs-6">
            <!-- small box -->
            <div class="small-box bg-aqua">
                <div class="inner">
                    <h3>Users</h3>
                    <p>Total <?= $user_count ?></p>
                </div>
                <div class="icon">
                    <i class="fa fas fa-users"></i>
                </div>
                <a href="<?= route('admin.users', ['role'=>$role_ids['Users']]) ?>" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
            </div>
        </div>

        <div class="col-lg-3 col-xs-6">
            <!-- small box -->
            <div class="small-box bg-aqua">
                <div class="inner">
                    <h3>Expert</h3>
                    <p>Total <?= $expert_count ?></p>
                </div>
                <div class="icon">
                    <i class="fa fas fa-users"></i>
                </div>
                <a href="<?= route('admin.users', ['role'=>$role_ids['Expert']]) ?>" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
            </div>
        </div>

        <div class="col-lg-3 col-xs-6">
            <!-- small box -->
            <div class="small-box bg-aqua">
                <div class="inner">
                    <h3>Category</h3>
                    <p>Total <?= $category ?></p>
                </div>
                <div class="icon">
                    <i class="fa fas fa-users"></i>
                </div>
                <a href="<?= route('categories.index') ?>" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
            </div>
        </div>

        <div class="col-lg-3 col-xs-6">
            <!-- small box -->
            <div class="small-box bg-aqua">
                <div class="inner">
                    <h3>User(Last 24 h)</h3>
                    <p>Total <?= $user_count_24h ?> </p>
                </div>
                <div class="icon">
                    <i class="fa fas fa-users"></i>
                </div>
                <a href="<?= route('admin.users', ['role'=>$role_ids['Users']]) ?>" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
            </div>
        </div>

        <div class="col-lg-3 col-xs-6">
            <!-- small box -->
            <div class="small-box bg-aqua">
                <div class="inner">
                    <h3>Expert (last 24 h)</h3>
                    <p>Total  <?= $expert_count_24h ?></p>
                </div>
                <div class="icon">
                    <i class="fa fas fa-users"></i>
                </div>
                <a href="<?= route('admin.users', ['role'=>$role_ids['Expert']]) ?>" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
            </div>
        </div>

        <div class="col-lg-3 col-xs-6">
            <!-- small box -->
            <div class="small-box bg-aqua">
                <div class="inner">
                    <h3>Call</h3>
                    <p>Total <?= $totalcall ?></p>
                </div>
                <div class="icon">
                    <i class="fa fas fa-users"></i>
                </div>
                <a href="#" class="small-box-footer"><i class="fa fa-arrow"></i></a>
            </div>
        </div>

        <div class="col-lg-3 col-xs-6">
            <!-- small box -->
            <div class="small-box bg-aqua">
                <div class="inner">
                    <h3>Call (Last 24 h)</h3>
                    <p>Total <?= $call24 ?></p>
                </div>
                <div class="icon">
                    <i class="fa fas fa-users"></i>
                </div>
                <a href="{{route('report.index')}}" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
            </div>
        </div>

        <div class="col-lg-3 col-xs-6">
            <!-- small box -->
            <div class="small-box bg-aqua">
                <div class="inner">
                    <h3>Call(last min.)</h3>
                    <p>Total 0 </p>
                </div>
                <div class="icon">
                    <i class="fa fas fa-users"></i>
                </div>
                <a href="{{route('report.index')}}" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
            </div>
        </div>

        <div class="col-lg-3 col-xs-6">
            <!-- small box -->
            <div class="small-box bg-aqua">
                <div class="inner">
                    <h3>Rating  all calls</h3>
                    <p>Total 0</p>
                </div>
                <div class="icon">
                    <i class="fa fas fa-users"></i>
                </div>
                <a href="#" class="small-box-footer"><i class="fa fa-arrow"></i></a>
            </div>
        </div>

        <div class="col-lg-3 col-xs-6">
            <!-- small box -->
            <div class="small-box bg-aqua">
                <div class="inner">
                    <h3>Total calls duration in minutes</h3>
                    <p>Total <?= date('H:i:s',$AllCallmin) ?> Minutes</p>
                </div>
                <div class="icon">
                    <i class="fa fas fa-users"></i>
                </div>
                <a href="#" class="small-box-footer"><i class="fa fa-arrow"></i></a>
            </div>
        </div>
     
        <!-- ./col -->
    </div>
    <!-- /.row -->
    <!-- Main row -->
    <div class="row">
        <!-- Left col -->

        <!-- right col -->
    </div>
    <!-- /.row (main row) -->

</section>
<!-- /.content -->
@endsection