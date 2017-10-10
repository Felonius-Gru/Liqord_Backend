<?php

namespace App\Http\Controllers;

use Auth;
use Input;
use Session;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;
use CodeItNow\BarcodeBundle\Utils\BarcodeGenerator;
use Datatables;
use DateTime;
use File;
use Image;
use Hash;
use Response;
use DB;
use Mail;
use PDF_Code39;
/** Models */
use App\User;
use App\UserLog;
use App\Common;
use App\Devices;
use App\Stores;
use App\Products;
use App\Items;
use App\StoreLocation;
use App\Locations;
use App\Shelf;
use App\Cart;
use App\Order;
use App\OrderItem;
use App\CutOffTime;

class ApiController extends Controller {

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('guest');
    }

    //Fetching all stores to app

    public function getAllStores(Request $request) {
        $data = $request->all();
        $rules["device_id"] = "required";
        $valid = Validator::make($data, $rules);
        if ($valid->passes()) {
            $stores = Stores::select('stores.id as store_id', 'stores.name as store_name')->where('is_active', 1)
                            ->orderBy('store_name', 'asc')->get();
            if ($stores) {
                return Response::json(array("response" => "success", "message" => "Stores fetched.", "stores" => $stores));
            } else {
                return Response::json(array("response" => "failed", "message" => "Something went wrong !"));
            }
        } else {
            return Response::json(array("response" => "failed", "message" => "Please fill required fields !", "errors" => $valid->messages()));
        }
    }

    //Fetching Store Locations

    public function getstoreLocations(Request $request) {
        $data = $request->all();
        $rules = array('store_id' => 'required', 'device_id' => 'required');
        $valid = Validator::make($data, $rules);
        if ($valid->passes()) {
            $store_locations = StoreLocation::join('locations', 'store_location.location_id', '=', 'locations.id')
                            ->select(['store_location.id', 'store_location.location_id', 'locations.name as location_name'])
                            ->where("store_location.is_active", 1)
                            ->where('store_location.store_id', $data['store_id'])->get();
            if (!is_null($store_locations)) {
                return Response::json(array("response" => "success", "message" => "Locations fetched.", "store_locations" => $store_locations));
            } else {
                return Response::json(array("response" => "failed", "message" => "No locations are available for this store.", "store_locations" => $store_locations));
            }
        } else {
            return Response::json(array("response" => "failed", "message" => "Please fill required fields !", "errors" => $valid->messages()));
        }
    }

    //Login and device Registration
    public function loginPost(Request $request) {
        $data = $request->all();
        $rules = array('username' => 'required', 'password' => 'required', 'device_id' => 'required');
//        $rules["device_id"] = "required";
        $valid = Validator::make($data, $rules);
        if ($valid->passes()) {
            $username = $request->username;
            $user = User::where("username", $request->username)
//                    ->where('store_id', $request->store_id)
//                    ->where('location_id', $request->location_id)
                    ->where("is_active", 1)
//                          ->where('role','=','StoreAdmin')
//                          ->orWhere('role','=','StoreUser')
                    ->first(); //if returns a data process continue

            if (is_null($user)) {
                return Response::json(array('response' => 'failed', 'message' => 'Invalid credentials.'));
            }
            $id = $user->id;
            $store_id = $user->store_id;
            $location_id = $user->location_id;
            $role = $user->role;
//           
            $password = $request->password;
            if($role=="StoreAdmin")
            {
               $store_locations = StoreLocation::join('locations', 'store_location.location_id', '=', 'locations.id')
                            ->select(['store_location.location_id', 'locations.name as location_name'])
                            ->where("store_location.is_active", 1)
                            ->where('store_location.store_id', $store_id)->get(); 
            }
            else
            {
              $store_locations="";  
            }
            if (Auth::attempt(array('username' => $username, 'password' => $password, 'is_active' => 1, 'store_id' => $store_id))) {

                return Response::json(array('response' => 'success', 'message' => 'Login success.', 'user_id' => $id, 'store_id' => $store_id, "location_id" => $location_id,"role" => $role,"store_locations"=> $store_locations));
            }
            return Response::json(array('response' => 'failed', 'message' => 'Invalid credentials.'));
        } else {
            return Response::json(array("response" => "failed", "message" => "Please fill required fields !", "errors" => $valid->messages()));
        }
    }

    public function searchProducts(Request $request) {
        $data = $request->all();
        $rules = array('searchparam' => 'required', 'device_id' => 'required','store_id' => 'required','location_id' => 'required');

        if ($data['searchparam'] == "name" || $data['searchparam'] == "code") {
            $rules['search'] = "required";
        }
        $valid = Validator::make($data, $rules);

        if ($valid->passes()) {
            $searchparam = $request->searchparam;

            if ($data['searchparam'] == "name" || $data['searchparam'] == "code") {
                $search = $request->search;
            }
            if ($searchparam == "name") {
                $products = Items::where("items.brand_name", 'like', '%' . $data['search'] . '%')
                        ->where('is_active', 1)
                        ->select('items.id as item_id', 'items.category', 'items.size', 'items.base_price', 'items.brand_name','items.pack_size')
                        ->orderBy('items.brand_name', 'asc')
                        ->get();
            } elseif ($searchparam == "code") {
                $products = Items::where("items.code", $data['search'])
                        ->select('items.id as item_id', 'items.code', 'items.brand_name', 'items.category', 'items.size', 'items.base_price','items.pack_size')
                        ->where('is_active', 1)
                        ->get();
            } elseif ($searchparam == "type") {

                $products = Items::select('items.category')
                        ->groupby('items.category')
                        ->orderBy('items.category', 'asc')
                        ->where('is_active', 1)
                        ->get();
            }
            if (count($products)) {
                foreach($products as $product)
              {
                $cart = Cart::where('product_id',$product->item_id)
                        ->where('store_id', $request->store_id)
                        ->where('location_id', $request->location_id)
                        ->first();
                if(count($cart))
                {
                    $product->is_cart  = 1;
                }
                 else {
                    $product->is_cart  = 0;
                 }
             }
                return Response::json(array('response' => 'success', 'message' => 'success.', "products" => $products));
            } else {
                return Response::json(array('response' => 'failed', "message" => 'Product is not available!Invalid search parameter'));
            }
        } else {
            return Response::json(array("response" => "failed", "message" => "Please fill required fields !", "errors" => $valid->messages()));
        }
    }

    public function getProducts(Request $request) {
        $data = $request->all();
        $rules = array('category' => 'required', 'device_id' => 'required');
        $valid = Validator::make($data, $rules);
        if ($valid->passes()) {
            $products = Items::where("items.category", $data['category'])
                    ->select('items.id as item_id', 'items.category', 'items.size', 'items.base_price', 'items.brand_name')
                    ->orderBy('items.category', 'asc')
                    ->where('is_active', 1)
                    ->get();
            if (count($products)) {
                return Response::json(array('response' => 'success', 'message' => 'success.', "products" => $products));
            } else {
                return Response::json(array('response' => 'failed', "message" => 'Product is not available!Invalid search parameter'));
            }
        } else {
            return Response::json(array("response" => "failed", "message" => "Please fill required fields !", "errors" => $valid->messages()));
        }
    }

    public function productdetails(Request $request) {
        $data = $request->all();
        $rules = array('searchparam' => 'required', 'device_id' => 'required', "product_id" => "required");
        $searchparam = $request->searchparam;
        $search = $request->search;
        $valid = Validator::make($data, $rules);
        if ($valid->passes()) {
            if ($data['size']) {
                $product_details = Product::where('size', $data['size'])
                        ->select(['products.id as product_id', 'products.code', 'products.brand_name', 'products.size', 'products.base_price'])
                        ->where('product_id', $data['product_id'])
                        ->get();
                if (is_null($product_details)) {
                    return Response::json(array("response" => "failed", "message" => "This  size is not  available for this product.", "product_details" => $product_details));
                } else {
                    return Response::json(array("response" => "success", "message" => "success product of this size is available.", "product_details" => $product_details));
                }
            } else {
                $product_details = Products::where('products.id', $data['product_id'])
                        ->select(['products.id as product_id', 'products.code', 'products.brand_name', 'products.size', 'products.base_price'])
                        ->get();
                if (is_null($product_details)) {
                    return Response::json(array("response" => "failed", "message" => "This  product is not available .", "product_details" => $product_details));
                } else {
                    return Response::json(array("response" => "success", "message" => "success product is available.", "product_details" => $product_details));
                }
            }
        } else {
            return Response::json(array("response" => "failed", "message" => "Please fill required fields !", "errors" => $valid->messages()));
        }
    }

    public function productquantity(Request $request) {
        $data = $request->all();
        $rules = array('quantity' => 'required', 'device_id' => 'required', "product_id" => "required");
        $valid = Validator::make($data, $rules);
        if ($valid->passes()) {
            $products = Items::where("items.id", $data['product_id'])
                    ->select('items.id as item_id', 'items.category', 'items.size', 'items.base_price', 'items.brand_name', 'items.pack_size')
                    ->get();

            if (count($products)) {
                foreach ($products as $product) {
                    $packsize = $product->pack_size;
                }
                if (($data['quantity'] % $packsize) == 0) {
                    return Response::json(array("response" => "success", "message" => "Success Quantity is available.", "products" => $products));
                } else {
                    return Response::json(array("response" => "failed", "message" => "Quantity is  available only in multiples of " . $packsize));
                }
            } else {
                return Response::json(array("response" => "failed", "message" => "Product is not available .", "products" => $products));
            }
        } else {
            return Response::json(array("response" => "failed", "message" => "Please fill required fields !", "errors" => $valid->messages()));
        }
    }

    public function getProduct(Request $request) {
        $data = $request->all();
        $rules = array('device_id' => 'required', "store_id" => "required", "location_id" => "required", "product_id" => "required");
        $valid = Validator::make($data, $rules);
        if ($valid->passes()) {

            $product_details = Items::where('id', $data['product_id'])
                    ->select(['items.*'])
                    ->first();
            if (count($product_details)){
            $cart = Cart::where('product_id',$data['product_id'])
                        ->where('store_id', $data['store_id'])
                        ->where('location_id', $data['location_id'])
                        ->first();
                if(count($cart))
                {
                    $product_details->is_cart  = 1;
                }
                 else {
                    $product_details->is_cart  = 0;
                 }
                
             }

            $product = Products::where('item_id', $product_details['id'])
                    ->where('store_id', $data['store_id'])
                    ->where('location_id', $data['location_id'])
                    ->first();
            if (count($product)) {
                $product_details->detail = Products::where('products.item_id', $product_details['id'])
                        ->where('products.store_id', $data['store_id'])
                        ->where('products.location_id', $data['location_id'])
                        ->select(['products.shelf_number', 'products.bottle_position'])
                        ->first();
            }



//           $product_details=Products::join('items','products.item_id','=','items.id')
//                  -> join('shelf','products.shelf_id','=','shelf.id')
//                     ->where('products.item_id',$data['product_id'])
//                                 ->where('products.store_id',$data['store_id'])
//                                 ->where('products.location_id',$data['location_id'])
//                                 ->select(['items.*','shelf.shelf_name','products.shelf_id','products.shelf_number','products.bottle_position'])
//                                 ->first();

            if (count($product_details)) {
                return Response::json(array('response' => 'success', 'message' => 'success.', "products" => $product_details));
            } else {
                return Response::json(array('response' => 'failed', "message" => 'Product not available!'));
            }
        } else {
            return Response::json(array("response" => "failed", "message" => "Please fill required fields !", "errors" => $valid->messages()));
        }
    }

    public function locateProducts(Request $request) {
        $data = $request->all();
        $rules = array('store_id' => 'required', 'location_id' => 'required', 'device_id' => 'required', 'shelf_number' => 'required', 'bottle_position' => 'required');
        $valid = Validator::make($data, $rules);
        if ($valid->passes()) {
            $product_details = Products::join('items', 'products.item_id', '=', 'items.id')
                    ->where('shelf_number', $data['shelf_number'])
                    ->where('bottle_position', $data['bottle_position'])
                    ->where('products.store_id', $data['store_id'])
                    ->where('products.location_id', $data['location_id'])
                    ->select(['items.*'])
                    ->first();
            if (count($product_details)) {
                $cart = Cart::where('product_id',$product_details->id)
                        ->where('store_id', $data['store_id'])
                        ->where('location_id', $data['location_id'])
                        ->first();
                if(count($cart))
                {
                    $product_details->is_cart  = 1;
                }
                 else {
                    $product_details->is_cart  = 0;
                 }
                
                return Response::json(array('response' => 'success', 'message' => 'success.', "products" => $product_details));
            } else {
                return Response::json(array('response' => 'failed', "message" => 'Product is not available!Failed to locate the product'));
            }
        } else {
            return Response::json(array("response" => "failed", "message" => "Please fill required fields !", "errors" => $valid->messages()));
        }
    }

    public function getShelves(Request $request) {
        $data = $request->all();
        $rules = array('device_id' => 'required', "store_id" => "required", "location_id" => "required");
        $valid = Validator::make($data, $rules);
        if ($valid->passes()) {
            $shelf = Shelf::select(['shelf_number','description'])->where('store_id', $data['store_id'])->where('location_id', $data['location_id'])
                    ->get();
            if (count($shelf)) {
                return Response::json(array('response' => 'success', 'message' => 'success.', "shelf" => $shelf));
            } else {
                return Response::json(array('response' => 'failed', "message" => 'Shelves are not available!'));
            }
        } else {
            return Response::json(array("response" => "failed", "message" => "Please fill required fields !", "errors" => $valid->messages()));
        }
    }

    public function cartPost(Request $request) {
        $data = $request->all();
//        print_r($data);die;
        $rules = array('device_id' => 'required', 'store_id' => 'required', 'location_id' => 'required',
            'product_id' => 'required', 'quantity' => 'required');
        $valid = Validator::make($data, $rules);
        if ($valid->passes()) {
            
           $cart = Cart::where("product_id", $request->product_id)->where("store_id", $request->store_id)
                ->where("location_id", $request->location_id)->first();
            if ($cart) {               
            $price = $cart->price/$cart->quantity ;
            $cart->quantity = $cart->quantity + $request->quantity;
            $cart->price = ($price * $cart->quantity);
             $result = $cart->save();
            }
           else{
            $cart = new Cart;
            $cart->store_id = HelperController::clean($request->store_id);
            $cart->location_id = HelperController::clean($request->location_id);
            $cart->product_id = HelperController::clean($request->product_id);
            $cart->quantity = HelperController::clean($request->quantity);
            $cart->created_at = date("Y-m-d h:i:s");
            $cart->updated_at = date("Y-m-d h:i:s");

            if (($request->changed_price) == 0) {
                $Itemprice = Items::where('id', $request->product_id)->select('items.base_price','items.license_price')->first();
                $price = $Itemprice['license_price'] * $request->quantity;
                $cart->price = $price;
            } else {
//                $price = $request->changed_price * $request->quantity;
//                $cart->price = $price;
                $Itemprice = Items::where('id', $request->product_id)->select('items.base_price','items.license_price')->first();
                $price = $Itemprice['license_price'] * $request->quantity;
                $cart->price = $price;
                
                $cart->price_change = 1;
                $cart->changed_price = $request->changed_price;
            }
            if ($request->label_quantity) {
                $cart->label_quantity = HelperController::clean($request->label_quantity);
//                $cart->description = HelperController::clean($request->description);
            }

            $result = $cart->save();
            }
            if ($result) {
                return Response::json(array("response" => "success", "message" => "Added to Cart.", "cart_id" => $cart->id));
            } else {
                return Response::json(array("response" => "failed", "message" => "Something went wrong !"));
            }
        } else {
            return Response::json(array("response" => "failed", "message" => "Please fill required fields !", "errors" => $valid->messages()));
        }
    }

    public function Cartview(Request $request) {
        $data = $request->all();
        $rules = array('device_id' => 'required', "store_id" => "required", "location_id" => "required");
        $valid = Validator::make($data, $rules);
        if ($valid->passes()) {

            $product_details = Cart::join('items', 'cart.product_id', '=', 'items.id')
                    ->where('cart.store_id', $data['store_id'])
                    ->where('cart.location_id', $data['location_id'])
                    ->select(['cart.id as cart_id', 'cart.quantity', 'cart.price','cart.changed_price', 'items.id as item_id', 'items.brand_name', 'items.size','items.code', 'items.base_price', 'items.minimum_shelf_price', 'items.pack_size'])
                    ->orderBy('cart_id', 'DESC')
                    ->get();



            if (count($product_details)) {
                foreach ($product_details as $product) {
                    $product->last_order = OrderItem::where('item_id', $product->item_id)
                            ->where('store_id', $data['store_id'])
                            ->where('location_id', $data['location_id'])
                            ->select('quantity as last_order')
                            ->orderBy('id', 'DESC')
                            ->first();

                    if (!$product->last_order) {
                        $product->last_order = '';
                    }
                }


//            $product_details = Cart::join('items', 'cart.product_id', '=', 'items.id')
//                    ->Leftjoin('order_item', 'order_item.item_id', '=', 'items.id')
//                    ->where('cart.store_id', $data['store_id'])
//                    ->where('cart.location_id', $data['location_id'])
//                    ->select(['cart.id as cart_id', 'cart.quantity', 'cart.price', 'items.id as item_id', 'items.brand_name', 'items.size', 'items.base_price', 'items.minimum_shelf_price', 'order_item.quantity as last_order'])
//                    ->get();
//            
//            
//            
//                if (count($product_details)) {
//                foreach ($product_details as $product) {
//                    if (!$product->last_order) {
//                        $product->last_order = '';
//                    }
//                }


                return Response::json(array('response' => 'success', 'message' => 'success', "products" => $product_details));
            } else {
                return Response::json(array('response' => 'failed', "message" => 'Product not available!'));
            }
        } else {
            return Response::json(array("response" => "failed", "message" => "Please fill required fields !", "errors" => $valid->messages()));
        }
    }

    public function Deletecart(Request $request) {
        $data = $request->all();
        $rules = array('device_id' => 'required', "cart_id" => "required");
        $valid = Validator::make($data, $rules);
        if ($valid->passes()) {

            $cart = $data['cart_id'];
            $carts = explode(",", $cart);
            foreach ($carts as $cart) {
                $delete = Cart::where('id', $cart)->delete();
            }
            if ($delete) {
                return Response::json(array("response" => "success", "message" => "Cart Deleted"));
            } else {
                return Response::json(array("response" => "failed", "message" => "Product not available!"));
            }
        } else {
            return Response::json(array("response" => "failed", "message" => "Please fill required fields !", "errors" => $valid->messages()));
        }
    }

    public function updateQuantity(Request $request) {
        $data = $request->all();
        $rules = array('device_id' => 'required', "cart_id" => "required", "quantity" => 'required');
        $valid = Validator::make($data, $rules);
        if ($valid->passes()) {
            $base_price = 0;
            $cart = Cart::where('id', $data['cart_id'])->first();
            $item = Items::where('id', $cart->product_id)->first();
            $base_price = $item->base_price;
            $cart->quantity = $data['quantity'];
            $cart->price = $data['quantity'] * $base_price;
            $save = $cart->save();
            if ($save) {
                return Response::json(array("response" => "success", "message" => "Quantity Updated.", "cart_id" => $cart->id, "cart" => $cart));
            } else {
                return Response::json(array('response' => 'failed', "message" => 'Failed to Update Qunatity!'));
            }
        } else {
            return Response::json(array("response" => "failed", "message" => "Please fill required fields !", "errors" => $valid->messages()));
        }
    }

    public function adjustPrice(Request $request) {
        $data = $request->all();
        $rules = array('device_id' => 'required', "cart_id" => "required", "price" => 'required');
        $valid = Validator::make($data, $rules);
        if ($valid->passes()) {
            $quantity = 0;
            $cart = Cart::where('id', $data['cart_id'])->first();
//            $item=Items::where('id',$cart->product_id)->first();
            $quantity = $cart->quantity;
            $cart->price = $data['price'] * $quantity;
            $cart->price_change = 1;
            $cart->changed_price = $data['price'];

            $save = $cart->save();
            if ($save) {
                return Response::json(array("response" => "success", "message" => "Price Updated.", "cart_id" => $cart->id, "cart" => $cart));
            } else {
                return Response::json(array('response' => 'failed', "message" => 'Failed to Update Price!'));
            }
        } else {
            return Response::json(array("response" => "failed", "message" => "Please fill required fields !", "errors" => $valid->messages()));
        }
    }

    public function Placeorder(Request $request) {
        $data = $request->all();
        $rules = array('device_id' => 'required', 'store_id' => 'required', 'location_id' => 'required', 'user_id' => 'required');
        $valid = Validator::make($data, $rules);
        if ($valid->passes()) {

            $cutoof_time = CutOffTime::where('store_id', $request->store_id)
                            ->where('location_id', $request->location_id)
                            ->where('is_active', 1)->first();
            if ($cutoof_time) {
                $cutofdate = date('Y-m-d', strtotime($cutoof_time->cutoffdate));
                $current_date = strtotime(date("Y-m-d"));
                $start_week = date("Y-m-d", strtotime('monday this week'));
                $check_startdate = strtotime($start_week);
                $check_cutofdate = strtotime($cutofdate);
                if (($current_date >= $check_startdate) && ($current_date <= $check_cutofdate)) {
//                if ($current_date == $check_cutofdate) {
                    $date = date("Y-m-d");
                    $order_check = Order::where('store_id', $request->store_id)
                            ->where('location_id', $request->location_id)
                            ->whereBetween('created_at', [$start_week, $cutofdate])
//                            ->where('created_at', '>=', "'$start_week'")
//                            ->where('created_at', '<=', "'$cutofdate'")
//                            ->where('created_at', 'LIKE', "%{$date}%")
                            ->first();                    
//                    print_r($order_check);die;
//                    echo count($order_check);die;
                    if (count($order_check) == 0) {
                        $time = $cutoof_time->end_time;
                        $check_time = strtotime($time);
                        $current_time = strtotime(date("H:i:s"));
                        
                        $today = strtotime($date);
                        
                        if (($today==$check_cutofdate)&&($current_time > $check_time)) {
                            return Response::json(array("response" => "failed", "message" => "Order couldn't be Placed.Cut off time exceeded !"));
                        } else {
                            $order = new Order;
                            $order->store_id = HelperController::clean($request->store_id);
                            $order->location_id = HelperController::clean($request->location_id);
                            $order->user_id = HelperController::clean($request->user_id);
                            $order->total_quantity = Cart::where("store_id", $request->store_id)
                                    ->where("location_id", $request->location_id)
                                    ->sum('quantity');
                            $order->total_price = Cart::where("store_id", $request->store_id)
                                    ->where("location_id", $request->location_id)
                                    ->sum('price');
//                $order->created_at = date( "Y-m-d h:i:s");
//                $order->updated_at = date( "Y-m-d h:i:s");
                            $order->order_status = 1;
                            $result = $order->save();


                            $all_order_items = Cart::where("store_id", $request->store_id)
                                    ->where("location_id", $request->location_id)
                                    ->get();

                            foreach ($all_order_items as $product) {
                                $order_item = new OrderItem;
                                $checkitem = Products::where("store_id", $product->store_id)
                                        ->where("location_id", $product->location_id)
                                        ->where("item_id", $product->product_id)
                                        ->first();
                                if (count($checkitem) == 0) {
                                    $shelf = Shelf::where('shelf_number', 'UNALLOCATED')
                                            ->where("location_id", $product->location_id)
                                            ->where("store_id", $product->store_id)
                                            ->first();
                                    if (count($shelf) == 0) {
                                        $shelf = new Shelf;
                                        $shelf->store_id = $product->store_id;
                                        $shelf->location_id = $product->location_id;
                                        $shelf->shelf_number = 'UNALLOCATED';
                                        $shelf->is_active = 1;
                                        $result = $shelf->save();
                                    }


                                    $item = new Products;
                                    $item->store_id = $product->store_id;
                                    $item->location_id = $product->location_id;
                                    $item->item_id = $product->product_id;
                                    $item->shelf_number = 'UNALLOCATED';
                                    $count = Products::where("store_id", $product->store_id)
                                            ->where("location_id", $product->location_id)
                                            ->where('shelf_number', 'UNALLOCATED')
                                            ->count();
                                    $item->bottle_position = $count + 1;
                                    $item->label = "Send";
                                    $result = $item->save();
                                    $order_item->shelf_tag_order = 1;
                                    $order_item->description = 'New Product';
                                    $order_item->label_quantity = $product->label_quantity;
                                    Order::where("id", $order->id)->update(['include_shelf_tag' => 1]);
                                }
                                if ($product->price_change == 1) {
                                    $order_item->shelf_tag_order = 1;
                                    $order_item->description = 'Price Change';
                                    $order_item->change_price = $product->changed_price;
                                    $order_item->label_quantity = $product->label_quantity;
                                    Order::where("id", $order->id)->update(['include_shelf_tag' => 1]);
                                }


                                $order_item->order_id = $order->id;
                                $order_item->store_id = $product->store_id;
                                $order_item->location_id = $product->location_id;
                                $order_item->user_id = $request->user_id;
                                $order_item->item_id = $product->product_id;
                                $order_item->quantity = $product->quantity;
                                $order_item->price = $product->price;

                                $result = $order_item->save();


                                Cart::where("id", $product->id)
                                        ->where("store_id", $product->store_id)
                                        ->where("location_id", $product->location_id)
                                        ->delete();
                            }
                            
               $checklabel = StoreLocation:: where("store_id", $product->store_id)
                ->where("location_id", $product->location_id)->first();
                if($checklabel ->print_label == 1){
                $orders = OrderItem::join('items', 'order_item.item_id', '=', 'items.id')
                ->select('order_item.id as order_item_id', 'order_item.quantity as quantity', 'order_item.price as price', 'order_item.label_quantity', 'order_item.change_price as new_price','order_item.description', 'items.*')
                ->where('shelf_tag_order', 1)
                ->where("order_id", $order->id)
                ->get();
                                
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
                    $barcode_image = asset("uploads/barcodes") . "/$sku.png";;
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
                    $pdf->SetFont('Impact', '', 42);
                    $width_price = $pdf->GetStringWidth($order->price);
                    $pdf->text( $x+(($width-$width_price)/2),$y+22,$order->price, 4);
//                    $a = strlen($order->price);
//                    $pdf->text($a > 5 ? $x+9 : ($a > 4 ?  $x+13 : $x+15),$y+22,$order->price, 4);
//                    $pdf->text($x+12,$y+20,$order->price, 4);       

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
                                $user = User::where("store_id", $request->store_id)->first();

                                $email_data['name'] = $user->first_name;
                                $email_data['email'] = $user->email;
                                $email_data['msg'] = "Please download the labels";
                                $email_data['pdf'] = $pdf->Output('shelftag.pdf', 'S');

                                $mail = Mail::send('email.label', $email_data, function($message) use ($email_data) {
                                            $message->from('dev@thetunagroup.com', $email_data['name']);
                                            $message->to($email_data['email'])->subject('Shelf Order Placed');
                                            $message->attachData($email_data['pdf'], "shelftag.pdf");
                                        });
//                      $email_data['name'] = 'Dev';
//                      $email_data['pdf'] =  $pdf->Output('shelftag.pdf', 'S');
//                      $mail = Mail::send('email.label', $email_data, function($message) use ($email_data) {
//                      $message->from('maria.antony4@gmail.com', $email_data['name']);
//                      $message->to('elizabeth.antony612@gmail.com')->subject('Shelf Order Placed');
//                      $message->attachData($email_data['pdf'], "shelftag.pdf");
//                       });
//            
                                }
                            
                            
                            if ($result) {
                                return Response::json(array("response" => "success", "message" => "Order Placed"));
                            } else {
                                return Response::json(array("response" => "failed", "message" => "Something went wrong !"));
                            }
                        }
                    } else {
                        return Response::json(array("response" => "failed", "message" => "Sorry.You can't place more orders this week !"));
                    }
                } else {
                    return Response::json(array("response" => "failed", "message" => "Please make order before cutoff date!"));
                }
            } else {
                return Response::json(array("response" => "failed", "message" => "Please set cut off time for this store!"));
            }
        } else {
            return Response::json(array("response" => "failed", "message" => "Please fill required fields !", "errors" => $valid->messages()));
        }
    }
    
    public function scannedproduct(Request $request) {
        $data = $request->all();
        $rules = array('device_id' => 'required', "upc" => "required");
        $valid = Validator::make($data, $rules);
        if ($valid->passes()) {
            $item = Items::where('upc', $data['upc'])->first();
            if ($item) {
                return Response::json(array("response" => "success", "message" => "product details fetched.", "item" => $item));
            } else {
                return Response::json(array('response' => 'failed', "message" => 'Failed to fetch the product!'));
            }
        } else {
            return Response::json(array("response" => "failed", "message" => "Please fill required fields !", "errors" => $valid->messages()));
        }
    }

    //Fetch orders based on store id and location id
    public function ordersListing(Request $request) {
        $data = $request->all();

        $rules = array('device_id' => 'required', "store_id" => "required", 'location_id' => "required");
        $valid = Validator::make($data, $rules);
        if ($valid->passes()) {
            $orders = Order::where('store_id', $data['store_id'])->where('location_id', $data['location_id'])->orderBy('id', 'desc')->get();
            if ($orders) {
                return Response::json(array("response" => "success", "message" => "Your orders are.", "orders" => $orders));
            } else {
                return Response::json(array('response' => 'failed', "message" => 'Currently there are no orders!'));
            }
        } else {
            return Response::json(array("response" => "failed", "message" => "Please fill required fields !", "errors" => $valid->messages()));
        }
    }

    //order item listing from orders
    public function orderItems(Request $request) {
        $data = $request->all();
        $rules = array('device_id' => 'required', "order_id" => "required");
        $valid = Validator::make($data, $rules);
        if ($valid->passes()) {
            $order_items = OrderItem::join('items','items.id','=','order_item.item_id')
                    ->select(['items.brand_name','items.code','items.size','order_item.price','order_item.quantity','order_item.order_id','order_item.id'])
                    ->where('order_id', $data['order_id'])
                    ->orderBy('order_item.id', 'desc')->get();
            
            if (count($order_items)) {
                return Response::json(array("response" => "success", "message" => "Order Items.", "order_items" => $order_items));
            } else {
                return Response::json(array('response' => 'failed', "message" => 'Currently there are no orders!'));
            }
        } else {
            return Response::json(array("response" => "failed", "message" => "Please fill required fields !", "errors" => $valid->messages()));
        }
    }
     public function addshelf(Request $request) {
        $data = $request->all();
//        print_r($data);die;
        $rules = array('device_id' => 'required', 'store_id' => 'required', 'location_id' => 'required',
            'shelf' => 'required');
        $valid = Validator::make($data, $rules);
        if ($valid->passes()) {
            
            $Shelves = new Shelf;
            
            $Shelves->store_id = HelperController::clean($data['store_id']); 
            $Shelves->location_id = HelperController::clean($data['location_id']);
            $Shelves->shelf_number = HelperController::clean($data['shelf']);
            $Shelves->description = HelperController::clean($data['description']);
            $check_shelf = Shelf::where("shelf_number", $Shelves->shelf_number)
                        ->where('store_id',$Shelves->store_id)
                        ->where('location_id',$Shelves->location_id)
                        ->count();
                if ($check_shelf != 0) {
                    return Response::json(array("response" => "failed", "message" => "Opps...Shelf Already Exist !"));
                
                }
            $result = $Shelves->save();
           
  
           
            if ($result) {
                return Response::json(array("response" => "success", "message" => "Shelf Created","Shelf" => $Shelves->shelf_number));
            } else {
                return Response::json(array("response" => "failed", "message" => "Something went wrong !"));
            }
        } else {
            return Response::json(array("response" => "failed", "message" => "Please fill required fields !", "errors" => $valid->messages()));
        }
    }

     public function addProduct(Request $request) {
        $data = $request->all();
//        print_r($data);die;
        $rules = array('device_id' => 'required', 'store_id' => 'required', 'location_id' => 'required',
            'product_id' => 'required', 'shelf' => 'required', 'bottle_position' => 'required',);
        $valid = Validator::make($data, $rules);
        if ($valid->passes()) {

            $Products = new Products;
           
            $check_shelf = Products:: where('shelf_number', $data['shelf'])
                    ->where('bottle_position', $data['bottle_position'])
                    ->where('store_id', $data['store_id'])
                    ->where('location_id', $data['location_id'])
                    ->first();
            if (count($check_shelf)) {
                
                if($check_shelf->item_id == 0)
                {
                    $postion = $check_shelf->bottle_position;
                    $save = Products::where('store_id', $data['store_id'])
                        ->where('location_id', $data['location_id'])
                        ->where('shelf_number', $data['shelf'])
                        ->where('bottle_position', $postion)
                        ->update(['item_id' => $data['product_id']]);
                     $product_details = Products::join('items', 'products.item_id', '=', 'items.id')
                        ->where('products.id', $check_shelf->id)
                        ->select(['products.id as product_id', 'products.shelf_number', 'products.bottle_position', 'items.brand_name', 'items.size', 'items.base_price', 'items.minimum_shelf_price', 'items.pack_size', 'items.code', 'items.upc'])
                        ->first();
                    return Response::json(array("response" => "success", "message" => "Product Updated to Shelf", "product_details" => $product_details));
            
                }
                else{
                $postion = $check_shelf->bottle_position;
                $save = Products::where('store_id', $data['store_id'])
                        ->where('location_id', $data['location_id'])
                        ->where('shelf_number', $data['shelf'])
                        ->where('bottle_position', '>=', $postion)
                        ->update(['bottle_position' => DB::raw('bottle_position+1')]);
                }
            }
            $Products->store_id = HelperController::clean($data['store_id']);
            $Products->location_id = HelperController::clean($data['location_id']);
            $Products->item_id = HelperController::clean($data['product_id']);
//          $Products->shelf_id = HelperController::clean($data['shelf']);
            $Products->shelf_number = HelperController::clean($data['shelf']);
            $Products->bottle_position = HelperController::clean($data['bottle_position']);
            $result = $Products->save();
            if ($result) {
                $product_details = Products::join('items', 'products.item_id', '=', 'items.id')
                        ->where('products.id', $Products->id)
                        ->select(['products.id as product_id', 'products.shelf_number', 'products.bottle_position', 'items.brand_name', 'items.size', 'items.base_price', 'items.minimum_shelf_price', 'items.pack_size', 'items.code', 'items.upc'])
                        ->first();
                return Response::json(array("response" => "success", "message" => "Product Added to Shelf", "product_details" => $product_details));
            } else {
                return Response::json(array("response" => "failed", "message" => "Something went wrong !"));
            }
        } else {
            return Response::json(array("response" => "failed", "message" => "Please fill required fields !", "errors" => $valid->messages()));
        }
    }

    public function addshelfProduct(Request $request) {
        $data = $request->all();
        $rules = array('device_id' => 'required', 'store_id' => 'required', 'location_id' => 'required',
            'product_id' => 'required', 'shelf' => 'required', 'parameter' => 'required');
        if ($request->parameter == 'replace') {
            $rules['bottle_position'] = 'required';
        }
        $valid = Validator::make($data, $rules);

        if ($valid->passes()) {

            if ($data['parameter'] == 'add') {

                if ($data['bottle_position']) {

                    $checkposition = Products::where('store_id', $data['store_id'])
                                    ->where('location_id', $data['location_id'])
                                    ->where('shelf_number', $data['shelf'])
                                    ->where('bottle_position', $data['bottle_position'])->first();

                    if (count($checkposition) > 0) {

                        Products::where('store_id', $data['store_id'])
                                ->where('location_id', $data['location_id'])
                                ->where('shelf_number', $data['shelf'])
                                ->where('bottle_position', '>=', $data['bottle_position'])
                                ->increment('bottle_position');
                    }
                    $Products = new Products;
                    $Products->store_id = HelperController::clean($data['store_id']);
                    $Products->location_id = HelperController::clean($data['location_id']);
                    $Products->item_id = HelperController::clean($data['product_id']);
                    $Products->shelf_number = HelperController::clean($data['shelf']);
                    $Products->bottle_position = $data['bottle_position'];
                    $save = $Products->save();
                } else {
                    $lastpos = Products:: where('shelf_number', $data['shelf'])
                            ->where('store_id', $data['store_id'])
                            ->where('location_id', $data['location_id'])
                            ->orderBy('bottle_position', 'desc')
                            ->first();
                    $Products = new Products;
                    $Products->store_id = HelperController::clean($data['store_id']);
                    $Products->location_id = HelperController::clean($data['location_id']);
                    $Products->item_id = HelperController::clean($data['product_id']);
                    $Products->shelf_number = HelperController::clean($data['shelf']);
                    $Products->bottle_position = $lastpos->bottle_position + 1;
                    $save = $Products->save();
                }
            } else {
                $save = Products::where('store_id', $data['store_id'])
                        ->where('location_id', $data['location_id'])
                        ->where('shelf_number', $data['shelf'])
                        ->where('bottle_position', $data['bottle_position'])
                        ->update(['item_id' => $data['product_id']]);
            }

            if ($save) {

                return Response::json(array("response" => "success", "message" => "Product Added to Shelf"));
            } else {
                return Response::json(array("response" => "failed", "message" => "Something went wrong !"));
            }
        } else {
            return Response::json(array("response" => "failed", "message" => "Please fill required fields !", "errors" => $valid->messages()));
        }
    }
        public function getNew(Request $request) {
        $data = $request->all();
        $rules = array('device_id' => 'required','store_id' => 'required','location_id' => 'required');
        $valid = Validator::make($data, $rules);
        if ($valid->passes()) {
            $products = Items::where("items.new_chng", 'NEW')
                        ->select('items.id as item_id', 'items.code', 'items.brand_name', 'items.category', 'items.size', 'items.base_price','items.minimum_shelf_price','items.pack_size')
                        ->where('is_active', 1)
                        ->get();
            if (count($products)) {
                        foreach($products as $product)
              {
                $cart = Cart::where('product_id',$product->item_id)
                        ->where('store_id', $data['store_id'])
                        ->where('location_id', $data['location_id'])
                        ->first();
                if(count($cart))
                {
                    $product->is_cart  = 1;
                }
                 else {
                    $product->is_cart  = 0;
                 }
             }
                return Response::json(array('response' => 'success', 'message' => 'success.', "products" => $products));
            } else {
                return Response::json(array('response' => 'failed', "message" => 'Shelves are not available!'));
            }
        } else {
            return Response::json(array("response" => "failed", "message" => "Please fill required fields !", "errors" => $valid->messages()));
        }
    }
         public function getShelfProduct(Request $request) {
        $data = $request->all();
        $rules = array('device_id' => 'required','store_id' => 'required','location_id' => 'required','shelf' => 'required');
        $valid = Validator::make($data, $rules);
        if ($valid->passes()) {
            $products = Products::Leftjoin('items', 'products.item_id', '=', 'items.id')
                   ->where('shelf_number', $data['shelf'])
                    ->where('products.store_id', $data['store_id'])
                    ->where('products.location_id', $data['location_id'])
//                  ->select(['products.id as product_id', 'products.shelf_number', 'products.bottle_position',  'items.brand_name', 'items.size', 'items.base_price', 'items.minimum_shelf_price', 'items.pack_size','items.code','items.upc'])
                   ->select([ 'products.bottle_position','products.item_id',  'items.size','products.id as product_id','items.brand_name'])
                   ->orderBy('products.bottle_position','asc')
                    ->get();
            
            if (count($products)) {
                   foreach($products as $product)
              {
                $cart = Cart::where('product_id',$product->item_id)
                        ->where('store_id', $data['store_id'])
                        ->where('location_id', $data['location_id'])
                        ->first();
               
                if(count($cart))
                {
                    $product->is_cart  = 1;
                }
                 else {
                    $product->is_cart  = 0;
                 }
             }
                return Response::json(array('response' => 'success', 'message' => 'success.', "products" => $products));
            } else {
                return Response::json(array('response' => 'success', "message" => 'Shelves are not available!'));
            }
        } else {
            return Response::json(array("response" => "failed", "message" => "Please fill required fields !", "errors" => $valid->messages()));
        }
    }
     public function DeleteItem(Request $request) {
        $data = $request->all();
        $rules = array('device_id' => 'required', "product_id" => "required");
        $valid = Validator::make($data, $rules);
        if ($valid->passes()) {
            $product = Products:: where('id', $data['product_id'])
                    ->first();
            
            $delete = Products::where('id', $data['product_id'])->delete();
            
             $save = Products::where('store_id', $product ->store_id)
                        ->where('location_id', $product ->location_id)
                        ->where('shelf_number', $product ->shelf_number)
                        ->where('bottle_position', '>', $product ->bottle_position)
                        ->update(['bottle_position' => DB::raw('bottle_position-1')]);
            
            

            if ($delete) {
                return Response::json(array("response" => "success", "message" => "Item Deleted"));
            } else {
                return Response::json(array("response" => "failed", "message" => "Product not available!"));
            }
        } else {
            return Response::json(array("response" => "failed", "message" => "Please fill required fields !", "errors" => $valid->messages()));
        }
    }
       public function clearItem(Request $request) {
        $data = $request->all();
        $rules = array('device_id' => 'required', "product_id" => "required");
        $valid = Validator::make($data, $rules);
        if ($valid->passes()) {
            $products = Products::where('id', $data['product_id'])->first();
             if(!is_null($products)) {
                $products->item_id = "";
                $save = $products->save();
           
        }
            if ($save) {
                return Response::json(array("response" => "success", "message" => "Item Cleared"));
            } else {
                return Response::json(array("response" => "failed", "message" => "Product not available!"));
            }
        } else {
            return Response::json(array("response" => "failed", "message" => "Please fill required fields !", "errors" => $valid->messages()));
        }
    }
     public function moveItem(Request $request) {
        $data = $request->all();
        $rules = array('device_id' => 'required', "direction" => "required", 'store_id' => 'required', 'location_id' => 'required', 'shelf' => 'required', 'bottle_position' => 'required');
        $valid = Validator::make($data, $rules);
        if ($valid->passes()) {
            
            if( $data['direction'] == 'left')
            {
                   $product = Products::where('store_id',$data['store_id'])
                        ->where('location_id', $data['location_id'])
                        ->where('shelf_number', $data['shelf'])
                        ->where('bottle_position',  $data['bottle_position'])
                        ->first();
                   $leftproduct = Products::where('store_id', $data['store_id'])
                        ->where('location_id', $data['location_id'])
                        ->where('shelf_number', $data['shelf'])
                        ->where('bottle_position', $data['bottle_position']-1)
                        ->first();
                   
                   
                    $save = Products::where('store_id', $data['store_id'])
                        ->where('location_id', $data['location_id'])
                        ->where('shelf_number', $data['shelf'])
                        ->where('bottle_position',  $data['bottle_position'])
                        ->update(['item_id' => $leftproduct->item_id]);
                    
                    $save = Products::where('store_id', $data['store_id'])
                        ->where('location_id', $data['location_id'])
                        ->where('shelf_number', $data['shelf'])
                        ->where('bottle_position',  $data['bottle_position']-1)
                        ->update(['item_id' => $product->item_id]);
                   
            }
          if( $data['direction'] == 'right')
            {
                   $product = Products::where('store_id',$data['store_id'])
                        ->where('location_id', $data['location_id'])
                        ->where('shelf_number', $data['shelf'])
                        ->where('bottle_position',  $data['bottle_position'])
                        ->first();
                   $rightproduct = Products::where('store_id', $data['store_id'])
                        ->where('location_id', $data['location_id'])
                        ->where('shelf_number', $data['shelf'])
                        ->where('bottle_position', $data['bottle_position']+1)
                        ->first();
                    $save = Products::where('store_id', $data['store_id'])
                        ->where('location_id', $data['location_id'])
                        ->where('shelf_number', $data['shelf'])
                        ->where('bottle_position',  $data['bottle_position'])
                        ->update(['item_id' => $rightproduct->item_id]);
                    
                    $save = Products::where('store_id', $data['store_id'])
                        ->where('location_id', $data['location_id'])
                        ->where('shelf_number', $data['shelf'])
                        ->where('bottle_position',  $data['bottle_position']+1)
                        ->update(['item_id' => $product->item_id]);
                   
            }
            
            

            if ($save) {
                return Response::json(array("response" => "success", "message" => "Item Swaped"));
            } else {
                  return Response::json(array("response" => "failed", "message" => "Something went wrong !"));
            }
        } else {
            return Response::json(array("response" => "failed", "message" => "Please fill required fields !", "errors" => $valid->messages()));
        }
    }
    
       public function sendLabel(Request $request) {
           
        $data = $request->all();
        $rules = array('device_id' => 'required', 'store_id' => 'required', 'location_id' => 'required');
        $valid = Validator::make($data, $rules);
        if ($valid->passes()) {
           
           
       $checklabel = StoreLocation:: where("store_id", $data['store_id'])
                ->where("location_id", $data['location_id'])->first();
                if($checklabel ->print_inventory_label == 1){
                    
                    $products = Products::join('items', 'products.item_id', '=', 'items.id')
                    ->where('products.store_id', $data['store_id'])
                    ->where('products.location_id', $data['location_id'])
                    ->where('products.label', "")       
                    ->select(['products.id as product_id', 'products.shelf_number', 'products.bottle_position',  'items.brand_name', 'items.size', 'items.base_price', 'items.ada_no', 'items.size','items.code','items.upc'])
                    ->orderBy('products.shelf_number', 'ASC')
                    ->orderBy('products.bottle_position', 'ASC')
                    ->get();
    
                                
                require_once(app_path() .'/PDF/code39.php');
                 $totwidth = 203.2;
                 $totheight = 198;
                
                $width = 50.8;
                $height = 31.75;

                $pdf = new PDF_Code39('l', 'mm', array($totwidth,$totheight));
                $margin = 1;
                $pdf->AddPage();
                $x = 0;$i=1;$y=0;
                foreach($products as $product){
                    
                    $sku = $product->upc;
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
                    $barcode_image = asset("uploads/barcodes") . "/$sku.png";;
//                    $pdf->Image($barcode_image, 20, 2, 14);
                     $pdf->Image($barcode_image,$x+(($width-32)/2), $y+2, 32,6);
                    //$pdf->Image($barcode_image, 20, 2, 10);
                    //$pdf->Code39(2,2,$product->upc,true, 1,7);

                    //PRICE
                     
                    $product->price =number_format((float)($product->base_price),2,'.','');
                    $pdf->AddFont('Impact','','impact.php');
                    $pdf->SetFont('Impact', '', 42);
                    $width_price = $pdf->GetStringWidth($product->price);
                    $pdf->text( $x+(($width-$width_price)/2),$y+22,$product->price, 4);
//                    $a = strlen($product->price);
//                    $pdf->text($a > 5 ? $x+9 : ($a > 4 ?  $x+13 : $x+15),$y+22,$product->price, 4);
                    
//                    $pdf->text($x+12,$y+20,$product->price, 4);       

                    //DESCRIPTION
                    $first = substr($product->brand_name, 0, 7);
                    $last = substr($product->brand_name, -2);  
                    $product->brand_name = $first." ".$last;
                    $pdf->SetFont('Impact', '', 10);
                    $pdf->text($x+2,$y+28,$product->brand_name, 4);      

                    //SIZE
                    $pdf->SetFont('Impact', '', 10);
                    $pdf->text($x+23,$y+28,$product->size."ML", 4);      

                    //VENDOR
                    $pdf->SetFont('Impact', '', 10);
//                    $pdf->text(40,28,$product->code, 4);   
                    $pdf->text($x+40,$y+28,$product->ada_no, 4); 
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
                    
                    
                    $save = Products::where("id", $product->product_id)->update(['label' => 'Send']); 
                }      
                                $user = User::where("store_id", $request->store_id)->first();

                                $email_data['name'] = $user->first_name;
                                $email_data['email'] = $user->email;
                                $email_data['msg'] = "Please download the labels";
                                $email_data['pdf'] = $pdf->Output('shelftag.pdf', 'S');

                                $mail = Mail::send('email.label', $email_data, function($message) use ($email_data) {
                                            $message->from('dev@thetunagroup.com', $email_data['name']);
                                            $message->to($email_data['email'])->subject('Inventory Label Placed');
                                            $message->attachData($email_data['pdf'], "shelftag.pdf");
                                        });
                                       
                                        
                                        
                }
               else {
              $save = Products::where("store_id", $data['store_id'])
              ->where("location_id",$data['location_id'])
              ->update(['label' => 'None']); 
                 }
                if ($save) {
                return Response::json(array("response" => "success", "message" => "Label Printed"));
            } else {
                  return Response::json(array("response" => "failed", "message" => "Something went wrong !"));
            }
        } else {
            return Response::json(array("response" => "failed", "message" => "Please fill required fields !", "errors" => $valid->messages()));
        }
    }
     public function movePosition(Request $request) {
        $data = $request->all();
        $rules = array('device_id' => 'required',  'store_id' => 'required', 'location_id' => 'required', 'shelf' => 'required',"old_position" => "required", 'new_position' => 'required');
        $valid = Validator::make($data, $rules);
        if ($valid->passes()) {
            $product = Products::where('store_id',$data['store_id'])
                        ->where('location_id', $data['location_id'])
                        ->where('shelf_number', $data['shelf'])
                        ->where('bottle_position',  $data['old_position'])
                        ->first();
            $itemid = $product->id;  
            $a = ($data['old_position']-$data['new_position']);
            if($a > 0)
            {
                $save = Products::where('store_id', $data['store_id'])
                    ->where('location_id', $data['location_id'])
                    ->where('shelf_number', $data['shelf'])
                    ->where('bottle_position', '<', $data['old_position'])
                    ->where('bottle_position', '>=', $data['new_position'])
                    ->update(['bottle_position' => DB::raw('bottle_position+1')]);

            }
           else {
            $save = Products::where('store_id', $data['store_id'])
                    ->where('location_id', $data['location_id'])
                    ->where('shelf_number', $data['shelf'])
                    ->where('bottle_position', '>', $data['old_position'])
                    ->where('bottle_position', '<=', $data['new_position'])
                    ->update(['bottle_position' => DB::raw('bottle_position-1')]);
           
           }     
             $save = Products::where('store_id', $data['store_id'])
                ->where('location_id', $data['location_id'])
                ->where('shelf_number', $data['shelf'])
                ->where('id',  $itemid)
                ->update(['bottle_position' => $data['new_position']]);
               
            if ($save) {
                return Response::json(array("response" => "success", "message" => "Item Moved"));
            } else {
                  return Response::json(array("response" => "failed", "message" => "Something went wrong !"));
            }
        } else {
            return Response::json(array("response" => "failed", "message" => "Please fill required fields !", "errors" => $valid->messages()));
        }
    }
    
