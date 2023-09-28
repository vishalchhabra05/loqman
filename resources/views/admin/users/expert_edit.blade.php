@extends('layouts.admin.admin')
@section('content')

<?php
if(!empty($entity->id)) {
    $action_route = ['admin.users.edit', $role, $entity->id];
    $method = 'PATCH';
}
$status_name_arr = Config::get('params.status_name_arr');
$deal_type_arr = Config::get('params.deal_type_arr');
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
                
                <div class="form-group">
                    <div class="col-md-6 <?= $errors->has('name') ? 'has-error' : '' ?>">
                        <label class="control-label" for="name">Name <span class="required">*</span></label>
                        <?=  Form::text('name', null, ['class'=>'form-control', 'placeholder'=>'Name', 'required'=>'required', 'maxlength'=>30] ) ?>
                        <span class="help-block"><?= $errors->has('name') ? $errors->first('name') : '' ?></span>
                    </div>
                    <div class="col-md-6 <?= $errors->has('email') ? 'has-error' : '' ?>">
                        <label class="control-label" for="end_date">Email <span class="required">*</span></label>
                        <?=  Form::email('email', null, ['class'=>'form-control', 'placeholder'=>'Email']) ?>
                        <span class="help-block"><?= $errors->has('email') ? $errors->first('email') : '' ?></span>
                    </div>
                </div>
                
                <div class="form-group">
                    <div class="col-md-6 <?= $errors->has('status') ? 'has-error' : '' ?>">
                        <label class="control-label" for="status">Status <span class="required">*</span></label>
                        <?= Form::select('status', $status_name_arr, null, ['placeholder'=>'Select Status','class'=>'form-control', 'required'=>true])?>
                        <span class="help-block"><?= $errors->has('status') ? $errors->first('status') : '' ?></span>
                    </div>
                    <div class="col-md-6 <?= $errors->has('number') ? 'has-error' : '' ?>">
                        <label class="control-label" for="number">Phone Number<span class="required">*</span></label>
                        <?=  Form::text('number', null, ['class'=>'form-control', 'placeholder'=>'Phone Number', 'required'=>'required', 'maxlength'=>30]) ?>
                        <span class="help-block"><?= $errors->has('number') ? $errors->first('number') : '' ?></span>
                    </div>
                </div>
                
            
                <div class="form-group <?= $errors->has('profile_image') ? 'has-error' : '' ?>">
                    <div class="col-md-12">
                        <label class="control-label" for="profile_image">Profile Picture</label>
                        {!! Form::file('profile_image'); !!}
                        <span class="help-block"><?= $errors->has('profile_image') ? $errors->first('profile_image') : '' ?></span>
                        <?php 
                        if (!empty($entity->profile_image_full)) {
                            ?>
                            <img src="{{ $entity->profile_image_full }}" style="max-width: 200px;" />
                            <?php
                        }
                        ?>
                    </div>
                </div>
                
                <div class="text-right">
                <?php  $role_ids = Config::get('params.role_ids'); ?>
                    <a href="{!! route('admin.users', ['role'=>$role_ids['Users']]) !!}" class="btn btn-default"> Cancel </a>
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

@section('uniquepagescript')
 <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDkMrMgB8f8eDAtQ83p7_HXZ3VJS6Py9kc&libraries=places&callback=initialize" async defer></script>
<script>

$("#autocomplete").change(function(){
    $("#latitude").val('');
    $("#longitude").val('');
});

</script>

 <script>
      // This example displays an address form, using the autocomplete feature
      // of the Google Places API to help users fill in the information.

      // This example requires the Places library. Include the libraries=places
      // parameter when you first load the API. For example:
      // <script src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&libraries=places">

      var placeSearch, autocomplete;
      var componentForm = {
        locality: 'long_name',
        country: 'long_name',
      };

      function initialize() {
        // Create the autocomplete object, restricting the search to geographical
        // location types.
        autocomplete = new google.maps.places.Autocomplete(
            /** @type {!HTMLInputElement} */(document.getElementById('autocomplete')),
            {types: ['geocode']});
        // When the user selects an address from the dropdown, populate the address
        // fields in the form.
        autocomplete.addListener('place_changed', fillInAddress);
      }

      function fillInAddress() {
        // Get the place details from the autocomplete object.
        var place = autocomplete.getPlace();
        var latitude = place.geometry.location.lat();
        var longitude = place.geometry.location.lng();
        document.getElementById('latitude').value = latitude;
        document.getElementById('longitude').value = longitude;

        for (var component in componentForm) {
          document.getElementById(component).value = '';
          document.getElementById(component).disabled = false;
        }

        // Get each component of the address from the place details
        // and fill the corresponding field on the form.
        for (var i = 0; i < place.address_components.length; i++) {
          var addressType = place.address_components[i].types[0];
          if (componentForm[addressType]) {
            var val = place.address_components[i][componentForm[addressType]];
            document.getElementById(addressType).value = val;
          }
        }
      }

    </script>


@endsection