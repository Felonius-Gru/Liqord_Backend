@extends('layout.homeregLayout')
@section("content")
<!-- BEGIN CONTENT-->
<div id="content">
    <section>
        <div class="section-body contain-lg">
            <div class="row">
                @if($active_sub == "storeadmin")
                <!-- BEGIN ADD CONTACTS FORM -->
                <div class="col-md-7 col-md-offset-2">
                    <form class="form" role="form" method="post"  action="{{asset('admin/registration')}}" onsubmit="return(validate());">
                        {{csrf_field()}}
                        <div class="card">
                            <div class="card-head style-primary">
                                <header>Registration : Store Admin</header>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <input type="text" class="form-control" id="full_name" name="first_name" value="{{ (Request::old('first_name'))}}" required>
                                    <label for="first_name">First name</label>
                                </div>

                                <div class="form-group">
                                    <input type="text" class="form-control" id="full_name" name="last_name" value="{{ (Request::old('last_name'))}}" required>
                                    <label for="last_name">Last name</label>
                                </div>
                                <div class="form-group">
                                    <input type="email" class="form-control" id="email" name="email" value="{{ (Request::old('email'))}}" required>
                                    <label for="email">Email</label>
                                </div>
                                <div class="form-group">
                                    <input type="text" class="form-control" id="phone" name="phone" value="{{ (Request::old('phone'))}}" required >
                                    <label for="phone">Phone</label>
                                </div>
                                <div class="form-group">
                                    <input type="text" class="form-control" id="username" name="username" value="{{ (Request::old('username'))}}" required>
                                    <label for="username">Username</label>
                                </div>

                                <div class="form-group">
                                    <input type="password" class="form-control" id="password" name="password" required>
                                    <label for="password">Password</label>
                                </div>
                                 <div class="form-group">
                                    <input type="text" class="form-control" id="card_num" name="card_num" value="{{ (Request::old('card_num'))}}" required>
                                    <label for="card_num">Card Number</label>
                                   
                                </div>
                                <div  id="carderror">  </div>
                                <div class="form-group">
                                    <input type="text" class="form-control" id="cvv" name="cvv" value="{{ (Request::old('cvv'))}}" required>
                                    <label for="cvv">CVV</label>
                                   
                                </div>
                                
                                <div class="form-group">
                                    <input type="text" class="form-control" id="expiry_date" name="expiry_date" value="{{ (Request::old('expiry_date'))}}" required>
                                    <label for="expiry_date">Expiry Date</label>
                                </div>
                                
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <input type="text" class="form-control" id="store" name="store" value="{{ (Request::old('store'))}}" required>
                                            <label for="store">Business Name</label>
<!--                                            <select name="store" id="store" class="form-control" required>
                                                <option value="">Select a Business Name</option>
                                                @foreach($stores as $store)
                                                <option value="{{$store->id}}" 
                                                         > {!!$store->name!!}</option>
                                                @endforeach                                
                                            </select>
                                         <a href="#" data-toggle="modal" data-target="#myModal1" title="Add New Business Name"> <i class="fa fa-cog fa-2x pull-right" style="padding-top:2px;"></i></a>  -->
                                        </div>
                                    </div>
                                </div>
                                <div class="repeatingSection" >
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <input type="text" class="form-control" id="location" name="location[]" required>
                                            <label for="location">Location</label>
