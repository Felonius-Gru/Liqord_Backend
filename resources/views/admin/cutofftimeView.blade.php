<?php 
$location= App\Locations::where('id',$result->location_id)->first();
?>

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

                <!-- BEGIN ADD CONTACTS FORM -->
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
                                            <label for="cutofftime">CutoffTime</label>
                                            <select name="cutoffdate" id="cutoffdate" required  class="form-control">
                                                <option value="0">Select a Day of the Week</option>
                                                <option value="sunday this week"  @if ($result->cutoffdate == "sunday this week")
                                                        ' selected="selected"'
                                                        @endif>Sunday</option>
                                                <option value="monday this week"@if ($result->cutoffdate == "monday this week")
                                                        ' selected="selected"'
                                                        @endif>Monday</option>
                                                <option value="tuesday this week" @if ($result->cutoffdate == "tuesday this week")
                                                      ' selected="selected"'
                                                        @endif>Tuesday</option>   
                                                <option value="wednesday this week" @if ($result->cutoffdate == "wednesday this week")
                                                       ' selected="selected"'
                                                        @endif>Wednesday</option>
                                                <option value="thursday this week" @if ($result->cutoffdate == "thursday this week")
                                                       ' selected="selected"'
                                                        @endif>Thursday</option>
                                                <option value="friday this week" @if ($result->cutoffdate == "friday this week")
                                                       ' selected="selected"'
                                                        @endif>Friday</option>
                                                <option value="saturday this week" @if ($result->cutoffdate == "saturday this week")
                                                       ' selected="selected"'
                                                                                                               @endif>Saturday</option>
                                              </select>
                                        </div>
                                    </div>
                                  </div>
                                
                                
                             <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">

                                            <input type="text" name="end_time" class="form-control time12-mask" id="end_time" required value="{{ (Request::old('end_time'))
                                                       ? Request::old('end_time') : date('g:iA',  strtotime($result->end_time)) }}">
                                            
<!--                                            <input type="time" class="form-control" id="end_time" name="end_time[]" required value="{{ (Request::old('end_time'))
                                                       ? Request::old('end_time') : date('g:iA',  strtotime($result->end_time)) }}">-->
                                            <label>Time </label>
                                            <p class="help-block">Time: am/pm</p>
                                        </div>
                                    </div>
                             </div>
                                    
                                

                                
                                 <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label for="store">Store</label>
                                            <select name="store" id="store" required  class="form-control"  @if($id) disabled="true" @else onchange="viewstorelocation(this.value); @endif">
                                                <option value="0">Select a Store</option>
                                                @foreach($stores as $store) 
                                                <option value="{{$store->id}}" @if ($store->id == ((old('store'))
                                                        ? old('store') : $result->store_id))
                                                        selected
                                                        @endif > {!!$store->name!!}</option>
                                                @endforeach                                
                                            </select>
                                        </div>
                                    </div>
                                  </div>
                                  <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label for="location">Location</label>
                                            <select name="location" id="location" required  class="form-control select2-list" data-placeholder="Select Location"  @if($id) disabled="true" @endif >
                                                
                                                @if($result->location_id))
                                                <option value="{{$location->id}}" @if ($location->id == ((old('location'))
                                                        ? old('location') : $result->location_id))
                                                        selected
                                                        @endif > {!!$location->name!!}</option>
                                                @endif                           
                                            </select>
                                        </div>
                                    </div>
                                   </div>
                                
                            </div><!--end .card-body -->
                            <div class="card-actionbar">
                                <div class="card-actionbar-row">

                                    <a href="{{asset('admin/cutofftime')}}" class="btn btn-flat ink-reaction">
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
<link type="text/css" rel="stylesheet" href="{{asset('themes/assets/css/modules/materialadmin/css/theme-default/libs/select2/select201ef.css?1422823373')}}" />

<link type="text/css" rel="stylesheet" href="{{asset('themes/assets/bootstrap-datepicker/datepicker3f394.css?1422823364')}}" />
<!--<link rel="stylesheet" type="text/css"--> 
      <!--href="{!!asset('themes/assets/css/modules/materialadmin/css/theme-default/libs/select2/select2.css')!!}">-->

<style>   

</style>
@stop
@section("script")
<script src="{!!asset('themes/assets/js/modules/materialadmin/libs/select2/select2.min.js')!!}"></script>
<script src="{!!asset('themes/assets/js/modules/materialadmin/core/demo/DemoFormComponents.js')!!}"></script>
<script src="{{asset('themes/assets/bootstrap-datepicker/bootstrap-datepicker.js')}}"></script>
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
</script>
<script type="text/javascript">
        var get_store = document.getElementById('store').value;
        //     alert(get_store);
        viewstorelocation(get_store)
        function viewstorelocation(val)
        {
            var store_id = document.getElementById('store').value;
//                                        alert(store_id);

//                                        var select = document.getElementById('store').value;

//                                                         $("#store").empty();
               url = "{{asset('admin/cutofftime/viewstorelocation')}}";
                        $.ajax({
                            url: url,
                            type: "post",
                            dataType: "text",
                            data: {
                                 store_id:store_id,
                                _token: '{!! csrf_token() !!}'
                            },
                            success: function (data) {
                                var select = document.getElementById('location');
                                $("#location").empty();
//                                                            alert(data);
                                var result = JSON.parse(data);
//                                                            alert(result);
                                var i = 0;
                                var len = result.length;
                                for (i = 0; i < len; i++)
                                {

                                   var option = document.createElement('option');
                                    option.value = result[i].id;
                                    option.innerHTML = result[i].name;
                                    select.appendChild(option);
                                }
                            }
                        });
}
    </script>

@stop

