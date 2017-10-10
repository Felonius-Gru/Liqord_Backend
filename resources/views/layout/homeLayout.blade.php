<?php
    use App\Menu;
    use App\Common;
    if(Auth::check()){
            $menu = Menu::where("is_active", "1")->where("dont_show_after_login", "0")->where("parent", "0")->orderBy("order_no", "asc")->get();

        }
        else{
            $menu = Menu::where("is_active", "1")->where("is_login", "0")->where("parent", "0")->orderBy("order_no", "asc")->get();

        }
    $common          = Common::where("is_active", 1)->get();
    $result = Menu::where("is_active", "1")->where("is_login", "0")->where("parent", "0")->orderBy("order_no", "asc")->get();

    $google_analytics = Common::where("id", "google_analytics")->first();
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8" />
    <title>{{$page_title}}</title>
    <meta name="description" content="" />
    <meta name="author" content="" />
    <link rel="shortcut icon" href="{{asset('themes/home/img/favicon.png')}}"" type="image/x-icon" />

    <!-- Mobile Specific Meta -->
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <!-- Style CSS -->
    <link rel="stylesheet" href="{{asset('themes/home/css/font-awesome.min.css')}}" />
    <link rel="stylesheet" href="{{asset('themes/home/css/bootstrap.css')}}" />
    <link rel="stylesheet" href="{{asset('themes/home/css/icomoon.css')}}" />
    <link rel="stylesheet" href="{{asset('themes/home/css/screen.css')}}" />
    <link type="text/css" rel="stylesheet" href="{{asset('themes/assets/css/modules/materialadmin/css/theme-default/libs/toastr/toastr9fec.css?1422823374')}}" />
    <style>
        .isotope-item img{
            height: 390px !important;
        }
        .breadcrumb {
            padding: 8px 15px;
            margin-bottom: 20px;
            list-style: none;
            background-color: #f5f5f5;
            border-radius: 4px;
            font-family: "Roboto", monospace;
            font-size: 14px;
            line-height: 1.42857143;
            color: #333333;
        }
        .breadcrumb ul, ol {
            margin-top: 0;
            margin-bottom: 10px;
        }
        .breadcrumb ol {
            display: block;
            list-style-type: decimal;
            -webkit-margin-before: 1em;
            -webkit-margin-after: 1em;
            -webkit-margin-start: 0px;
            -webkit-margin-end: 0px;
            -webkit-padding-start: 40px;
        }
        .breadcrumb > li {
            display: inline-block;
        }
        .pagination {
        display: inline-block;
        padding-left: 0;
        margin: 24px 0;
        border-radius: 2px;
      }
      .pagination > li {
        display: inline;
      }
      .pagination > li > a,
      .pagination > li > span {
        position: relative;
        float: left;
        padding: 4.5px 14px;
        line-height: 1.846153846;
        text-decoration: none;
        color: #0aa89e;
        background-color: #ffffff;
        border: 1px solid #dddddd;
        margin-left: -1px;
      }
      .pagination > li:first-child > a,
      .pagination > li:first-child > span {
        margin-left: 0;
        border-bottom-left-radius: 2px;
        border-top-left-radius: 2px;
      }
      .pagination > li:last-child > a,
      .pagination > li:last-child > span {
        border-bottom-right-radius: 2px;
        border-top-right-radius: 2px;
      }
      .pagination > li > a:hover,
      .pagination > li > span:hover,
      .pagination > li > a:focus,
      .pagination > li > span:focus {
        color: #06605a;
        background-color: #e5e6e6;
        border-color: #dddddd;
      }
      .pagination > .active > a,
      .pagination > .active > span,
      .pagination > .active > a:hover,
      .pagination > .active > span:hover,
      .pagination > .active > a:focus,
      .pagination > .active > span:focus {
        z-index: 2;
        color: #ffffff;
        background-color: #0aa89e;
        border-color: #0aa89e;
        cursor: default;
      }
      .pagination > .disabled > span,
      .pagination > .disabled > span:hover,
      .pagination > .disabled > span:focus,
      .pagination > .disabled > a,
      .pagination > .disabled > a:hover,
      .pagination > .disabled > a:focus {
        color: #969c9c;
        background-color: #ffffff;
        border-color: #dddddd;
        cursor: not-allowed;
      }
      .pagination-lg > li > a,
      .pagination-lg > li > span {
        padding: 10px 16px;
        font-size: 17px;
      }
      .pagination-lg > li:first-child > a,
      .pagination-lg > li:first-child > span {
        border-bottom-left-radius: 2px;
        border-top-left-radius: 2px;
      }
      .pagination-lg > li:last-child > a,
      .pagination-lg > li:last-child > span {
        border-bottom-right-radius: 2px;
        border-top-right-radius: 2px;
      }
      .pagination-sm > li > a,
      .pagination-sm > li > span {
        padding: 5px 10px;
        font-size: 12px;
      }
      .pagination-sm > li:first-child > a,
      .pagination-sm > li:first-child > span {
        border-bottom-left-radius: 0px;
        border-top-left-radius: 0px;
      }
      .pagination-sm > li:last-child > a,
      .pagination-sm > li:last-child > span {
        border-bottom-right-radius: 0px;
        border-top-right-radius: 0px;
      }
      .pager {
        padding-left: 0;
        margin: 24px 0;
        list-style: none;
        text-align: center;
      }
      .pager li {
        display: inline;
      }
      .pager li > a,
      .pager li > span {
        display: inline-block;
        padding: 5px 14px;
        background-color: #ffffff;
        border: 1px solid #dddddd;
        border-radius: 0px;
      }
      .pager li > a:hover,
      .pager li > a:focus {
        text-decoration: none;
        background-color: #e5e6e6;
      }
      .pager .next > a,
      .pager .next > span {
        float: right;
      }
      .pager .previous > a,
      .pager .previous > span {
        float: left;
      }
      .pager .disabled > a,
      .pager .disabled > a:hover,
      .pager .disabled > a:focus,
      .pager .disabled > span {
        color: #969c9c;
        background-color: #ffffff;
        cursor: not-allowed;
      }

      .social-image {
      	width:24px;
      	margin:-13px 5px 0px 5px;
      	border-radius: 2px;
      }

    </style>
    @yield('header')

