@extends('layout.adminLayout')

@section("content")
<!-- BEGIN CONTENT-->
<div id="content">
    <section>

        <div class="section-body contain-lg">
            <div class="row">

                <!-- BEGIN ADD CONTACTS FORM -->
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-head style-primary">
                            <header>Profile</header>
                        </div>
                        <form class="form form-validate"  role="form" method="post" novalidate="novalidate" enctype="multipart/form-data">
                            <!-- BEGIN DEFAULT FORM ITEMS -->
                            <div class="card-body style-primary form-inverse">
                                <div class="row">
                                    <div class="col-xs-12">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group floating-label">
                                                    <input type="text" class="form-control input-lg" id="full_name" name="full_name" required value="{{((Request::old('first_name'))? Request::old('first_name') : $result->first_name)}}">
                                                    <label for="full_name">First name</label>
                                                </div>
                                            </div><!--end .col -->
                                            <div class="col-md-6">
                                                <div class="form-group floating-label">
                                                    <input type="text" class="form-control input-lg" id="username" name="username" required value="{{((Request::old('username'))? Request::old('username') : $result->username)}}" readonly>
                                                    <label for="username">Username</label>

                                                </div>
                                            </div><!--end .col -->

                                        </div><!--end .row -->

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group floating-label">
                                                    <input type="password" class="form-control input-lg" id="password" name="password" >
                                                    <label for="password">Password</label>
                                                </div>
                                            </div><!--end .col -->
                                            <div class="col-md-6">
                                                <div class="form-group floating-label">
                                                    <input type="password" class="form-control input-lg" id="confirm_password" data-rule-equalTo="#password" name="confirm_password">
                                                    <label for="confirm_password">Confirm password</label>

                                                </div>
                                            </div><!--end .col -->

                                        </div><!--end .row -->
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group floating-label">
                                                    <input type="file" class="" id="pic" name="pic" onchange="document.getElementById('preview_avatar').src = window.URL.createObjectURL(this.files[0])" accept="image/*">
                                                    <label for="pic">Avatar [{{env('PROFILE_PIC_WIDTH')}}x{{env('PROFILE_PIC_HEIGHT')}}]</label>
                                                </div>
                                            </div><!--end .col -->
                                            <img id="preview_avatar" src="{{asset('uploads/user_files')}}/{{Auth::user()->pic}}" alt="" witdth="60" height="60"/>
                                        </div>



                                    </div><!--end .col -->
                                </div><!--end .row -->
                            </div><!--end .card-body -->
                            <!-- END DEFAULT FORM ITEMS -->

                            <!-- BEGIN FORM TABS -->
                            <div class="card-head style-primary">
                                <ul class="nav nav-tabs tabs-text-contrast tabs-accent" data-toggle="tabs">
                                    <li class="active"><a href="#contact">CONTACT INFO</a></li>
                                </ul>
                            </div><!--end .card-head -->
                            <!-- END FORM TABS -->

                            <!-- BEGIN FORM TAB PANES -->
                            <div class="card-body tab-content">
                                <div class="tab-pane active" id="contact">
                                    <div class="row">
                                        <div class="col-md-12">

                                            <div class="row">

                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <input type="email" class="form-control" id="email" name="email" required value="{{((Request::old('email'))? Request::old('email') : $result->email)}}">
                                                        <label for="email">Email</label>
                                                    </div><!--end .form-group -->
                                                </div><!--end .col -->

                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <input type="text" class="form-control" id="phone" name="phone" data-inputmask="'mask':'(999) 999-9999'" value="{{((Request::old('phone'))? Request::old('phone') : $result->phone)}}">
                                                        <label for="phone">Phone</label>
                                                    </div>
                                                </div><!--end .col -->

                                            </div><!--end .row -->

                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <input type="text" class="form-control" id="address1" name="address1" value="{{((Request::old('address1'))? Request::old('address1') : $result->address1)}}">
                                                        <label for="address1">Address1</label>
                                                    </div>
                                                </div><!--end .col -->
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <input type="text" class="form-control" id="address2" name="address2" value="{{((Request::old('address2'))? Request::old('address2') : $result->address2)}}">
                                                        <label for="address2">Address2</label>
                                                    </div>
                                                </div><!--end .col -->
                                            </div><!--end .row -->
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <input type="text" class="form-control" id="city" name="city" value="{{((Request::old('city'))? Request::old('city') : $result->city)}}">
                                                        <label for="city">City</label>
                                                    </div>
                                                </div><!--end .col -->
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <input type="text" class="form-control" id="state" name="state" value="{{((Request::old('state'))? Request::old('state') : $result->state)}}">
                                                        <label for="state">State</label>
                                                    </div>
                                                </div><!--end .col -->

                                            </div><!--end .row -->
                                            <div class="row">

                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <input type="text" class="form-control" id="zip" name="zip" value="{{((Request::old('zip'))? Request::old('zip') : $result->zip)}}">
                                                        <label for="zip">Zip</label>
                                                    </div>
                                                </div><!--end .col -->

                                            </div><!--end .row -->
                                        </div><!--end .col -->
                                        <div class="col-md-4">

                                        </div><!--end .col -->
                                    </div><!--end .row -->
                                </div><!--end .tab-pane -->

                            </div><!--end .card-body.tab-content -->
                            <!-- END FORM TAB PANES -->

                            <!-- BEGIN FORM FOOTER -->
                            <div class="card-actionbar">
                                <div class="card-actionbar-row">
                                    
                                    <button type="submit" class="btn btn-flat btn-accent">{!!$submit!!}</button>
                                </div><!--end .card-actionbar-row -->
                            </div><!--end .card-actionbar -->
                            <!-- END FORM FOOTER -->

                        </form>
                    </div><!--end .card -->
                </div><!--end .col -->
                <!-- END ADD CONTACTS FORM -->

            </div><!--end .row -->
        </div><!--end .section-body -->
    </section>

</div><!--end #content-->
<!-- END CONTENT -->
@stop

@section("style")
<style>

</style>
 
@stop
@section("script")

<script>
        
</script>
@stop

