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
							<header>Stores in {!!$location->name!!}</header>
<!--                                                        <div class="tools">
                                                                <a class="btn ink-reaction btn-primary" href='#myModal' data-toggle='modal'><span class="glyphicon glyphicon-plus"></span> &nbsp;New Location</a>
                                                        </div>-->
						</div>
                                                <!-- END CONTACT DETAILS HEADER -->

				<!-- BEGIN CONTACT DETAILS -->
				<div class="card-tiles">
					<div class="hbox-md col-md-12">
						<div class="hbox-column col-md-9">
							<div class="row">

								<table id="items_table" class="table table-bordered mbn">
                                                                    <thead>
                                                                        <tr>
                                                                            <th>ID</th>
                                                                            <th>NAME</th>
<!--                                                                            
                                                                            <th>Edit</th>
                                                                            <th>Action</th>-->
                                                                            
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                    @if(count($locationstore))
                                                                    
                                                                        @foreach($locationstore as $locationstores)
                                                                    <tr><td>{!!$locationstores->store_id!!}</td>
                                                                    <td>{!!$locationstores->store_name!!}</td>
<!--                                                                    <td></td>
                                                                    <td></td>-->
                                                                    </tr>
                                                                    
                                                                    @endforeach
                                                                    @endif
                                                                    </tbody>
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
<!--<script>
        jQuery(document).ready(function () {
            $('#items_table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{asset('admin/locations/store/result')}}",
                columns: [
                    {data: 'store_id', name: 'store_id'},
                    {data: 'store_name', name: 'store_name'},
//                    {data: 'created_at', name: 'created_at'},
                    {data: 'edit', name: 'edit',orderable: false, searchable: false},
                    {data: 'action', name: 'action',orderable: false, searchable: false},
                    
                ]
            });
        });
        </script>-->
        @stop
                        
