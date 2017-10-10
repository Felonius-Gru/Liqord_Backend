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
                                                  @if (Auth::user()->role=="Admin")
                                                 <th>Status</th>
                                                 @endif
                                                 @if (Auth::user()->role=="StoreAdmin")
                                                 <th>Edit</th>
                                                 @endif
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
                                                    @if (Auth::user()->role=="Admin")
                                                    <td>
                                                        @if($order_item->order_status==1)
                                                        
                                                      <a href='{{asset('admin/order/item/status/outofstock')}}/{{$order_item->order_item_id}}' id='alter_link'
                                        >In Stock
<!--                                                <i class='fa fa-times-circle fa-2x'></i>-->
                                                   </a>
                                                    @else
                                                    <a href='{{asset('admin/order/item/status/instock')}}/{{$order_item->order_item_id}}' id='alter_link'
                                        >Out Of Stock
                                                <!--<i class='fa fa-check-circle fa-2x'></i>-->
                                                   </a>
                                                    @endif
                                                       
                                                    </td>
                                                    @endif
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
                                         <?php echo $order->render(); ?>
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
<script type="text/javascript">
    
//    $(document).ready(function() {  
//   $('input[name=status]').change(function(){ 
//       var option = $('input[type="radio"]:checked').val();
//       alert(option);
//       $.ajax( {
//      type: "POST",
//      url: "{{asset('admin/orders/item/order_status')}}",
//       data: { status : option }
//     
//    });
////        $('form').submit(); 
//   }); 
//  }); 
    
//    $(document).ready(function () {
//    var i = $("input[type=radio][name=status]:checked").val();
//    console.log(i);
//});

//correct alert with values
//   $(document).ready(function() {
//  var ckbox = $("input[name='status']");
//  var chkId = '';
//  $('input').on('click', function() {
//    
//    if (ckbox.is(':checked')) {
//      $("input[name='status']:checked").each ( function() {
//   			chkId = $(this).val() + ",";
//        chkId = chkId.slice(0, -1);
// 	  });
////       if(chkId==1)
////       {
////           
////       }
//       alert ( $(this).val() ); // return all values of checkboxes checked
//       alert(chkId); // return value of checkbox checked
//    }     
//  });
//});

    
//    $('input[type=radio]').on('change', function() {
//    $(this).closest("formsubmit").submit();
//});
//    $(document).ready(function() { 
//   $('input[name=status]').change(function(){
//        $('formsubmit').submit();
//   });
//  });
//    $(document).ready(function() { 
//   $('input[name=status]').change(function(){
//        $('formsubmit').submit();
//   });
//  });

//    
//    $(document).ready(function() {
//$('input[type="radio"]').click(function(){
//    document.getElementById('status').checked="true";
//    document.getElementById('instock').value = result.item_id;
  
//    }
//$('form#formnobtn').submit();
//});
//})
//if (document.getElementById('instock').checked) {
//  rate_value = document.getElementById('instock').value;
//  alert(rate_value);
//}
</script>
@stop 