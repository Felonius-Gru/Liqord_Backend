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
							<header>Users Info</header>
                                                       
                                                        <div class="tools">
                                                                <a class="btn btn-flat hidden-xs" href="{{asset('admin/tabusers/register')}}"><span class="glyphicon glyphicon-plus"></span> &nbsp;Add New</a>
                                                        </div> 
                                                      
                                                       
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
                                                                           
                                                                            <th>Username</th>
                                                                            <th>Store</th>
                                                                            <th>Location</th>
                                                                            <th>Edit</th>
                                                                            <th>Action</th>
                                                                            
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
                ajax: "{{asset('admin/tabusers/result')}}",
                columns: [
                    {data: 'username', name: 'username'},
                    {data: 'store_id', name: 'store_id'},
                    {data: 'location_id', name: 'location_id'},
                    {data: 'edit', name: 'edit',orderable: false, searchable: false},
                    {data: 'action', name: 'action',orderable: false, searchable: false},
                    
                ]
            });
        });

    </script>
@stop
