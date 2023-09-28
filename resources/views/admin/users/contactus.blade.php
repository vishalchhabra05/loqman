@extends('layouts.admin.admin')
@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>   Contact Us Enquiry  </h1>
    @include('admin.includes.breadcumb')
</section>

<!-- Main content -->
    <section class="content">
      <div class="row">
        <div class="col-xs-12">
          <div class="box">
            <!-- /.box-header -->
            <div class="box-body">
              <table id="dataTable" class="table table-bordered table-hover">
                <thead>
                <tr>
                    <th>S.No.</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Contact Number</th>
                    <th>Zip Code</th>
                    <th>Created At</th>
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

$(function() {
var table = $('#dataTable').DataTable({
processing: true,
        serverSide: true,
        pageLength: admin_page_length,
        order: [[0, "desc" ]],
        "ajax":{
        "url": '{!! route('admin.contactusDataTable') !!}',
                "dataType": "json",
                "type": "POST",
                "data":{ _token: "{{csrf_token()}}"}
        },
        columns: [
        { data: 'id', name: 'id', orderable:true, "visible": true },
        { data: 'name', name: 'name', orderable:true },
        { data: 'email', name: 'email', orderable:true  },
        { data: 'mobileNum', name: 'mobileNumr', orderable:false},
        { data: 'zipcode', name: 'zipcode', orderable:false},
        { data: 'created_at', name: 'created_at', orderable:true },
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