<?php

namespace App\Http\Controllers;

use Auth;
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
use App\Common;
use App\Items;
use App\Stores;
use App\Locations;
use App\StoreLocation;
use App\Billing;
use App\BillingInfo;

class BillingController extends Controller {

    public function index() {
        if (!HelperController::checkPrivilege()) {
            return Redirect::to('admin');
        }
        $page_title = "Billing";
        $breadcrumb = "<li class='active'>Billing</li>";
        $active = "billing";
        $active_sub = "";

        return view("admin.billing")->with("page_title", $page_title)
                        ->with("breadcrumb", $breadcrumb)
                        ->with("active", $active)
                        ->with("active_sub", $active_sub);
    }

    /**
     * billing result page - ajax
     */
    public function Results() {
        $results = Stores::select(['id', 'name', 'is_active']);

        return Datatables::of($results)
                        ->addColumn('edit', function ($result) {
                            return "<a href='" . asset("admin/billinglocation/{$result->id}") . "'>
                            <span class='fa fa-pencil-square fa-2x'></span>
                        </a>";
                        })
                        ->addColumn('history', function ($result) {
                            return "<a href='" . asset("admin/billinghistory/{$result->id}") . "' title = 'View Billing History'>
                            <span class='fa fa-eye fa-2x'></span>
                        </a>";
                        })
                        ->make(true);
    }

   public function viewlocation($id = false) {
        if (!HelperController::checkPrivilege()) {
            return Redirect::to('admin');
        }
        $page_title = "Billing Locations";
        $breadcrumb = "<li class='active'>Billing Locations</li>";
        $active = "billing";
        $active_sub = "";

        return view("admin.billinglocation")->with("page_title", $page_title)
                        ->with("breadcrumb", $breadcrumb)
                        ->with("active", $active)
                        ->with("id", $id)
                        ->with("active_sub", $active_sub);
    }   
    
