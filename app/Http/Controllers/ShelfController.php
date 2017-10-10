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
use Excel;
use DB;
use DateTime;
use File;
use Image;

/** Models */
use App\User;
use App\Shelf;



class ShelfController extends Controller {

    public function index($location_id = false) {
        if (!HelperController::checkPrivilege()) {
            return Redirect::to('admin');
        }
       
        $page_title = "SHELVES";
        $breadcrumb = "<li class='active'>Shelves</li>";
        $active = "store_locations";
        $active_sub = "";

        return view("admin.shelf")->with("page_title", $page_title)
                        ->with("breadcrumb", $breadcrumb)
                        ->with("active", $active)
                        ->with("location_id", $location_id)
                        ->with("active_sub", $active_sub);
    }

    /**
     * Users result page - ajax
     */
    public function results($location_id = false) {
      
        $storeid = Auth::user()->store_id;
        $results=Shelf::where('store_id',$storeid)->where('location_id',$location_id)->get();
        return Datatables::of($results)  
                 ->editColumn('created_at', function ($result) {

                               return strip_tags(date('d F Y',  strtotime($result->created_at)));
                         
                        })
                        ->addColumn('edit', function ($result) {
                            return "<a href='" . asset("admin/shelves/update/{$result->location_id}/{$result->id}") . "'>
                    <span class='fa fa-pencil-square fa-2x'></span>
                </a>";
                        })
                        ->addColumn('action', function ($result) {
                            if ($result->is_active == 0) {
                                $url = asset("admin/shelves/enable") . "/";
                                $link = "<a href='#' id='alter_link'
                               onclick=\"return action_confirm('{$url}{$result->id}','Shelf','Enable this Shelf ?');\" >
                                <i class='fa fa-check-circle fa-2x'></i>
                            </a>";
                            } else if ($result->is_active == 1) {
                                $url = asset("admin/shelves/disable") . "/";
                                $link = "<a href='#' id='alter_link'
                               onclick=\"return action_confirm('{$url}{$result->id}','Shelf','Disable this Shelf ?');\" >
                                <i class='fa fa-times fa-2x'></i>
                            </a>";
                            }
                            return $link;
                        })
                       
                        ->make(true);
    }
   
     /**
     * User enable page
     */
    
    
    
    public function view($location_id,$id = false) {
       
         if (!HelperController::checkPrivilege()) {
            return Redirect::to('admin');
        }
        $submit = "update";
        if ($id == false) {
            $submit = "add";
            $result =new Shelf;
            $page_title = "Add Shelf";
            $header = "<header>Add Shelf</header>";
        } else {
            $result = Shelf::where('id',$id)->first();
            if (is_null($result)) {
                Session::flash("fail", "No such Shelf");
                return Redirect::back();
            }
          
             $page_title = "Update Shelf Details";
             $header = "<header>Update Shelf</header>";
  
        }
        
        
        $breadcrumb = "<li class='active'>Shelf</li>";
        $active = "store_locations";
        $active_sub = "";

        return view("admin.shelfView")->with("page_title", $page_title)
                        ->with("breadcrumb", $breadcrumb)
                        ->with("active", $active)
                        ->with("header", $header)
                        ->with("active_sub", $active_sub)
                        ->with("id", $id) 
                        ->with("location_id", $location_id) 
                        ->with("result", $result)
                        ->with("submit", $submit);
  
    }

    /**
     * Shelf update page - Update
     */
    
      public function update(Request $request,$location_id,$id = false) {
        if(!HelperController::checkPrivilege()) {
            return Redirect::to("admin");
        }   
        $data = $request->all();
        
        $rules = array();
        if($id == false) { 
            $Shelves =new Shelf;
            $rules = array('shelf_number' => 'required');
         
        }
        else {
            $Shelves = Shelf::find($id);
            if(is_null($Shelves)) {
                Session::flash("fail", "No such Shelf");
                return Redirect::back();
            }
            
        }
        
        $valid = Validator::make($data, $rules);
       
        if ($valid->passes()) {
            $Shelves->store_id = Auth::user()->store_id; 
            $Shelves->location_id = $location_id;
            $Shelves->shelf_number = HelperController::clean($data['shelf_number']);
            $Shelves->description = HelperController::clean($data['description']);
             $check_shelf = Shelf::where("shelf_number", $Shelves->shelf_number)
                        ->where('store_id',$Shelves->store_id)
                        ->where('location_id',$Shelves->location_id)
                        ->count();
                if ($check_shelf != 0) {
                    Session::flash("fail", "Opps...Shelf Number Already Exist");
                    $request->flash();
                    return Redirect::back();
                }
            $save = $Shelves->save();
      
            if ($save) {
                if($id == false) {
                    Session::flash("success", "Shelf saved");
                    return Redirect::to("admin/shelves/update/{$location_id}/{$Shelves->id}");
                }
                else {
                    Session::flash("success", "Shelf updated");  
                    return Redirect::back();
                }                               
            }
            else {
                // Something went wrong.
                Request::flash();
                Session::flash("fail", "Opps... Something went wrong");
            }               

            return Redirect::back();
        } 
        else {
            // Something went wrong.
            return Redirect::back()->withErrors($valid);
        }
    } 
    public function enable($id) {
        if (!HelperController::checkPrivilege()) {
            return Redirect::to('admin');
        }

        $save = Shelf::where("id", $id)->update(['is_active' => 1]);
        if ($save) {
            Session::flash("success", "Shelf enabled");
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

        $save = Shelf::where("id", $id)->update(['is_active' => 0]);
        if ($save) {
            Session::flash("success", "Shelf disabled");
        } else {
            // Something went wrong.
            Session::flash("fail", "Opps... Something went wrong");
        }

        return Redirect::back();
    }  
 
}


