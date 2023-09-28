@extends('layouts.admin.admin')
@section('content')

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

                {!! Form::model($entity, ['route' => ['admin.pages.edit', $entity->slug], 'method' => 'POST', 'class'=>'form-horizontal validate', 'enctype'=>'multipart/form-data']) !!}
                {{ csrf_field() }}
                
                
                <div class="form-group">
                    <div class="col-md-12 <?= $errors->has('title') ? 'has-error' : '' ?>">
                        <label class="control-label" for="title">Title<span class="required">*</span>
                        </label>
                        <div class="">
                            {!! Form::text('title', null, ['class'=>'form-control','placeholder'=>'Title', 'required'=>true, 'max'=>255]) !!}  
                            <span class="help-block"><?= $errors->has('title') ? $errors->first('title') : '' ?></span>                    
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <div class="col-md-12 <?= $errors->has('description') ? 'has-error' : '' ?>">
                        <label class="control-label" for="description">Description<span class="required">*</span>
                        </label>
                        <div class="">
                            {!! Form::textArea('description', null, ['class'=>'form-control','id'=>'description-editor','placeholder'=>'Description', 'required'=>true]) !!}   
                            <span class="help-block"><?= $errors->has('description') ? $errors->first('description') : '' ?></span>                     
                        </div>
                    </div>
                </div>
                
                <div class="text-right">
                    <a href="{!! route('admin.pages.index') !!}" class="btn btn-default"> Cancel </a>
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
@section('uniquepagescript')
<script src="{{ asset('public/assets/laravel-ckeditor/ckeditor.js') }}"></script>
<script>
    $(".icon_info").tooltip();
    CKEDITOR.replace( 'description-editor' );
</script>
@endsection