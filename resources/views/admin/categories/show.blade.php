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


                <div class="box-body box-profile">
                    <table class="table table-bordered table-hover">
                        <tbody>

                            <tr>
                                <th>Category Name</th>
                                <td>{{$entity->category_name}}</td>
                            </tr>
                            
                            <tr>
                                <th>Assign Expert:</th>
                                @if(!empty($assignuser))
                                   @foreach($assignuser as $usersName)
                                      <td>{{$usersName->UsercategroyAssing->name ?? ''}}</td>
                                   @endforeach
                                @endif
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
                }
        });
    }
    );
}
    
</script>




@endsection