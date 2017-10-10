<!DOCTYPE HTML>
<html>
    <head>
        <style>
            body{
                width: 192px;
                margin: 0px;
            }
            @page {
                size: 192px 120px;       
                margin: 0px;
                margin-bottom: 0px;
                padding-bottom: 0px;
            }
            .page-break {
                page-break-after: always;
            }
        </style>
    </head>
    <body>
        @if(count($order))
        @foreach($order as $orders)
            <table style="text-align: center; width: 100%;border: 2px solid black;">
                <tbody>
                    <tr>
                        <td colspan="3" style="font-size: 12px; font-family: Arial, Helvetica, sans-serif; font-weight: bold; padding: 1px 3px;height: 30px;">@if($orders->upc != 0) {{$orders->upc}} @endif</td> 

                    </tr>
                    <tr>
                        <?php $orders->price = number_format((float) ($orders->price / $orders->quantity), 2, '.', ''); ?>
                        <td colspan="3" style="font-size: 25px; font-family: Arial, Helvetica, sans-serif; font-weight: bold; padding: 1px 3px;height: 30px;">{{$orders->price}}</td> 

                    </tr>
                    <tr>
                        <td  style="font-size: 9px; font-family: Arial, Helvetica, sans-serif; font-weight: bold; padding: 1px 1px; text-transform: uppercase;">{{$orders->brand_name}}</td>
                        <td  style="font-size: 9px; font-family: Arial, Helvetica, sans-serif; font-weight: bold; padding: 1px 1px; text-transform: uppercase;">{{$orders->size}}ML</td>
                        <td  style="font-size: 9px; font-family: Arial, Helvetica, sans-serif; font-weight: bold; padding: 1px 1px; text-transform: uppercase;">{{$orders->code}}</td>
                    </tr>
                </tbody>
            </table>
        <div class="page-break"></div>
        @endforeach
        <!---------- top header ent ------------->

        @endif
    </body>
</html>

