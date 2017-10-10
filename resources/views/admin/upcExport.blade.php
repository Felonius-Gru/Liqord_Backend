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
                    <div class="card">
                        <div class="card-head style-primary">
                            <header>Import UPC File</header>
                            <div class="tools" style="color: yellow">
                                 NB: Upload LARA File before uploading UPC File
                         </div> 
                        </div>
                        <!-- END CONTACT DETAILS HEADER -->

                        <!-- BEGIN CONTACT DETAILS -->
                        <div class="card-tiles">
                            <div class="hbox-md col-md-12">
                                <div class="hbox-column col-md-9">
                                    <!--<div class="row">-->

                                    <form action="{{ URL::to('admin/upcExcel') }}" class="form-horizontal" method="post" enctype="multipart/form-data" onsubmit="start_toster();">

<!--                                       <div class="row">
                                            <div class="col-sm-12">
                                                <div class="form-group">
                                                     <label for="quantity">Upload LARA File</label>
                                                     <input type="file" name="import_file" required/>

                                                </div>
                                            </div>
                                        </div>-->
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <div class="form-group">
                                                    <label for="quantity">Upload UPC File</label>
                                                    <input type="file" name="sku_file" required/>

                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">

                                            <button  class="btn btn-primary">Import File</button>
                                        </div>
                                    </form>

                                    <!--</div>-->
                                </div><!--end .hbox-column -->
                                <!-- END CONTACTS COMMON DETAILS -->

                            </div><!--end .hbox-md -->
                        </div><!--end .card-tiles -->
                    </div><!--end .card -->
                </div><!--end .col -->
                <!-- END ADD CONTACTS FORM -->

            </div><!--end .row -->
        </div><!--end .section-body -->
    </section>

</div><!--end #content-->

@stop
@section("script")
<!--<script src="{{asset('themes/assets/js/modules/materialadmin/libs/menu/jquery.magnific-popupd424.js')}}"></script>-->

<script>
    function start_toster() {
        $("#loaderModal").show();

//    $.magnificPopup.open({
//        items: {
//            src: "<div id='toster_status' style='text-align:center; color:white;'><img src='{{asset('themes/assets/img/modules/materialadmin/loading.gif')}}' alt=\"Liqord\" /><br/>Loading</div>"
//        },
//        type: 'inline',
//        closeOnBgClick: false,
//        showCloseBtn: false,
//        closeOnContentClick: false,
//        enableEscapeKey: false
//    }, 0
//            );
    }

</script>
@stop 
