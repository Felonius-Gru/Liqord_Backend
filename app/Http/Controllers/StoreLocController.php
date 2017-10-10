<?php

namespace App\Http\Controllers;

use Auth;
use Input;
use Session;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;
use Hash;
use Response;
use Datatables;
use Mail;

use DateTime;
use File;
use Image;

/** Models */
use App\User;
use App\Products;
use App\Locations;
use App\StoreLocation;



class StoreLocController extends Controller {

    public function index() {
        if (!HelperController::checkPrivilege()) {
            return Redirect::to('admin');
        }
        $page_title = "Store Locations";
        $breadcrumb = "<li class='active'>Store Locations</li>";
        $active = "store_locations";
        $active_sub = "";

        return view("admin.storelocations")->with("page_title", $page_title)
                        ->with("breadcrumb", $breadcrumb)
                        ->with("active", $active)
                        ->with("active_sub", $active_sub);
    }

    /**
     * Users result page - ajax
     */
    public function results() {
        
        
        $storeid = Auth::user()->store_id;
       
        $results = StoreLocation::join('locations', 'store_location.location_id', '=', 'locations.id')
                    ->where('store_location.store_id', $storeid)
                    ->select('store_location.id','locations.name','store_location.is_active','store_location.print_label','store_location.print_inventory_label','store_location.location_id');

        
        return Datatables::of($results)  

                        ->addColumn('product', function ($result) {
                            return "<a href='" . asset("admin/product/{$result->location_id}") . "'>
                    <span class='fa fa-plus-square fa-2x'></span>
                </a>";
                        })
                           ->addColumn('shelves', function ($result) {
                            return "<a href='" . asset("admin/shelves/{$result->location_id}") . "'>
                    <span class='fa fa-plus-square fa-2x'></span>
                </a>";
                        })
                        
                         ->addColumn('checkbox', function ($result) {
                            if ($result->print_label == 0) {
                                $url = asset("admin/storelocations/printenable") . "/";
                                $link = "<a href='#' id='alter_link'
                               onclick=\"return action_confirm('{$url}{$result->id}','Store Location','Enable Print Label ?');\" >
                                <i class='fa fa-square-o fa-2x'></i>
                            </a>";
                            } else if ($result->print_label == 1) {
                                $url = asset("admin/storelocations/printdisable") . "/";
                                $link = "<a href='#' id='alter_link'
                               onclick=\"return action_confirm('{$url}{$result->id}','Store Location','Disable Print Label ?');\" >
                                <i class='fa fa-check-square-o fa-2x'></i>
                            </a>";
                            }
                            return $link;
                        })
                             ->addColumn('checklabel', function ($result) {
                            if ($result->print_inventory_label == 0) {
                                $url = asset("admin/storelocations/inventoryenable") . "/";
                                $link = "<a href='#' id='alter_link'
                               onclick=\"return action_confirm('{$url}{$result->id}','Store Location','Enable Print Inventory Label ?');\" >
                                <i class='fa fa-square-o fa-2x'></i>
                            </a>";
                            } else if ($result->print_inventory_label == 1) {
                                $url = asset("admin/storelocations/inventorydisable") . "/";
                                $link = "<a href='#' id='alter_link'
                               onclick=\"return action_confirm('{$url}{$result->id}','Store Location','Disable Print Inventory Label ?');\" >
                                <i class='fa fa-check-square-o fa-2x'></i>
                            </a>";
                            }
                            return $link;
                        })
                        
                       ->addColumn('action', function ($result) {
                            if ($result->is_active == 0) {
                                $url = asset("admin/storelocations/enable") . "/";
                                $link = "<a href='#' id='alter_link'
                               onclick=\"return action_confirm('{$url}{$result->id}','Store Location','Enable this Store Location ?');\" >
                                <i class='fa fa-check-circle fa-2x'></i>
                            </a>";
                            } else if ($result->is_active == 1) {
                                $url = asset("admin/storelocations/disable") . "/";
                                $link = "<a href='#' id='alter_link'
                               onclick=\"return action_confirm('{$url}{$result->id}','Store Location','Disable this Store Location ?');\" >
                                <i class='fa fa-times fa-2x'></i>
                            </a>";
                            }
                            return $link;
                        })
                        ->make(true);
    }
   
