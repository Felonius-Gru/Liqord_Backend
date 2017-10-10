@extends('layout.adminLayout')
@section("content")
<!-- BEGIN CONTENT-->
<div id="content">
	<section>
		<div class="section-header">
            <ol class="breadcrumb"></ol>
		</div>
		<div class="section-body contain-lg">
			<div class="row">
				<!-- BEGIN ADD CONTACTS FORM -->
				<div class="col-md-12">
                    <form method="post" class="form form-validate" role="form" novalidate="novalidate" id="setting_form" name="social" >
						<div class="card">
							<div class="card-head style-primary">
								<header>{!!$header!!}</header>
							</div>
							<div class="card-body">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <input type="text" class="form-control" name="tax" id="tax" required="" value="{{$result["tax"]}}">   
                                            <label for="tax">Tax</label>
                                        </div>
                                    </div>
                                </div>	
                                
                            </div><!--end .card-body -->
							<div class="card-actionbar">
								<div class="card-actionbar-row">
                                    {{csrf_field()}} 
                                    <button type="submit" class="btn btn-flat btn-primary ink-reaction">Save</button>
                                </div>
							</div>
						</div>
					</form>
				</div><!--end .col -->
				<!-- END ADD CONTACTS FORM -->

			</div><!--end .row -->  
		</div><!--end .section-body -->
	</section>
                    
		</div><!--end #content-->
                @stop

@section("style")
<style>
    
</style>
@stop

@section("script")


@stop
