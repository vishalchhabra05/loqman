@extends('layouts.admin.login')
@section('content')
<?php $site_title = Session::get('settings.site_title'); ?>
<div class="login-box">
    <div class="login-logo">
        <a href="javascript:void(0);"><b><?= $site_title ?></b></a>
    </div>
    <!-- /.login-logo -->
    <div class="login-box-body">
        <center><img src="{{asset('public/images/logo.png')}}" style="width: 100px"></center>
        <p class="login-box-msg">Sign in to start your session</p>
        {!! Form::open() !!}
        <div class="form-group <?= $errors->has('email') ? 'has-error' : '' ?>">
            <?= Form::text('email', null, ['class' => 'form-control', 'maxlength' => '255', 'autofocus' => 'autofocus', 'placeholder' => 'Email', 'id' => 'inputEmail']) ?>
            <span class="help-block"><?= $errors->has('email') ? $errors->first('email') : '' ?></span>
        </div>
        <div class="form-group <?= $errors->has('password') ? 'has-error' : '' ?>">
            <?= Form::password('password', ['class' => 'form-control', 'placeholder' => 'Password', 'id' => 'inputPassword']); ?>
            <span class="help-block"><?= $errors->has('password') ? $errors->first('password') : '' ?></span>
        </div>

        <div class="form-group row">
            <div class="col-md-6 offset-md-4">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                        <label class="form-check-label" for="remember">
                        {{ __('Remember Me') }}
                    </label>
                </div>
            </div>
        </div>
        <div class="row">

            <!-- /.col -->
            <div class="col-xs-4">
                <?= Form::submit('Log In', ['class' => 'btn btn-primary btn-block btn-flat']) ?>
            </div>
            <!-- /.col -->
        </div>
        {{ Form::close() }}


        <!-- /.social-auth-links -->

        <a href="<?= route('admin.forgot') ?>">I forgot my password</a><br>
        <input type="hidden" value="27-April-2023 4:30">
    </div>
    <!-- /.login-box-body -->
</div>
@endsection