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
							<header>Import Excel</header>
 <span class="pull-right"> <a href="{!!asset('admin/product')!!}/{{$location_id}}" class="btn btn-primary">Back to all Products </a> </span>
                                                </div>
                                                <!-- END CONTACT DETAILS HEADER -->

				<!-- BEGIN CONTACT DETAILS -->
				<div class="card-tiles">
					<div class="hbox-md col-md-12">
						<div class="hbox-column col-md-9">
							<div class="row">

							<form action="{{ URL::to('admin/products/importExport') }}/{{$location_id}}" class="form-horizontal" method="post" enctype="multipart/form-data">
                                                            <input type="file" name="import_file" /><br>
			<button class="btn btn-primary">Import File</button>
		</form>

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

@stop