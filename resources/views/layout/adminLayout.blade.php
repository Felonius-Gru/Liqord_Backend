<?php
use App\Order;
//$new = Order::where("is_new", 1)->where("include_shelf_tag",0)->count('id');
$new = Order::where("is_new", 1)->count('id');
?>
<!DOCTYPE html>
<html lang="en">
    <meta http-equiv="content-type" content="text/html;charset=UTF-8" />
    <head>
        <title>{{$page_title}}</title>	
        <!-- BEGIN META -->
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="keywords" content="">
        <meta name="description" content="">
        <!-- END META -->
        <!-- BEGIN STYLESHEETS -->
        <link href='http://fonts.googleapis.com/css?family=Roboto:300italic,400italic,300,400,500,700,900' rel='stylesheet' type='text/css'/>
        <link type="text/css" rel="stylesheet" href="{{asset('themes/assets/css/modules/materialadmin/css/theme-default/bootstrap94be.css?1422823238')}}" />
        <link type="text/css" rel="stylesheet" href="{{asset('themes/assets/css/modules/materialadmin/css/theme-default/materialadminb0e2.css?1422823243')}}" />
        <link type="text/css" rel="stylesheet" href="{{asset('themes/assets/css/modules/materialadmin/css/theme-default/font-awesome.min753e.css?1422823239')}}" />
        <link type="text/css" rel="stylesheet" href="{{asset('themes/assets/css/modules/materialadmin/css/theme-default/material-design-iconic-font.mine7ea.css?1422823240')}}" />
        <link type="text/css" rel="stylesheet" href="{{asset('themes/assets/css/modules/materialadmin/css/theme-default/libs/rickshaw/rickshawd56b.css?1422823372')}}" />
        <link type="text/css" rel="stylesheet" href="{{asset('themes/assets/css/modules/materialadmin/css/theme-default/libs/morris/morris.core5e0a.css?1422823370')}}" />
        <link type="text/css" rel="stylesheet" href="{{asset('themes/assets/css/modules/materialadmin/css/theme-default/libs/toastr/toastr9fec.css?1422823374')}}" />
        <!--<link type="text/css" rel="stylesheet" href="{{asset('themes/assets/css/modules/materialadmin/css/theme-default/libs/bootstrap-tagsinput/bootstrap-tagsinputdee9.css')}}" />-->

        <!-- Datatable CSS -->
        <link rel="stylesheet" type="text/css"
              href="{{asset('themes/assets/datatables/datatables.bootstrap.css')}}">
        <!-- END STYLESHEETS -->
        <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- Custome CSS For pages -->
        @yield("style")
    </head>
    <body class="menubar-hoverable header-fixed menubar-pin  " @if($active=='videos') oncontextmenu="return false;" @endif>
          <!-- BEGIN HEADER-->
          <header id="header">
            <div class="headerbar">
                <!-- Brand and toggle get grouped for better mobile display -->
                <div class="headerbar-left">
                    <ul class="header-nav header-nav-options">
                                <li class="header-nav-brand" >
                                        <div class="brand-holder">
                                                <a href="@if(Auth::user()->role=='Admin') {{asset('admin/home')}} @endif">
                                                   <span class="text-lg text-bold text-primary"><img src="{{asset('themes/assets/img/modules/materialadmin/logo1.png')}}"  alt="" style="max-width: 190px;"></span>
                                                </a>
                                        </div>
                                </li>
                                <li>
                                        <a class="btn btn-icon-toggle menubar-toggle" data-toggle="menubar" href="javascript:void(0);">
                                                <i class="fa fa-bars"></i>
                                        </a>
                                </li>
                        </ul>
                </div>

                <!-- Collect the nav links, forms, and other content for toggling -->
                <div class="headerbar-right">
                    <ul class="header-nav header-nav-options">



                    </ul><!--end .header-nav-options -->
                    <ul class="header-nav header-nav-profile">
                      <li class="dropdown">
                                        <a href="javascript:void(0);" class="dropdown-toggle ink-reaction" data-toggle="dropdown">
                                            @if(Auth::user()->pic)
                                           <img src="{{asset('uploads/user_files')}}/{{Auth::user()->pic}}" alt="" />                              
                                           @else
                                           <img src="{{asset('uploads/user_files/user.png')}}" width="40px" height="40px" alt="" />
                                           @endif    
                                                <span class="profile-info">
                                                        {{Auth::user()->full_name}}
                                                        <small>{{Auth::user()->role}}</small>
                                                </span>
                                        </a>
                                        <ul class="dropdown-menu animation-dock">
                                                <li class="dropdown-header">Config</li>
                                                <li><a href="{{asset('admin/profile')}}">My profile</a></li>
                                                <li class="divider"></li>
                                                <li><a href="{{asset('admin/logout')}}"><i class="fa fa-fw fa-power-off text-danger"></i> Logout</a></li>
                                        </ul><!--end .dropdown-menu -->
                                </li><!--end .dropdown -->
                    </ul><!--end .header-nav-profile -->          
                </div><!--end #header-navbar-collapse -->
            </div>
        </header>
        <!-- END HEADER-->
        <!-- BEGIN BASE-->
        <div id="base">
            <!-- BEGIN OFFCANVAS LEFT -->
            <div class="offcanvas">
            </div>
            <!--end .offcanvas-->
            <!-- END OFFCANVAS LEFT -->
            @yield("content")



            <!-- BEGIN MENUBAR-->
            <div id="menubar" class="menubar-inverse ">
                <div class="menubar-fixed-panel">
                    <div>
                        <a class="btn btn-icon-toggle btn-default menubar-toggle" data-toggle="menubar" href="javascript:void(0);">
                            <i class="fa fa-bars"></i>
                        </a>
                    </div>
                    <div class="expanded">
                        <a href="{{asset('admin/home')}}">
                            <span class="text-lg text-bold text-primary "></span>
                        </a>
                    </div>
                </div>
                <div class="menubar-scroll-panel">
                    <!-- BEGIN MAIN MENU -->
                     <ul id="main-menu" class="gui-controls">
                                   
                                    <!-- BEGIN DASHBOARD -->
                                    <li>
                                            <a href="{{asset('admin/home')}}" @if($active == 'dashboard') class="active" @endif>
                                                    <div class="gui-icon"><i class="md md-home"></i></div>
                                                    <span class="title">Dashboard</span>
                                            </a>
                                    </li><!--end /menu-li -->
                                    @if (Auth::user()->role=="Admin" )
                                    <li>
                                        <a href="{{asset('admin/users')}}" @if($active == 'users') class="active" @endif>
                                           <div class="gui-icon"><i class="fa fa-users"></i></div>
                                            <span class="title">Customers</span>



                                        </a>
                                    </li>
                                    @endif
                                    @if (Auth::user()->role=="Admin" || Auth::user()->role=="StoreAdmin" )
                                    <li>
                                        <a href="{{asset('admin/orders')}}"  @if($active == 'orders') class="active" @endif >
                                           <div class="gui-icon"><i class="md md-shopping-cart"></i></div>
                                            <span class="title">Orders
                                                @if (Auth::user()->role == "Admin")  
                                                @if($new != 0)
                                                <span class="badge style-accent">new</span>
                                                @endif
                                                @endif
                                            </span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{asset('admin/shelf/tagorders')}}"  @if($active == 'tagorders') class="active" @endif >
                                           <div class="gui-icon"><i class="md md-add-shopping-cart"></i></div>
                                            <span class="title">Shelf Tags (Orders)</span>
                                        </a>
                                    </li>
                                    <li>
                                     <a href="{{asset('admin/shelf/taginventory')}}"  @if($active == 'taginventory') class="active" @endif >
                                           <div class="gui-icon"><i class="md md-add-shopping-cart"></i></div>
                                         <span class="title">Shelf Tags (Inventory) </span>
                                     </a>
                                     </li>
                                    @endif
                                    @if ( Auth::user()->role=="StoreAdmin")
                                    <li>
                                        <a href="{{asset('admin/storelocations')}}" @if($active == 'store_locations') class="active" @endif>
                                           <div class="gui-icon"><i class="md md-location-on"></i></div>
                                            <span class="title">Store Locations</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{asset('admin/tabusers')}}" @if($active == 'storeusers') class="active" @endif>
                                           <div class="gui-icon"><i class="md md-devices"></i></div>
                                            <span class="title">Store Users</span>
                                        </a>
                                    </li>
                                        <li class="gui-folder">
                                        <a>
                                            <div class="gui-icon"><i class="md md-payment"></i></div>
                                            <span class="title">Billing</span>
                                        </a>

                                        <ul>
                                            <li><a href="{{asset('admin/billing_section')}}"  @if($active_sub == 'billing_section') class="active" @endif ><span class="title">Next Billing Dates</span></a></li> 
                                            <li><a href="{{asset('admin/billing_history')}}" @if($active_sub == 'billing_history') class="active" @endif><span class="title">Billing History</span></a></li> 
                                            <li><a href="{{asset('admin/declined_charges')}}" @if($active_sub == 'declined_charges') class="active" @endif><span class="title">Declined Charges</span></a></li>  
                                        </ul>
                                    </li> 
                                    @endif
                                    <!-- END DASHBOARD -->


                                    @if (Auth::user()->role=="Admin")
