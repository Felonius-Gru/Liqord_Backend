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
                                  <div class="tools">
                              <a href="{!! asset('admin/orders/view/') !!}" class="btn btn-flat hidden-xs ink-reaction">
                            Back to all Orders List
                             </a>
                                </div>
                                </div>
                            <div class="card-body">
                                
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <input type="text" class="form-control" name="brand_name" id="brand_name" required value="{{$item->brand_name }}"readonly>
                                                      
                                            <label for="brand_name">Brand Name</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <input type="text" class="form-control" name="quantity" id="quantity" required value="{{ (Request::old('quantity')) 
                                                       ? Request::old('quantity') : $result->quantity }}" ;>
                                            <label for="quantity">Quantity</label>
                                        </div>
                                    </div>
                                </div>
                                
                                
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label>Please Enter quantities in multiples of {{$packsize}}</label>
                                        </div>
                                        
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <input type="text" class="form-control" name="price" id="price" required value="{{ (Request::old('quantity')) 
                                                       ? Request::old('price') : $result->price }}" readonly>
                                            <label for="price">Price</label>
                                        </div>
                                    </div>
                                </div>

                                    <div class="card-actionbar">
                                    <div class="card-actionbar-row">

                                        <a href="" class="btn btn-flat ink-reaction">
                                            Cancel
                                        </a>
                                        <input type="hidden" name="id" id="id" value="{{$result->id}}"/>
                                        <input type="hidden" name="submit" value="submit" />

                                        <button type="submit" class="btn btn-flat btn-primary ink-reaction">Submit</button>


                                    </div>
                                </div>


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

