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
use App\Stores;
use App\Locations;
use App\StoreLocation;

class TabUserController extends Controller {

    public function users() {
        if (!HelperController::checkPrivilege()) {
            return Redirect::to('admin');
        }
        $page_title = "Users";
        $breadcrumb = "<li class='active'>Users</li>";
        $active = "storeusers";
        $active_sub = "";

        return view("admin.tabusers")->with("page_title", $page_title)
                        ->with("breadcrumb", $breadcrumb)
                        ->with("active", $active)
                        ->with("active_sub", $active_sub);
    }

    /**
     * Users result page - ajax
     */
    public function usersResults() {

        $results = User::select(['id', 'username', 'store_id', 'location_id', 'is_active'])
                        ->where('store_id', Auth::user()->store_id)
                        ->where('role', '=', '')->get();

        return Datatables::of($results)
                        ->editColumn('store_id', function ($result) {
                            $store = Stores::where("id", $result->store_id)->select('name')->first();
                            return $store['name'];
                        })
                        ->editColumn('location_id', function ($result) {
                            $location = Locations::where("id", $result->location_id)->select('name')->first();
                            return $location['name'];
                        })
                        ->addColumn('edit', function ($result) {
                            return "<a href='" . asset("admin/tabusers/update/{$result->id}") . "'>
                            <span class='fa fa-pencil-square fa-2x'></span>
                        </a>";
                        })
                        ->addColumn('action', function ($result) {
                            if ($result->is_active == 0) {
                                $url = asset("admin/tabusers/enable") . "/";
                                $link = "<a href='#' id='alter_link'
                               onclick=\"return action_confirm('{$url}{$result->id}','User','Do you want to enable this user ?');\" >
                                <i class='fa fa-check-circle fa-2x'></i>
                            </a>";
                            } else if ($result->is_active == 1) {
                                $url = asset("admin/tabusers/disable") . "/";
                                $link = "<a href='#' id='alter_link'
                               onclick=\"return action_confirm('{$url}{$result->id}','User','Do you want to disable this user ?');\" >
                                <i class='fa fa-times fa-2x'></i>
                            </a>";
                            }
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
        $store = Stores::where('is_active', 1)->where('id',Auth::user()->store_id)->first();
        $store_locations =StoreLocation::join('locations','store_location.location_id','=','locations.id')
                           ->where('store_location.store_id',Auth::user()->store_id)
                           ->select('store_location.location_id','locations.id','locations.name')->get();

        if ($id == false) {
            $submit = "Register";
            $result = new User;
            // For menu part
            $page_title = "Device Registration";
            $breadcrumb = "<li class='active'>Device Registration</li>";
            $header = "<header>Add a new User</header>";
            $active = "storeusers";
            $active_sub = "";
        } else {
            $result = User::find($id);
            if (is_null($result)) {
                Session::flash("fail", "No such User");
                return Redirect::back();
            }
            // For menu part
            $page_title = "Update";
            $breadcrumb = "<li class='active'>Update</li>";
            $header = "<header>Update user</header>";
            $active = "storeusers";
            $active_sub = "";
        }
//        print_r($location->name);die;
        return view("admin.tabusersView")->with("page_title", $page_title)
                        ->with("breadcrumb", $breadcrumb)
                        ->with("header", $header)
                        ->with("active", $active)
                        ->with("active_sub", $active_sub)
                        ->with("result", $result)
                        ->with("store", $store)
                        ->with("store_locations", $store_locations)
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
        if ($id) {
            $rules = array(
                'store' => 'required',
                'location' => 'required'
            );
        } else {
            $rules = array(
                'username' => 'required',
                'password' => 'required'
            );
        }
        if ($id == false) {
            $user = new User;
        } else {
            $user = User::find($id);
            if (is_null($user)) {
                Session::flash("fail", "No such user found");
                $request->flash();
                return Redirect::to("admin/users");
            }
        }

        $valid = Validator::make($data, $rules);

        if ($valid->passes()) {

            if ($request->has("username"))
                $user->username = HelperController::clean($data['username']);
            if ($id == false) {
                $check_user = User::where("username", $user->username)->count();
                if ($check_user != 0) {
                    Session::flash("fail", "Opps... Username already taken");
                    $request->flash();
                    return Redirect::back();
                }
            }
            if ($request->has("password"))
                $user->password = bcrypt(HelperController::clean($data['password']));
            $user->store_id = Auth::user()->store_id;
            $user->location_id = HelperController::clean($data['location']);
            $save = $user->save();

            if ($save) {
                if ($id == false) {
                    Session::flash("success", "Device Registered for the user");
                    return Redirect::to("admin/tabusers");
                } else {
                    Session::flash("success", "User updated");
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

    public function enableUser($id) {
        if (!HelperController::checkPrivilege()) {
            return Redirect::to('admin');
        }

        $save = User::where("id", $id)->update(['is_active' => 1]);
        if ($save) {
            Session::flash("success", "User enabled");
        } else {
            // Something went wrong.
            Session::flash("fail", "Opps... Something went wrong");
        }

        return Redirect::back();
    }

    /**
     * User disable page
     */
    public function disableUser($id) {
        if (!HelperController::checkPrivilege()) {
            return Redirect::to('admin');
        }

        $save = User::where("id", $id)->update(['is_active' => 0]);
        if ($save) {
            Session::flash("success", "User disabled");
        } else {
            // Something went wrong.
            Session::flash("fail", "Opps... Something went wrong");
        }

        return Redirect::back();
    }

    /**
     * Delete user
     */
    public function deleteUser($id) {
        if (!HelperController::checkPrivilege()) {
            return Redirect::to('admin');
        }

        $user = User::find($id);
        if ($user->pic != "user.png") {
            File::delete("uploads/user_files/" . $user->pic);
        }

        $users = User::where("referral", $id)->get();
        foreach ($users as $user) {
            if ($user->pic != "user.png") {
                File::delete("uploads/user_files/" . $user->pic);
            }
        }

        User::where("id", $id)->delete();
        User::where("referral", $id)->delete();
        Session::flash("success", "User and all its referal deleted");

        return Redirect::back();
    }

    public function viewstorelocation(Request $request) {
        $data = $request->all();
        $data = HelperController::cleanArray($data);
//          echo $data;die;
        if ($request->has('store_id')) {
            $storeid = $data['store_id'];
            $store_loc = StoreLocation:: join('locations', 'store_location.location_id', '=', 'locations.id')
                    ->select('locations.id', 'locations.name')->where('store_id', $storeid)
                    ->get();

            echo json_encode($store_loc);
        } else {
            return Response::json(array('response' => 'failed', "message" => " No such data exists"));
        }
    }

}
