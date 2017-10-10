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
                                                        @if(Auth::user()->role == "Admin")
                                                        <div class="tools">
                                                                <a class="btn btn-flat hidden-xs" href="{{asset('admin/users/register')}}"><span class="glyphicon glyphicon-plus"></span> &nbsp;Add Customer</a>
                                                        </div> 
                                                        @endif
                                                        @if(Auth::user()->role == "StoreAdmin")
                                                        <div class="tools">
                                                                <a class="btn btn-flat hidden-xs" href="{{asset('admin/users/register')}}"><span class="glyphicon glyphicon-plus"></span> &nbsp;Add User</a>
                                                        </div> 
                                                        @endif
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
                                                                            <th>ID</th>
                                                                            <th>Username</th>
                                                                            <th>Email</th>
                                                                            <th>Role</th>
<!--                                                                            <th>Refill Card</th>-->
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
                ajax: "{{asset('admin/users/result')}}",
                columns: [
                    {data: 'id', name: 'id'},
                    {data: 'username', name: 'username'},
                    {data: 'email', name: 'email'},
                    {data: 'role', name: 'role'},
//                    {data: 'refill', name: 'refill',orderable: false, searchable: false},
                    {data: 'edit', name: 'edit',orderable: false, searchable: false},
                    {data: 'action', name: 'action',orderable: false, searchable: false},
                    
                ]
            });
        });

    </script>
@stop
