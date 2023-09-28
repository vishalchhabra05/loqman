@extends('layouts.admin.admin')
@section('content')

<?php 
$role_ids = Config::get('params.role_ids');
?>
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
      <?php if($title_page == 'Users'){
        echo 'Users';
      }elseif($title_page == 'Expert'){
       echo "Expert";
      }elseif($title_page == 'Guestuser') {
        echo 'Guestuser';
      }else{
        echo 'Sub-Admin';
      }?>
        
    </h1>
    @include('admin.includes.breadcumb')
</section>

<!-- Main content -->
    <section class="content">
      <div class="row">
        <div class="col-xs-12">
          <div class="box">
          <div class="box-header">
                @if($title_page == 'Users')
                <form class="box bg-white" method="post" action="{{route('admin.UsersExcel')}}">
                  @csrf
                   <button type="submit" class="btn btn-primary getExcel" style="float: right;">
                       <i class="fa fa-plus"></i> Excel
                  </button> 
                </form>
                @elseif($title_page == 'Expert')
                  <form class="box bg-white" method="post" action="{{route('admin.UsersExpertExcel')}}">
                    @csrf
                    <button type="submit" class="btn btn-primary getExcel" style="float: right;">
                        <i class="fa fa-plus"></i> Excel
                    </button> 
                  </form>
                @elseif($title_page == 'Guestuser')
                  <form class="box bg-white" method="post" action="">
                    @csrf
                    <button type="submit" class="btn btn-primary getExcel" style="float: right;">
                        <i class="fa fa-plus"></i> Excel
                    </button> 
                  </form>
                @endif
               
            </div>
            <div class="box-header">
                
                <button type="button" class="btn btn-primary getExcel" style="float: right;" onclick="sendNotification();">
                    <i class="fa fa-plus"></i> Send Notification
                </button> 
               
            </div>

            <div class="box-header">
                
                <button type="button" class="btn btn-primary getExcel" style="float: right;" onclick="ActiveUser();">
                    <i class="fa fa-plus"></i> User Active/Deactivate
                </button> 
               
            </div>

            @if($title_page == 'Users' || $title_page == 'Expert')
            <div class="box-header">
                
                <a href="<?php  echo route('admin.users.create', $role) ?>" class="btn btn-primary getExcel" style="float: right;">
                    <i class="fa fa-plus"></i> Add New
                </a> 
               
            </div>
            @endif
            <!-- /.box-header -->
            <div class="box-body">
              <table id="dataTable" class="table table-bordered table-hover">
                <thead>
                <tr>
                    <th>SNo</th>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone Number</th>
                    <th>Image</th>
                    <th>Status</th>
                    <th>Timestamps</th>
                    <th>Select Notification |  <input class ="checkall" type="checkbox"></th>
                    <th>Select User Active/Deactivate |  <input class ="Selectuser" type="checkbox"></th>
                    <th>Action</th>
                </tr>
                </thead>
                
              </table>
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->

          
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->
    </section>
    <!-- /.content -->
@endsection

@section('uniquepagestyle')
<link rel="stylesheet" href="{{asset('public/assets/admin/dataTables.bootstrap.min.css')}}">
@endsection

@section('uniquepagescript')
<script src="{{asset('public/assets/admin/js/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('public/assets/admin/js/dataTables.bootstrap4.min.js')}}"></script>

<script>

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

function sendNotification(){
  var user_id = [];
  $('input[name^="notification"]:checked').each(function() {
     user_id.push(this.value);
  });
  if(user_id ==""){
    swal({
        title: "Please select notification",
        type: "warning",
        showCancelButton: "No",
        confirmButtonClass: "btn-danger",
        confirmButtonText: "Ok",
    })
  }
    $.ajax({
      url: '{{route('admin.push_notification')}}',
              type: 'POST',
              data:{user_id:user_id},
              headers: {
              'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
              },
              success: function (response) {
                swal({
                      title: "Successfully Notification send ",
                      type: "success",
                      showCancelButton: "No",
                      confirmButtonClass: "btn-danger",
                      confirmButtonText: "Ok",
                  })
              }
    });
}

//User Active work
function ActiveUser(){
  var user_id = [];
  $('input[name^="Users"]:checked').each(function() {
     user_id.push(this.value);
  });
  console.log(user_id);
  if(user_id == ""){
    swal({
        title: "Please select Active/Deactivate",
        type: "warning",
        showCancelButton: "No",
        confirmButtonClass: "btn-danger",
        confirmButtonText: "Ok",
    })
  }

  $.ajax({
      url: '{{route('admin.useractives')}}',
              type: 'POST',
              data:{user_id:user_id},
              headers: {
              'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
              },
              success: function (response) {
                swal({
                      title: "Select user are active and deactivate",
                      type: "success",
                      showCancelButton: "No",
                      confirmButtonClass: "btn-danger",
                      confirmButtonText: "Ok",
                  },
                  function(){ 
                      location.reload();
                  }
                  )
              }
    });
}
// select all usercheck box//

var clicked = false;
$(".checkall").on("click", function() {
  $(".checkhour").prop("checked", !clicked);
  clicked = !clicked;
  this.innerHTML = clicked ? 'Deselect' : 'Select';
});

//Select Active and deactive user
var clicked = false;
$(".Selectuser").on("click", function() {
  $(".SelectOver").prop("checked", !clicked);
  clicked = !clicked;
  this.innerHTML = clicked ? 'Deselect' : 'Select';
});

$(function() {
var table = $('#dataTable').DataTable({
processing: true,
        serverSide: true,
        pageLength: admin_page_length,
        order: [[0, "desc" ]],
        "ajax":{
        "url": '{!! route('admin.usersDataTable', ['role'=>$role]) !!}',
                "dataType": "json",
                "type": "POST",
                "data":{ _token: "{{csrf_token()}}"}
        },
        columns: [
        { data: 'id', name: 'sno', orderable:true, "visible": true },
        { data: 'user_id', name: 'user_id', orderable:true},
        { data: 'name', name: 'name', orderable:true },
        { data: 'email', name: 'email', orderable:true  },
        { data: 'number', name: 'number', orderable:true  },
        { data: 'profile_image', name: 'profile_image', orderable:false},
        { data: 'status', name: 'status', orderable:false},
        { data: 'created_at', name: 'created_at', orderable:false},
        { data: 'notification', name: 'notification', orderable:false},
        { data: 'Users', name: 'Users', orderable:false},
        { data: 'action', name: 'action', orderable:false},
        ],
        "columnDefs": [
        { "searchable": false, "targets": 0 }
        ]
        , language: {
        searchPlaceholder: "Search"
        },
});
});
</script>

@endsection