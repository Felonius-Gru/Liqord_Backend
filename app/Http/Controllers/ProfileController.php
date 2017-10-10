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
use File;
use Image;
/** Models */
use App\User;
class ProfileController extends Controller {

    public function profile() {
        if (!HelperController::checkPrivilege("")) {
            return Redirect::to('admin');
        }
        $page_title = "Account Settings";
        $breadcrumb = "<li class='crumb-trail'>Account Settings</li>";
        $active = "profile";
        $active_sub = "";
        $result = User::where("id", Auth::user()->id)->first();
        $submit = "Update";
        return view("admin.profile")->with("page_title", $page_title)
                        ->with("breadcrumb", $breadcrumb)
                        ->with("active", $active)
                        ->with("active_sub", $active_sub)
                        ->with("result", $result)
                        ->with("submit", $submit);
    }

    /**
     * Profile update page
     */
    public function profilePost(Request $request) {


        if (!HelperController::checkPrivilege("post")) {
            return Redirect::to('admin');
        }

        $data = $request->all();
//        var_dump($data);die();
        $rules = array();
        $valid = Validator::make($data, $rules);
        if ($valid->passes()) {
            $user = User::find(Auth::user()->id);
            $user->first_name = HelperController::clean($data['full_name']);
            if ($request->has("password"))
                $user->password = bcrypt(HelperController::clean($data['password']));
            $user->email = HelperController::clean($data['email']);
            $user->phone = HelperController::clean($data['phone']);
            $user->address1 = HelperController::clean($data['address1']);
            $user->address2 = HelperController::clean($data['address2']);
            $user->city = HelperController::clean($data['city']);
            $user->state = HelperController::clean($data['state']);
            $user->zip = HelperController::clean($data['zip']);
            if ($request->hasFile('pic')) {
                if($user->pic !="user.png"){
                    File::delete("uploads/user_files/".$user->pic);
                }

                $file = $request->pic;
                $extension = strtolower($file->getClientOriginalExtension());
                $original_name = $file->getClientOriginalName();
                $original_name = substr($original_name, 0, -4);
                $filename = IMAGE_STARTS_WITH . time() . "-.{$extension}";

                if (!is_dir("uploads/user_files"))
                    mkdir("uploads/user_files", 0777);
                $file->move("uploads/user_files/", $filename);

                $logo = "uploads/user_files/".$filename;
                $img = Image::make($logo);
                $img->resize(PROFILE_PIC_WIDTH, PROFILE_PIC_HEIGHT);
                $img->save($logo);

                $user->pic = $filename;
            }
            if ($request->has('pass')) {
                $password = $data['pass'];
                $confirm_password = $data['cfpass'];
                if ($password == $confirm_password) {
                    $user->password = Hash::make($password);
                }
            }
            $save = $user->save();
            if ($save) {
                Session::flash("success", "User updated");
            } else {
                // Something went wrong.
                Request::flash();
                Session::flash("fail", "Opps... Something went wrong");
            }
            return Redirect::back();
        }
        else {
            // Something went wrong.
            return Redirect::to('admin/profile')->withErrors($valid);
        }
    }
    /**
     * User enable page
     */
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

}