</head>
<body id="front-page" data-smooth-scroll="on">
	<form class="global-search-form" action="{{asset('home/search/educationalArchive')}}">
		<div class="container">
			<input type="text" name="search" class="search-input js-input" placeholder="Type here ..." />
		</div>
	</form>

	<!-- Page Wrapper -->
	<div id="page">
		<!-- Register Poup -->
		<div class="register-popup">
			<div class="popup-wrapper">
				<span class="close-popup-btn icon-cross"></span>

                                <form id="register-form" method="post" action='{{asset("home/registration/user")}}'>
					<div class="section-header">
						<h1>Register</h1>
					</div>

					<label>
						<span>First Name *</span>
						<input type="text" class="js-input" name="first_name" />
					</label>

					<label>
						<span>Last Name *</span>
						<input type="text" class="js-input" name="last_name" />
					</label>

					<label>
						<span>E-mail address *</span>
						<input type="text" class="js-input" name="email" />
					</label>

					<label>
						<span>Password *</span>
						<input type="password" class="js-input" name="password" />
					</label>

					<label>
						<span>Confirm Password *</span>
						<input type="password" class="js-input" name="password_confirmation" />
					</label>

					<div class="btn-wrapper">
						<button class="btn theme-btn-3">Register</button>
					</div>

					<div class="section-header small">
						<h1><span>or</span></h1>
					</div>

<!--					<div class="social-buttons">
						<a href="#" class="facebook">facebook</a>
						<a href="#" class="google-plus">google+</a>
					</div>

					<p><a href="#" class="forgot-password">Forgot password?</a></p>-->
					<p>Have an account already? <a href="#" class="login-btn">Login here</a></p>
				</form>

				<form id="login-form" method="post" action='{{asset("login")}}'>
					<div class="section-header">
						<h1>Login</h1>
					</div>

					<label>
						<span>User ID *</span>
						<input type="text" class="js-input" name="email" id='email' />
					</label>

					<label>
						<span>Password *</span>
						<input type="password" class="js-input" name="password" name="password"/>
					</label>

					<div class="btn-wrapper">
						<button class="btn theme-btn-3">Login</button>
					</div>

					<div class="section-header small">
						<h1><span>or</span></h1>
					</div>