<!--                                    <li>
                                        <a href="{{asset('admin/importExport')}}" @if($active == 'import') class="active" @endif>
                                           <div class="gui-icon"><i class="md md-announcement"></i></div>
                                            <span class="title">Upload LARA file</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{asset('admin/upcExport')}}" @if($active == 'upc') class="active" @endif>
                                           <div class="gui-icon"><i class="md md-announcement"></i></div>
                                            <span class="title">Upload UPC file</span>
                                        </a>
                                    </li>-->
                                     <li class="gui-folder">
                                        <a>
                                            <div class="gui-icon"><i class="md md-payment"></i></div>
                                            <span class="title">Billing</span>
                                        </a>

                                        <ul>
                                            <li><a href="{{asset('admin/billing')}}"  @if($active == 'billing') class="active" @endif ><span class="title">Bill Settings</span></a></li> 
                                            <li><a href="{{asset('admin/decline_charges')}}" @if($active_sub == 'decline_charges') class="active" @endif><span class="title">Declined Charges</span></a></li> 
                                            <li><a href="{{asset('admin/charges_month')}}" @if($active_sub == 'charges_month') class="active" @endif><span class="title">Charges this month</span></a></li> 
                                        </ul>
                                    </li> 
                                    <li>
                                        <a href="{{asset('admin/items')}}" @if($active == 'items') class="active" @endif>
                                           <div class="gui-icon"><i class="md md-shop"></i></div>
                                            <span class="title">Item List</span>
                                        </a>
                                    </li>

                                    <li>
                                        <a href="{{asset('admin/locations')}}" @if($active == 'locations') class="active" @endif>
                                           <div class="gui-icon"><i class="md md-location-on"></i></div>
                                            <span class="title">Locations</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{asset('admin/stores')}}" @if($active == 'stores') class="active" @endif>
                                           <div class="gui-icon"><i class="md md-store"></i></div>
                                            <span class="title">Stores</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{asset('admin/cutofftime')}}" @if($active == 'cutoffTime') class="active" @endif>
                                           <div class="gui-icon"><i class="md md-timer-off"></i></div>
                                            <span class="title">Cut off Time</span>
                                        </a>
                                    </li>
                                    @endif        
                     </ul>
                    <div class="menubar-foot-panel">
                         <small class="no-linebreak hidden-folded">
                                            <span class="opacity-75">Copyright &copy; {{date('Y')}}</span> <strong>Tuna Group</strong>
                                    </small>
                    </div>
                </div><!--end .menubar-scroll-panel-->
            </div><!--end #menubar-->
            <!-- END MENUBAR -->	
        </div><!--end #base-->	
        <!-- END BASE -->
        <!-- BEGIN ACTION MARKUP -->
        <div class="modal fade" id="action" tabindex="-1" role="dialog" aria-labelledby="actionLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title" id="actionLabel"></h4>
                    </div>
                    <div class="modal-body">
                        <p id="actionMessage"></p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">No</button>
                        <a href="#" id="actionURL" class="btn btn-primary">Yes</a>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->
        <!-- END SIMPLE MODAL MARKUP -->


        <!-- BEGIN Loader MARKUP -->
        <div class="loading" id="loaderModal">
            <div id='toster_status'><img src="{{asset('themes/assets/img/modules/materialadmin/loader.gif')}}" /><br/>Please wait... </div>	
        </div><!-- /.modal -->
      
        <!-- END Loader MODAL MARKUP -->
        <style>
            /* Absolute Center CSS Spinner */
            .loading {
                position: fixed;
                top: 0;
                left: 0;
                background: rgba(0,0,0,0.6);
                z-index: 1000;
                width: 100%;
                height: 100%;
                display:none;
                z-index: 9999;
            }
            #toster_status
            {
                text-align:center; 
                color:white;
                top:calc(50% - 9px) !important;
                left:calc(50% - 128px) !important;
                position:absolute;
            }

        </style>

        <!-- BEGIN JAVASCRIPT -->	
        @if(!isset($disable_core_js))
        <script src="{{asset('themes/assets/js/modules/materialadmin/libs/jquery/jquery-1.11.2.min.js')}}"></script>
        @endif
        <script src="{{asset('themes/assets/js/modules/materialadmin/libs/jquery/jquery-migrate-1.2.1.min.js')}}"></script>
        <script src="{{asset('themes/assets/js/modules/materialadmin/libs/bootstrap/bootstrap.min.js')}}"></script>
        <script src="{{asset('themes/assets/js/modules/materialadmin/libs/spin.js/spin.min.js')}}"></script>
        <script src="{{asset('themes/assets/js/modules/materialadmin/libs/autosize/jquery.autosize.min.js')}}"></script>
        <script src="{{asset('themes/assets/js/modules/materialadmin/libs/moment/moment.min.js')}}"></script>
        <script src="{{asset('themes/assets/js/modules/materialadmin/core/cache/ec2c8835c9f9fbb7b8cd36464b491e73.js')}}"></script>
        <script src="{{asset('themes/assets/js/modules/materialadmin/libs/jquery-knob/jquery.knob.min.js')}}"></script>
        <script src="{{asset('themes/assets/js/modules/materialadmin/libs/sparkline/jquery.sparkline.min.js')}}"></script>
        <!--<script src="{{asset('themes/assets/js/modules/materialadmin/libs/nanoscroller/jquery.nanoscroller.min.js')}}"></script>-->
        <script src="{{asset('themes/assets/js/modules/materialadmin/core/cache/43ef607ee92d94826432d1d6f09372e1.js')}}"></script>
        <script src="{{asset('themes/assets/js/modules/materialadmin/libs/rickshaw/rickshaw.min.js')}}"></script>
        <script src="{{asset('themes/assets/js/modules/materialadmin/core/cache/63d0445130d69b2868a8d28c93309746.js')}}"></script>
        <script src="{{asset('themes/assets/js/modules/materialadmin/libs/toastr/toastr.js')}}"></script>
        <!-- jQuery -->
        <script src="{!!asset('themes/assets/js/modules/jquery_ui/jquery-ui.min.js')!!}"></script>

        <!-- Summernote Plugin -->
        <script src="{{asset('themes/assets/js/modules/materialadmin/libs/summernote/summernote.min.js')}}"></script>

        @if($active == "dashboard") 
        <script src="{{asset('themes/assets/js/modules/materialadmin/core/demo/Demo.js')}}"></script>
