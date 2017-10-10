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
                              <a href="{!! asset('admin/shelf/taginventory') !!}" class="btn btn-flat hidden-xs ink-reaction">
                            Back to all Inventory
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
                                                 <!--<th>#</th>-->
                                                  <th>Code</th>
                                                 <th>Product</th>                                        
                                                
                                                 <th>Price</th>
                                                 <th>Shelf Number</th>
                                                 <th>Bottle Position</th>
                                                 <th>Print</th>
          
                                              </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($products as $order_item)
                                                <tr>
                                                    <!--<td>{!! $order_item->order_item_id !!}</td>-->
                                                    <td>{!! $order_item->code !!}</td>
                                                    <td>{!! $order_item->brand_name !!}</td>
                                                    <td>{!! $order_item->base_price !!}</td>
                                                    <td>{!! $order_item->shelf_number !!}</td>
                                                    <td>{!! $order_item->bottle_position !!}</td>
<!--                                                    <td>{!! $order_item->change_price !!}</td>-->
                                                    <!--<td>$ {!! $order_item->price !!}</td>-->
                                                      <td><a href="{{asset("admin/shelf/taginventory/printitem/{$order_item->product_id}")}}">
                                                   <span class='fa fa-pencil-square fa-2x'></span> </a></td>
                                                  
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                        <?php echo $products->render(); ?>
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