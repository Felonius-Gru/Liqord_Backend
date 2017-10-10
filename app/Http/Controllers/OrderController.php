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
use Hash;
use Response;
use Datatables;
use Mail;
use DateTime;
/** Models */
use App\User;
use App\Order;
use App\OrderItem;
use App\Products;
use App\Items;
use App\Stores;
use App\Locations;
use App\CutOffTime;
use App\StoreLocation;

class OrderController extends Controller {

    public function index() {
        if (!HelperController::checkPrivilege()) {
            return Redirect::to('admin');
        }
        $page_title = "Orders Placed";
        $breadcrumb = "<li class='active'>Orders Placed</li>";
        $header = "<header>Orders Placed</header>";
        $active = "orders";
        $active_sub = "";
        $stores = Stores::where('is_active', 1)->get();
        if (Auth::user()->role == "Admin") {

            $location = Locations::where('is_active', 1)->get();
            $update = Order::where("is_new", 1)->get();
            foreach ($update as $updates) {
                $updates->is_new = 0;
                $updates->save();
            }
        } elseif (Auth::user()->role == "StoreAdmin") {
            $storeid = Auth::user()->store_id;
            $location = StoreLocation:: join('locations', 'store_location.location_id', '=', 'locations.id')
                    ->select('locations.id', 'locations.name')->where('store_id', $storeid)
                    ->get();
        }
        return view("admin.orders")->with("page_title", $page_title)
                        ->with("breadcrumb", $breadcrumb)
                        ->with("header", $header)
                        ->with("stores", $stores)
                        ->with("location", $location)
                        ->with("active", $active)
                        ->with("active_sub", $active_sub);
    }