<!--                <script src="{{asset('themes/assets/js/modules/materialadmin/core/demo/DemoDashboard.js')}}"></script>	-->

        @endif


        <script src="{{asset('themes/assets/js/modules/materialadmin/libs/jquery-validation/dist/jquery.validate.min.js')}}"></script>
        <script src="{{asset('themes/assets/js/modules/materialadmin/libs/jquery-validation/dist/additional-methods.min.js')}}"></script>

        <!-- Datatable JS -->
        <script src="{{asset('themes/assets/datatables/js/jquery.dataTables.min.js')}}"></script>
        <script src="{{asset('themes/assets/datatables/js/datatables.bootstrap.js')}}"></script>
        <script src="{{asset('themes/assets/datatables/js/handlebars.js')}}"></script>
        <script>
        //  Notification if the process was a success
        @if (Session::has('success'))
        // Create new Notification
        toastr.clear();
        toastr.options = {
        "closeButton": "true",
                "progressBar": "false",
                "debug": "false",
                "positionClass": "toast-top-right",
                "showDuration": "330",
                "hideDuration": "330",
                "timeOut":  "10000",
                "extendedTimeOut": "1000",
                "showEasing": "swing",
                "hideEasing": "swing",
                "showMethod": "slideDown",
                "hideMethod": "slideUp",
                "onclick": null
        }
