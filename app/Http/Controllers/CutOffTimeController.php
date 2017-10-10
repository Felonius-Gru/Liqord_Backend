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
use App\CutOffTime;


class CutOffTimeController extends Controller {

    public function cutofftime() {
        if (!HelperController::checkPrivilege()) {
            return Redirect::to('admin');
        }
        $page_title = "cutoffTime";
        $breadcrumb = "<li class='active'>cutoffTime</li>";
        $active = "cutoffTime";
        $active_sub = "";

        return view("admin.cutofftime")->with("page_title", $page_title)
                        ->with("breadcrumb", $breadcrumb)
                        ->with("active", $active)
                        ->with("active_sub", $active_sub);
    }

    /**
     * Stores result page - ajax
     */
    public function cutofftimeResults() {
         $results = CutOffTime::join("locations", "cutofftime.location_id", "=", "locations.id")
                                 ->join('stores','cutofftime.store_id','=','stores.id')
                                 ->select(['cutofftime.id', 'cutofftime.cutoffdate', 'cutofftime.location_id','cutofftime.store_id', 'locations.name', 'cutofftime.is_active','stores.name as store_name'])
                                 ->get();
         
         
       return Datatables::of($results)
               
                  ->editColumn('cutoffdate', function ($result) {
                    
                           if($result->cutoffdate == "sunday this week")
                           {
                               $result->cutoffdate="Sunday";
                           }
                           elseif($result->cutoffdate == "monday this week")
                           {
                               $result->cutoffdate="Monday";
                               
                           }
                           elseif($result->cutoffdate == "tuesday this week")
                           {
                               $result->cutoffdate="Tuesday";
                               
                           }
                           
                           elseif($result->cutoffdate == "wednesday this week")
                           {
                               $result->cutoffdate="Wednesday";
                               
                               
                           }
                           
                           elseif($result->cutoffdate == "thursday this week")
                           {
                               $result->cutoffdate="Thursday";
                               
                           }
                           
                           elseif($result->cutoffdate == "friday this week")
                           {
                               $result->cutoffdate="Friday";
                               
                           }
                           
                           elseif($result->cutoffdate == "saturday this week")
                           {
                               $result->cutoffdate="Saturday";
                               
                           }
                           return strip_tags($result->cutoffdate);
                        })
//                       ->editColumn('end_time', function ($result) {
//
//                              return strip_tags(date('h:i A',  strtotime($result->end_time)));
//                         
//                        })
                   ->editColumn('location', function ($result) {
                    return strip_tags($result->name);
                        })
                    ->editColumn('store', function ($result) {
                    return strip_tags($result->store_name);
                        })
                  ->addColumn('edit', function ($result) {
                            return "<a href='" . asset("admin/cutofftime/update/{$result->id}") . "'>
                            <span class='fa fa-pencil-square fa-2x'></span>
                        </a>";
                        })         
                 ->addColumn('action', function ($result) {
                if ($result->is_active == 0) {
                    $url = asset("admin/cutofftime/enable") . "/";
                    $link = "<a href='#' id='alter_link'
                   onclick=\"return action_confirm('{$url}{$result->id}','Store','Enable this Cut off Time ?');\" >
                    <i class='fa fa-check-circle fa-2x'></i>
                </a>";
                } else if ($result->is_active == 1) {
                    $url = asset("admin/cutofftime/disable") . "/";
                    $link = "<a href='#' id='alter_link'
                   onclick=\"return action_confirm('{$url}{$result->id}','Store','Disable this Cut off Time ?');\" >
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
    
//        $cutofftime = CutOffTime::where('is_active',1)->get();
//        $locations = Locations::get();
        $stores=Stores::get();

            
        if ($id == false) {
            $submit = "add";
            $result = new CutOffTime;     
            $page_title = "Add CutOffTime";
            $header = "<header>Add CutOffTime</header>";
            $loc = '';
        } 
        else {
            //$result = Slider::where("id", $id)->first();
            $result = CutOffTime::find($id); 
           
           
           
            $page_title = "Update CutOffTime Details";
            $header = "<header>Update CutOffTime</header>";
            
            if (is_null($result)) {
                Session::flash("fail", "CutOffTime not found");
                //return Redirect::back();
                 return Redirect::to("admin/cutofftime");
            }
        }
        $breadcrumb = "<li class='active'>ADD CutOffTime</li>";
        $active = "cutoffTime";
       
        $active_sub = "";
//       echo "<pre>";
//        print_r($result);die;
        return view("admin.cutofftimeView")->with("page_title", $page_title)
                        ->with("breadcrumb", $breadcrumb)
                        ->with("active", $active)
                        ->with("header", $header)
                       ->with("active_sub",$active_sub)
                        ->with("result", $result)
                        ->with("id", $id)
//                        ->with("location", $location)
//                       ->with("loc", $loc)
                        ->with("stores", $stores)
                        ->with("submit", $submit);
    }
    /**
     * Store update page    
     */
    public function update(Request $request, $id = false) {
       if (!HelperController::checkPrivilege()) {
            return Redirect::to('admin');
        }

        $data = $request->all();
       
            $rules = array(
                'cutoffdate' => 'required',
              
            );
            
            if($id==false)
            {
              $rules = array(
                'cutoffdate' => 'required',
                  'location'=>'required',
                  'store'=>'required',
              
            );  
            }
      
        if ($id == false) {
            $cutofftime = new CutOffTime;
        } else {
            $cutofftime = CutOffTime::find($id);
            $location=Locations::where('id',$cutofftime->location_id)->first();
            
            if (is_null($cutofftime)) {
                Session::flash("fail", "Store not found");
                $request->flash();
                return Redirect::to("admin/cutofftime");
            }
        }

        $valid = Validator::make($data, $rules);

        if ($valid->passes()) {
//            $date=strtotime($data['start_time']." ".$data['cutoffdate']); 
            
            $cutofftime->cutoffdate=HelperController::clean($data['cutoffdate']);
            $date = strtotime($data['end_time']);
            $cutofftime->end_time= date("H:i:s", $date);
            
            if($id==false)
            {
            $cutofftime->location_id = HelperController::clean($data['location']);
            
            $cutofftime->store_id = HelperController::clean($data['store']);
            }
            $check_time = CutOffTime::where('location_id',$cutofftime->location_id)
                                         ->where('store_id',$cutofftime->store_id)
                                         ->count();
            
            if($id==false)
            {
                if ($check_time != 0) {
                      Session::flash("fail", "Opps... CutoffTime for this location and store  already added");
                      $request->flash();
                      return Redirect::back();
                }
             }
            $save = $cutofftime->save();
            if ($save) {
                if ($id == false) {
                    Session::flash("success", "cutofftime added");
                    
                    return Redirect::to("admin/cutofftime");
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
    public function enablecutofftime($id) {
        if (!HelperController::checkPrivilege()) {
            return Redirect::to('admin');
        }

        $save = CutOffTime::where("id", $id)->update(['is_active' => 1]);
        if ($save) {
            Session::flash("success", "Cut-off Time enabled");
        } else {
            // Something went wrong.
            Session::flash("fail", "Opps... Something went wrong");
        }

        return Redirect::back();
    }

    /**
     * Store disable page
     */
    public function disablecutofftime($id) {
        if (!HelperController::checkPrivilege()) {
            return Redirect::to('admin');
        }

        $save = CutOffTime::where("id", $id)->update(['is_active' => 0]);
        if ($save) {
            Session::flash("success", "Cut-off Time disabled");
        } else {
            // Something went wrong.
            Session::flash("fail", "Opps... Something went wrong");
        }

        return Redirect::back();
    }
     public function viewstorelocation(Request $request) {
          $data = $request->all();
          $data = HelperController::cleanArray($data);
//          echo "hai";die;
             if ($request->has('store_id')) {
                    $storeid = $data['store_id'];
                    $store_loc = StoreLocation:: join('locations','store_location.location_id','=','locations.id')
                                              ->select('locations.id','locations.name')->where('store_id', $storeid)
                                             ->get();

                    echo json_encode($store_loc);
                }
           else {
              return Response::json(array('response' => 'failed', "message" => " No such data exists"));      
                }
           
            
    }
    
}
