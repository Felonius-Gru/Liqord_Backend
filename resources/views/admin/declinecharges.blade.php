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
							<header>{{$page_title}}</header>    
						</div>
                                                <!-- END CONTACT DETAILS HEADER -->

				<!-- BEGIN CONTACT DETAILS -->
				<div class="card-tiles">
					<div class="hbox-md col-md-12">
						<div class="hbox-column col-md-9">
							<div class="row">

								<table id="users_table" class="table table-bordered mbn">
                                                                    <thead>
                                                                        <tr>
                                                                            <!--<th>ID</th>-->
                                                                            <th>STORE</th>
                                                                            <th>LOCATION</th>
                                                                            <th>MONTHLY COST</th>
                                                                            <th>BILL DATE</th>
                                                                            <th>ERROR MESSAGE</th>
                                                                            
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
            $('#users_table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{asset('admin/decline_charges/result')}}",
                columns: [
//                    {data: 'id', name: 'id'},
                    {data: 'store', name: 'store'},
                    {data: 'location', name: 'location'},
                    {data: 'orginal_price', name: 'orginal_price'},
                    {data: 'bill_date', name: 'bill_date'},
                    {data: 'error', name: 'error'},
                ],
           aaSorting: [[3, 'desc']],
            });
        });
          
 </script>
@stop
