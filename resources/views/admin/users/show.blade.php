@extends('layouts.admin.admin')
@section('content')
<?php
$file_save_path = Config::get('params.file_save_path');
$admin_default_image = Config::get('params.admin_default_image');
$file_save_path = Config::get('params.file_save_path');
$profile_pic = !empty('public/'.$entity->profile_image) ?: asset('public/'.$admin_default_image);
$role_names = Config::get('params.role_names')
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


                <div class="box-body box-profile">
                <?php if(!empty($entity->profile_image)){?>
                    <img class="profile-user-img img-responsive img-circle" src="{{ asset('public/'.$entity->profile_image) }}" alt="User profile picture">
                    <?php } else { ?>
                        <img class="profile-user-img img-responsive img-circle" src="{{ asset('public/images/profile-image.svg') }}" alt="User profile picture">
                    <?php } ?>

                    <h3 class="profile-username text-center"><?= $entity->full_name ?></h3>

                    <p class="text-muted text-center"><?= $entity->designation ?></p>

                    <table class="table table-bordered table-hover">
                        <tbody>

                            <tr>
                                <th>Name</th>
                                <td>{{$entity->name}}</td>
                                <th>Email:</th>
                                <td>{{ $entity->email }}</td>
                                <th>Role:</th>
                                <td>{{ isset($role_names[$entity->role]) ? $role_names[$entity->role] : '' }}</td>
                            </tr>
                            
                            <tr>
                                <th>Number :</th>
                                <td> {{$entity->number}} </td>
                            </tr>
                            
                            <tr>
                                <th>Number of calls made :</th>
                                <td> {{$totalcall}} </td>
                            </tr>
                            <tr>
                                <th>Average rating:</th>
                                <td> {{$userrating}} </td>
                            </tr>
                            @if(!empty($entity))
                                @if($entity->role == 3)
                                    <tr>
                                        <th>Category Name</th>
                                        <td>
                                            <?php $UsercatgroyGet=CategroyCheck($entity->id)?>

                                            @if(!empty($UsercatgroyGet))
                                                <?php 
                                                    $catgoroy_Name=[];
                                                    foreach($UsercatgroyGet as $value){
                                                        $catgoroy_Name[]=$value->category_getf->category_name;
                                                    }
                                                ?>
                                                {{implode(', ', $catgoroy_Name)}}
                                            @endif
                                        </td>
                                    </tr>

                                    <tr>
                                        <th>Number of call minutes</th>
                                        <td> {{$AllCallmin}} </td>
                                   </tr>
                                @endif
                            @endif

                            @if(!empty($Totalbages))
                                <tr>
                                    <th>Total Bages</th>
                                    <td>{{$Totalbages}}</td>
                                </tr>
                            @endif
                            
                            <tr>
                                <th>Status</th>
                                <td> <?= getStatus($entity->status,$entity->id) ?> </td>
                                
                            </tr>
                        </tbody>
                    </table>
                </div>




            </div>
        </div>
    </div>






    <!-- /.row -->
</section>
<!-- /.content -->
@endsection
@section('uniquepagescript')
<script>
    $(".icon_info").tooltip();
    
    function changeStatus(id) {
    swal({
    title: "Are you sure?",
            type: "warning",
            showCancelButton: "No",
            confirmButtonClass: "btn-danger",
            confirmButtonText: "Yes",
            cancelButtonText: "No",
    },
    function(){
        jQuery.ajax({
        url: '{{route('admin.users.statusUpdate')}}',
                type: 'POST',
                data:{id:id},
                headers: {
                'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                $("#status_" + id).html(response);
                location.reload();
                }
        });
    }
    );
}
    
</script>




@endsection