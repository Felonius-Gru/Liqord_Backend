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
                    <form method="post" class="form form-validate" role="form" novalidate="novalidate" id="news_form" name="news_form" enctype="multipart/form-data">
                        <div class="card">
                            <div class="card-head style-primary">
                                    <header>{!!$header!!}</header>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <input type="text" class="form-control" name="category" id="category" required value="{{ (Request::old('category')) 
                           ? Request::old('category') : $result->category }}">
                                                <label for="category">Category</label>
                                        </div>
                                    </div>
                                   
                                </div>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <input type="text" class="form-control" name="ada_no" id="ada_no" required value="{{ (Request::old('ada_no')) 
                           ? Request::old('ada_no') : $result->ada_no }}">
                                                <label for="ada_no">Ada No.</label>
                                        </div>
                                    </div>
                                    
                                </div>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <input type="text" class="form-control" name="code" id="code" required value="{{ (Request::old('code')) 
                           ? Request::old('code') : $result->code }}">
                                                <label for="code">Code</label>
                                        </div>
                                    </div>
                                  
                                </div>
                                 <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <input type="text" class="form-control" name="upc" id="code" required value="{{ (Request::old('upc')) 
                           ? Request::old('upc') : $result->upc }}">
                                                <label for="upc">UPC</label>
                                        </div>
                                    </div>
                                  
                                </div>
                                 <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <input type="text" class="form-control" name="brand_name" id="brand_name" required value="{{ (Request::old('brand_name')) 
                           ? Request::old('brand_name') : $result->brand_name }}">
                                                <label for="brand_name">Brand Name</label>
                                        </div>
                                    </div>
                                   
                                </div>
                                 <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <input type="text" class="form-control" name="proof" id="proof" required value="{{ (Request::old('proof')) 
                           ? Request::old('proof') : $result->proof }}">
                                                <label for="proof">Proof</label>
                                        </div>
                                    </div>
                                   
                                </div>
                                 <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <input type="text" class="form-control" name="size" id="size" required value="{{ (Request::old('size')) 
                           ? Request::old('size') : $result->size }}">
                                                <label for="size">Size in ML</label>
                                        </div>
                                    </div>
                                   
                                </div>
                                 <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <input type="text" class="form-control" name="pack_size" id="pack_size" required value="{{ (Request::old('pack_size')) 
                           ? Request::old('pack_size') : $result->pack_size }}">
                                                <label for="pack_size">Pack Size</label>
                                        </div>
                                    </div>
                                   
                                </div> 
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <input type="text" class="form-control" name="base_price" id="base_price" required value="{{ (Request::old('base_price')) 
                           ? Request::old('base_price') : $result->base_price }}">
                                                <label for="base_price">Base Price</label>
                                        </div>
                                    </div>
                                   
                                </div>
                                 <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <input type="text" class="form-control" name="license_price" id="license_price" required value="{{ (Request::old('license_price')) 
                           ? Request::old('license_price') : $result->license_price }}">
                                                <label for="license_price">License Price</label>
                                        </div>
                                    </div>
                                   
                                </div>
                                 <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <input type="text" class="form-control" name="minimum_shelf_price" id="minimum_shelf_price" required value="{{ (Request::old('minimum_shelf_price')) 
                           ? Request::old('minimum_shelf_price') : $result->minimum_shelf_price }}">
                                                <label for="minimum_shelf_price">Minimum Shelf Price</label>
                                        </div>
                                    </div>
                                   
                                </div>
                             
                                </div>
                            </div><!--end .card-body -->
                            <div class="card-actionbar">
                                    <div class="card-actionbar-row">

                                        <a href="{{asset('admin/items')}}" class="btn btn-flat ink-reaction">
                                            Cancel
                                        </a>
                                        {{csrf_field()}} 
                                        <input type="hidden" name="submit" value="{{$submit}}" />
                                        <button type="submit" class="btn btn-flat btn-primary ink-reaction">{!!$submit!!}</button>


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
        <!-- END CONTENT -->
@stop

@section("style")