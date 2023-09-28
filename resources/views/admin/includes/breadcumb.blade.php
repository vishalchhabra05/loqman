

<!-- Breadcrumbs-->
<ol class="breadcrumb">
    
    
     <li class="active"><i class="fa fa-dashboard"></i><a href="{!! route('admin.dashboard')!!}">Dashboard</a></li>
    <?php
    if (!empty($breadcumb)) {
        foreach ($breadcumb as $key => $link) {
            if ($link) {
                ?>
                <li class="breadcrumb-item">
                    <a href="{!! $link!!}">{!! $key !!}</a>
                </li>
                <?php
            } else {
                ?>
                <li class="breadcrumb-item active">{!! $key !!}</li>
                    <?php
                }
            }
        }
        ?>

</ol>