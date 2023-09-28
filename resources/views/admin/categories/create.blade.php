@extends('layouts.admin.admin')
@section('content')

<?php
if(empty($entity->id)) {
    $action_route = ['categories.store'];
    $method = 'POST';
} else {
    $action_route = ['categories.update', $entity->id];
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
                
                <div class="form-group <?= $errors->has('category_name') ? 'has-error' : '' ?>">
                    <div class="col-md-12">
                        <label class="control-label" for="title">Categroy Name<span class="required">*</span>
                        </label>
                        <div class="">
                            {!! Form::text('category_name', null, ['class'=>'form-control','placeholder'=>'Categroy Name' ]) !!}
                            <span class="help-block" style="color:red;">
                                <?= $errors->has('category_name') ? $errors->first('category_name') : '' ?>
                            </span>
                        </div>
                    </div>
                </div>
                
                <div class="form-group <?= $errors->has('category_image') ? 'has-error' : '' ?>">
                    <div class="col-md-12">
                        <label class="control-label" for="image">Categroy Image</label>
                        {!! Form::file('category_image'); !!}
                        <span class="help-block"><?= $errors->has('category_image') ? $errors->first('category_image') : '' ?></span>
                    </div>
                </div>
                
                
                
                <div class="text-right">
                    <a href="{!! route('categories.index') !!}" class="btn btn-default"> Cancel </a>
                    <?= Form::submit('Submit', ['class' => 'btn btn-primary mutipleselect']) ?>

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
<script>
   $(".validate").validate({
        rules: {
            category_name: "required",
        },
        submitHandler: function(form) {
            $('.mutipleselect').prop('disabled', true);
            form.submit();
        }
    });
</script>
@endsection
