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
                    <!-- <a href="<?php //route('bages.create') ?>" class="btn btn-primary getExcel" style="float: right;">
                        <i class="fa fa-plus"></i> Add New
                    </a> -->
                </div>

                <!-- /.box-header -->
                <div class="box-body">
                    <table id="dataTable" class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th> SNo </th>
                                <th> Title </th>
                                <th> Price </th>
                                <th>Status</th>
                                <th>Created At</th>
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
        url: '{{route('admin.bages.updatestatus')}}',
                type: 'POST',
                data:{id:id},
                headers: {
                'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                $("#status_" + id).html(response);
                }
        });
    }
    );
}

$(function () {
    var table = $('#dataTable').DataTable({
        processing: true,
        serverSide: true,
        pageLength: admin_page_length,
        //order: [[0, "desc"]],
        "ajax": {
            "url": '{!! route('admin.bages.datatables') !!}',
            "dataType": "json",
            "type": "POST",
            "data": {_token: "{{csrf_token()}}"}
        },
        columns: [
            { data: 'id', name: 'id', orderable:true, "visible": true },
            {data: 'title', name: 'title', orderable: true},
            {data: 'price', name: 'price', orderable: true},
            { data: 'status', name: 'status', orderable:false},
            {data: 'created_at', name: 'created_at', orderable: true},
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