//    public function storebottle() {
//        $stores = Products::select('store_id')->groupBy('store_id')->get();
//        foreach($stores as $store)
//        {
//            $location = Products::select('store_id','location_id')
//                    ->where('store_id',$store->store_id)
//                    ->groupBy('location_id')->get();
//            
//            foreach($location as $locations)
//            {
//                 $shelf = Products::select('store_id','location_id','shelf_number')
//                    ->where('store_id',$locations->store_id)
//                         ->where('location_id',$locations->location_id)
//                         ->groupBy('shelf_number')->get();
//                 
//                 foreach($shelf as $shelfs)
//            {
//                 $bottle = Products::select('id','store_id','location_id','shelf_number','bottle_position')
//                    ->where('store_id',$shelfs->store_id)
//                         ->where('location_id',$shelfs->location_id)
//                          ->where('shelf_number',$shelfs->shelf_number)
//                         ->orderBy('bottle_position','asc')
//                          ->get();
//                 $temp = 1;
//                 foreach($bottle as $bottles)
//                 {
//                     if($bottles->bottle_position ==1)
//                     {
//                         break;
//                     }
//                         else {
//                           $save = Products::where('store_id',$bottles->store_id)
//                ->where('location_id', $bottles->location_id)
//                ->where('shelf_number', $bottles->shelf_number)
//                 ->where('id', $bottles->id)                   
//                ->update(['bottle_position' => $temp]); 
//                         $temp =$temp+1;  
//                         }
////                     if($temp == -1){
////                         $temp = $bottles->bottle_position;
////                         
////                     }
////                     else {
////                        if(($temp+1) == $bottles->bottle_position){
////                          $temp = $bottles->bottle_position;
////                        }
////                         else {
////                            echo $bottles;break;
////                         }
////                     }
//                 }
//                 
//            }
//            
//            
//        }
//        
//        }
//        
//    }
}
