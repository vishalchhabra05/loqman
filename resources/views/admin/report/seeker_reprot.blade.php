@extends('layouts.admin.admin')
@section('content')


<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        <?= $title_page ?>
    </h1>
    @include('admin.includes.breadcumb')
</section>
<div class="box-header">
        <form class="box bg-white" method="post" action="{{route('admin.seeker_report_excel')}}">
            @csrf
            <button type="submit" class="btn btn-primary getExcel" style="float: right;">
                <i class="fa fa-plus"></i> Excel
            </button> 
        </form>
</div>
<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-header">
                    <h3 class="box-title">List</h3>
                </div>

                <!-- /.box-header -->
                <div class="box-body">
                   
                    <table id="dataTable" class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th> SNo </th>
                                <th>User Name</th>
                                <th>Expert Name</th>
                                <th>Status</th>
                                <th>Call Date</th>
                                <th>Time</th>
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

$(function () {
    var table = $('#dataTable').DataTable({
        processing: true,
        serverSide: true,
        pageLength: admin_page_length,
        order: [[0, "desc"]],
        "ajax": {
            "url": '{!! route('admin.report.datatable') !!}',
            "dataType": "json",
            "type": "POST",
            "data": {_token: "{{csrf_token()}}"}
        },
        columns: [
            {data: 'id', name: 'id', orderable:true, "visible": true },
            {data: 'recive_id', name: 'recive_id', orderable: true},
            {data: 'send_id', name: 'send_id', orderable: true},
            {data: 'status', name: 'status', orderable: false},
            {data: 'start_date', name: 'start_date', orderable: false},
            {data: 'start_time', name: 'start_time', orderable: false},
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