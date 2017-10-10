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
							<header>Store Locations</header>
                                                       <div class="tools">
								<a class="btn ink-reaction btn-primary" onclick=viewlocation(); href='#myModal' data-toggle='modal'><span class="glyphicon glyphicon-plus"></span> &nbsp;New Location</a>
							 </div> 
<!--                                                        end .tools -->
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
                                                                            <th>PRODUCT</th>
                                                                            <th>SHELVES</th>
                                                                            <th>PRINT LABELS</th>
                                                                            <th width="20%">PRINT INVENTORY LABELS</th>
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
                        
                                <div id="myModal" class="modal fade" role="dialog">
                <div class="modal-dialog">

                    <!-- Modal content-->
                    <div class="modal-content" style="width:430px;height:auto;overflow-x: scroll">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" id="btn_modal" >X</button>
                            <h4 class="modal-title">Add New Store Location</h4>
                        </div>
                        <div class="modal-body" >
                            <form  action="{{asset('admin/storelocations/add')}}" method="post" enctype="multipart/form-data">
                                {{csrf_field()}}
                                 <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                          <label for="location">Location</label>
                                             <select class="form-control select2-list" id="location" name="location"  data-placeholder="Select Location" required >
                                        </select>
                                            
                                            
                                        </div>
                                    </div>
                                </div>
                                <input type="submit" class="button btn-primary" id="modalLink" value="Submit"/>
                            </form>
                        </div>

                    </div>

                </div>
            </div> 
                        
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
                ajax: "{{asset('admin/storelocations/result')}}",
                columns: [
                    {data: 'name', name: 'name'},
                    {data: 'product', name: 'product'},
                     {data: 'shelves', name: 'shelves'},
                     {data: 'checkbox', name: 'checkbox'},
                      {data: 'checklabel', name: 'checklabel'},
                    {data: 'action', name: 'action',orderable: false, searchable: false}
                   
                ]
            });
        });
                                    function viewlocation()
                                    {
                                        var select = document.getElementById('location');
                                                         $("#location").empty();
                                           url = "{{asset('admin/storelocations/viewlocation')}}";
                                                    $.ajax({
                                                        url: url,
                                                        type: "post",
                                                        dataType: "text",
                                                        data: {
                                                            
                                                            _token: '{!! csrf_token() !!}'
                                                        },
                                                        
                                                        success: function (data) {                                                        
                                                            var result = JSON.parse(data);    
                                                            var i = 0;
                                                            var len = result.length;
                                                            for (i = 0; i < len; i++)
                                                            {

                                                               var option = document.createElement('option');
                                                                option.value = result[i].id;
                                                                option.innerHTML = result[i].name;
                                                                select.appendChild(option);
                                                            }
                                                        }
                                                    });
}
    </script>
@stop
