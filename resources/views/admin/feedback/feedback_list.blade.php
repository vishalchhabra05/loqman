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
                </div>

                <!-- /.box-header -->
                <div class="box-body">
                    <table id="dataTable" class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th> SNo </th>
                                <th> Name </th>
                                <th> Email</th>
                                <th> Subject</th>
                                <th> Message</th>
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
        //order: [[0, "desc"]],
        "ajax": {
            "url": '{!! route('admin.feedback.datatable') !!}',
            "dataType": "json",
            "type": "POST",
            "data": {_token: "{{csrf_token()}}"}
        },
        columns: [
            { data: 'id', name: 'id', orderable:true, "visible": true },
            {data: 'name', name: 'name', orderable: true},
            {data: 'email', name: 'email', orderable: true},
            {data: 'subject', name: 'subject', orderable: true},
            {data: 'message', name: 'message', orderable: false}
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