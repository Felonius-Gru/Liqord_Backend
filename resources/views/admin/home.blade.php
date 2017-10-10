@extends('layout.adminLayout')

@section("content")
<!-- BEGIN CONTENT-->
		<div id="content">
			
	<section>
		<div class="section-body">
                    @if(Auth::user()->role == 'Admin')
                    <div class="row">
<!--                        <div class="col-md-3 col-sm-6">
                            <a href="{{asset('admin/users')}}">
                            <div class="card">
                                <div class="card-body no-padding">
                                        <div class="alert alert-callout alert-success no-margin">
                                                <h1 class="pull-right text-success"><i class="md md-account-circle"></i></h1>
                                                <strong class="text-xl">User Info</strong><br/>
                                                <span class="opacity-50">All Users</span>
                                        </div>
                                </div>end .card-body 
                            </div>end .card 
                            </a>
                        </div>-->

                        <div class="col-md-4 col-sm-6">
                            <a href="{{asset('admin/importExport')}}">
                            <div class="card">
                                <div class="card-body no-padding">
                                        <div class="alert alert-callout alert-success no-margin">
                                                <h1 class="pull-right text-success"><i class="md md-import-export"></i></h1>
                                                <strong class="text-xl">Upload LARA file</strong><br/>
                                                <span class="opacity-50">Upload LARA file</span>
                                        </div>
                                </div><!--end .card-body -->
                            </div><!--end .card -->
                            </a>
                        </div>
                       <div class="col-md-4 col-sm-6">
                            <a href="{{asset('admin/upcExport')}}">
                            <div class="card">
                                <div class="card-body no-padding">
                                        <div class="alert alert-callout alert-success no-margin">
                                                <h1 class="pull-right text-success"><i class="md md-import-export"></i></h1>
                                                <strong class="text-xl">Upload UPC file</strong><br/>
                                                <span class="opacity-50">Upload UPC file</span>
                                        </div>
                                </div><!--end .card-body -->
                            </div><!--end .card -->
                            </a>
                        </div>
                   </div>
                    <div class="row">
                      <div class="col-md-4 col-sm-6">
                            <a href="{{asset('admin/stores')}}">
                            <div class="card">
                                <div class="card-body no-padding">
                                        <div class="alert alert-callout alert-success no-margin">
                                                <h1 class="pull-right text-success"><i class="md md-store"></i></h1>
                                                <strong class="text-xl">Add Stores</strong><br/>
                                                <span class="opacity-50">Add Stores</span>
                                        </div>
                                </div><!--end .card-body -->
                            </div><!--end .card -->
                            </a>
                        </div>
                          <div class="col-md-4 col-sm-6">
                            <a href="{{asset('admin/billing')}}">
                            <div class="card">
                                <div class="card-body no-padding">
                                        <div class="alert alert-callout alert-success no-margin">
                                                <h1 class="pull-right text-success"><i class="md md-attach-money"></i></h1>
                                                <strong class="text-xl">Set Monthly Cost</strong><br/>
                                                <span class="opacity-50">Set Monthly Cost</span>
                                        </div>
                                </div><!--end .card-body -->
                            </div><!--end .card -->
                            </a>
                        </div>
                       
                    </div>
                     </div>
                    @endif
                  @if(Auth::user()->role == 'StoreAdmin' && Auth::user()->card_num == '')
                    <div class="tools">
                        <a class="btn ink-reaction btn-primary" href='#myModal' id="test" data-toggle='modal'><span class="glyphicon glyphicon-plus"></span> &nbsp;ADD Card Number</a>
                    </div>
                    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="actionLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                    <h4 class="modal-title">Add Card Number</h4>
                                </div>
                                <form action="{{asset('admin/store/card')}}" method="post" class="form-horizontal" role="form" onsubmit="return(validate());">
                                    {{ csrf_field() }}
                                   <div class="modal-body">
                                        <div class="form-group">
                                            <div class="col-sm-12">
                                                <input type="text" class="form-control" name="card_num" id="card_num" placeholder="Card Number" required  value=""><div class="form-control-line"></div>
                                            </div>
                                            <div class="col-sm-12" id="carderror">
                                             </div> 
                                            </div>
                                             
                                               <div class="form-group">
                                                <div class="col-sm-12">
                                                    <input type="text" class="form-control" name="cvv" id="cvv" placeholder="CVV" required  value=""><div class="form-control-line"></div>
                                                </div>
                                                <div class="col-sm-12">
                                                    <input type="text" class="form-control" name="expiry_date" id="expiry_date" placeholder="Expiry Date" required  value=""><div class="form-control-line"></div>
                                                </div>
                                            
                                             </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn CancelBtn" data-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn LoginBtn">Submit</button>
                                    </div>
                                </form>
                            </div><!-- /.modal-content -->
                        </div><!-- /.modal-dialog -->
                    </div>
                   @endif 
                    @if(Auth::user()->role == 'StoreAdmin' && Auth::user()->card_num != '')
                    <div class="tools">
                      <?php $card = Auth::user()->card_num ;
                      $cvv = Auth::user()->cvv ;
                      $expiry_date = Auth::user()->expiry_date ; ?>
                        
                        <a class="btn ink-reaction btn-primary" onclick="viewdetail({{$card}},{{$cvv}},'{{$expiry_date}}');" href='#myModal' id="test" data-toggle='modal'><span class="glyphicon glyphicon-plus"></span> &nbsp;EDIT Card Number</a>
                    </div>
                    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="actionLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal1" aria-hidden="true">&times;</button>
                                    <h4 class="modal-title">Edit Card Number</h4>
                                </div>
                                
                                <form  action="{{asset('admin/store/editcard')}}" method="post" class="form-horizontal" role="form" onsubmit="return(validate());">
                                    {{ csrf_field() }}
                                    <div class="modal-body">
                                          <div class="form-group">

                                            <div class="col-sm-12">
                                                <label for="card_num">Card Number</label>
                                                <input type="text" class="form-control" name="card_num" id="card_num" placeholder="Card Number" required  value=""><div class="form-control-line"></div>
                                                <!--<label for="card_num">Card Number</label>-->
                                            </div>
                                            <div class="col-sm-12">
                                                <label for="cvv">CVV</label>
                                                <input type="text" class="form-control" name="cvv" id="cvv" placeholder="CVV" required  value=""><div class="form-control-line"></div>

                                            </div>
                                            <div class="col-sm-12">
                                                <label for="expiry_date">Expiry Date</label>
                                                <input type="text" class="form-control" name="expiry_date" id="expiry_date" placeholder="Expiry Date" required  value=""><div class="form-control-line"></div>

                                            </div>
                                            <div class="col-sm-12" id="carderror">

                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn CancelBtn" data-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn LoginBtn">Submit</button>
                                    </div>
                                </form>
                            </div><!-- /.modal-content -->
                        </div><!-- /.modal-dialog -->
                    </div>
                    @endif
                    
			
		</div><!--end .section-body -->
	</section>

		</div><!--end #content-->		
		<!-- END CONTENT -->
