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
							<header>Locations</header>
                                                        <div class="tools">
                                                                <a class="btn ink-reaction btn-primary" href='#myModal' data-toggle='modal'><span class="glyphicon glyphicon-plus"></span> &nbsp;New Location</a>
                                                        </div>
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
                                                                            <th>VIEW STORES</th>
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
                        
               <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="actionLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title">Add New Location</h4>
                    </div>
                    <form action="{{asset('admin/location/add')}}" method="post" class="form-horizontal" role="form">
                        {{ csrf_field() }}
                        <div class="modal-body">
                            <div class="form-group">

                                <div class="col-sm-12">
                                    <input type="text" class="form-control" name="location" id="location" placeholder="Location" required  value=""><div class="form-control-line"></div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn CancelBtn" data-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn LoginBtn">Submit</button>
                        </div>
                    </form>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->  
        <div class="modal fade" id="myModal1" tabindex="-1" role="dialog" aria-labelledby="actionLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title">Edit Location</h4>
                    </div>
                    <form action="{{asset('admin/location/edit')}}" method="post" class="form-horizontal" role="form">
                        {{ csrf_field() }}
                        <div class="modal-body">
                            <div class="form-group">

                                <div class="col-sm-12">
                                     <input type="hidden" name="id" class="form-control" id="id" required value="">
                                    <input type="text" class="form-control" name="location" id="location1" placeholder="Location" required  value=""><div class="form-control-line"></div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn CancelBtn" data-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn LoginBtn">Submit</button>
                        </div>
                    </form>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->         
                        
                        
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
            $('#items_table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{asset('admin/locations/result')}}",
                columns: [
                    {data: 'id', name: 'id'},
                    {data: 'name', name: 'name'},
                    {data:'view_stores',name:'view_stores'},
//                    {data: 'created_at', name: 'created_at'},
                    {data: 'edit', name: 'edit',orderable: false, searchable: false},
                    {data: 'action', name: 'action',orderable: false, searchable: false},
                    
                ]
            });
        });
                                          function viewdetail(id,name)
                                                {
                                                        var location = name.split('&').join(' ');  
                                                        document.getElementById('id').value = id;
                                                        document.getElementById('location1').value = location;
                                                }
    </script>
@stop
