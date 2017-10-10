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


                <div class="col-md-12">
                    <form method="post" class="form form-validate" role="form" enctype="multipart/form-data" novalidate="novalidate" id="product_form" name="product_form">
                        {{csrf_field()}}
                        <div class="card">
                            <div class="card-head style-primary">
                                <header>{!!$header!!}</header>
                                 <span class="pull-right"> <a href="{!!asset('admin/shelves')!!}/{{$location_id}}" class="btn btn-primary">Back to Shelves </a> </span>
                                </div>
                            <div class="card-body">

                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <input type="text" class="form-control" name="shelf_number" id="category" required value="{{ (Request::old('shelf_number')) 
                                                       ? Request::old('shelf_number') : $result->shelf_number }}" ;>
                                            <label for="shelf_number">Shelf Number</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <input type="text" class="form-control" name="description" id="category"  value="{{ (Request::old('description')) 
                                                       ? Request::old('description') : $result->description }}" ;>
                                            <label for="description">Description</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-actionbar">
                                    <div class="card-actionbar-row">

                                        <a href="{!!asset('admin/shelves')!!}/{{$location_id}}" class="btn btn-flat ink-reaction">
                                            Cancel
                                        </a>
                                        <!--<input type="hidden" name="location_id" id="location_id" value="{{$location_id}}"/>-->
                                        <input type="hidden" name="submit" value="{{$submit}}" />

                                        <button type="submit" class="btn btn-flat btn-primary ink-reaction">{!!$submit!!}</button>


                                    </div>
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
<!-- END CONTENT -->
@section("style")

@stop
@section("script")


@stop 

