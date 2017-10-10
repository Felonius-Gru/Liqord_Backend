<?php

namespace App\Http\Controllers;

use Auth;
use DB;
use Input;
use Session;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;
use CodeItNow\BarcodeBundle\Utils\BarcodeGenerator;
use Hash;
use Response;
use Datatables;
use Mail;
use DateTime;
use PDF_Code39;

/** Models */
use App\User;
use App\Common;
use App\Items;
use App\Order;
use App\OrderItem;
use App\Stores;
use App\Locations;
use App\StoreLocation;

class ShelfTagOrderController extends Controller {

    public function tagorders() {
        if (!HelperController::checkPrivilege()) {
            return Redirect::to('admin');
        }
        $page_title = "Shelf-Tag Orders";
        $breadcrumb = "<li class='active'>Shelf-Tag Orders</li>";
        $header = "<header>Tag Orders Placed</header>";
        $active = "tagorders";
        $active_sub = "";
        $stores = Stores::where('is_active', 1)->get();
        if (Auth::user()->role == "Admin") {

            $location = Locations::where('is_active', 1)->get();
        } elseif (Auth::user()->role == "StoreAdmin") {
            $storeid = Auth::user()->store_id;
            $location = StoreLocation:: join('locations', 'store_location.location_id', '=', 'locations.id')
                    ->select('locations.id', 'locations.name')->where('store_id', $storeid)
                    ->get();
        }
        return view("admin.tagorders")->with("page_title", $page_title)
                        ->with("breadcrumb", $breadcrumb)
                        ->with("header", $header)
                        ->with("active", $active)
                        ->with("stores", $stores)
                        ->with("location", $location)
                        ->with("active_sub", $active_sub);
    }

