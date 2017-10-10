
<!DOCTYPE HTML>
<html>
<head>
<!--[if lt IE 7 ]> <html xmlns="http://www.w3.org/1999/xhtml" class="ie ie6"> <![endif]-->
<!--[if IE 7 ]>    <html xmlns="http://www.w3.org/1999/xhtml" class="ie ie7"> <![endif]-->
<!--[if IE 8 ]>    <html xmlns="http://www.w3.org/1999/xhtml" class="ie ie8"> <![endif]-->
<!--[if IE 9 ]>    <html xmlns="http://www.w3.org/1999/xhtml" class="ie ie9"> <![endif]-->
    
	<meta charset="UTF-8">
  	<meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="SKYPE_TOOLBAR" content="SKYPE_TOOLBAR_PARSER_COMPATIBLE" />
	<title>  </title>

	<link href="style.css" rel="stylesheet" type="text/css">
   <link type="text/css" rel="stylesheet" href="{{asset('themes/assets/css/modules/materialadmin/css/theme-default/sfont.css')}}" />
    
    <style>
		body{background: #fff; color: #000; font-family: 'Open Sans', sans-serif !important; font-weight: 400; font-size: 16px;}
		ul{list-style-type: none; padding: 0; margin: 0;}
		.single-box{width:75%;height:25%;border: 1px solid #303030;margin-botton:20px;margin-top:20px}
		.two-box{width: 100%;}
                .outer-box{text-align: center;padding: 20px 20px}
    	/*.outer-box{padding: 70px 70px; border: 1px solid #303030; text-align: center; text-transform: uppercase; margin: 20px; float: left;}*/
    	.outer-box .bar-box img{width: 300px;}
		/*.three-box {display: inline-block; margin: 10px 10px; font-weight: bold;}*/
                .sub{float:left;padding:10px;display: inline-block;font-weight: bold;text-align:center}
		.price-box{font-size: 8em; font-weight: 700; line-height: 130px;text-align: center;}
		.bar-box ul li{text-align: center; font-size: 1.5em; font-weight: 700;}
    </style>
    
    
</head>
<body>
	@if(count($order))
        @foreach($order as $orders)
         
    <div class="single-box">
    
        <!---------- top header ------------->
            <div class="outer-box">
                <div class="bar-box">
                	<ul>
                    	<li>1111111111</li>
                    	
                    </ul>
                </div>
                <div class="price-box">{{$orders->price}}</div>
                <!--<div class="three-box">-->
                    
                    <div class="sub">{{$orders->brand_name}}</div>
                    <div class="sub">{{$orders->size}}mL</div>
                    <div class="sub">{{$orders->code}}</div>
                    
                <!--</div>-->
            </div>
    </div>
        @endforeach
        <!---------- top header ent ------------->
    
    @endif
</body>
</html>
