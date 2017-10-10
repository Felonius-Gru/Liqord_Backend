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
use App\Locations;
use App\StoreLocation;


class LocationController extends Controller {

    public function locations() {
        if (!HelperController::checkPrivilege()) {
            return Redirect::to('admin');
        }
        $page_title = "Locations";
        $breadcrumb = "<li class='active'>Locations</li>";
        $active = "locations";
        $active_sub = "";

        return view("admin.locations")->with("page_title", $page_title)
                        ->with("breadcrumb", $breadcrumb)
                        ->with("active", $active)
                        ->with("active_sub", $active_sub);
    }

    /**
     * Users result page - ajax
     */
    public function locationResults() {
        $results = Locations::select(['id', 'name','is_active']);
    
       return Datatables::of($results)

                        
             ->editColumn('edit', function ($result) {
                        $location = str_replace(" ", "&", $result->name);
                        return "<a onclick=viewdetail('$result->id','$location'); href='#myModal1' data-toggle='modal'>
                            <span class='fa fa-pencil-square fa-2x'></span>
                        </a>";
                        })  
                         ->addColumn('view_stores', function ($result) {
                                return "<a href='" . asset("admin/locations/store/view/{$result->id}") . "'>
                            <span class='fa fa-pencil-square fa-2x'></span>
                        </a>";
                            })
//                        ->editColumn('view_stores', function ($result) {
//                                return strip_tags($result->stores->store_id);
//                            })
             ->addColumn('action', function ($result) {
                if ($result->is_active == 0) {
                    $url = asset("admin/locations/enable") . "/";
                    $link = "<a href='#' id='alter_link'
                   onclick=\"return action_confirm('{$url}{$result->id}','Location','Do you want to enable this Location ?');\" >
                    <i class='fa fa-check-circle fa-2x'></i>
                </a>";
                } else if ($result->is_active == 1) {
                    $url = asset("admin/locations/disable") . "/";
                    $link = "<a href='#' id='alter_link'
                   onclick=\"return action_confirm('{$url}{$result->id}','Location','Do you want to disable this Location ?');\" >
                    <i class='fa fa-times fa-2x'></i>
                </a>";
                }
                return $link;
            })
                       
                        ->make(true);
    }


    /**
     * User update page
     */
    public function update(Request $request, $id = false) {
//        if (!HelperController::checkPrivilege("post")) {
//            return Redirect::to('admin');
//        }
        $data = $request->all();
        $rules = array(
                'location' => 'required'
            );
       
        if (isset($data['id']) == '') {
            $location = new Locations;
        } else {
            $id = $data['id'];
            $location = Locations::find($id);
            if (is_null($location)) {
                Session::flash("fail", "No such location found");
                $request->flash();
                return Redirect::to("admin/locations");
            }
        }

        $valid = Validator::make($data, $rules);

        if ($valid->passes()) {
            $check_location = Locations::where('name',$data['location']) ->count();  
            if ($check_location == 0) { 
            $location->name = HelperController::clean($data['location']);
           
          
            $save = $location->save();
            if ($save) {
                if ($id == false) {
                    Session::flash("success", "Location Added");
                    return Redirect::back();                   
                } else {
                    Session::flash("success", "Location updated");
                    return Redirect::back();
                }
            } else {
                // Something went wrong.
                $request->flash();
                Session::flash("fail", "Opps... Something went wrong");
            }
            }else{
                  Session::flash("fail", "This Location Already Exist");
            }
            return Redirect::back();
        } else {
            // Something went wrong.
            return Redirect::back()->withErrors($valid);
        }
    }
   
  /**
     * Location enable page
     */
    public function enableLocation($id) {
        if (!HelperController::checkPrivilege()) {
            return Redirect::to('admin');
        }

        $save = Locations::where("id", $id)->update(['is_active' => 1]);
        if ($save) {
            Session::flash("success", "Location enabled");
        } else {
            // Something went wrong.
            Session::flash("fail", "Opps... Something went wrong");
        }

        return Redirect::back();
    }

    /**
     * User disable page
     */
    public function disableLocation($id) {
        if (!HelperController::checkPrivilege()) {
            return Redirect::to('admin');
        }

        $save = Locations::where("id", $id)->update(['is_active' => 0]);
        if ($save) {
            Session::flash("success", "Location disabled");
        } else {
            // Something went wrong.
            Session::flash("fail", "Opps... Something went wrong");
        }

        return Redirect::back();
    }
    
    public function locationStore($id)
    {
        if (!HelperController::checkPrivilege()) {
            return Redirect::to('admin');
        }
        
        
        $page_title = "Stores";
        $breadcrumb = "<li class='active'>Stores</li>";
        $active = "locations";
        $active_sub = "";
        $locationstore=StoreLocation::join('stores','store_location.store_id','=','stores.id')
                                  ->select('stores.name as store_name','stores.id as store_id','store_location.location_id as location_id')
                                  ->where('store_location.location_id',$id)
                                  ->orderBy('stores.id','asc')->get();
        $location=Locations::where('id',$id)->first();

        return view("admin.locationStores")->with("page_title", $page_title)
                        ->with("breadcrumb", $breadcrumb)
                        ->with("location", $location)
                        ->with("id", $id)
                        ->with("locationstore", $locationstore)
                        ->with("active", $active)
                        ->with("active_sub", $active_sub);
    }
//    public function locationStoreResults() {
//        $results=StoreLocation::join('stores','store_location.store_id','=','stores.id')
//                                  ->select('stores.name as store_name','stores.id as store_id','store_location.location_id as location_id')
//                                  ->where('store_location.location_id',$id)
//                                  ->orderBy('stores.id','asc')->get();
//    
//       return Datatables::of($results)
//                       
//                        ->make(true);
//    }
}