    /**
     * Shelf Tag result page - ajax
     */
//    public function tagordersResults() {
    public function tagordersResults($search = false, $storeid = false, $locationid = false, $startdate = false, $enddate = false) {
        if ($search == 1) {
            $storeid = explode('=', $storeid);
            $locationid = explode('=', $locationid);
            $sdate = explode('=', $startdate);
            $edate = explode('=', $enddate);
            $query = DB::table('order')->join('order_item', 'order.id', '=', 'order_item.order_id');
            
            $query->join('store_location', function($join) {
                    $join->on('order.store_id', '=', 'store_location.store_id')
                    ->on('order.location_id', '=', 'store_location.location_id');
                });
                $query->where('store_location.print_label', 0);
            if ($storeid[1])
                $query->where('order.store_id', $storeid[1]);
            if ($locationid[1])
                $query->where('order.location_id', $locationid[1]);
            if ($sdate[1])
                $query->where('order.created_at', '>=', $sdate[1]);
            if ($edate[1])
                $query->where('order.created_at', '<=', $edate[1]);
            $query->where('order.include_shelf_tag', 1);
            $query->select(['order.id', 'order.store_id', 'order.location_id', 'order.created_at']);
            $query->groupBy('order.id');
//            $query->groupBy('order.date');
            $query->orderBy('order.created_at', 'asc');
            $results = $query->get();
            $results = collect($results);
        }else {
            if (Auth::user()->role == "Admin") {

                $results = Order::join('store_location', function($join) {
                            $join->on('order.store_id', '=', 'store_location.store_id')
                            ->on('order.location_id', '=', 'store_location.location_id');
                        })
                        ->where('order.include_shelf_tag', 1)
                        ->where('store_location.print_label', 0)
                        ->select('order.*')    
                        ->orderBy('order.created_at', 'desc');
            
//            if ($storeid[1])
//                $query->where('order.store_id', $storeid[1]);
//            if ($locationid[1])
//                $query->where('order.location_id', $locationid[1]);
//            if ($sdate[1])
//                $query->where('order.created_at', '>=', $sdate[1]);
//            if ($edate[1])
//                $query->where('order.created_at', '<=', $edate[1]);
//            $query->where('order.include_shelf_tag', 1);
//            $query->select(['order.id', 'order.store_id', 'order.location_id', 'order.created_at']);
//            $query->groupBy('order.id');
////            $query->groupBy('order.date');
//            $query->orderBy('order.created_at', 'asc');
//            $results = $query->get();
//            $results = collect($results);
//        }else {
//            if (Auth::user()->role == "Admin") {
//                
//                
//                $results = Order::where('include_shelf_tag', 1)->orderBy('created_at', 'desc');
            } elseif (Auth::user()->role = "StoreAdmin") {
                $storeid = Auth::user()->store_id;
                $results = Order::where('store_id', $storeid)->where('include_shelf_tag', 1)->orderBy('created_at', 'desc');
            }
//        $results = Items::select(['id', 'category','ada_no','code','brand_name','proof','created_at'])->orderBy('id', 'desc');
        }
        return Datatables::of($results)
                        ->editColumn('store_id', function ($result) {
                            $store = Stores::where("id", $result->store_id)->select('name')->first();
                            return $store['name'];
                        })
                        ->editColumn('location_id', function ($result) {
                            $location = Locations::where("id", $result->location_id)->select('name')->first();
                            return $location['name'];
                        })
//                        ->editColumn('label_quantity', function ($result) {
//                           $total = OrderItem::where("order_id", $result->id)->where('shelf_tag_order',1)->sum('label_quantity');
//                           return $total;
//                        })
//                        ->editColumn('total_price', function ($result) {
//                           $total_price = OrderItem::where("order_id", $result->id)->where('shelf_tag_order',1)->sum('price');
//                           return $total_price;
//                        })
                        ->editColumn('created_at', function ($result) {
                            return strip_tags(date('d M Y', strtotime($result->created_at)));
                        })
                        ->addColumn('action', function ($result) {
                            return "<a href='" . asset("admin/shelf/tagorders/view/{$result->id}") . "'>
                        <span class='fa fa-pencil-square fa-2x'></span>
                        </a>";
                        })
                        ->addColumn('print', function ($result) {
                            return "<a href='" . asset("admin/shelf/tagorders/print/{$result->id}") . "' target='_blank'>
                        <span class='fa fa-pencil-square fa-2x'></span>
                        </a>";
                        })
                        ->addColumn('csv', function ($result) {
                        return "<a href='" . asset("admin/shelf/tagorders/csv/{$result->id}") . "'>
                        <span class='fa fa-download fa-2x'></span>
                        </a>";
                        })
                        ->make(true);
    }

    /**
     * Tag view page
     */
    public function view($id) {
        if (!HelperController::checkPrivilege()) {
            return Redirect::to('admin');
        }

        $order = OrderItem::join('items', 'order_item.item_id', '=', 'items.id')
                ->select('order_item.id as order_item_id', 'order_item.quantity as quantity', 'order_item.label_quantity', 'order_item.change_price','order_item.description', 'order_item.price as price', 'items.*')
                ->where('shelf_tag_order', 1)
                ->where("order_id", $id)
                 ->paginate(15);
        foreach ($order as $orders) {
             if($orders->description  == 'Price Change') {
                  $orders->price =number_format((float)($orders->change_price),2,'.','');
             }
             else{
             $orders->price =number_format((float)($orders->price/$orders->quantity),2,'.','');
             }
             }
        if (is_null($order)) {
            Session::flash("fail", "No such order");
            return Redirect::to("admin/shelf/tagorders");
        }
        $order_table = Order::where('id', $id)->where('include_shelf_tag', 1)->first();

        $page_title = "Shelf-Tag Orders";
        $breadcrumb = "<li class='active'>Shelf-Tag Order Details</li>";
        $active = "tagorders";
        $active_sub = "";
        $header = "<header>Orders Detail</header>";

        return view("admin.tagView")->with("page_title", $page_title)
                        ->with("breadcrumb", $breadcrumb)
                        ->with("active", $active)
                        ->with("active_sub", $active_sub)
//                        ->with("order_lock",$order_lock)
                        ->with("header", $header)
                        ->with("order", $order);
    }