      public function viewlocation() {
          $storeid = Auth::user()->store_id;
          $store_loc = StoreLocation::where('store_id', $storeid)->get();
           $locations=array();
            foreach($store_loc as $list)
            {
                $locations[] = $list->location_id;
            }

            $ids = join(",",$locations); 
            $location = DB::select("SELECT * FROM `locations` where id NOT IN ($ids)");
            echo json_encode($location);
    }
    
   public function update(Request $request, $id = false) {
        if (!HelperController::checkPrivilege("post")) {
            return Redirect::to('admin');
        }
        $data = $request->all();
        $rules = array(
                'location' => 'required'
            );
       
        if (isset($data['id']) == '') {
            $location = new StoreLocation;
        }

        $valid = Validator::make($data, $rules);

        if ($valid->passes()) {
            $location->store_id = Auth::user()->store_id;
            $location->location_id = HelperController::clean($data['location']);
           
          
            $save = $location->save();
            if ($save) {
                if ($id == false) {
                    Session::flash("success", "Location Added");
                    return Redirect::to("admin/locations");
                } else {
                    Session::flash("success", "Location updated");
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
    
    public function enable($id) {
        if (!HelperController::checkPrivilege()) {
            return Redirect::to('admin');
        }

        $save = StoreLocation::where("id", $id)->update(['is_active' => 1]);
        if ($save) {
            Session::flash("success", "Store Location enabled");
        } else {
            // Something went wrong.
            Session::flash("fail", "Opps... Something went wrong");
        }

        return Redirect::back();
    }

    /**
     * News disable page     
     */
    public function disable($id) {
        if (!HelperController::checkPrivilege()) {
            return Redirect::to('admin');
        }

        $save = StoreLocation::where("id", $id)->update(['is_active' => 0]);
        if ($save) {
            Session::flash("success", "Store Location disabled");
        } else {
            // Something went wrong.
            Session::flash("fail", "Opps... Something went wrong");
        }

        return Redirect::back();
    }  
    
    public function printenable($id) {
        if (!HelperController::checkPrivilege()) {
            return Redirect::to('admin');
        }

        $save = StoreLocation::where("id", $id)->update(['print_label' => 1]);
        if ($save) {
            Session::flash("success", "Print Label enabled");
        } else {
            // Something went wrong.
            Session::flash("fail", "Opps... Something went wrong");
        }

        return Redirect::back();
    }

    /**
     * News disable page     
     */
    public function printdisable($id) {
        if (!HelperController::checkPrivilege()) {
            return Redirect::to('admin');
        }

        $save = StoreLocation::where("id", $id)->update(['print_label' => 0]);
        if ($save) {
            Session::flash("success", "Print Label disabled");
        } else {
            // Something went wrong.
            Session::flash("fail", "Opps... Something went wrong");
        }

        return Redirect::back();
    }  
    
    public function inventoryenable($id) {
        if (!HelperController::checkPrivilege()) {
            return Redirect::to('admin');
        }

        $save = StoreLocation::where("id", $id)->update(['print_inventory_label' => 1]);
        if ($save) {
            Session::flash("success", "Print Inventory Label enabled");
        } else {
            // Something went wrong.
            Session::flash("fail", "Opps... Something went wrong");
        }

        return Redirect::back();
    }

    /**
     * News disable page     
     */
    public function inventorydisable($id) {
        if (!HelperController::checkPrivilege()) {
            return Redirect::to('admin');
        }

        $save = StoreLocation::where("id", $id)->update(['print_inventory_label' => 0]);
        if ($save) {
            Session::flash("success", "Print Inventory Label disabled");
        } else {
            // Something went wrong.
            Session::flash("fail", "Opps... Something went wrong");
        }

        return Redirect::back();
    }  
}


