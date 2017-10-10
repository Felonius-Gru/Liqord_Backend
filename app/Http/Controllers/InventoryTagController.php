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
use App\Products;
use App\Stores;
use App\Locations;
use App\StoreLocation;

class InventoryTagController extends Controller {

    public function tagorders() {
        if (!HelperController::checkPrivilege()) {
            return Redirect::to('admin');
        }
        $page_title = "Shelf-Tag Inventory";
        $breadcrumb = "<li class='active'>Shelf-Tag Inventory</li>";
        $header = "<header>Inventory Tag</header>";
        $active = "taginventory";
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
        return view("admin.inventorytag")->with("page_title", $page_title)
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
            
            $query = DB::table('products')->join('items', 'products.item_id', '=', 'items.id');
            
            $query->join('store_location', function($join) {
                    $join->on('products.store_id', '=', 'store_location.store_id')
                    ->on('products.location_id', '=', 'store_location.location_id');
                });
           $query->where('store_location.print_inventory_label', 0);
            if ($storeid[1])
                $query->where('products.store_id', $storeid[1]);
            if ($locationid[1])
                $query->where('products.location_id', $locationid[1]);
            if ($sdate[1])
                $query->where('products.created_at', '>=', $sdate[1]);
            if ($edate[1])
                $query->where('products.created_at', '<=', $edate[1]);
            $query->where('products.label', "None");
            $query->select(['products.id as id','products.store_id','products.location_id','products.created_at', 'products.shelf_number', 'products.bottle_position',  'items.brand_name', 'items.size', 'items.base_price', 'items.ada_no', 'items.size','items.code','items.upc']);
            $query->groupBy('products.location_id');
            $query->orderBy('products.created_at', 'asc');
            $results = $query->get();
            $results = collect($results);
        }else {
            if (Auth::user()->role == "Admin") {
                $results = Products::join('store_location', function($join) {
                            $join->on('products.store_id', '=', 'store_location.store_id')
                            ->on('products.location_id', '=', 'store_location.location_id');
                        })
                        ->where('products.label', "None")
                        ->where('store_location.print_inventory_label', 0)
                        ->select('products.*') 
                       ->groupBy('products.store_id', 'products.location_id')
                        ->orderBy('products.created_at', 'desc');
            
            } elseif (Auth::user()->role = "StoreAdmin") {
                $storeid = Auth::user()->store_id;
                $results = Products::where('store_id', $storeid) ->where('label', "None")->groupBy( 'location_id')->orderBy('created_at', 'desc');
            }
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

                        ->editColumn('created_at', function ($result) {
                            return strip_tags(date('d M Y', strtotime($result->created_at)));
                        })
                        ->addColumn('action', function ($result) {
                            return "<a href='" . asset("admin/shelf/taginventory/view/{$result->store_id}/{$result->location_id}") . "'>
                        <span class='fa fa-pencil-square fa-2x'></span>
                        </a>";
                        })
//                        ->addColumn('print', function ($result) {
//                            return "<a href='" . asset("admin/shelf/taginventory/print/{$result->id}") . "'>
//                        <span class='fa fa-pencil-square fa-2x'></span>
//                        </a>";
//                        })
                        ->make(true);
    }

    /**
     * Tag view page
     */
    public function view($strore_id,$location_id) {
        if (!HelperController::checkPrivilege()) {
            return Redirect::to('admin');
        }
        
        $products = Products::join('items', 'products.item_id', '=', 'items.id')
                    ->where('products.store_id', $strore_id)
                    ->where('products.location_id', $location_id)
                    ->where('products.label', "None")       
                    ->select(['products.id as product_id', 'products.shelf_number', 'products.bottle_position',  'items.brand_name', 'items.size', 'items.base_price', 'items.ada_no', 'items.size','items.code','items.upc'])
                    ->orderBy('products.shelf_number', 'ASC')
                    ->orderBy('products.bottle_position', 'ASC')
                    ->paginate(15);
    

        if (is_null($products)) {
            Session::flash("fail", "No such order");
            return Redirect::to("admin/shelf/taginventory");
        }
//        $order_table = Order::where('id', $id)->where('include_shelf_tag', 1)->first();

        $page_title = "Products Added In the Sheld";
        $breadcrumb = "<li class='active'>Product Details</li>";
        $active = "taginventory";
        $active_sub = "";
        $header = "<header>Product Detail</header>";

        return view("admin.inventoryView")->with("page_title", $page_title)
                        ->with("breadcrumb", $breadcrumb)
                        ->with("active", $active)
                        ->with("active_sub", $active_sub)
//                        ->with("order_lock",$order_lock)
                        ->with("header", $header)
                        ->with("products", $products);
    }