    /**
     * Tag update page
     */
    public function update(Request $request, $id = false) {
        if (!HelperController::checkPrivilege("post")) {
            return Redirect::to('admin');
        }
        $data = $request->all();
        $rules = array(
            'category' => 'required',
            'ada_no' => 'required',
            'code' => 'required',
            'brand_name' => 'required',
            'proof' => 'required',
            'size' => 'required',
            'base_price' => 'required',
            'license_price' => 'required'
        );

        if ($id == false) {
            $item = new Items;
        } else {
            $item = Items::find($id);
            if (is_null($item)) {
                Session::flash("fail", "No such item found");
                $request->flash();
                return Redirect::to("admin/items");
            }
        }

        $valid = Validator::make($data, $rules);

        if ($valid->passes()) {
            $item->category = HelperController::clean($data['category']);
            $item->ada_no = HelperController::clean($data['ada_no']);
            $item->code = HelperController::clean($data['code']);
            $item->brand_name = HelperController::clean($data['brand_name']);
            $item->proof = HelperController::clean($data['proof']);
            $item->size = HelperController::clean($data['size']);
            $item->pack_size = HelperController::clean($data['pack_size']);
            $item->base_price = HelperController::clean($data['base_price']);
            $item->license_price = HelperController::clean($data['license_price']);
            $item->minimum_shelf_price = HelperController::clean($data['minimum_shelf_price']);

            $save = $item->save();
            if ($save) {
                if ($id == false) {
                    Session::flash("success", "Items created");
                    return Redirect::to("admin/items");
                } else {
                    Session::flash("success", "Items updated");
                    return Redirect::back();
                }
            } else {
                // Something went wrong.
                $request->flash();
                Session::flash("fail", "Opps... Something went wrong");
            }

            return Redirect::back();
        } else {
            // Something went wrong.
            return Redirect::back()->withErrors($valid);
        }
    }

    /**
     * Delete Item
     */
    public function deleteItem($id) {
        if (!HelperController::checkPrivilege()) {
            return Redirect::to('admin');
        }

        $item = Items::find($id);
        $save = $item->delete();
        if ($save) {
            Session::flash("success", "Item deleted");
        } else {
            // Something went wrong.
            Session::flash("fail", "Opps... Something went wrong");
        }

        return Redirect::back();
    }
    
