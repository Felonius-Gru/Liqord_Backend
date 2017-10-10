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
                                            <input type="text" class="form-control" name="store" id="store" required  value="{{ (Request::old('name')) 
                                                       ? Request::old('name') : $result->name }}">
                                            <label for="location">Store</label>
                                        </div>
                                    </div>
                                </div>

                
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                            <!--<label for="role">Staff</label>-->
                                           
                                            <select class="form-control select2-list" name="location[]"  data-placeholder="Select Location" required multiple>
                                       
                                                 @if($loc != '')
                                                 @foreach($locations as $staffs)
                                                <option value="{!!$staffs->id!!}"
                                                      @if(in_array($staffs->id,$loc))
                                                                        selected
                                                                    @endif  
                                                        >{!!$staffs->name!!}</option>
                                                @endforeach
                                             
                                                @else
                                                @foreach($locations as $staffs)
                                                <option value="{!!$staffs->id!!}">{!!$staffs->name!!}</option>
                                                @endforeach
                                               @endif
                                                

                                            </select>

                                            
                                            <label>Choose Location</label>
                                           </div>
                                        </div>
                                    </div>
                          

                            </div><!--end .card-body -->
                            <div class="card-actionbar">
                                <div class="card-actionbar-row">

                                    <a href="{{asset('admin/stores')}}" class="btn btn-flat ink-reaction">
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

@stop


