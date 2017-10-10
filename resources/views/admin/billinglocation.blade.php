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
							<header>Billing Locations</header>
                                                        <div class="tools">	
                                                         <a class="btn ink-reaction btn-primary" href='{{asset('admin/billing')}}' data-toggle='modal'>Back</a>   
							 </div> 
                                                 <!--   end .tools -->
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
                                                                            
                                                                            <th>LOCATION</th>
                                                                            <th>MONTHLY COST</th>
                                                                            <th>ADD/EDIT</th>
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
                        <h4 class="modal-title">Set Monthly Cost</h4>
                    </div>
                    <form action="{{asset('admin/billing/add')}}" method="post" class="form-horizontal" role="form">
                        {{ csrf_field() }}
                        <div class="modal-body">
                            <div class="form-group">

                                <div class="col-sm-12">
                                    <input type="hidden" name="store_id" class="form-control" id="store_id" required value="">
                                    <input type="hidden" name="location_id" class="form-control" id="location_id" required value="">
                                    <input type="text" class="form-control" name="monthly_cost" id="location" placeholder="Monthly Cost" required  value=""><div class="form-control-line"></div>
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
                        <h4 class="modal-title">Edit Monthly Cost</h4>
                    </div>
                    <form action="{{asset('admin/billing/edit')}}" method="post" class="form-horizontal" role="form">
                        {{ csrf_field() }}
                        <div class="modal-body">
                            <div class="form-group">

                                <div class="col-sm-12">
                                     <input type="hidden" name="id" class="form-control" id="id" required value="">
                                    <input type="hidden" name="store_id" class="form-control" id="store_id1" required value="">
                                    <input type="hidden" name="location_id" class="form-control" id="location_id1" required value="">
                                     <input type="text" class="form-control" name="monthly_cost" id="monthly_cost1" placeholder="Monthly Cost" required  value=""><div class="form-control-line"></div>
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
            $('#users_table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{asset('admin/billinglocation/result')}}/{{$id}}",
                columns: [
                    {data: 'name', name: 'name'},
                    {data: 'cost', name: 'cost'},
                    {data: 'edit', name: 'edit',orderable: false, searchable: false},
                ]
            });
        });
            function viewdetail(id,monthly_cost,store_id,location_id)
            {
                    document.getElementById('id').value = id;
                    document.getElementById('store_id1').value = store_id;
                    document.getElementById('location_id1').value = location_id;
                    document.getElementById('monthly_cost1').value = monthly_cost;
            } 
             function viewstore(store_id,location_id)
            {
                    document.getElementById('store_id').value = store_id;
                    document.getElementById('location_id').value = location_id;
            }
 </script>
@stop