<!--					<div class="social-buttons">
						<a href="#" class="facebook">facebook</a>
						<a href="#" class="google-plus">google+</a>
					</div>

					<p><a href="#" class="forgot-password">Forgot password?</a></p>-->
					<p>Login as office administrator ? <a href="{{asset('admin')}}" class="">Click here</a></p>
				</form>
			</div>
		</div>

		<!-- Header -->
		<header>
			<div class="container">
				<div class="row">
                                    <div class="col-xs-4 col-sm-2" style="top: -25px !important;">
						<a class="brand" href="{{asset('/')}}">
							<img src="{{asset('themes/home/img/identity.png')}}"')}}" alt="identity" />
						</a>
					</div>

					<div class="col-xs-8 col-sm-10">
						<div class="action-bar">
							@foreach($common as $item)
							<ul class="social-block">
								<!-- <li><a href="https://www.facebook.com/Wellness-Dentistry-Network-931690036906421/"><i class="fa fa-facebook"></i></a></li>
								<li><a href="https://twitter.com/WellDentNet"><i class="fa fa-twitter"></i></a></li> -->
								<li>
									<a href="{{$item->value}}">
										@if($item->id == "facebook")
											<img src="{{ asset("themes/home/img/facebook.png") }}"
													class="social-image" />
										@else
											<i class="fa fa-{{$item->id}}"></i>
										@endif
									</a>
								</li>
							</ul>
							@endforeach
                                                        @if(Auth::check())