@stop

@section("style")
<style>
    .icon-bg {
        right: 2px;
        top: 12px;
        font-size: 62px;
        line-height: 62px;
    }
</style>
@stop

@section("script")
<script>
     @if(Auth::user()->role == 'StoreAdmin' && Auth::user()->card_num == '')
    {
        document.getElementById("test").click();
    }
    @endif
    function checkCardNumber(ccordcnumber)
    {
    var americancardno = /^(?:3[47][0-9]{13})$/;
            var visacardno = /^(?:4[0-9]{12}(?:[0-9]{3})?)$/;
            var masterCard = /^(?:5[1-5][0-9]{14})$/;
            var discover = /^(?:6(?:011|5[0-9][0-9])[0-9]{12})$/;
            var dinersclub = /^(?:3(?:0[0-5]|[68][0-9])[0-9]{11})$/;
            var JCBcard = /^(?:(?:2131|1800|35\d{3})\d{11})$/;
            if (ccordcnumber.match(americancardno))
    {
    return true;
    } else if (ccordcnumber.match(visacardno))
    {
    return true;
    } else if (ccordcnumber.match(masterCard))
    {
    return true;
    } else if (ccordcnumber.match(discover))
    {
    return true;
    } else if (ccordcnumber.match(dinersclub))
    {
    return true;
    } else if (ccordcnumber.match(JCBcard))
    {
    return true;
    } else
    {
    return false;
    }
    }
    function validate()
    {
    $('#carderror').html;
    var ccordcnumber = $("#card_num").val();
    if (checkCardNumber(ccordcnumber) == false)
    {
    $('#carderror').html('Not a valid card number.').css("color", "red");
            return false;
    }
    }
     function viewdetail(card,cvv,expiry_date)
    {
     document.getElementById('card_num').value = card;
     document.getElementById('cvv').value = cvv;
     document.getElementById('expiry_date').value = expiry_date;

    }
</script>
@stop