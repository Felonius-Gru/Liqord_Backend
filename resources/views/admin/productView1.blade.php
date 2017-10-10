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

                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label for="searchname">SEARCH PRODUCT</label><br>
                                            <div class="ui-widget"> 
                                               <select name="combobox" id="combobox" required class="form-control" >
                                                   <option value="">Select one...</option>
                                                     @foreach($items as $item)
                                                <option value="{{$item->id}}" @if ($item->id == ((old('combobox'))
                                                        ? old('combobox') : $result->item_id))
                                                        selected
                                                        @endif > {!!$item->brand_name!!}</option>
                                                @endforeach  
                                            </select>
                                             </div>                             
                                            </div>
                                    </div>
                                </div>
                                 <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label for="shelf">Shelf</label>
                                            <select name="shelf" id="shelf" required  class="form-control">
                                                <option value="">Select a Shelf</option>
                                                @foreach($shelves as $shelf)
                                                <option value="{{$shelf->id}}" @if ($shelf->id == ((old('shelf'))
                                                        ? old('shelf') : $result->shelf_id))
                                                        selected
                                                        @endif > {!!$shelf->shelf_name!!}</option>
                                                @endforeach                                
                                            </select>
                                        </div>
                                    </div>
                                        </div>
                          
                               <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <input type="text" class="form-control" name="shelf_number" id="shelf_number" required value="{{ (Request::old('shelf_number')) 
                                                       ? Request::old('shelf_number') : $result->shelf_number }}">
                                            
                                           
                                            
                                            <label for="shelf_number">SHELF NUMBER </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <input type="text" class="form-control" name="bottle_position" id="bottle_position" required value="{{ (Request::old('bottle_position')) 
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
 <link type="text/css" rel="stylesheet" href="{{asset('themes/assets/css/modules/materialadmin/css/theme-default/libs/jquery-ui/jquery-ui.css')}}" />
 <style>
    .custom-combobox {
        position: relative;
        display: inline-block;
    }
    .custom-combobox-toggle {
        position: absolute;
        top: 0;
        bottom: 0;
        margin-left: -1px;
        padding: 0;
    }
    .custom-combobox-input {
        margin: 0;
        padding: 5px 10px;
    }
    </style>
@stop
@section("script")
<script src="{!!asset('themes/assets/js/modules/materialadmin/libs/jquery-ui/jquery-ui.js')!!}"></script>

<script>
 $( function() {
        $.widget( "custom.combobox", {
            _create: function() {
                this.wrapper = $( "<span>" )
                    .addClass( "custom-combobox" )
                    .insertAfter( this.element );
                this.element.hide();
                this._createAutocomplete();
                this._createShowAllButton();
            },
            _createAutocomplete: function() {
                var selected = this.element.children( ":selected" ),
                    value = selected.val() ? selected.text() : "";
                this.input = $( "<input>" )
                    .appendTo( this.wrapper )
                    .val( value )
                    .attr( "title", "" )
                    .addClass( "custom-combobox-input ui-widget ui-widget-content ui-state-default ui-corner-left" )
                    .autocomplete({
                        delay: 0,
                        minLength: 0,
                        source: $.proxy( this, "_source" )
                    })
                    .tooltip({
                        classes: {
                            "ui-tooltip": "ui-state-highlight"
                        }
                    });
                this._on( this.input, {
                    autocompleteselect: function( event, ui ) {
                        ui.item.option.selected = true;
                        this._trigger( "select", event, {
                            item: ui.item.option
                        });
                    },
                    autocompletechange: "_removeIfInvalid"
                });
            },
            _createShowAllButton: function() {
                var input = this.input,
                    wasOpen = false;
                $( "<a>" )
                    .attr( "tabIndex", -1 )
                    .attr( "title", "Show All Items" )
                    .tooltip()
                    .appendTo( this.wrapper )
                    .button({
                        icons: {
                            primary: "ui-icon-triangle-1-s"
                        },
                        text: false
                    })
                    .removeClass( "ui-corner-all" )
                    .addClass( "custom-combobox-toggle ui-corner-right" )
                    .on( "mousedown", function() {
                        wasOpen = input.autocomplete( "widget" ).is( ":visible" );
                    })
                    .on( "click", function() {
                        input.trigger( "focus" );
                        // Close if already visible
                        if ( wasOpen ) {
                            return;
                        }
                        // Pass empty string as value to search for, displaying all results
                        input.autocomplete( "search", "" );
                    });
            },
            _source: function( request, response ) {
                var matcher = new RegExp( $.ui.autocomplete.escapeRegex(request.term), "i" );
                response( this.element.children( "option" ).map(function() {
                    var text = $( this ).text();
                    if ( this.value && ( !request.term || matcher.test(text) ) )
                        return {
                            label: text,
                            value: text,
                            option: this
                        };
                }) );
            },
            _removeIfInvalid: function( event, ui ) {
                // Selected an item, nothing to do
                if ( ui.item ) {
                    return;
                }
                // Search for a match (case-insensitive)
                var value = this.input.val(),
                    valueLowerCase = value.toLowerCase(),
                    valid = false;
                this.element.children( "option" ).each(function() {
                    if ( $( this ).text().toLowerCase() === valueLowerCase ) {
                        this.selected = valid = true;
                        return false;
                    }
                });
                // Found a match, nothing to do
                if ( valid ) {
                    return;
                }
                // Remove invalid value
                this.input
                    .val( "" )
                    .attr( "title", value + " didn't match any item" )
                    .tooltip( "open" );
                this.element.val( "" );
                this._delay(function() {
                    this.input.tooltip( "close" ).attr( "title", "" );
                }, 2500 );
                this.input.autocomplete( "instance" ).term = "";
            },
            _destroy: function() {
                this.wrapper.remove();
                this.element.show();
            }
        });
        $( "#combobox" ).combobox();
        $( "#toggle" ).on( "click", function() {
            $( "#combobox" ).toggle();
        });
    } );

</script>
@stop 

