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
                                 <span class="pull-right"> <a href="{!!asset('admin/product')!!}/{{$location_id}}" class="btn btn-primary">Back to all Products </a> </span>
                                </div>
                            <div class="card-body">

                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <input type="text" class="form-control" name="category" id="category" required value="{{ (Request::old('category')) 
                                                       ? Request::old('category') : $result->category }}" ;>
                                            <label for="category">CATEGORY</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                   <div class="col-sm-12">
                                       <div class="form-group">
                                           <input type="text" class="form-control" name="ada_no" id="ada_no" required value="{{ (Request::old('ada_no')) 
                                               ? Request::old('ada_no') : $result->ada_no }}">
                                           <label for="ada_no">ADA NO </label>
                                       </div>
                                   </div>
                               </div>
                                <div class="row">
                                   <div class="col-sm-12">
                                       <div class="form-group">
                                           <input type="text" class="form-control" name="code" id="code" required value="{{ (Request::old('code')) 
                                               ? Request::old('code') : $result->code }}">
                                           <label for="code">CODE </label>
                                       </div>
                                   </div>
                               </div>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <input type="text" class="form-control" name="brand_name" id="brand_name" required value="{{ (Request::old('brand_name')) 
                                                       ? Request::old('brand_name') : $result->brand_name }}">
                                            <label for="brand_name">BRAND NAME</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <input type="text" class="form-control" name="proof" id="proof" required value="{{ (Request::old('proof')) 
                                                       ? Request::old('proof') : $result->proof }}">
                                            <label for="proof">PROOF </label>
                                        </div>
                                    </div>
                                </div>
                                

                                 <div class="row">
                                   <div class="col-sm-12">
                                       <div class="form-group">
                                           <input type="text" class="form-control" name="size" id="size" required value="{{ (Request::old('size')) 
                                               ? Request::old('size') : $result->size }}">
                                           <label for="size">SIZE </label>
                                       </div>
                                   </div>
                               </div>
                                
                                
                                <div class="row">
                                   <div class="col-sm-12">
                                       <div class="form-group">
                                           <input type="text" class="form-control" name="pack_size" id="pack_size" required value="{{ (Request::old('pack_size')) 
                                               ? Request::old('pack_size') : $result->pack_size }}">
                                           <label for="pack_size">PACK SIZE </label>
                                       </div>
                                   </div>
                               </div>
                                
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <input type="text" class="form-control" name="base_price" id="base_price" required value="{{ (Request::old('base_price')) 
                                                       ? Request::old('base_price') : $result->base_price }}">
                                            <label for="base_price">BASE PRICE </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <input type="text" class="form-control" name="license_price" id="license_price" required value="{{ (Request::old('license_price')) 
                                                       ? Request::old('license_price') : $result->license_price }}">
                                            <label for="license_price">LICENSE PRICE </label>
                                        </div>
                                    </div>
                                </div>
                                
                                 <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <input type="text" class="form-control" name="minimum_shelf_price" id="minimum_shelf_price" required value="{{ (Request::old('minimum_shelf_price')) 
                                                       ? Request::old('minimum_shelf_price') : $result->minimum_shelf_price }}">
                                            <label for="minimum_shelf_price">MINIMUM SHELF PRICE </label>
                                        </div>
                                    </div>
                                </div>
                               <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <input type="text" class="form-control" name="shelf_number" id="shelf_number" required value="{{ (Request::old('shelf_number')) 
                                                       ? Request::old('shelf_number') : $result->shelf_number }}">
                                            <label for="shelf_number">SHELF NUMBER </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <input type="text" class="form-control" name="bottle_position" id="bottle_position" required value="{{ (Request::old('bottle_position')) 
                                                       ? Request::old('bottle_position') : $result->bottle_position }}">
                                            <label for="bottle_position">BOTTLE POSITION </label>
                                        </div>
                                    </div>
                                </div>
                               


                                <div class="card-actionbar">
                                    <div class="card-actionbar-row">

                                        <a href="{!!asset('admin/product')!!}/{{$location_id}}" class="btn btn-flat ink-reaction">
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
<link type="text/css" rel="stylesheet" href="{{asset('themes/assets/css/modules/materialadmin/css/theme-default/libs/summernote/summernote9fec.css')}}" />
<link type="text/css" rel="stylesheet" href="{{asset('themes/assets/bootstrap-datepicker/datepicker3f394.css?1422823364')}}" />

<style>   

</style>
@stop
@section("script")


@stop 

