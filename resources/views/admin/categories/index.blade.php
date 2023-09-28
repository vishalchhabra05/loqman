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
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-header">
                    <h3 class="box-title">List</h3>
                    <a href="<?= route('categories.create') ?>" class="btn btn-primary getExcel" style="float: right;">
                        <i class="fa fa-plus"></i> Add New
                    </a>
                </div>
                <div class="box-header">
                
                    <button type="button" class="btn btn-primary getExcel" style="float: right;" onclick="sendNotification();">
                        <i class="fa fa-plus"></i> Send Notification
                    </button> 
               
                </div>

                <div class="box-header">
                
                    <button type="button" class="btn btn-primary getExcel" style="float: right;" onclick="SelectCategory();">
                        <i class="fa fa-plus"></i> Delete Category
                    </button> 
               
                </div>

                <!-- /.box-header -->
                <div class="box-body">
                    <table id="dataTable" class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th> SNo </th>
                                <th> Title </th>
                                <th> Category Image</th>
                                <th>Select Notification |  <input class ="checkall" type="checkbox"></th>
                                <th>Select Delete Category |  <input class ="categroy" type="checkbox"></th>
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


// select all usercheck box//

var clicked = false;
$(".checkall").on("click", function() {
  $(".checkhour").prop("checked", !clicked);
  clicked = !clicked;
  this.innerHTML = clicked ? 'Deselect' : 'Select';
});

var clicked = false;
$(".categroy").on("click", function() {
  $(".CategroyValue").prop("checked", !clicked);
  clicked = !clicked;
  this.innerHTML = clicked ? 'Deselect' : 'Select';
});

function sendNotification(){
  var category_id = [];
  $('input[name^="notification"]:checked').each(function() {
    category_id.push(this.value);
  });
  $.ajax({
      url: '{{route('admin.category_push_notification')}}',
              type: 'POST',
              data:{category_id:category_id},
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

function SelectCategory(){
    var category_id = [];
  $('input[name^="category"]:checked').each(function() {
    category_id.push(this.value);
  });
  $.ajax({
      url: '{{route('admin.categroydelete')}}',
              type: 'POST',
              data:{category_id:category_id},
              headers: {
              'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
              },
              success: function (response) {
                swal({
                      title: "Successfully Delete",
                      type: "success",
                      showCancelButton: "No",
                      confirmButtonClass: "btn-danger",
                      confirmButtonText: "Ok",
                  })
              }
      });
}

$(function () {
    var table = $('#dataTable').DataTable({
        processing: true,
        serverSide: true,
        pageLength: admin_page_length,
        //order: [[0, "desc"]],
        "ajax": {
            "url": '{!! route('admin.categories.datatables') !!}',
            "dataType": "json",
            "type": "POST",
            "data": {_token: "{{csrf_token()}}"}
        },
        columns: [
            { data: 'id', name: 'id', orderable:true, "visible": true },
            {data: 'category_name', name: 'category_name', orderable: true},
            {data: 'category_image', name: 'category_image', orderable: true},
            { data: 'notification', name: 'notification', orderable:false},
            { data: 'category', name: 'category', orderable:false},
            {data: 'action', name: 'action', orderable: false}
        ],
        "columnDefs": [
            {"searchable": false, "targets": 0},
            {className: 'text-center', targets: [1]},
        ]
        , language: {
            searchPlaceholder: "Search"
        }
    });
});
</script>

@endsection