  public function resultlocation($id = false) {
       
        $results = StoreLocation::join('locations', 'store_location.location_id', '=', 'locations.id')
                    ->where('store_location.store_id', $id)
                    ->select('store_location.id','locations.name','store_location.is_active','store_location.print_label','store_location.print_inventory_label','store_location.location_id','store_location.store_id');

        
        return Datatables::of($results)  

                        ->addColumn('cost', function ($result) {
                            $cost = Billing::where('store_id',$result->store_id)
                                    ->where('location_id',$result->location_id)->first();
                            if($cost){
                            return '$'.$cost->monthly_cost;}
                            else {return "";}
                        })
                        
                      ->editColumn('edit', function ($result) {
                         $cost = Billing::where('store_id',$result->store_id)
                                    ->where('location_id',$result->location_id)->first();
                         if(isset($cost->monthly_cost)){
                        $monthly_cost = str_replace(" ", "&", $cost->monthly_cost);
                        return "<a onclick=viewdetail('$cost->id','$monthly_cost','$result->store_id','$result->location_id'); href='#myModal1' data-toggle='modal'>
                            <span class='fa fa-pencil-square fa-2x'></span>
                        </a>";
                         }
                          else{
                              return "<a onclick=viewstore('$result->store_id','$result->location_id'); href='#myModal' data-toggle='modal'>
                            <span class='fa fa-pencil-square fa-2x'></span>
                        </a>";
                         }
                        })  
                        
                        ->make(true);
    }
   
    
      public function update(Request $request, $id = false) {

        $data = $request->all();
        $rules = array(
                'monthly_cost' => 'required'
            );
       
        if (isset($data['id']) == '') {
            $billing = new Billing;
        } else {
            $id = $data['id'];
            $billing = Billing::find($id);
            if (is_null($billing)) {
                Session::flash("fail", "No such Bill Settings found");
                $request->flash();
                return Redirect::to("admin/billing");
            }
        }

        $valid = Validator::make($data, $rules);

        if ($valid->passes()) {
            $billing->store_id = HelperController::clean($data['store_id']);
            $billing->location_id = HelperController::clean($data['location_id']);
            $billing->monthly_cost = HelperController::clean($data['monthly_cost']);
            $save = $billing->save();
            if ($save) {
                if ($id == false) {
                    Session::flash("success", "Settings Added");
                    return Redirect::back();                   
                } else {
                    Session::flash("success", "Settings updated");
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
      public function billing() {
        if (!HelperController::checkPrivilege()) {
            return Redirect::to('admin');
        }
        $page_title = "Billing Locations";
        $breadcrumb = "<li class='active'>Billing Locations</li>";
        $active = "";
        $active_sub = "billing_section";
        $id = Auth::user()->store_id;
        return view("admin.billlocations")->with("page_title", $page_title)
                        ->with("breadcrumb", $breadcrumb)
                        ->with("active", $active)
                        ->with("id", $id)
                        ->with("active_sub", $active_sub);
    }

    public function billingResults() {
        $id = Auth::user()->store_id;

        $results = StoreLocation::join('locations', 'store_location.location_id', '=', 'locations.id')
                ->where('store_location.store_id', $id)
                ->select('store_location.id', 'store_location.created_at', 'locations.name', 'store_location.is_active', 'store_location.print_label', 'store_location.print_inventory_label', 'store_location.location_id', 'store_location.store_id');


        return Datatables::of($results)
                        ->addColumn('cost', function ($result) {
                            $cost = Billing::where('store_id', $result->store_id)
                                    ->where('location_id', $result->location_id)->first();
                            if ($cost) {
                                return '$' . $cost->monthly_cost;
                            } else {
                                return "";
                            }
                        })
                        ->addColumn('date', function ($result) {
                            $date1 = $result->created_at;
                            $date2 = date('Y-m-d');
                            $ts1 = strtotime($date1);
                            $ts2 = strtotime($date2);
                            $year1 = date('Y', $ts1);
                            $year2 = date('Y', $ts2);

                            $month1 = date('m', $ts1);
                            $month2 = date('m', $ts2);

                            $diff = (($year2 - $year1) * 12) + ($month2 - $month1);
                            $bill_date = date('Y-m-d', strtotime($result->created_at . '+' . $diff . 'month'));
                            $bill = BillingInfo::where('store_id', $result->store_id)
                                    ->where('location_id', $result->location_id)
                                    ->where('billing_status', 1)
                                    ->whereDate('bill_date','=',$bill_date)
                                    ->first();
                           if(count($bill) == 1) {
                               return date('d-F-Y', strtotime($bill_date . '+1 month'));
                           }
                           else if ( strtotime(date("Y-m-d")) > strtotime($bill_date)) {
                                return date('d-F-Y', strtotime($bill_date . '+1 month'));
                            } else {
                                return date('d-F-Y', strtotime($result->created_at . '+' . $diff . 'month'));
                            }
//                            if (strtotime(date("Y-m-d")) > strtotime($bill_date)) {
//                                return date('d-F-Y', strtotime($bill_date . '+1 month'));
//                            } else {
//                                return date('d-F-Y', strtotime($result->created_at . '+' . $diff . 'month'));
//                            }
                        })
                        ->make(true);
    }
    
     public function billing_history($id = false) {
        if (!HelperController::checkPrivilege()) {
            return Redirect::to('admin');
        }
        $page_title = "Billing History";
        $breadcrumb = "<li class='active'>Billing History</li>";
        $active = "billing";
        $active_sub = "billing_history";
        if ($id == false) {      
        $id = Auth::user()->store_id;
        }
        return view("admin.billhistory")->with("page_title", $page_title)
                        ->with("breadcrumb", $breadcrumb)
                        ->with("active", $active)
                        ->with("id", $id)
                        ->with("active_sub", $active_sub);
    }

    public function historyResults($id) {
//        $id = Auth::user()->store_id;

        $results = BillingInfo::where('store_id', $id)->where('billing_status',1)
                                ->get();
                return Datatables::of($results)
                        ->addColumn('location', function ($result) {
                            $loc = Locations::where('id', $result->location_id)
                                    ->first();
                            
                           return  $loc->name;
                           
                        })
                        ->addColumn('bill_date', function ($result) {
                                      return strip_tags(date('d M Y',  strtotime($result->bill_date)));
                                })
                                 ->addColumn('created_at', function ($result) {
                                      return strip_tags(date('d M Y',  strtotime($result->created_at)));
                                })
                        ->make(true);
    }
    
     public function decline_charges() {
        if (!HelperController::checkPrivilege()) {
            return Redirect::to('admin');
        }
        $page_title = "Declined Charges";
        $breadcrumb = "<li class='active'>Declined Charges</li>";
        $active = "";
        $active_sub = "decline_charges";

        return view("admin.declinecharges")->with("page_title", $page_title)
                        ->with("breadcrumb", $breadcrumb)
                        ->with("active", $active)
                        ->with("active_sub", $active_sub);
    }

    public function declineResults() {

        $results = BillingInfo::where('billing_status', '')
                ->where('payment_status',0)
                ->groupBy('store_id', 'location_id','bill_date', 'orginal_price')
                ->get();
      
                return Datatables::of($results)
                                ->addColumn('store', function ($result) {
                                    $store = Stores::where('id', $result->store_id)
                                            ->first();

                                    return $store->name;
                                })
                                ->addColumn('location', function ($result) {
                                    $loc = Locations::where('id', $result->location_id)
                                            ->first();

                                    return $loc->name;
                                })
                                ->make(true);
 
    }
    
    public function charges_month() {
        if (!HelperController::checkPrivilege()) {
            return Redirect::to('admin');
        }
        $page_title = "Charges this month";
        $breadcrumb = "<li class='active'>Charges this month</li>";
        $active = "";
        $active_sub = "charges_month";

        return view("admin.monthcharges")->with("page_title", $page_title)
                        ->with("breadcrumb", $breadcrumb)
                        ->with("active", $active)
                        ->with("active_sub", $active_sub);
    }

    public function monthResults() {

        $first_day_this_month = date('Y-m-01'); 
        $last_day_this_month  = date('Y-m-t');
  
        $results = BillingInfo::where('billing_status', 1)
                ->where('payment_status',1)
                ->where('created_at', '>=', $first_day_this_month)
                ->where('created_at','<=',$last_day_this_month)
                ->get();
      
                return Datatables::of($results)
                                ->addColumn('store', function ($result) {
                                    $store = Stores::where('id', $result->store_id)
                                            ->first();

                                    return $store->name;
                                })
                                ->addColumn('location', function ($result) {
                                    $loc = Locations::where('id', $result->location_id)
                                            ->first();

                                    return $loc->name;
                                })
                                ->addColumn('bill_date', function ($result) {
                                      return strip_tags(date('d M Y',  strtotime($result->bill_date)));
                                })
                                 ->addColumn('created_at', function ($result) {
                                      return strip_tags(date('d M Y',  strtotime($result->created_at)));
                                })
                                ->make(true);
 
    }

       public function declined_charges() {
        if (!HelperController::checkPrivilege()) {
            return Redirect::to('admin');
        }
        $page_title = "Declined Charges";
        $breadcrumb = "<li class='active'>Declined Charges</li>";
        $active = "";
        $active_sub = "declined_charges";

        return view("admin.declinedcharges")->with("page_title", $page_title)
                        ->with("breadcrumb", $breadcrumb)
                        ->with("active", $active)
                        ->with("active_sub", $active_sub);
    }

    public function declinedResults() {
         $id = Auth::user()->store_id;
        $results = BillingInfo::where('billing_status', '')
                ->where('payment_status',0)
                ->where('store_id',$id)
                ->groupBy('location_id','bill_date', 'orginal_price')
                ->get();
      
                return Datatables::of($results)
                               
                                ->addColumn('location', function ($result) {
                                    $loc = Locations::where('id', $result->location_id)
                                            ->first();

                                    return $loc->name;
                                })
                                ->addColumn('edit', function ($result) {
                            return "<a href='" . asset("admin/editcard/{$result->id}") . "'>
                            <span class='fa fa-pencil-square fa-2x'></span>
                        </a>";
                        })
                                ->make(true);
 
    }
       public function viewcard($id = false) {
        if (!HelperController::checkPrivilege()) {
            return Redirect::to('admin');
        }
        $page_title = "Edit Card";
        $breadcrumb = "<li class='active'>Edit Card</li>";
        $active = "";
        $active_sub = "declined_charges";
        $header = "<header>Update Card Information</header>";
        
        $result = BillingInfo::find($id);
        $user = User::where('store_id', $result->store_id)
                ->where('role','StoreAdmin')
                ->first();
        return view("admin.cardView")->with("page_title", $page_title)
                        ->with("breadcrumb", $breadcrumb)
                        ->with("active", $active)
                        ->with("result", $result)
                        ->with("user", $user)
                        ->with("header", $header)
                        ->with("active_sub", $active_sub);
    }
}