     public function printall($id) {
        if (!HelperController::checkPrivilege()) {
            return Redirect::to('admin');
        }

        $orders = OrderItem::join('items', 'order_item.item_id', '=', 'items.id')
                ->select('order_item.id as order_item_id', 'order_item.quantity as quantity', 'order_item.price as price', 'order_item.label_quantity', 'order_item.change_price as new_price','order_item.description', 'items.*')
                ->where('shelf_tag_order', 1)
                ->where("order_id", $id)
                ->get();
        if (is_null($orders)) {
            Session::flash("fail", "No such order");
            return Redirect::to("admin/shelf/tagorders");
        }
        
        //FPDF
        
        require_once(app_path() .'/PDF/code39.php');
        $width = 50.8;
        $height = 31.75;
        
         $totwidth = 203.2;
         $totheight = 198;
        

        $pdf = new PDF_Code39('l', 'mm', array($totwidth,$totheight));
        $margin = 1;
        
        $pdf->AddPage();
        
        $x = 0;$i=1;$y=0;
        foreach($orders as $order){           
            $sku = $order->upc;
            $barcode = new BarcodeGenerator();
            $barcode->setText($sku);
            $barcode->setType(BarcodeGenerator::Upca);
            $barcode->setScale(18);
    //        $barcode->setSize(300);
            $barcode->setThickness(40);
            $barcode->setFontSize(150);
            $code = $barcode->generate();
            $barcode_image = "uploads/barcodes/{$sku}.png";
            file_put_contents($barcode_image, base64_decode($code));
       
            // BORDER
            $pdf->Rect( $x+1, $y+1 , $width - 2 , $height - 2);
            
            //UPC BARCODE
            $barcode_image = asset("uploads/barcodes") . "/$sku.png";
//            $pdf->Image($barcode_image, 20, 2, 14);
             $pdf->Image($barcode_image,$x+(($width-32)/2), $y+2, 32,6);
            //$pdf->Code39(2,2,$order->upc,true, 1,7);
            if($order->description  == 'Price Change') {
                  $order->price =number_format((float)($order->new_price),2,'.','');
             }
             else{
             $order->price =number_format((float)($order->price/$order->quantity),2,'.','');
             }
            $pdf->AddFont('Impact','','impact.php');
            $pdf->SetFont('Impact','', 42); 
             
            $width_price = $pdf->GetStringWidth($order->price);
            $pdf->text( $x+(($width-$width_price)/2),$y+22,$order->price, 4);
//            $a = strlen($order->price);
//            $pdf->text($a > 5 ? $x+9 : ($a > 4 ?  $x+13 : $x+15),$y+22,$order->price, 4);    
            
            //DESCRIPTION
            $first = substr($order->brand_name, 0, 7);
            $last = substr($order->brand_name, -2);  
            $order->brand_name = $first." ".$last;
            $pdf->SetFont('Impact', '', 10);
            $pdf->text($x+2,$y+28,$order->brand_name, 4);      
            
            //SIZE
            $pdf->SetFont('Impact', '', 10);
            $pdf->text($x+23,$y+28,$order->size."ML", 4);      
            
            //VENDOR
            $pdf->SetFont('Impact', '', 10);
            $pdf->text($x+40,$y+28,$order->ada_no, 4);    
            
            $x = $x + 50;
            if ($i % 4 == 0) {
                $y = $y + 32;
                $x=0;
            }
            $i++;   
            if (($i-1) % 24 == 0) {           
               $pdf->AddPage();  
                $x=0;$y=0;
            }
        }      
        $pdf->Output("I");
        ob_end_clean();
        dd();
    
     }
//    public function printall($id) {
//        if (!HelperController::checkPrivilege()) {
//            return Redirect::to('admin');
//        }
//
//        $orders = OrderItem::join('items', 'order_item.item_id', '=', 'items.id')
//                ->select('order_item.id as order_item_id', 'order_item.quantity as quantity', 'order_item.price as price', 'order_item.label_quantity', 'order_item.change_price as new_price','order_item.description', 'items.*')
//                ->where('shelf_tag_order', 1)
//                ->where("order_id", $id)
//                ->get();
//        if (is_null($orders)) {
//            Session::flash("fail", "No such order");
//            return Redirect::to("admin/shelf/tagorders");
//        }
//        
//        //FPDF
//        
//        require_once(app_path() .'/PDF/code39.php');
//        $width = 50.8;
//        $height = 31.75;
//
//
//
//        $pdf = new PDF_Code39('l', 'mm', array($width,$height));
//        $margin = 1;
//        
//        foreach($orders as $order){
//            $sku = $order->upc;
//            $barcode = new BarcodeGenerator();
//            $barcode->setText($sku);
//            $barcode->setType(BarcodeGenerator::Upca);
//            $barcode->setScale(18);
//    //        $barcode->setSize(300);
//            $barcode->setThickness(40);
//            $barcode->setFontSize(150);
//            $code = $barcode->generate();
//            $barcode_image = "uploads/barcodes/{$sku}.png";
//            file_put_contents($barcode_image, base64_decode($code));
//            
//            $pdf->AddPage();
//            
//            // BORDER
//            $pdf->Rect( $margin, $margin , $width - 2 , $height - 2);
//            
//            //UPC BARCODE
//            $barcode_image = asset("uploads/barcodes") . "/$sku.png";;
////            $pdf->Image($barcode_image, 20, 2, 14);
//             $pdf->Image($barcode_image,11, 2, 28,10);
//            //$pdf->Code39(2,2,$order->upc,true, 1,7);
//            if($order->description  == 'Price Change') {
//                  $order->price =number_format((float)($order->new_price),2,'.','');
//             }
//             else{
//             $order->price =number_format((float)($order->price/$order->quantity),2,'.','');
//             }
//         
//            $pdf->SetFont('Arial', 'B', 30);
//            $pdf->text(12,20,$order->price, 4);       
//            
//            //DESCRIPTION
//            $first = substr($order->brand_name, 0, 7);
//            $last = substr($order->brand_name, -2);  
//            $order->brand_name = $first." ".$last;
//            $pdf->SetFont('Arial', 'B', 9);
//            $pdf->text(2,28,$order->brand_name, 4);      
//            
//            //SIZE
//            $pdf->SetFont('Arial', 'B', 9);
//            $pdf->text(23,28,$order->size."ML", 4);      
//            
//            //VENDOR
//            $pdf->SetFont('Arial', 'B', 9);
//            $pdf->text(40,28,$order->ada_no, 4);      
//        }      
//        $pdf->Output("I");
//        ob_end_clean();
//        dd();
//        
//        
//        //DOM PDF
//        //$invoice = PDF::loadView('admin.printView', $invoiceInfo);
//        //[0,0,144.00, 90.00] for 2inch x 1.25 inch (array values are in pt)
//        //return $invoice->setPaper([0,0,144.00, 90.00])->setOrientation('landscape')->stream();
//    }

