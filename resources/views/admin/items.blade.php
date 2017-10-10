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
							<header>Items List</header>
<!--                                                        <div class="tools">
                                                                <a class="btn btn-flat hidden-xs" href="{{asset('admin/users/register')}}"><span class="glyphicon glyphicon-plus"></span> &nbsp;REGISTER</a>
                                                        </div>end .tools -->
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
                                                                            <!--<th>ID</th>-->
                                                                            <th>CATEGORY</th>
<!--                                                                            <th>ADA NO.</th>-->
                                                                            <th>CODE</th>
                                                                             <th>UPC</th>
                                                                            <th>BRAND NAME</th>
                                                                            <th>PROOF</th>
                                                                            <th>DATE OF <br>INSERTION</th>
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
            $('#items_table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{asset('admin/items/result')}}",
                columns: [
//                    {data: 'id', name: 'id'},
                    {data: 'category', name: 'category'},
//                    {data: 'ada_no', name: 'ada_no'},
                    {data: 'code', name: 'code'},
                     {data: 'upc', name: 'upc'},
                    {data: 'brand_name', name: 'brand_name'},
                    {data: 'proof', name: 'proof'},
                    {data: 'created_at', name: 'created_at'},
                    {data: 'edit', name: 'edit',orderable: false, searchable: false},
                    {data: 'action', name: 'action',orderable: false, searchable: false},
                    
                ]
            });
        });

    </script>
@stop
