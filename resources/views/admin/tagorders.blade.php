@extends('layout.adminLayout')
@section("content")
<!-- BEGIN CONTENT-->

<div id="content">
    <section>
        <div class="section-header">
            <ol class="breadcrumb">
                {!!$breadcrumb!!}  

            </ol>

        </div>
        <div class="section-body contain-lg">
            <div class="row">

                <!-- BEGIN ADD CONTACTS FORM -->
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-head style-primary">
                            {!!$header!!} 

                        </div>
                        <!-- END CONTACT DETAILS HEADER -->

                        <!-- BEGIN CONTACT DETAILS -->
                        <div class="card-tiles">
                            <div class="hbox-md col-md-12">
                                <div class="hbox-column col-md-9">
                                    <div class="row"> 
                                        <?php if (Auth::user()->role == "Admin") { ?>
                                            <div class="col-sm-3">
                                                <div class="form-group">
                                                    <label for="users">Store</label>
                                                    <select name="store_id" id="store_id" required class="form-control" onchange="searchlist();">

                                                        <option value="">SELECT -A-Store</option>

                                                        @foreach ($stores as $option)                                    
                                                        <option value="{!!$option->id!!}" <?php if ($option->id == Auth::user()->store_id) { ?>selected <?php } ?>>{!!$option->name!!}</option>                          
                                                        @endforeach
                                                    </select>


                                                </div>
                                            </div>
                                        <?php } else {
                                            ?> <input  type="hidden" id="store_id" value="<?php echo Auth::user()->store_id; ?>">
                                        <?php } ?>


                                        <div class="col-sm-3">
                                            <div class="form-group">
                                                <label for="users">Location</label>
                                                <select name="location_id" id="location_id" required class="form-control" onchange="searchlist();">

                                                    <option value="">SELECT -A-Location</option>

                                                    @foreach ($location as $options)                                    
                                                    <!--<option value="{!!$options->id!!}" <?php // if ($options->id == Auth::user()->location_id) {    ?>selected <?php // }    ?>>{!!$options->name!!}</option>-->                          
                                                    <option value="{!!$options->id!!}">{!!$options->name!!}</option> 
                                                    @endforeach
                                                </select>


                                            </div>
                                        </div>

                                        <div class="col-sm-3">
                                            <div class="form-group control-width-normal">
                                                <label for="datepicker">Start Date</label>

                                                <input type="text" class="form-control" name="start_date" id="start_date" onchange="searchlist();">


                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="form-group control-width-normal">
                                                <label for="datepicker">End Date</label>

                                                <input type="text" class="form-control" name="end_date" id="end_date" onchange="searchlist();">


                                            </div>
                                        </div>


                                    </div>
                                    <div class="row">

                                        <table id="order_table" class="table table-bordered mbn">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Store</th>
                                                    <th>Location</th>
<!--                                                    <th>Label Quantity</th>-->
<!--                                                    <th>Total Price</th>-->
                                                    <!-- <th>User</th>-->
                                                    <th>Placed On</th>
                                                    
                                                    <th>View</th> 
                                                    <th>Print</th>
                                                    <th>Download CSV</th>
                                                    <!--                                                                            @if(Auth::user()->role=="Admin")
                                                                                                                                <th>Lockout</th>
                                                                                                                                @endif-->

                                                </tr>
                                            </thead>
                                        </table>

                                    </div><!--end .row -->
                                </div><!--end .hbox-column -->
                                <!-- END CONTACTS COMMON DETAILS -->

                            </div><!--end .hbox-md -->
                        </div><!--end .card-tiles -->
                    </div><!--end .card -->
                </div><!--end .col -->
                <!-- END ADD CONTACTS FORM -->

            </div><!--end .row -->
        </div><!--end .section-body -->
    </section>

</div><!--end #content-->		
<!-- END CONTENT -->
@stop

@section("style")
<link type="text/css" rel="stylesheet" href="{{asset('themes/assets/bootstrap-datepicker/datepicker3f394.css?1422823364')}}" />
<style>

</style>
@stop
@section("script")
<script src="{{asset('themes/assets/bootstrap-datepicker/bootstrap-datepicker.js')}}"></script>
<script>
    jQuery(document).ready(function () {
        $('#order_table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{asset('admin/shelf/tagorders/result')}}",
            columns: [
                {data: 'id', name: 'order.id'},
                {data: 'store_id', name: 'order.store_id'},
                {data: 'location_id', name: 'order.location_id'},
//                {data: 'label_quantity', name: 'order.label_quantity'},
//                {data: 'total_price', name: 'order.total_price'},
//                {data: 'user_id', name: 'order.user_id'},
                {data: 'created_at', name: 'order.created_at'},
                
                {data: 'action', name: 'action', orderable: false, searchable: false},
                {data: 'print', name: 'print'},
                {data: 'csv', name: 'csv'},
            ]
        });
    });
    function searchlist() {
        var storeid = $("#store_id").val();
        var locationid = $("#location_id").val();
        var startdate = $("#start_date").val();
        var enddate = $("#end_date").val();
        //    $('#order_table').destroy();
        $('#order_table').DataTable({
            processing: true,
            serverSide: true,
            destroy: true,
            ajax: "{{asset('admin/shelf/tagorders/result')}}/1/storeid=" + storeid + "/locationid=" + locationid + "/startdate=" + startdate + "/enddate=" + enddate,
            columns: [
                {data: 'id', name: 'order.id'},
                {data: 'store_id', name: 'order.store_id'},
                {data: 'location_id', name: 'order.location_id'},
//                {data: 'label_quantity', name: 'order.label_quantity'},
//                {data: 'total_price', name: 'order.total_price'},
                //  {data: 'user_id', name: 'order.user_id'},
                {data: 'created_at', name: 'order.created_at'},
                {data: 'action', name: 'action', orderable: false, searchable: false},
                {data: 'print', name: 'print'},
                {data: 'csv', name: 'csv'},
            ]

        });
    }
    $('#start_date').datepicker({
        format: "yyyy-mm-dd"

    });
    $('#end_date').datepicker({
        format: "yyyy-mm-dd"

    });
</script>
@stop
