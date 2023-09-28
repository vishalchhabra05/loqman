@extends('layouts.admin.login')
@section('content')
<?php $site_title = Session::get('settings.site_title'); ?>
<div class="login-box">
  <div class="login-logo">
    <a href="javascript:void(0);"><b><?= $site_title ?></b></a>
  </div>
  <!-- /.login-logo -->
  <div class="login-box-body">
    <p class="login-box-msg">Reset Password</p>
    
    {!! Form::open() !!}
      <div class="form-group <?= $errors->has('password') ? 'has-error' : '' ?>">
            <?= Form::password('password', ['class' => 'form-control', 'placeholder' => 'Password', 'id' => 'inputPassword', 'autofocus' => 'autofocus']); ?>
        <span class="help-block"><?= $errors->has('password') ? $errors->first('password') : '' ?></span>
      </div>
      <div class="form-group <?= $errors->has('confirm_password') ? 'has-error' : '' ?>">
        <?= Form::password('confirm_password', ['class' => 'form-control', 'placeholder' => 'Confirm Password', 'id' => 'inputConfirmPassword']); ?>
        <span class="help-block"><?= $errors->has('confirm_password') ? $errors->first('confirm_password') : '' ?></span>
      </div>
      <div class="row">
        
        <!-- /.col -->
        <div class="col-xs-4">
            <?= Form::submit('Reset', ['class' => 'btn btn-primary btn-block btn-flat']) ?>
        </div>
        <!-- /.col -->
      </div>
    {{ Form::close() }}

    
    <!-- /.social-auth-links -->

    <a href="{!! route('admin.login')!!}">Login</a><br>
  </div>
  <!-- /.login-box-body -->
</div>
@endsection
