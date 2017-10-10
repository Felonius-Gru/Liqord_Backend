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


class StoreController extends Controller {

    public function stores() {
        if (!HelperController::checkPrivilege()) {
            return Redirect::to('admin');
        }
        $page_title = "Stores";
        $breadcrumb = "<li class='active'>Stores</li>";
        $active = "stores";
        $active_sub = "";

        return view("admin.stores")->with("page_title", $page_title)
                        ->with("breadcrumb", $breadcrumb)
                        ->with("active", $active)
                        ->with("active_sub", $active_sub);
    }

    /**
     * Stores result page - ajax
     */
    public function storeResults() {
        $results = Stores::select(['id', 'name','is_active']);

       return Datatables::of($results)

                        
             ->addColumn('edit', function ($result) {
                            return "<a href='" . asset("admin/stores/update/{$result->id}") . "'>
                            <span class='fa fa-pencil-square fa-2x'></span>
                        </a>";
                        })         
             ->addColumn('action', function ($result) {
                if ($result->is_active == 0) {
                    $url = asset("admin/stores/enable") . "/";
                    $link = "<a href='#' id='alter_link'
                   onclick=\"return action_confirm('{$url}{$result->id}','Store','Do you want to enable this Store ?');\" >
                    <i class='fa fa-check-circle fa-2x'></i>
                </a>";
                } else if ($result->is_active == 1) {
                    $url = asset("admin/stores/disable") . "/";
                    $link = "<a href='#' id='alter_link'
                   onclick=\"return action_confirm('{$url}{$result->id}','Store','Do you want to disable this Store ?');\" >
                    <i class='fa fa-times fa-2x'></i>
                </a>";
                }
                return $link;
            })
                       
                        ->make(true);
    }


    
      /**
     * Store view page
     */
    public function view($id = false) {
        if (!HelperController::checkPrivilege()) {
            return Redirect::to('admin');
        }

        $submit = "update";
    
        $locations = Locations::where('is_active',1)->get();
    
        if ($id == false) {
            $submit = "add";
            $result = new Stores;     
            $page_title = "Add Store";
            $header = "<header>Add Store</header>";
            $loc = '';
        } 
        else {
            //$result = Slider::where("id", $id)->first();
            $result = Stores::find($id); 
            $storelocation = StoreLocation::where('store_id',$id)->get();
            $loc = array();
            foreach ($storelocation as $location) {
                $loc[] = $location->location_id;
            }
            $page_title = "Update Store Details";
            $header = "<header>Update Store</header>";
            
            if (is_null($result)) {
                Session::flash("fail", "Store not found");
                //return Redirect::back();
                 return Redirect::to("admin/stores");
            }
        }
        $breadcrumb = "<li class='active'>ADD STORE</li>";
        $active = "stores";
       
        $active_sub = "";
//       echo "<pre>";
//        print_r($result);die;
        return view("admin.storesView")->with("page_title", $page_title)
                        ->with("breadcrumb", $breadcrumb)
                        ->with("active", $active)
                        ->with("header", $header)
                       ->with("active_sub",$active_sub)
                        ->with("result", $result)
                        ->with("id", $id)
                        ->with("locations", $locations)
                        ->with("loc", $loc)
                        ->with("submit", $submit);
    }
    /**
     * Store update page    
     */
    public function update(Request $request, $id = false) {
//       if (!HelperController::checkPrivilege()) {
//            return Redirect::to('admin');
//        }

        $data = $request->all();
       
            $rules = array(
                'store' => 'required'                
            );
       
        
        if ($id == false) {
            $store = new Stores;
        } else {
            $store = Stores::find($id);
            if (is_null($store)) {
                Session::flash("fail", "Store not found");
                $request->flash();
                return Redirect::to("admin/users");
            }
        }

        $valid = Validator::make($data, $rules);

        if ($valid->passes()) {
            $store->name = HelperController::clean($data['store']);
            $save = $store->save();
            if (isset($data['location'])) {
                if ($id == false) {
                    $storelocation = new StoreLocation;
                    $storeid = $store->id;
                } else {

                    $storelocation = new StoreLocation;
                    $storeid = $id;
                }

                $locations = $data['location'];

                foreach ($locations as $location) {
                    $res = StoreLocation::where('store_id', $id)->where('location_id', $location)->get();
                    if (count($res) == 0) {
                        $dt = new DateTime();
                        $created_at = $dt->format('Y-m-d H:i:s');
                        $updated_at = $dt->format('Y-m-d H:i:s');
                        $save = $storelocation->insert(
                                ['store_id' => $storeid, 'location_id' => $location, 'created_at' => $created_at, 'updated_at' => $updated_at]
                        );
                    }
                }
                $location = StoreLocation::where('store_id', $id)->get();
                foreach ($location as $loc_id) {
                    if (!in_array($loc_id->location_id, $locations)) {
                        $dellocation = StoreLocation::where('store_id', $id)
                                ->where('location_id', $loc_id->location_id)
                                ->delete();
                    }
                }
            }
           
            if ($save) {
                if ($id == false) {
                    Session::flash("success", "Store created");
                    
                    return Redirect::back();
                } else {
                    Session::flash("success", "Store updated");
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
     * Store enable page
     */
    public function enableStore($id) {
        if (!HelperController::checkPrivilege()) {
            return Redirect::to('admin');
        }

        $save = Stores::where("id", $id)->update(['is_active' => 1]);
        if ($save) {
            Session::flash("success", "Store enabled");
        } else {
            // Something went wrong.
            Session::flash("fail", "Opps... Something went wrong");
        }

        return Redirect::back();
    }

    /**
     * Store disable page
     */
    public function disableStore($id) {
        if (!HelperController::checkPrivilege()) {
            return Redirect::to('admin');
        }

        $save = Stores::where("id", $id)->update(['is_active' => 0]);
        if ($save) {
            Session::flash("success", "Store disabled");
        } else {
            // Something went wrong.
            Session::flash("fail", "Opps... Something went wrong");
        }

        return Redirect::back();
    }
}