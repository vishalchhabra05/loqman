@extends('layouts.admin.admin')
@section('content')

<?php
if(empty($entity->id)) {
    $action_route = ['bages.store'];
    $method = 'POST';
} else {
    $action_route = ['bages.update', $entity->id];
    $method = 'PATCH';
}
?>
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

                {!! Form::model($entity, ['route' => $action_route, 'method' => $method, 'class'=>'form-horizontal validate', 'enctype'=>'multipart/form-data']) !!}
                {{ csrf_field() }}
                
                <div class="form-group <?= $errors->has('title') ? 'has-error' : '' ?>">
                    <div class="col-md-12">
                        <label class="control-label" for="title">Title<span class="required">*</span>
                        </label>
                        <div class="">
                            {!! Form::text('title', null, ['required'=>'required','class'=>'form-control','placeholder'=>'Title', 'max'=>255]) !!}
                            <span class="help-block">
                                <?= $errors->has('title') ? $errors->first('title') : '' ?>
                            </span>
                        </div>
                    </div>
                </div>
                
                <div class="form-group <?= $errors->has('price') ? 'has-error' : '' ?>">
                    <div class="col-md-12">
                        <label class="control-label" for="price">Price<span class="required">*</span>
                        </label>
                        <div class="">
                            {!! Form::text('price', null, ['required'=>'required','class'=>'form-control','placeholder'=>'Price', 'max'=>255]) !!}
                            <span class="help-block">
                                <?= $errors->has('price') ? $errors->first('price') : '' ?>
                            </span>
                        </div>
                    </div>
                </div>
                
                
                
                <div class="text-right">
                    <a href="{!! route('bages.index') !!}" class="btn btn-default"> Cancel </a>
                    <?= Form::submit('Submit', ['class' => 'btn btn-primary ']) ?>

                </div>
                {{ Form::close() }}

            </div>
        </div>
    </div>






    <!-- /.row -->
</section>
<!-- /.content -->
@endsection