    public function printItem($id) {
        if (!HelperController::checkPrivilege()) {
            return Redirect::to('admin');
        }

        $order_item = OrderItem::join('items', 'order_item.item_id', '=', 'items.id')
                ->select('order_item.id as order_item_id', 'order_item.quantity as quantity', 'order_item.price as price', 'order_item.label_quantity','order_item.description', 'order_item.change_price as new_price', 'items.*')
                ->where('shelf_tag_order', 1)
                ->where("order_item.id", $id)
                ->first();


        $price = ($order_item->price / $order_item->quantity);

//        print_r($order_item);die;
        if (is_null($order_item)) {
            Session::flash("fail", "No such order");
            return Redirect::to("admin/shelf/tagorders");
        }
        $sku = $order_item->upc;
        $barcode = new BarcodeGenerator();
        $barcode->setText($sku);
        $barcode->setType(BarcodeGenerator::Upca);
        $barcode->setScale(18);
//        $barcode->setSize(300);
        $barcode->setThickness(40);
        $barcode->setFontSize(150);
        $code = $barcode->generate();
        if (!is_dir("uploads/barcodes")){
        mkdir("uploads/barcodes", 0777);}
        $barcode_image = "uploads/barcodes/{$sku}.png";
        file_put_contents($barcode_image, base64_decode($code));
        
         require_once(app_path() .'/PDF/code39.php');
         
          $width = 50.8;
          $height = 31.75;
        
         $totwidth = 203.2;
         $totheight = 198;
         

        $pdf = new PDF_Code39('l', 'mm', array($totwidth,$totheight));
        $margin = 1;
        
       
            $pdf->AddPage();
            
            // BORDER
            $pdf->Rect( $margin, $margin , $width - 2 , $height - 2);
            
            //UPC BARCODE
//            $barcode_image = asset("uploads/barcodes") . "/barcode.png";
            $barcode_image = asset("uploads/barcodes") . "/$sku.png";
//            $pdf->Image($barcode_image, 20, 2, 14);
            
             $pdf->Image($barcode_image,(($width-32)/2), 2, 32,6);
            //$pdf->Code39(2,2,$order_item->upc,true, 1,7);
            
            //PRICE
              if($order_item->description  == 'Price Change') {
                  $order_item->price =number_format((float)($order_item->new_price),2,'.','');
             }
             else{
             $order_item->price =number_format((float)($order_item->price/$order_item->quantity),2,'.','');
             }
//            $order_item->price =number_format((float)($order_item->price/$order_item->quantity),2,'.','');
            $pdf->AddFont('Impact','','impact.php');
            $pdf->SetFont('Impact', '', 42);
            
            $width_price = $pdf->GetStringWidth($order_item->price);
            $pdf->text((($width-$width_price)/2),22,$order_item->price, 4);
            
//            $a = strlen($order_item->price);
//            $pdf->text($a > 5 ? 9 : ($a > 4 ?  13 : 15),22,$order_item->price, 4);
//            $pdf->text(12,20,$order_item->price, 4);       
            
            //DESCRIPTION
            $first = substr($order_item->brand_name, 0, 7);
            $last = substr($order_item->brand_name, -2);  
            $order_item->brand_name = $first." ".$last;
            $pdf->SetFont('Impact', '', 10);
            $pdf->text(2,28,$order_item->brand_name, 4);      
            
            //SIZE
            $pdf->SetFont('Impact', '', 10);
            $pdf->text(23,28,$order_item->size."ML", 4);      
            
            //VENDOR
            $pdf->SetFont('Impact', '', 10);
            //$pdf->text(40,28,$order_item->code, 4);      
            $pdf->text(40,28,$order_item->ada_no, 4); 
            
        $pdf->Output("I");
        ob_end_clean();
        dd();
        
    }
    
