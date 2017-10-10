<!DOCTYPE html>
<html lang="en">
    <meta http-equiv="content-type" content="text/html;charset=UTF-8" />
    <head>
        <title>LIQORD</title>	
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
    <body>
          <!-- BEGIN HEADER-->
      
        <!-- END HEADER-->
        <!-- BEGIN BASE-->
        <div id="base">
            <!-- BEGIN OFFCANVAS LEFT -->
            <div class="offcanvas">
            </div>
            <!--end .offcanvas-->
            <!-- END OFFCANVAS LEFT -->
            @yield("content")



          
            <!-- END MENUBAR -->	
        </div><!--end #base-->	
        <!-- END BASE -->

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

       

        <script src="{{asset('themes/assets/js/modules/materialadmin/libs/jquery-validation/dist/jquery.validate.min.js')}}"></script>
        <script src="{{asset('themes/assets/js/modules/materialadmin/libs/jquery-validation/dist/additional-methods.min.js')}}"></script>

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

   
        </script>
        @yield("script");

        <!-- END JAVASCRIPT -->

    </body>

</html>