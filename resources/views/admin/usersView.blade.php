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
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <input type="text" class="form-control" name="first_name" id="first_name" required value="{{ (Request::old('first_name')) 
                           ? Request::old('first_name') : $result->first_name }}">
                                                <label for="first_name">First name</label>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <input type="text" class="form-control" name="last_name" id="last_name" required value="{{ (Request::old('last_name')) 
                           ? Request::old('last_name') : $result->last_name }}">
                                                <label for="last_name">Last name</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <input type="text" class="form-control" name="address1" id="address1" required value="{{ (Request::old('address1')) 
                           ? Request::old('address1') : $result->address1 }}">
                                                <label for="address1">Address1</label>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <input type="text" class="form-control" name="address2" id="address2" required value="{{ (Request::old('address2')) 
                           ? Request::old('address2') : $result->address2 }}">
                                                <label for="address2">Address2</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <input type="text" class="form-control" name="city" id="city" required value="{{ (Request::old('city')) 
                           ? Request::old('city') : $result->city }}">
                                                <label for="city">City</label>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <input type="text" class="form-control" name="state" id="state" required value="{{ (Request::old('state')) 
                           ? Request::old('state') : $result->state }}">
                                                <label for="state">State</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <input type="text" class="form-control" name="zip" id="zip" required value="{{ (Request::old('zip')) 
                           ? Request::old('zip') : $result->zip }}">
                                                <label for="zip">Zip</label>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <input type="text" class="form-control" name="phone" id="phone" required value="{{ (Request::old('phone')) 
                           ? Request::old('phone') : $result->phone }}">
                                                <label for="phone">Phone</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <input type="text" class="form-control" name="email" id="email" required value="{{ (Request::old('email')) 
                           ? Request::old('email') : $result->email }}">
                                                <label for="email">Email</label>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <input type="text" class="form-control" name="username" id="username" required value="{{ (Request::old('username')) 
                           ? Request::old('username') : $result->username }}">
                                                <label for="username">Username</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <input type="password" class="form-control" id="password" name="password" @if($id==false) required @endif >
                                                <label for="password">Password</label>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <input type="password" class="form-control" data-rule-equalTo="#password" name="confirm_password" @if($id==false) required @endif>
                                                <label for="confirm_password">Confirm Password</label>
                                        </div>
                                    </div>
                                </div>  
                                @if(Auth::user()->role=="Admin")
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            @if($id == false)
                                            <input type="text" class="form-control" name="store" id="store" required value="{{ (Request::old('store')) 
                           ? Request::old('store') : $result->store_id }}">
                                            @else 
                                              <input type="text" class="form-control" name="store" id="store" required value=" <?php $store = Stores::where("id", $result->store_id)->select('name')->first();
                                              echo $store['name'];?>" readonly>
                                            @endif
                                            <label for="store">Business Name</label>
<!--                                            <select name="store" id="store" required  class="form-control">
                                                <option value="">Select a Business Name</option>
                                                @foreach($stores as $store)
                                                <option value="{{$store->id}}" @if ($store->id == ((old('store'))
                                                        ? old('store') : $result->store_id))
                                                        selected
                                                        @endif > {!!$store->name!!}</option>
                                                @endforeach                                
                                            </select>-->
                                             <!--<a href="#" data-toggle="modal" data-target="#myModal1" title="Add New Business Name"> <i class="fa fa-cog fa-2x pull-right" style="padding-top:2px;"></i></a>-->  
                                        </div>
                                    </div>
                                </div>
                                @if($submit == 'Register')
                                <div class="repeatingSection" >
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                           <input type="text" class="form-control" name="location[]" id="location[]" required value="">
                                           <label for="location">Location</label>
<!--                                            <select name="location[]" id="location" required  class="form-control">
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
                                        <button type="button"  class="btn ink-reaction btn-raised btn-xs btn-primary add_field_button1 repeat"><span class="glyphicon glyphicon-plus"></span> Add More Locations</button>   
                                        <button type="button"  class="btn ink-reaction btn-raised btn-xs btn-primary add_field_button1 remove"><span class="glyphicon glyphicon-minus"></span> Remove</button>   
                                        <!--<a href="#" data-toggle="modal" data-target="#myModal" title="Add New Location"> <i class="fa fa-cog fa-2x pull-right" style="padding-top:2px;"></i></a>-->
                                    </div>
                                </div>
                                @endif
                                @endif
                               </div>
                            </div><!--end .card-body -->
                            <div class="card-actionbar">
                                    <div class="card-actionbar-row">

                                        <a href="{{asset('admin/users')}}" class="btn btn-flat ink-reaction">
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
</script>
@stop