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
                    <form method="post" class="form form-validate" role="form" enctype="multipart/form-data" novalidate="novalidate" id="product_form" name="product_form">
                        {{csrf_field()}}
                        <div class="card">
                            <div class="card-head style-primary">
                                <header>{!!$header!!}</header>
                                <span class="pull-right"> <a href="{!!asset('admin/product')!!}/{{$location_id}}" class="btn btn-primary">Back to all Products </a> </span>
                            </div>
                            <div class="card-body">
                                @if($id == false)
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label for="searchname">SEARCH PRODUCT</label><br>
                                            <input type="search" id="brand_name" name="searchname" class="form-control" placeholder="Search..." data-provide="typeahead" autocomplete="off" />
                                            <input type="hidden" name="product" id="hiddenInputElement">
                                        </div>
                                    </div>
                                </div>
                                @else 
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label for="searchname">PRODUCT</label>
                                            <div class="ui-widget"> 
                                               <input type="hidden" name="product" id="product" value="{{$result->item_id}}"> 
                                                <select name="product" id="product" required class="form-control"  disabled>

                                                    @foreach($items as $item)
                                                    <option value="{{$item->id}}" @if ($item->id == ((old('product'))
                                                            ? old('product') : $result->item_id))
                                                            selected
                                                            @endif > {!!$item->brand_name!!}</option>
                                                    @endforeach  
                                                </select>
                                            </div>                             
                                        </div>
                                    </div>
                                </div>
                                @endif

                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label for="shelf">SHELF NUMBER</label>
                                            <select name="shelf_number" id="shelf_number" required  class="form-control">
                                                <option value="">Select a Shelf</option>
                                                @foreach($shelves as $shelf)
                                                <option value="{{$shelf->shelf_number}}" @if ($shelf->shelf_number == ((old('shelf'))
                                                        ? old('shelf') : $result->shelf_number))
                                                        selected
                                                        @endif > {!!$shelf->shelf_number!!}</option>
                                                @endforeach                                
                                            </select>
                                        </div>
                                    </div>
                                </div>

<!--                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <input type="text" class="form-control" name="shelf_number" id="shelf_number" required value="{{ (Request::old('shelf_number')) 
                                                       ? Request::old('shelf_number') : $result->shelf_number }}">



                                            <label for="shelf_number">SHELF NUMBER </label>
                                        </div>
                                    </div>
                                </div>-->
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <input type="number" class="form-control" name="bottle_position" id="bottle_position" required value="{{ (Request::old('bottle_position')) 
                                                       ? Request::old('bottle_position') : $result->bottle_position }}">
                                            <label for="bottle_position">BOTTLE POSITION </label>
                                        </div>
                                    </div>
                                </div>



                                <div class="card-actionbar">
                                    <div class="card-actionbar-row">

                                        <a href="{!!asset('admin/product')!!}/{{$location_id}}" class="btn btn-flat ink-reaction">
                                            Cancel
                                        </a>
                                        <!--<input type="hidden" name="location_id" id="location_id" value="{{$location_id}}"/>-->
                                        <input type="hidden" name="submit" value="{{$submit}}" />

                                        <button type="submit" class="btn btn-flat btn-primary ink-reaction">{!!$submit!!}</button>


                                    </div>
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
@stop
<!-- END CONTENT -->
@section("style")

@stop
@section("script")
<script src="{!!asset('themes/assets/js/modules/materialadmin/libs/bootstrap-3-typeahead/bootstrap3-typeahead.min.js')!!}"></script> 
<script type="text/javascript">

$(document).ready(function () {

    $("#brand_name").typeahead({
        source: function (query, process) {
            objects = [];
            map = {};
            $.ajax({
                url: "{{asset('admin/autocomplete')}}",
                data: {brand_name: $("#brand_name").val()},
                type: 'POST',
                dataType: "text",
                success: function (data)
                {
                    $.each(JSON.parse(data), function (i, object) {
                        map[object.label] = object;
                        objects.push(object.label);
                    });
                    process(objects);
                }
            });

        },
        updater: function (item) {
            $('#hiddenInputElement').val(map[item].id);
            return item;
        }
    });

});
//function searchitem()
//{
//  
//                                  url = "{{asset('admin/products/search')}}";                                  
//                                  $.ajax({
//                                      url: url,
//                                      type: "post",
//                                      dataType: "text",
//                                      data: {
//                                      id: id,
//                                      _token: '{!! csrf_token() !!}'
//                                    },
//
//                                     success: function(data){
//                                        
//
//                                     }
//                                   });
//
//                              }

</script>


@stop 