<!--                                            <select name="location[]" id="location"  class="form-control">
                                                <option value="0">Select a Location</option>
                                                @foreach($locations as $location)
                                                <option value="{{$location->id}}"> {!!$location->name!!}</option>
                                                @endforeach                                
                                            </select>-->
                                        </div>
                                    </div>
                                </div>                                          
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="cutofftime">CutoffTime</label>
                                            <select name="cutoffdate[]" id="cutoffdate" required  class="form-control">
                                                <option value="0">Select a Day of the Week</option>
                                                <option value="sunday this week" >Sunday</option>
                                                <option value="monday this week">Monday</option>
                                                <option value="tuesday this week">Tuesday</option>   
                                                <option value="wednesday this week">Wednesday</option>
                                                <option value="thursday this week">Thursday</option>
                                                <option value="friday this week">Friday</option>
                                                <option value="saturday this week">Saturday</option>
                                              </select>
                                        </div>
                                    </div>                               
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <!--<input type="text" name="end_time[]" class="form-control time12-mask" id="end_time" required value="">-->
                                            <input type="time" class="form-control" id="end_time" name="end_time[]" required>
                                            <label>Time </label>
                                            <p class="help-block">Time: am/pm</p>
                                        </div>
                                    </div>
                                </div>     
                                </div>
                                <div class="row">
                                    <div class="col-sm-12">  
                                        <button type="button"  class="btn ink-reaction btn-raised btn-xs btn-primary add_field_button1 repeat"> <span class="glyphicon glyphicon-plus"></span> Add More Locations</button>   
                                        <button type="button"  class="btn ink-reaction btn-raised btn-xs btn-primary add_field_button1 remove"><span class="glyphicon glyphicon-minus"></span> Remove</button>   
                                        <!--<a href="#" data-toggle="modal" data-target="#myModal" title="Add New Location"> <i class="fa fa-cog fa-2x pull-right" style="padding-top:2px;"></i></a>-->
                                    </div>
                                </div>
                            </div><!--end .card-body -->
                            <div class="card-actionbar">
                                <div class="card-actionbar-row">
                                    <a href="{{asset('admin')}}" class="btn btn-flat btn-primary ink-reaction">Cancel</a>
                                    <button type="submit" class="btn btn-flat btn-primary ink-reaction">Create account</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div><!--end .col -->
                <!-- END ADD CONTACTS FORM -->
                @endif

            </div><!--end .row -->
                        <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="actionLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title">Add New Location</h4>
                    </div>
                    <form action="{{asset('admin/location/add')}}" method="post" class="form-horizontal" role="form">
                        {{ csrf_field() }}
                        <div class="modal-body">
                            <div class="form-group">

                                <div class="col-sm-12">
                                    <input type="text" class="form-control" name="location" id="location" placeholder="Location" required  value=""><div class="form-control-line"></div>
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
        </div><!-- /.modal -->  
          
    <div class="modal fade" id="myModal1" tabindex="-1" role="dialog" aria-labelledby="actionLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Add New Business Name</h4>
            </div>
            <form action="{{asset('admin/stores/add')}}" method="post" class="form-horizontal" role="form">
                {{ csrf_field() }}
                <div class="modal-body">
                    <div class="form-group">

                        <div class="col-sm-12">
                            <input type="text" class="form-control" name="store" id="store" placeholder="Business Name" required  value=""><div class="form-control-line"></div>
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
</div><!-- /.modal -->
        </div><!--end .section-body -->
    </section>

</div><!--end #content-->		
<!-- END CONTENT -->
@stop

@section("style")
<style>

</style>
@stop
@section("script")
<script src="{{asset('themes/assets/js/modules/materialadmin/libs/inputmask/jquery.inputmask.bundle.min.js')}}"></script>
<script>    
(function() {
$('input[id$="end_time"]').inputmask(
        "hh:mm",
{
placeholder: "HH:MM xm",
        insertMode: false,
        showMaskOnHover: false,
        hourFormat: 12,
        mask: "h:s t\\m",
}
);
}());

$('.remove').click(function(){
if ($('.repeatingSection').length > 1){
$('.repeatingSection').last().remove(); }
});

$('.repeat').click(function(){
var currentCount = $('.repeatingSection').length;
var newCount = currentCount + 1;
var lastRepeatingGroup = $('.repeatingSection').last();
var newSection = lastRepeatingGroup.clone();
newSection.insertAfter(lastRepeatingGroup);
newSection.find("input").each(function (index, input) {
input.id = input.id.replace("_" + currentCount, "_" + newCount);
input.name = input.name.replace("_" + currentCount, "_" + newCount);
input.value = "";              
});
$('input[id$="end_time"]').inputmask(
        "hh:mm",
{
placeholder: "HH:MM am",
        insertMode: false,
        showMaskOnHover: false,
        hourFormat: 12,
        mask: "h:s t\\m",
}
);  
return false;
});
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
</script>
@stop