     /**
     * Report  - Download CSV
     */
     public function csvDownload($id) {
         if (!HelperController::checkPrivilege()) {
            return Redirect::to('admin');
        }
    $headers = array(
        "Content-type" => "text/csv",
        "Content-Disposition" => "attachment; filename=file.csv",
        "Pragma" => "no-cache",
        "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
        "Expires" => "0"
    );

    $orders = OrderItem::join('items', 'order_item.item_id', '=', 'items.id')
                ->select('order_item.id as order_item_id', 'order_item.quantity as quantity', 'order_item.price as price', 'order_item.label_quantity', 'order_item.change_price as new_price','order_item.description', 'items.*')
                ->where('shelf_tag_order', 1)
                ->where("order_id", $id)
                ->get();
    $columns = array('SKU', 'PRICE', 'BRAND NAME', 'SIZE', 'DISTRIBUTOR');

    $callback = function() use ($orders, $columns)
    {
        $file = fopen('php://output', 'w');
        fputcsv($file, $columns);

        foreach($orders as $order) {
            if($order->description  == 'Price Change') {
                              $price =number_format((float)($order->new_price),2,'.','');
                         }
                         else{
                         $price =number_format((float)($order->price/$order->quantity),2,'.','');
                         }
            fputcsv($file, array($order->upc, $price, $order->brand_name, $order->size."ML", $order->ada_no));
        }
        fclose($file);
    };
    return Response::stream($callback, 200, $headers);
}
    
    
    public function csvDownloaddd($id) {
         if (!HelperController::checkPrivilege()) {
            return Redirect::to('admin');
        }
        
           $filename = "report-" . time() . ".csv";
            
            header('Content-Type: text/csv; charset=utf-8');
            header("Content-Disposition: attachment; filename={$filename}");
            // create a file pointer connected to the output stream
            $output = fopen('php://output', 'w');
            fputcsv($output, array('SKU', 'PRICE', 'BRAND NAME', 'SIZE', 'DISTRIBUTOR'));
            $skip = 0;
            $take = 50;
            while(true) {
                 $orders = OrderItem::join('items', 'order_item.item_id', '=', 'items.id')
                ->select('order_item.id as order_item_id', 'order_item.quantity as quantity', 'order_item.price as price', 'order_item.label_quantity', 'order_item.change_price as new_price','order_item.description', 'items.*')
                ->where('shelf_tag_order', 1)
                ->where("order_id", $id)
                         ->skip($skip)
                                ->take($take)
                ->get();
                 
                if(count($orders) > 0) {
                     $skip = $skip + $take;
                    foreach($orders as $order) {
                        $row = [];
                        
                         // SKU #
                         $row[] = " . $order->upc . ";
                        
                         // PRICE
                         if($order->description  == 'Price Change') {
                              $order->price =number_format((float)($order->new_price),2,'.','');
                         }
                         else{
                         $order->price =number_format((float)($order->price/$order->quantity),2,'.','');
                         }
                        $row[] = $order->price;
                        // BRAND NAME
                        $row[] = $order->brand_name;
                        // SIZE
                        $row[] = $order->size."ML";   
                        // DISTRIBUTOR
                        $row[] = $order->ada_no;    
                        fputcsv($output, $row);
                    }
                }
                else {
                    break;
                }
            }
    }

}
