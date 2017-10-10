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
<!--                    <form method="post" class="form form-validate" role="form" enctype="multipart/form-data" novalidate="novalidate" id="product_form" name="product_form">
                        {{csrf_field()}}-->
                        <div class="card">
                            <div class="card-head style-primary">
                                <header>{!!$header!!}</header>
                             
                                <div class="tools">
                              <a href="{!! asset('admin/orders') !!}" class="btn btn-flat hidden-xs ink-reaction">
                            Back to all Orders
                             </a>
                                </div>
                              
                            </div>
                            <div class="card-body">
                            </div>
                               <div class="card-tiles">
                            <div class="hbox-md col-md-12">
                                <div class="hbox-column col-md-9">
                                    <div class="row">
                                        <table class="table table-bordered mbn">
                                            <thead>
                                              <tr>
                                                 <th>#</th>
                                                 <th>Code</th>
                                                 <th>Product</th>                                        
                                                 <th>Quantity</th>
                                                 <th>Price</th>
           @if (Auth::user()->role=="StoreAdmin")<th>Edit</th>@endif
                                              </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($order as $order_item)
                                                <tr>
                                                    <td>{!! $order_item->order_item_id !!}</td>
                                                     <td>{!! $order_item->code !!}</td>
                                                    <td>{!! $order_item->brand_name !!}</td>
                                                    <td>{!! $order_item->quantity !!}</td>
                                                    <td>$ {!! $order_item->price !!}</td>
                                                   @if (Auth::user()->role=="StoreAdmin")
                                                    @if($order_lock==2 || $order_lock==0)<td> <a href="{{asset("admin/orders/item/update/{$order_item->order_item_id}")}}">
                                                   <span class='fa fa-pencil-square fa-2x'></span> </a></td>
                                                   @elseif($order_lock==1)<td> <a href="#">
                                                   <span class='fa fa-lock fa-2x'></span> </a></td>
                                                      @endif
                                                     @endif
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div><!--end .row -->
                                </div><!--end .hbox-column -->
                                <!-- END CONTACTS COMMON DETAILS -->

                            </div><!--end .hbox-md -->
                        </div><!--end .card-tiles -->
                        </div>
                  

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