    /**
     * Tag update page
     */
//    public function update(Request $request, $id = false) {
//        if (!HelperController::checkPrivilege("post")) {
//            return Redirect::to('admin');
//        }
//        $data = $request->all();
//        $rules = array(
//            'category' => 'required',
//            'ada_no' => 'required',
//            'code' => 'required',
//            'brand_name' => 'required',
//            'proof' => 'required',
//            'size' => 'required',
//            'base_price' => 'required',
//            'license_price' => 'required'
//        );
//
//        if ($id == false) {
//            $item = new Items;
//        } else {
//            $item = Items::find($id);
//            if (is_null($item)) {
//                Session::flash("fail", "No such item found");
//                $request->flash();
//                return Redirect::to("admin/items");
//            }
//        }
//
//        $valid = Validator::make($data, $rules);
//
//        if ($valid->passes()) {
//            $item->category = HelperController::clean($data['category']);
//            $item->ada_no = HelperController::clean($data['ada_no']);
//            $item->code = HelperController::clean($data['code']);
//            $item->brand_name = HelperController::clean($data['brand_name']);
//            $item->proof = HelperController::clean($data['proof']);
//            $item->size = HelperController::clean($data['size']);
//            $item->pack_size = HelperController::clean($data['pack_size']);
//            $item->base_price = HelperController::clean($data['base_price']);
//            $item->license_price = HelperController::clean($data['license_price']);
//            $item->minimum_shelf_price = HelperController::clean($data['minimum_shelf_price']);
//
//            $save = $item->save();
//            if ($save) {
//                if ($id == false) {
//                    Session::flash("success", "Items created");
//                    return Redirect::to("admin/items");
//                } else {
//                    Session::flash("success", "Items updated");
//                    return Redirect::back();
//                }
//            } else {
//                // Something went wrong.
//                $request->flash();
//                Session::flash("fail", "Opps... Something went wrong");
//            }
//
//            return Redirect::back();
//        } else {
//            // Something went wrong.
//            return Redirect::back()->withErrors($valid);
//        }
//    }

    /**
     * Delete Item
     */
//    public function deleteItem($id) {
//        if (!HelperController::checkPrivilege()) {
//            return Redirect::to('admin');
//        }
//
//        $item = Items::find($id);
//        $save = $item->delete();
//        if ($save) {
//            Session::flash("success", "Item deleted");
//        } else {
//            // Something went wrong.
//            Session::flash("fail", "Opps... Something went wrong");
//        }
//
//        return Redirect::back();
//    }

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
//    }

    public function printItem($id) {
        if (!HelperController::checkPrivilege()) {
            return Redirect::to('admin');
        }

   $products = Products::join('items', 'products.item_id', '=', 'items.id')
                    ->where('products.id', $id)
                    ->where('products.label', "None")       
                    ->select(['products.id as product_id', 'products.shelf_number', 'products.bottle_position',  'items.brand_name', 'items.size', 'items.base_price', 'items.ada_no', 'items.size','items.code','items.upc'])
                    ->orderBy('products.shelf_number', 'ASC')
                    ->orderBy('products.bottle_position', 'ASC')
                    ->first();

//        print_r($products);die;
        if (is_null($products)) {
            Session::flash("fail", "No such order");
            return Redirect::to("admin/shelf/taginventory");
        }
        $sku = $products->upc;
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
             
            //$pdf->Code39(2,2,$products->upc,true, 1,7);
            
            //PRICE
           
           $products->price =number_format((float)($products->base_price),2,'.','');
             
//            $products->price =number_format((float)($products->price/$products->quantity),2,'.','');
            $pdf->AddFont('Impact','','impact.php');
           $pdf->SetFont('Impact', '', 42);
           $width_price = $pdf->GetStringWidth($products->price);
           $pdf->text( (($width-$width_price)/2),22,$products->price, 4);
//            $a = strlen($products->price);
//            $pdf->text($a > 5 ? 9 : ($a > 4 ?  13 : 15),22,$products->price, 4);
//            $pdf->text(12,20,$products->price, 4);       
            
            //DESCRIPTION
            $first = substr($products->brand_name, 0, 7);
            $last = substr($products->brand_name, -2);  
            $products->brand_name = $first." ".$last;
            $pdf->SetFont('Impact', '', 10);
            $pdf->text(2,28,$products->brand_name, 4);      
            
            //SIZE
            $pdf->SetFont('Impact', '', 10);
            $pdf->text(23,28,$products->size."ML", 4);      
            
            //VENDOR
            $pdf->SetFont('Impact', '', 10);
            //$pdf->text(40,28,$products->code, 4);      
            $pdf->text(40,28,$products->ada_no, 4); 
            
        $pdf->Output("I");
        ob_end_clean();
        dd();
        
    }

}
