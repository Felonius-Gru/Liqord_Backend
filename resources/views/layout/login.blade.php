<!DOCTYPE html>
<html lang="en">
    <meta http-equiv="content-type" content="text/html;charset=UTF-8" />
    <head>
        <title>{{$title}}</title>
        <!-- BEGIN META -->
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="keywords" content="">
        <meta name="description" content="{{env('PAGE_TITLE')}}">
        <!-- END META -->
        <!-- BEGIN STYLESHEETS -->
        <link href='http://fonts.googleapis.com/css?family=Roboto:300italic,400italic,300,400,500,700,900' rel='stylesheet' type='text/css'/>
        <link type="text/css" rel="stylesheet" href="{{asset('themes/assets/css/modules/materialadmin/css/theme-default/bootstrap94be.css?1422823238')}}" />

        <link type="text/css" rel="stylesheet" href="{{asset('themes/assets/css/modules/materialadmin/css/theme-default/materialadminb0e2.css?1422823243')}}" />

        <link type="text/css" rel="stylesheet" href="{{asset('themes/assets/css/modules/materialadmin/css/theme-default/font-awesome.min753e.css?1422823239')}}" />

        <link type="text/css" rel="stylesheet" href="{{asset('themes/assets/css/modules/materialadmin/css/theme-default/material-design-iconic-font.mine7ea.css?1422823240')}}" />
        <link type="text/css" rel="stylesheet" href="{{asset('themes/assets/css/modules/materialadmin/css/theme-default/libs/toastr/toastr9fec.css?1422823374')}}" />
        <!-- Favicon -->
        <link rel="shortcut icon" href="{{asset('themes/favicon.png')}}">
    </head>
    <body class="menubar-hoverable header-fixed ">
        <!-- BEGIN LOGIN SECTION -->
	<section class="section-account">
		<div class="img-backdrop" style="background-image: url('{{asset("themes/assets/img/modules/materialadmin/background.jpg")}}')"></div>
                <div class="spacer"></div>
		<div class="card contain-sm style-transparent">
			<div class="card-body">
				<div class="row">
					<div class="col-sm-6">
                                            
                                                 <span class="text-lg text-bold text-primary"><img src="{{asset('themes/assets/img/modules/materialadmin/logo1.png')}}" alt="Liqord"></span>
                                               
                                                <br/>
                                                </span>
                                                <br/>
                                                <form class="form form-validate floating-label" action="{{asset('admin/login')}}" accept-charset="utf-8" method="post" autocomplete="false" novalidate="novalidate">
                                                    <div class="form-group">
                                                        <input type="text" class="form-control" id="username" name="username" autocomplete="off" required value="{{$username}}">
                                                        <label for="username">Username</label>
                                                    </div>
                                                    <div class="form-group">
                                                        <input type="password" class="form-control" id="password" name="password" autocomplete="off" required value="{{$password}}">
                                                        <label for="password">Password</label>

                                                        <p class="help-block"><a href="#" id="forgot_password" data-toggle="modal" data-target="#action_forgot_password">Forgot password?</a></p>
                                                    </div>
                                                    <br/>
                                                    <div class="form-group">
                                                        <small class="text-danger">
                                                            @if($errors->has())
                                                                {{ implode('', $errors->all()) }}
                                                            @endif
                                                        </small>
                                                    </div>

                                                    <br/>
                                                    <div class="row">
                                                        <div class="col-xs-6 text-left">
                                                            <div class="checkbox checkbox-inline checkbox-styled">
                                                                <label>
                                                                    <input type="checkbox" name="remember" checked> <span>Remember me</span>
                                                                </label>
                                                            </div>
                                                        </div><!--end .col -->
                                                        <div class="col-xs-6 text-right">
                                                            <button class="btn btn-primary btn-raised ink-reaction" type="submit">Login</button>
                                                        </div><!--end .col -->
                                                    </div><!--end .row -->
                                                </form>
					</div><!--end .col -->
                                        <br/>
                                        <br/>
					<div class="col-sm-5 col-sm-offset-1 text-center">
						<br><br>
						<h3 class="text-light">
							Sign up here
						</h3>
						<a class="btn btn-block btn-raised btn-primary" href="{{asset('admin/registration')}}">STORE ADMIN</a> 
						
					</div><!--end .col -->
				</div><!--end .row -->
			</div><!--end .card-body -->
		</div><!--end .card -->
	</section>
	<!-- END LOGIN SECTION -->
        <!-- BEGIN ACTION MARKUP -->
        <div class="modal fade" id="action_forgot_password" tabindex="-1" role="dialog" aria-labelledby="actionLabel" aria-hidden="true">
                <div class="modal-dialog">
                        <div class="modal-content">
                                <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                        <h4 class="modal-title">Forgot password?</h4>
                                </div>
                                <form action="{{asset('admin/users/forgotPassword')}}" method="post" class="form-horizontal" role="form">
                                        <div class="modal-body">
                                            <div class="form-group">

                                                    <div class="col-sm-12">
                                                            <input type="text" name="username" id="username" class="form-control" placeholder="User Name"><div class="form-control-line"></div>
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
        <!-- END SIMPLE MODAL MARKUP -->

        <!-- BEGIN JAVASCRIPT -->
        <script src="{{asset('themes/assets/js/modules/materialadmin/libs/jquery/jquery-1.11.2.min.js')}}"></script>
        <script src="{{asset('themes/assets/js/modules/materialadmin/libs/jquery/jquery-migrate-1.2.1.min.js')}}"></script>
        <script src="{{asset('themes/assets/js/modules/materialadmin/libs/bootstrap/bootstrap.min.js')}}"></script>
        <script src="{{asset('themes/assets/js/modules/materialadmin/libs/spin.js/spin.min.js')}}"></script>
        <script src="{{asset('themes/assets/js/modules/materialadmin/libs/autosize/jquery.autosize.min.js')}}"></script>
        <script src="{{asset('themes/assets/js/modules/materialadmin/libs/nanoscroller/jquery.nanoscroller.min.js')}}"></script>
        <script src="{{asset('themes/assets/js/modules/materialadmin/core/cache/63d0445130d69b2868a8d28c93309746.js')}}"></script>
        <script src="{{asset('themes/assets/js/modules/materialadmin/core/demo/Demo.js')}}"></script>
        <script src="{{asset('themes/assets/js/modules/materialadmin/libs/jquery-validation/dist/jquery.validate.min.js')}}"></script>
        <script src="{{asset('themes/assets/js/modules/materialadmin/libs/jquery-validation/dist/additional-methods.min.js')}}"></script>
        <script src="{{asset('themes/assets/js/modules/materialadmin/libs/toastr/toastr.js')}}"></script>
        <script>
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
			toastr.success("{{Session::get('success')}}",'Success');
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
			toastr.error("{{Session::get('fail')}}",'Error');
		@endif
        </script>

        <!-- END JAVASCRIPT -->
    </body>
</html>