toastr.success("{{Session::get('success')}}", 'Success');
        @endif

        // Notification if the process was a failure
        @if (Session::has('fail'))
        // Create new Notification
        toastr.clear();
        toastr.options = {
        "closeButton": "true",
                "progressBar": "false",
                "debug": "false",
                "positionClass": "toast-top-right",
                "showDuration": "330",
                "hideDuration": "330",
                "timeOut":  "10000",
                "extendedTimeOut": "1000",
                "showEasing": "swing",
                "hideEasing": "swing",
                "showMethod": "slideDown",
                "hideMethod": "slideUp",
                "onclick": null
        }
toastr.error("{{Session::get('fail')}}", 'Error');
        @endif

        // Notification if the process was a failure due to data posted
        @if ($errors -> any())
        // Create new Notification
        toastr.clear();
        toastr.options = {
        "closeButton": "true",
                "progressBar": "false",
                "debug": "false",
                "positionClass": "toast-top-right",
                "showDuration": "330",
                "hideDuration": "330",
                "timeOut":  "10000",
                "extendedTimeOut": "1000",
                "showEasing": "swing",
                "hideEasing": "swing",
                "showMethod": "slideDown",
                "hideMethod": "slideUp",
                "onclick": null
        }
toastr.error("{!! implode('',$errors->all('<li>:message</li>')); !!}", 'Error');
        @endif
        $(".form-loader").validate({
submitHandler: function(form) {
$(".loading").show();
        form.submit();
}
});
        function action_confirm(url, label, message)
        {
        $("#actionLabel").html(label);
                $("#actionMessage").html(message);
                $("#actionURL").attr("href", url);
                $("#action").modal();
                return false;
        }
        </script>
        @yield("script");

        <!-- END JAVASCRIPT -->

    </body>

</html>