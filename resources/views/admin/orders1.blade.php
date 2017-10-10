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

								<table id="order_table" class="table table-bordered mbn">
                                                                    <thead>
                                                                        <tr>
                                                                            <th>#</th>
                                                                            <th>Store</th>
                                                                            <th>Location</th>
                                                                            <th>User</th>
                                                                            <th>Placed On</th>
                                                                            <th>View</th> 
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
<style>
    
</style>
@stop
@section("script")
<script>
        jQuery(document).ready(function () { 
            $('#order_table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{asset('admin/orders/result')}}",
                columns: [
                {data: 'id', name: 'order.id'},
                {data: 'store_id', name: 'order.store_id'},
                {data: 'location_id', name: 'order.location_id'},
                {data: 'user_id', name: 'order.user_id'},
                {data: 'created_at', name: 'order.created_at'},
                {data: 'action', name: 'action', orderable: false, searchable: false}, 
                <?php // if(Auth::user()->role=="Admin"){?>
                
//                {data: 'edit', name: 'edit'} <?php // } ?>
                
                
                ]
            });
        });
    </script>
@stop
