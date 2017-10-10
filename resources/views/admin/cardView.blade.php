@extends('layout.adminLayout')

@section("content")
<?php use App\Stores;?>
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
                                            <input type="text" class="form-control" name="card_num" id="card_num" required value="{{ (Request::old('card_num')) 
                           ? Request::old('card_num') : $user->card_num }}">
                                                <label for="card_num">Card Number</label>
                                        </div>
                                    </div>
                                 </div>
                                 <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <input type="text" class="form-control" name="cvv" id="cvv" required value="{{ (Request::old('cvv')) 
                           ? Request::old('cvv') : $user->cvv }}">
                                                <label for="cvv">CVV</label>
                                        </div>
                                    </div>
                                 </div>
                                 <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <input type="text" class="form-control" name="exp_date" id="expiry_date" required value="{{ (Request::old('expiry_date')) 
                           ? Request::old('expiry_date') : $user->expiry_date }}">
                                                <label for="expiry_date">Expiry Date</label>
                                        </div>
                                    </div>
                                 </div>
                               </div>
                            </div><!--end .card-body -->
                            <div class="card-actionbar">
                                    <div class="card-actionbar-row">

                                        <a href="{{asset('admin/declined_charges')}}" class="btn btn-flat ink-reaction">
                                            Cancel
                                        </a>
                                        {{csrf_field()}} 
                                         <input type="hidden" name="user_id" value="{{$user->id}}" />
                                         <input type="hidden" name="store_id" value="{{$result->store_id}}" />
                                         <input type="hidden" name="location_id" value="{{$result->location_id}}" />
                                         <input type="hidden" name="bill_amount" value="{{$result->orginal_price}}" />
                                         <input type="hidden" name="bill_date" value="{{$result->bill_date}}" />  
                                         <input type="hidden" name="submit" value="Pay Now" />
                                        <button type="submit" class="btn btn-flat btn-primary ink-reaction">Pay Now</button>


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
@stop
@section("script")
<script>    

</script>
@stop