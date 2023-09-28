@extends('layouts.admin.admin')
@section('content')
<?php $file_save_path = Config::get('params.file_save_path'); ?>

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        <?= $title_page ?>
    </h1>
    @include('admin.includes.breadcumb')
</section>

<!-- Main content -->
<section class="content">

    <div class="box box-default">
        <div class="box-header with-border">
            <h3 class="box-title">Information</h3>
            <div class="pull-right">
                <a href="{!! route('admin.changePassword')!!}" class="btn btn-block btn-info">Change Password</a>
            </div>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
            <div class="col-md-12">
                {!! Form::model($user, ['method' => 'post', 'class'=>'form-horizontal validate', 'enctype'=>'multipart/form-data']) !!}
                {{ csrf_field() }}
                
                <div class="row">
                    <div class="col-md-6 <?= $errors->has('name') ? 'has-error' : '' ?>">
                        <label class="control-label" for="name">Name<span class="required">*</span>
                        </label>
                        {!! Form::text('name', null, ['class'=>'form-control','placeholder'=>'Name', 'required'=>true, 'maxlength'=>255]) !!}     
                        <span class="help-block"><?= $errors->has('name') ? $errors->first('name') : '' ?></span>
                    </div>
                    <div class="col-md-6 <?= $errors->has('email') ? 'has-error' : '' ?>">
                        <label class="control-label" for="email">Email<span class="required">*</span></label>
                        {!! Form::email('email', null, ['class'=>'form-control','placeholder'=>'Email','required'=>true, 'maxlength'=>255]) !!}                      
                        <span class="help-block"><?= $errors->has('email') ? $errors->first('email') : '' ?></span>
                    </div>
                </div>

                <div class="form-group <?= $errors->has('profile_image') ? 'has-error' : '' ?>">
                    <div class="col-md-12">
                        <label class="control-label" for="profile_image">Profile Picture</label>
                        {!! Form::file('profile_image'); !!}
                        <span class="help-block"><?= $errors->has('profile_image') ? $errors->first('profile_image') : '' ?></span>
                        <?php 
                        if (!empty($user->profile_image_full)) {
                            ?>
                            <img src="{{ $user->profile_image_full }}" style="max-width: 200px;" />
                            <?php
                        }
                        ?>
                    </div>
                </div>

                <div class="btn-block text-right">
                    <a href="{!! route('admin.dashboard') !!}" class="btn btn-default"> Cancel </a>
                    <?= Form::submit('Update', ['class' => 'btn btn-primary ']) ?>

                </div>
                {{ Form::close() }}

            </div>
        </div>
    </div>






    <!-- /.row -->
</section>
<!-- /.content -->
@endsection