<!--							<span class="search-box-toggle no-select">
								<i class="icon fa fa-search"></i>
							</span>-->
                                                        @endif

							<span class="menu-toggle no-select">Menu
								<span class="hamburger">
									<span class="menui top-menu"></span>
									<span class="menui mid-menu"></span>
									<span class="menui bottom-menu"></span>
								</span>
							</span>

                                                        @if (Auth::check())
							<a class="my-account" href="{{asset("home/profile")}}"><span class="icon icon-MyAccount"></span><span class="popup">Hi {{Auth::user()->full_name}}</span></a>

                                                        <a class="my-account-logout" href="{{asset('logout')}}">Logout</a>
                                                        @else
                                                         <a class="my-account-login" id="reg-pop" href="#">Member Login</a> @endif
						</div>
					</div>
				</div>
			</div>

			<nav>
				<ul>
					@foreach($menu as $item)
						<?php
							$current_url = Request::fullUrl();
							$menu_url = asset($item->url);
						?>
					<li @if($current_url == $menu_url || $current_url . "/" == $menu_url)
							class="current-menu-item"
						@endif
						><a href="{{$item->url}}">{{$item->name}}</a>
					</li>
					@endforeach
					<!-- <li class="@if($active=='home')current-menu-item @endif"><a href="{{asset('/')}}">Dashboard</a></li>
                    @if(Auth::check())
                    <li class="@if($active=='categories')current-menu-item @endif"><a href="{{asset('home/resourceLibrary')}}">Office Forms (Tools) Library</a></li>
                    <li class="@if($active=='documents')current-menu-item @endif"><a href="{{asset('home/documents')}}">Documents</a></li>
                    @endif
					<li class="@if($active=='members')current-menu-item @endif"><a href="{{asset('members')}}">Directory of Members</a></li>
                    <li class="@if($active=='join')current-menu-item @endif"><a href="{{asset('join')}}">JOIN NOW</a></li>
                    @if(!Auth::check())
                    <li class="@if($active=='courses')current-menu-item @endif"><a href="{{asset('courses')}}">COURSES</a></li>

                    <li class="@if($active=='doug')current-menu-item @endif"><a href="{{asset('doug')}}">FROM DR. DOUG</a></li>
                    @endif
					@if(Auth::check())
                    <li class="@if($active=='forum')current-menu-item @endif"><a href="{{asset('forum')}}">Forum</a></li>
					<li class="@if($active=='quiz')current-menu-item @endif"><a href="{{asset('home/quiz')}}">Test Your Knowledge</a></li>
                    @endif
                    <li class="@if($active=='contact')current-menu-item @endif" ><a href="{{asset('contact')}}">CONTACT US</a></li> -->

				</ul>
			</nav>
		</header>

		<!-- Main Content -->
		<div class="content-wrapper">
			@yield('content')
		</div>

		<!-- Footer -->
                <footer class="fixed" style="opacity: 0;color: black;font-family: serif">
			<div class="container">
				<div class="footer-wrapper">
					<!-- <img src="{{asset('themes/home/img/director.png')}}" alt="footer brand" class="footer-brand" /> -->

					<!-- <ul class="social-block">
						<li><a href="https://www.facebook.com/Wellness-Dentistry-Network-931690036906421/"><i class="fa fa-facebook"></i></a></li>
								<li><a href="https://twitter.com/WellDentNet"><i class="fa fa-twitter"></i></a></li>
					</ul> -->
					<ul class="social-block">
						@foreach($common as $item)
							<li>
								<a href="{{$item->value}}">
									@if($item->id == "facebook")
										<img src="{{ asset("themes/home/img/facebook.png") }}"
												class="social-image"/>
									@else
									<i class="fa fa-{{$item->id}}"></i>
									@endif
								</a>
							</li>
						@endforeach
					</ul>

					<div class="main-area">
                                            <div class="menu" style="text-align: center;">
							<ul>
								<!-- {{-- <li><a href="{{asset('/')}}">HOME</a></li>
								<li><a href="{{asset('courses')}}">COURSE OFFERINGS</a></li>
								<li><a href="{{asset('doug')}}">FROM DR. DOUG</a></li> --}}
								<li><a href="{{asset('contact')}}">CONTACT US</a></li>
								<li><a href="{{ asset("home/conference") }}">Materials download/email capture for current seminar</a></li> -->
								
                                                                @foreach($menu as $item)
                                                                        <?php
                                                                                $current_url = Request::fullUrl();
                                                                                $menu_url = asset($item->url);
                                                                        ?>
                                                                <li @if($current_url == $menu_url || $current_url . "/" == $menu_url)
                                                                                class="current-menu-item"
                                                                        @endif
                                                                        ><a href="{{$item->url}}">{{$item->name}}</a>
                                                                </li>
                                                                @endforeach
							</ul>
						</div>

						<!-- <div class="row">
							<div class="col-md-4">
								<div class="footer-widget widget_info">
									<p><span>Douglas G. Thompson</span><br />D.D.S.<br /></p>
								</div>
							</div>

							<div class="col-md-4">
								<div class="footer-widget widget_contact">
									<p>3684 Maple<br />Bloomfield Hills, MI 48301</p>
								</div>
							</div>


						</div> -->
					</div>

					<div class="copyrights">
						<p>Copyright {{date("Y")}}. Designed by <a href="//thetunagroup.com" target="blank">Tuna Group</a></p>
					</div>
				</div>
			</div>
		</footer>
	</div>

	<!-- Scripts -->
	<script src="{{asset('themes/home/js/jquery.js')}}"></script>
	<script src="{{asset('themes/home/js/jquery-ui.js')}}"></script>
	<script src="{{asset('themes/home/js/lightbox.js')}}"></script>
	<script src="{{asset('themes/home/js/velocity.js')}}"></script>
	<script src="{{asset('themes/home/js/modernizr.js')}}"></script>
<!--	<script src="{{asset('themes/home/js/smooth-scroll.js')}}"></script>-->
        <script src="{{asset('themes/home/js/imagesloaded.js')}}"></script>
	<script src="{{asset('themes/home/js/bxslider.js')}}"></script>
	<script src="{{asset('themes/home/js/options.js')}}"></script>
        <script src="{{asset('themes/assets/js/modules/materialadmin/libs/toastr/toastr.js')}}"></script>

@if(!is_null($google_analytics))
  {!! $google_analytics->value !!}
@endif

        <script>
                @if (Session::has('success'))
			// Create new Notification
			toastr.clear();
				toastr.options = {
				  "closeButton": "true",
				  "progressBar": "false",
				  "debug": "false",
				  "positionClass": "toast-bottom-full-width",
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
				  "positionClass": "toast-bottom-full-width",
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

		// Notification if the process was a failure due to data posted
		@if ($errors->any())
			// Create new Notification
			toastr.clear();
				toastr.options = {
				  "closeButton": "true",
				  "progressBar": "false",
				  "debug": "false",
				  "positionClass": "toast-bottom-full-width",
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
			toastr.error("{!! implode('',$errors->all('<li>:message</li>')); !!}",'Error');
		@endif

        </script>
        @yield('footer')
</body>
</html>