    public function results($search = false, $storeid = false, $locationid = false, $startdate = false, $enddate = false) {
        if ($search == 1) {
            $storeid = explode('=', $storeid);
            $locationid = explode('=', $locationid);
            $sdate = explode('=', $startdate);
            $edate = explode('=', $enddate);
            $query = DB::table('order')->join('order_item', 'order.id', '=', 'order_item.order_id');
            if ($storeid[1])
                $query->where('order.store_id', $storeid[1]);
            if ($locationid[1])
                $query->where('order.location_id', $locationid[1]);
            if ($sdate[1])
                $query->where('order.created_at', '>=', $sdate[1]);
            if ($edate[1])
                $query->where('order.created_at', '<=', $edate[1]);
//            $query->where('order.include_shelf_tag', 0);
            $query->select(['order.id', 'order.store_id', 'order.location_id', 'order.created_at']);
            $query->groupBy('order.id');
//            $query->groupBy('order.date');
            $query->orderBy('order.id', 'asc');
            $results = $query->get();
            $results = collect($results);
        }else {
            if (Auth::user()->role == "Admin") {
//         $results = Order::where('include_shelf_tag',0)->orderBy('created_at', 'desc');    
                $results = Order::orderBy('created_at', 'desc');
            } elseif (Auth::user()->role = "StoreAdmin") {
                $storeid = Auth::user()->store_id;
//        $results=Order::where('include_shelf_tag',0)->where('store_id',$storeid)->orderBy('created_at', 'desc');
                $results = Order::where('store_id', $storeid)->orderBy('created_at', 'desc');
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
                        ->editColumn('total_quantity', function ($result) {
//                            $total = OrderItem::where("order_id", $result->id)->where('shelf_tag_order', 0)->sum('quantity');
                            $total = OrderItem::where("order_id", $result->id)->sum('quantity');
                            return $total;
                        })
                        ->editColumn('total_price', function ($result) {
//                            $total_price = OrderItem::where("order_id", $result->id)->where('shelf_tag_order', 0)->sum('price');
                            $total_price = OrderItem::where("order_id", $result->id)->sum('price');
                            return $total_price;
                        })
//                         ->editColumn('user_id', function ($result) {
//                           $user = User::where("id", $result->user_id)->select('username')->first();
//                           return $user['username'];
//                        })
                        ->editColumn('created_at', function ($result) {
                            return strip_tags(date('d M Y', strtotime($result->created_at)));
                        })
                        ->addColumn('action', function ($result) {
                            return "<a href='" . asset("admin/orders/view/{$result->id}") . "'>
                        <span class='fa fa-pencil-square fa-2x'></span>
                        </a>";
                        })
//                                 ->addColumn('edit', function ($result) {
//                                     
//                                    return "<a href='" . asset("admin/orders/view/{$result->id}") . "'>
//                        <span class='fa fa-lock fa-2x'></span>
//                        </a>";
//                                     
//                                })
                        ->make(true);
    }

    public function view($id) {
        if (!HelperController::checkPrivilege()) {
            return Redirect::to('admin');
        }

        $order = OrderItem::join('items', 'order_item.item_id', '=', 'items.id')->select('order_item.id as order_item_id', 'order_item.quantity as quantity', 'order_item.price as price', 'order_item.order_status', 'items.*')
                ->where("order_id", $id)
                 ->paginate(15);
//       $order=OrderItem::join('items','order_item.item_id','=','items.id')->select('*')
//                ->where("order_id", $id)
//                ->get();
        if (is_null($order)) {
            Session::flash("fail", "No such order");
            return Redirect::to("admin/order");
        }
        $order_table = Order::where('id', $id)->first();
        $cutoof_time = CutOffTime::where('store_id', $order_table->store_id)
                        ->where('location_id', $order_table->location_id)
                        ->where('is_active', 1)->first();
        $order_lock = 0;
        if ($cutoof_time) {
            $cutofdate = date('w', strtotime($cutoof_time->cutoffdate));
//                print_r($cutofdate+1);die;

            $todaydate = date('w');
//                print_r($todaydate+1);die;
            if (($todaydate + 1) <= ($cutofdate + 1)) {

                $cut_time = date('H:i:s', strtotime($cutoof_time->end_time));
//                    print_r($cut_time);
                $todaytime = date('H:i:s');
//                   print_r($todaytime);die;
                if ($todaytime > $cut_time) {
                    $order_lock = 1;
                } else {
                    $order_lock = 2;
                }
            } else {
                $order_lock = 1;
            }
        }
        $page_title = "Orders Detail";
        $breadcrumb = "<li class='active'>Order Details</li>";
        $active = "orders";
        $active_sub = "orders";
        $header = "<header>Orders Detail</header>";

        return view("admin.ordersView")->with("page_title", $page_title)
                        ->with("breadcrumb", $breadcrumb)
                        ->with("active", $active)
                        ->with("active_sub", $active_sub)
                        ->with("order_lock", $order_lock)
                        ->with("header", $header)
                        ->with("order", $order);
    }

    public function itemUpdate($id) {
        if (!HelperController::checkPrivilege()) {
            return Redirect::to('admin');
        }

        $result = OrderItem::where("id", $id)->first();
        $item = Items::where('id', $result->id)->first();
        $packsize = $item->pack_size;
        $page_title = "Order Item Edit";
        $breadcrumb = "<li class='active'>Order Item Details</li>";
        $active = "orders";
        $active_sub = "orders";
        $header = "<header>Order Item Edit</header>";
        return view("admin.orderItemView")->with("page_title", $page_title)
                        ->with("breadcrumb", $breadcrumb)
                        ->with("active", $active)
                        ->with("active_sub", $active_sub)
                        ->with("header", $header)
                        ->with("result", $result)
                        ->with('item', $item)
                        ->with("packsize", $packsize);
    }

    public function itemUpdatePost(Request $request, $id = false) {
        if (!HelperController::checkPrivilege()) {
            return Redirect::to("admin");
        }
        $data = $request->all();
//        print_r($data);die;

        $rules = array();
        if ($id) {
            $rules = array('quantity' => 'required');
        } else {
            Session::flash("fail", "No such Product");
            return Redirect::back();
        }
        $valid = Validator::make($data, $rules);

        if ($valid->passes()) {
            $order_item = OrderItem::where('id', $id)->first();
            $item = Items::where('id', $order_item->item_id)->first();
            $order = Order::where('id', $order_item->order_id)->first();
            $packsize = 0;
            $quantity = 0;
            $quantity = $data['quantity'];
            $packsize = $item->pack_size;
            if ($data['quantity'] % $packsize == 0) {
                $order->total_quantity = $order->total_quantity - $order_item->quantity;
                $order_item->quantity = $data['quantity'];
                $order->total_price = $order->total_price - $order_item->price;
                $order_item->price = $item->base_price * $data['quantity'];
                $order->total_price = $order->total_price + $order_item->price;
                $order->total_quantity = $order->total_quantity + $data['quantity'];
                $order->save();
            } else {

                Session::flash("fail", "Opps... Please enter a valid quantity ");
                $request->flash();
                return Redirect::back();
            }

            $save = $order_item->save();

            if ($save) {
                Session::flash("success", "Product saved");
                return Redirect::to("admin/orders/item/update/{$id}");
            } else {
                // Something went wrong.
                Request::flash();
                Session::flash("fail", "Opps... Something went wrong");
            }

            return Redirect::back();
        } else {
            // Something went wrong.
            return Redirect::back()->withErrors($valid);
        }
    }

    public function instock($id) {
        if (!HelperController::checkPrivilege()) {
            return Redirect::to('admin');
        }

        $save = OrderItem::where("id", $id)->update(['order_status' => 1]);
        if ($save) {
            Session::flash("success", "Status set to instock");
        } else {
            // Something went wrong.
            Session::flash("fail", "Opps... Something went wrong");
        }

        return Redirect::back();
    }

    /**
     * Store disable page
     */
    public function outofstock($id) {
        if (!HelperController::checkPrivilege()) {
            return Redirect::to('admin');
        }

        $save = OrderItem::where("id", $id)->update(['order_status' => 0]);
        if ($save) {
            Session::flash("success", "Status set to outofstock");
        } else {
            // Something went wrong.
            Session::flash("fail", "Opps... Something went wrong");
        }

        return Redirect::back();
    }

    public function updateStatus(Request $request) {
        if (!HelperController::checkPrivilege()) {
            return Redirect::to("admin");
        }
//        echo "hi";die;
        $data = $request->all();
//        print_r($data);die;
        $id = $data['item_id'];
//        echo $id;die;
        $status = $data['status'];
        $order = OrderItem::where('id', $id)->first();
        $order->order_status = $data['status'];
        $save = $order->save();
        if ($save) {
            if ($status == 1)
                Session::flash("success", "Status  Updated to Product In stock");
            else
                Session::flash("success", "Status  Updated to Product Out Of stock");
            return Redirect::back();
        } else {
            // Something went wrong.
            Session::flash("fail", "Opps... Something went wrong");
        }

        return Redirect::back();
    }

    public function viewstatus(Request $request) {
        $data = $request->all();
        $data = HelperController::cleanArray($data);
        if ($request->has('item_id')) {
            echo "hi";
            die;
            $id = $data['item_id'];
            $status = $data['status'];
            $orderstatus = OrderItem::where('id', $id)->first();
            return Response::json(array("response" => 'success',
                        "status" => $status, "item_id" => $id));
        } else {
            return Response::json(array('response' => 'failed', "message" => " No such data exists"));
        }
    }

}
