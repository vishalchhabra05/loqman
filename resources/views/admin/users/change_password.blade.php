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
        </div>
        <!-- /.box-header -->
        <div class="box-body">
            <div class="col-md-12">
                {!! Form::model($user, ['method' => 'post', 'class'=>'form-horizontal validate', 'enctype'=>'multipart/form-data']) !!}
                {{ csrf_field() }}
                <div class="row">
                    <div class="col-md-6 <?= $errors->has('old_password') ? 'has-error' : '' ?>">
                        <label class="control-label" for="old_password">Current Password<span class="required">*</span>
                        </label>
                        {!! Form::password('old_password', ['class'=>'form-control','placeholder'=>'Current Password', 'required'=>'', 'maxlength'=>255]) !!}     
                        <span class="help-block"><?= $errors->has('old_password') ? $errors->first('old_password') : '' ?></span>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 <?= $errors->has('new_password') ? 'has-error' : '' ?>">
                        <label class="control-label" for="new_password">New Password<span class="required">*</span>
                        </label>
                        {!! Form::password('new_password', ['class'=>'form-control','placeholder'=>'New Password', 'required'=>true, 'maxlength'=>255]) !!}     
                        <span class="help-block"><?= $errors->has('new_password') ? $errors->first('new_password') : '' ?></span>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 <?= $errors->has('confirm_password') ? 'has-error' : '' ?>">
                        <label class="control-label" for="confirm_password">Confirm Password<span class="required">*</span>
                        </label>
                        {!! Form::password('confirm_password',['class'=>'form-control','placeholder'=>'Confirm Password', 'required'=>true, 'maxlength'=>255]) !!}     
                        <span class="help-block"><?= $errors->has('confirm_password') ? $errors->first('confirm_password') : '' ?></span>
                    </div>
                </div>
                
                <div class="col-md-6">
                <div class="btn-block text-right">
                    <a href="{!! route('admin.myprofile') !!}" class="btn btn-default"> Cancel </a>
                    <?= Form::submit('Update', ['class' => 'btn btn-primary ']) ?>

                </div>
                </div>
                {{ Form::close() }}

            </div>
        </div>
    </div>






    <!-- /.row -->
</section>
<!-- /.content -->
@endsection

