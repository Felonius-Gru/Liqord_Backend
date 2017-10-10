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


class ItemsController extends Controller {

    public function items() {
        if (!HelperController::checkPrivilege()) {
            return Redirect::to('admin');
        }
        $page_title = "Items";
        $breadcrumb = "<li class='active'>Items</li>";
        $active = "items";
        $active_sub = "";

        return view("admin.items")->with("page_title", $page_title)
                        ->with("breadcrumb", $breadcrumb)
                        ->with("active", $active)
                        ->with("active_sub", $active_sub);
    }

    /**
     * Users result page - ajax
     */
    public function itemsResults() {
        $results = Items::select(['id', 'category','ada_no','code','upc','brand_name','proof','created_at'])->where('is_active',1)->orderBy('id', 'desc');

       return Datatables::of($results)
                        ->editColumn('created_at', function ($result) {

                           return strip_tags(date("F d , Y",  strtotime($result->created_at)));
                       
                       })    
                        ->addColumn('edit', function ($result) {
                            return "<a href='" . asset("admin/items/edit/{$result->id}") . "'>
                            <span class='fa fa-pencil-square fa-2x'></span>
                        </a>";
                        })
                        ->addColumn('action', function ($result) {
                          $url = asset("admin/items/delete") . "/";
                           $link = "<a href='#' id='alter_link'
                              onclick=\"return action_confirm('{$url}{$result->id}','Items','Delete this Item ?');\" >
                               <i class='fa fa-times fa-2x'></i>
                           </a>";
                           return $link;
                        })
                       
                        ->make(true);
    }

    /**
     * User view page
     */
    public function view($id = false) {
        if (!HelperController::checkPrivilege()) {
            return Redirect::to('admin');
        }
        $submit = "Update";
        
        if ($id == false) {
            $submit = "Save";

            $result = new Items;
            // For menu part
            $page_title = "Item Details";
            $breadcrumb = "<li class='active'>Item Details</li>";
            $header = "<header>Edit Item</header>";
            $active = "item";
            $active_sub = "";


        } else {
            $result = Items::where("id", $id)->first();

            if (is_null($result)) {
                Session::flash("fail", "No such Item");
                return Redirect::back();
            }
            // For menu part
            $page_title = "Update";
            $breadcrumb = "<li class='active'>Update</li>";
            $header = "<header>Update Item</header>";
            $active = "items";
            $active_sub = "";
        }
        
        return view("admin.itemView")->with("page_title", $page_title)
                        ->with("breadcrumb", $breadcrumb)
                        ->with("header", $header)
                        ->with("active", $active)
                        ->with("active_sub", $active_sub)
                        ->with("result", $result)
                        ->with("id", $id)
                        ->with("submit", $submit);
    }

    /**
     * User update page
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
            $item->upc = HelperController::clean($data['upc']);
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
        if(!HelperController::checkPrivilege())
        {
            return Redirect::to('admin');
        }

        $item = Items::find($id);
        $save =  $item->delete();
        if($save) {
            Session::flash("success", "Item deleted");
        } 
        else {
            // Something went wrong.
            Session::flash("fail", "Opps... Something went wrong");
        }        
        
        return Redirect::back();
        
    }

  
}