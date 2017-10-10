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
use App\Subscription;
use App\SubscriptionRenewal;
use App\BillingInfo;
use App\PracticeRegistration;
use App\AuthorizeNet;
use App\UserLog;
use App\Stores;
use App\Locations;
use App\StoreLocation;
use App\CutOffTime;
class UserController extends Controller {

    public function users() {
        if (!HelperController::checkPrivilege()) {
            return Redirect::to('admin');
        }
        $page_title = "Users";
        $breadcrumb = "<li class='active'>Users</li>";
        $active = "users";
        $active_sub = "";

        return view("admin.users")->with("page_title", $page_title)
                        ->with("breadcrumb", $breadcrumb)
                        ->with("active", $active)
                        ->with("active_sub", $active_sub);
    }

    /**
     * Users result page - ajax
     */
    public function usersResults() {

        if (Auth::user()->role == "Admin") {
            $results = User::select(['id', 'first_name', 'username', 'email', 'role', 'is_active'])->where('role', '=', 'StoreAdmin')->get();
        } elseif (Auth::user()->role == "StoreAdmin") {
            $storeid = Auth::user()->store_id;
            $results = User::select(['id', 'first_name', 'username', 'email', 'role', 'is_active'])->where('role', '=', 'StoreUser')->where('store_id', $storeid)->get();
        }

        return Datatables::of($results)
                        ->addColumn('edit', function ($result) {
                            return "<a href='" . asset("admin/users/update/{$result->id}") . "'>
                            <span class='fa fa-pencil-square fa-2x'></span>
                        </a>";
                        })
                        ->addColumn('action', function ($result) {
                            if ($result->is_active == 0) {
                                $url = asset("admin/users/enable") . "/";
                                $link = "<a href='#' id='alter_link'
                               onclick=\"return action_confirm('{$url}{$result->id}','User','Do you want to enable this user ?');\" >
                                <i class='fa fa-check-circle fa-2x'></i>
                            </a>";
                            } else if ($result->is_active == 1) {
                                $url = asset("admin/users/disable") . "/";
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
        $stores = Stores::where('is_active', 1)->get();
        $locations = Locations::where('is_active', 1)->get();
        if ($id == false) {
            $submit = "Register";

            $result = new User;
            // For menu part
            $page_title = "Register";
            $breadcrumb = "<li class='active'>Register</li>";
            $header = "<header>Add a new person</header>";
            $active = "users";
            $active_sub = "";
        } else {
            $result = User::where("id", $id)->first();

            if (is_null($result)) {
                Session::flash("fail", "No such User");
                return Redirect::back();
            }
            // For menu part
            $page_title = "Update";
            $breadcrumb = "<li class='active'>Update</li>";
            $header = "<header>Update user</header>";
            $active = "users";
            $active_sub = "";
        }

        return view("admin.usersView")->with("page_title", $page_title)
                        ->with("breadcrumb", $breadcrumb)
                        ->with("header", $header)
                        ->with("active", $active)
                        ->with("active_sub", $active_sub)
                        ->with("result", $result)
                        ->with("stores", $stores)
                        ->with("locations", $locations)
                        ->with("id", $id)
                        ->with("submit", $submit);
    }

    /**
     * User update page
     */
    public function update(Request $request, $id = false) {
//        if (!HelperController::checkPrivilege("post")) {
//            return Redirect::to('admin');
//        }
        $data = $request->all();
        if ($id) {
            $rules = array(
                'first_name' => 'required',
                'email' => 'required'
            );
        } else {
            $rules = array(
                'first_name' => 'required',
                'username' => 'required',
                'password' => 'required',
                'email' => 'required'
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
            $user->first_name = HelperController::clean($data['first_name']);
            $user->last_name = HelperController::clean($data['last_name']);
            if ($id == false) {
                $user->username = HelperController::clean($data['username']);
                $check_user = User::where("username", $user->username)->count();
                if ($check_user != 0) {
                    Session::flash("fail", "Opps... Username already taken");
                    $request->flash();
                    return Redirect::back();
                }
            }
            if ($request->has("password"))
            $user->password = bcrypt(HelperController::clean($data['password']));
//            $user->role = HelperController::clean($data['role']);
            if(isset($data['card_num'])) {
                 $user->card_num = HelperController::clean($data['card_num']);
            }
             if(isset($data['cvv'])) {
                 $user->cvv = HelperController::clean($data['cvv']);
            }
            if(isset($data['expiry_date'])) {
                 $user->expiry_date = HelperController::clean($data['expiry_date']);
            }
            $user->email = HelperController::clean($data['email']);
            $user->phone = HelperController::clean($data['phone']);
            if(isset($data['address1'])) {
            $user->address1 = HelperController::clean($data['address1']);
            $user->address2 = HelperController::clean($data['address2']);
            $user->city = HelperController::clean($data['city']);
            $user->state = HelperController::clean($data['state']);
            $user->zip = HelperController::clean($data['zip']);
            }
            if(isset(Auth::user()->role)) {
            if (Auth::user()->role == "Admin") {
                $user->role = "StoreAdmin";
//                $user->store_id = HelperController::clean($data['store']);
            } else {
                if (Auth::user()->role == "StoreAdmin")
                    $user->role = "StoreUser";
            }
            }

//            else { 
            $storename = HelperController::clean($data['store']);
                 $store = Stores::where('name',$storename)
                                         ->where('is_active',1)
                                         ->first(); 
                
                if(count($store)== 0){
                    $store = new Stores;
                        $store->name = HelperController::clean($data['store']);
                        $store->is_active = 1 ;
                        $save = $store->save();
                }
 
                $user->role = "StoreAdmin";
                $user->store_id = $store->id;
//            }


            $save = $user->save();
               if ($id == false) {         
                    $locations = $data['location'];
//                    print_r($locations);die;
                    $cutoffdate = $data['cutoffdate'];
                    $end_time = $data['end_time'];
//                    foreach ($locations as $location) {
                         for ($i=0; $i<count($locations);$i++) {
                          $loc = Locations::where('name',$locations[$i])
                                         ->where('is_active',1)
                                         ->first(); 
                if(count($loc)== 0){
                        $loc = new Locations;
                        $loc->name = $locations[$i];
                        $loc->is_active = 1 ;
                        $save = $loc->save();
                }
                        
                        $check_location = StoreLocation::where('location_id',$loc->id)
                                         ->where('store_id',$store->id)
                                         ->count();                        
                        if ($check_location == 0) { 
                        $storelocation = new StoreLocation;
                        $storelocation->store_id = $store->id;
                        $storelocation->location_id = $loc->id;  
                        $save = $storelocation->save();
                        }
                        
                       $check_time = CutOffTime::where('location_id',$loc->id)
                                         ->where('store_id',$store->id)
                                         ->count();                        
                       if ($check_time == 0) {    
                        $cutofftime = new CutOffTime;
                        $cutofftime->location_id = $loc->id;             
                        $cutofftime->store_id = $store->id;
                        $cutofftime->cutoffdate= $cutoffdate[$i];      
                        $date = strtotime($end_time[$i]);
                        $cutofftime->end_time= date("H:i:s", $date);   
                        $save = $cutofftime->save();
                        } 
                       
                    }
               }
            
            if ($save) {
                if ($id == false) {                 
                    Session::flash("success", "User created");
                    return Redirect::back();
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

    public function profile() {
        if (!Auth::check()) {
            return Redirect::to('admin');
        }
        checkPrivilege();

        $page_title = "Account Settings";
        $breadcrumb = "<li class='crumb-trail'>Account Settings</li>";
        $active = "";
        $active_sub = "";

        return view("admin.profile")->with("page_title", $page_title)
                        ->with("breadcrumb", $breadcrumb)
                        ->with("active", $active)
                        ->with("active_sub", $active_sub);
    }

    /**
     * Profile update page
     */
    public function profilePost() {
        if (!Auth::check()) {
            return Redirect::to('admin');
        }
        checkPrivilege();

        $data = Request::all();
        $rules = array('firstname' => 'required', 'lastname' => 'required');
        $valid = Validator::make($data, $rules);
        if ($valid->passes()) {
            $user = User::find(Auth::user()->id);

            if (Request::has('pass')) {
                $password = $data['pass'];
                $confirm_password = $data['cfpass'];
                if ($password == $confirm_password) {
                    $user->password = Hash::make($password);
                }
            } else {
                $user->first_name = $data['firstname'];
                $user->last_name = $data['lastname'];
            }

            $save = $user->save();

            if ($save)
                Session::flash("success", "Details updated");
            else
                Session::flash("fail", "Opps... Something went wrong");

            return Redirect::to('admin/profile');
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

    /**
     * Forgot Password
     */
    public function forgotPassword(Request $request) {
        $data = $request->all();
        $rules = array('username' => 'required');
        $valid = Validator::make($data, $rules);
        if ($valid->passes()) {

            $user = User::where("username", $request->username)->first();
            if (is_null($user)) {
                Session::flash("fail", "Opps... Please check your username");
                return Redirect::back();
            }
            $subject = "Liqord: Forgot Password Request";
            $name = $user->first_name;
            $username = $user->username;
            $password = bin2hex(openssl_random_pseudo_bytes(3));
            if (!filter_var($user->email, FILTER_VALIDATE_EMAIL) === false) {

//                $email_forgot_password = Common::where("id","email_forgot_password")->first();
//                if(is_null($email_forgot_password))
//                    $email_forgot_password = "";
//                else
//                    $email_forgot_password = $email_forgot_password->value;
                $email_forgot_password = "We have changed your password";
                $data = array('email' => $user->email, 'subject' => $subject, 'name' => $name, 'username' => $username, 'password' => $password, "email_forgot_password" => $email_forgot_password);
                Mail::send('email.forgotPassword', $data, function($message) use ($data) {
                    $message->from("dev@thetunagroup.com", "Liqord");
                    $message->to($data['email'], $data['email'])->subject($data['subject']);
                });
                $user->password = bcrypt($password);
            }
            $save = $user->save();
            if ($save) {
                Session::flash("success", "Please check your mail for new password");
            } else {
                // Something went wrong.
                Session::flash("fail", "Opps... Something went wrong");
            }

            return Redirect::back();
        } else {
            // Something went wrong.
            return Redirect::back()->withErrors($valid);
        }
    }

    //Logs
    public function logs() {
        if (!HelperController::checkPrivilege()) {
            return Redirect::to('admin');
        }
        $page_title = "UserLogs";
        $header = "<header>UserLogs</header>";
        $breadcrumb = "<li class='active'>Logs</li>";
        $active = "userlogs";
        $active_sub = "";

        return view("admin.userlogs")->with("page_title", $page_title)
                        ->with("breadcrumb", $breadcrumb)
                        ->with("active", $active)
                        ->with("header", $header)
                        ->with("active_sub", $active_sub);
    }

    /**
     * Logs result page - ajax
     */
    public function logResults() {
        $results = UserLog::join('users', 'users.id', '=', 'user_log.user_id')->select(['users.first_name', 'user_log.card_amount', 'user_log.recharge_amount', 'user_log.created_at', 'user_log.id'])->orderBy("user_log.id", "asc")->get();

        return Datatables::of($results)

//                        ->addColumn('user_id', function ($result) {
//                            return $result->user_id."<br/>".UserController::getUserName($result->user_id);
//                        })
                        ->make(true);
    }

    public static function getUserName($id) {
        $user = User::find($id);
        if (is_null($user)) {
            return "NA";
        } else {
            return $user->full_name;
        }
    }

    public function refillcardview($id) {
        if (!HelperController::checkPrivilege()) {
            return Redirect::to('admin');
        }
        $submit = "Update";

        if ($id == false) {
            $submit = "Register";

            $result = new User;
        } else {
            $result = User::where("id", $id)->first();

            if (is_null($result)) {
                Session::flash("fail", "No such User");
                return Redirect::back();
            }
        }
        $user = User::find($id);
        $user_logs = UserLog::where('user_id', $id)->paginate(10);
        $page_title = "Refill Card";
        $header = "<header>Refill Card</header>";
        $breadcrumb = "<li class='active'>Refill Card</li>";
        $active = "refillcard";
        $active_sub = "";
        $heading = "<header>Refill Card</header>";

        return view("admin.refillcard")->with("page_title", $page_title)
                        ->with("breadcrumb", $breadcrumb)
                        ->with("active", $active)
                        ->with("header", $header)
                        ->with("heading", $heading)
                        ->with("result", $result)
                        ->with("user_logs", $user_logs)
                        ->with("user", $user)
                        ->with("id", $id)
                        ->with("submit", $submit)
                        ->with("active_sub", $active_sub);
    }

    public function refillcard(Request $request, $id = false) {
        if (!HelperController::checkPrivilege()) {
            return Redirect::to("admin");
        } $data = $request->all();
//        dd($data);


        $rules = array('recharge_amount' => 'required');
        if ($id == false) {
            $user = new User;
        } else {
            $user = User::find($id);
            if (is_null($user)) {
                Session::flash("fail", "No such user found");
                return Redirect::back();
            }
        }
        $user_logs = UserLog::where('user_id', $id)->get();

        $valid = Validator::make($data, $rules);

        if ($valid->passes()) {

//            $user->card_amount = HelperController::clean($data['card_amount']);
            $user_logs->recharge_amount = HelperController::clean($data['recharge_amount']);
            $recharge_amount = HelperController::clean($data['recharge_amount']);
            $user->card_amount = $data['recharge_amount'] + $user->card_amount;
            $dt1 = new DateTime();
            $created_at1 = $dt1->format('Y-m-d H:i:s');
            $updated_at1 = $dt1->format('Y-m-d H:i:s');
            $save = $user->save();
            $user_logs = new UserLog;
            $save1 = $user_logs->insert(
                    ['user_id' => $user->id, 'card_amount' => $user->card_amount, 'recharge_amount' => $recharge_amount, 'created_at' => $created_at1, 'updated_at' => $updated_at1]
            );

            if ($save) {
                if ($id == false) {
                    Session::flash("success", "Product saved");
                    return Redirect::to("admin/inventories/update/{$inventory->id}");
                } else {
                    Session::flash("success", "Inventory updated");
                    return Redirect::back();
                }
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
    
    public function addcard(Request $request) {

        $data = $request->all();
        $rules = array(
            'card_num' => 'required',
            'cvv' => 'required',
            'expiry_date' => 'required'
            );
       
        $id = Auth::user()->id;
        
           
            $user = User::find($id);
            if (is_null($user)) {
                Session::flash("fail", "No such user found");
                $request->flash();
                return Redirect::back();
            }
        

        $valid = Validator::make($data, $rules);

        if ($valid->passes()) {
           
            $user->card_num = HelperController::clean($data['card_num']);
            $user->cvv = HelperController::clean($data['cvv']);
            $user->expiry_date = HelperController::clean($data['expiry_date']);
          
            $save = $user->save();
            if ($save) {
              
                    Session::flash("success", "Card Number Saved");
                    return Redirect::back();
               
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

}
