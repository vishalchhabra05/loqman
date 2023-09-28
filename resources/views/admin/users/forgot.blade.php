@extends('layouts.admin.login')
@section('content')

<div class="login-box">
  <div class="login-logo">

  </div>
  <!-- /.login-logo -->
  <div class="login-box-body">
    <p class="login-box-msg">Forgot your password?</p>
    
    {!! Form::open() !!}
      <div class="form-group <?= $errors->has('email') ? 'has-error' : '' ?>">
           <?= Form::text('email', null, ['class' => 'form-control', 'maxlength' => '255', 'autofocus' => 'autofocus', 'placeholder' => 'Email']) ?>
          <span class="help-block"><?= $errors->has('email') ? $errors->first('email') : '' ?></span>
        <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
      </div>
      
      <div class="row">
        
        <!-- /.col -->
        <div class="col-xs-4">
            <?= Form::submit('Submit', ['class' => 'btn btn-primary btn-block btn-flat']) ?>
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