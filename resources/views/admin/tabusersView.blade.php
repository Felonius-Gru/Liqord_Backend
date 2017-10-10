<?php $location = App\Locations::where('id', $result->location_id)->first(); ?>
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
                    <form method="post" class="form form-validate" role="form" novalidate="novalidate" id="news_form" name="news_form" enctype="multipart/form-data">
                        <div class="card">
                            <div class="card-head style-primary">
                                <header>{!!$header!!}</header>
                            </div>
                            <div class="card-body">

                                <div class="row">

                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <input type="text" class="form-control" name="username" id="username" required value="{{ (Request::old('username')) 
                           ? Request::old('username') : $result->username }}" @if($id!=false) disabled @endif >
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

                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <input type="text" class="form-control" name="store" id="store" required value="{{$store->name}}" readonly >
                                            <label for="store">Store</label>
                                        </div>
                                    </div>
                                </div>


                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label for="location">Location</label>
                                            <select name="location" id="location"   class="form-control">
                                                <option value="0">Select a Location</option>
                                                @foreach($store_locations as $location)
                                                <option value="{{$location->id}}" @if ($location->id == ((old('location'))
                                                        ? old('location') : $result->location_id))
                                                        selected
                                                        @endif > {!!$location->name!!}</option>
                                                @endforeach                                
                                            </select>

                                        </div>
                                    </div>
                                </div>


                            </div>
                        </div><!--end .card-body -->
                        <div class="card-actionbar">
                            <div class="card-actionbar-row">

                                <a href="{{asset('admin/tabusers')}}" class="btn btn-flat ink-reaction">
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
<style>
</style>
@stop
@section("script")
<script src="{!!asset('themes/assets/js/modules/materialadmin/libs/select2/select2.min.js')!!}"></script>
<script src="{!!asset('themes/assets/js/modules/materialadmin/core/demo/DemoFormComponents.js')!!}"></script>
<script src="{{asset('themes/assets/bootstrap-datepicker/bootstrap-datepicker.js')}}"></script>
<script src="{{asset('themes/assets/js/modules/materialadmin/libs/inputmask/jquery.inputmask.bundle.min.js')}}"></script>


<script type="text/javascript">
</script>

@stop