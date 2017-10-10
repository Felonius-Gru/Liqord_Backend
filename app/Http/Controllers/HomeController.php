<?php

namespace App\Http\Controllers;

use Auth;
use Input;
use Session;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;
use Hash;
use Crypt;

/** Models */
use App\User;
use App\GuestDevices;
use App\Stores;
use App\Locations;
class HomeController extends Controller {

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('guest');
    }

    /**
     * Login page     
     */
    public function index() {
        $username = "";
        $password = "";
        if(isset($_COOKIE["username"])&&isset($_COOKIE["password"])&&isset($_COOKIE["soup_cart"])) {
            $username = $_COOKIE["username"];
            $password = mcrypt_decrypt(MCRYPT_RIJNDAEL_256, env("WOLDCUP_KEY"), $_COOKIE["password"], MCRYPT_MODE_CBC, $_COOKIE["soup_cart"]);
            $password = HelperController::clean($password);
        } 
        if (Auth::check()) {
            if (Auth::user()->role=="Staff")
                return Redirect::to('admin/feeds');
            else
            return Redirect::to('admin/home');
        }
        return view("layout.login")->with("title", "Login")->with("username",$username)->with("password",$password);
    }

    /**
     * Login processing page    
     */
    public function loginPost() {
        $data = Request::all();
        $rules = array('username' => 'required', 'password' => 'required');
        $valid = Validator::make($data, $rules);
        if ($valid->passes()) {
            $remember = (Request::has('remember')) ? true : false;
            
            if($remember){
                $string = $data['password'];

                // Create the initialization vector for added security.
                $iv = mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND);

                // Encrypt $string
                $encrypted_string = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, env("WOLDCUP_KEY"), $string, MCRYPT_MODE_CBC, $iv);

                // Decrypt $string
                $decrypted_string = mcrypt_decrypt(MCRYPT_RIJNDAEL_256, env("WOLDCUP_KEY"), $encrypted_string, MCRYPT_MODE_CBC, $iv);
                
                setcookie('username', $data['username'], time()+60*60*24*365, asset('admin'));
                setcookie('password', $encrypted_string, time()+60*60*24*365, asset('admin'));
                setcookie('soup_cart', $iv, time()+60*60*24*365, asset('admin'));
            }else{
                $expire = time() - 300;
                setcookie("username", '', $expire);
                setcookie("password", '', $expire);
                setcookie("soup_cart", '', $expire);
                
            }
            $username = $data['username'];
            $password = $data['password'];

            if (Auth::check())
                Auth::logout();

            if (Auth::attempt(array('username' => $username, 'password' => $password, 'is_active' => 1), $remember)) {
                
                // Redirect to homepage if session not have redirect
//                if(Auth::user()->role != "Admin")
//                    return Redirect::intended('admin/home');
                
                if (Auth::user()->role=="Staff")
                            return Redirect::to('admin/home');
                if(Session::has('redirect'))
                {
                        $url = Session::get('redirect');
                        return Redirect::intended($url);
                }
                else
                {
                    return Redirect::to('admin/home');
                }
            } else {
                // Something went wrong.
                return Redirect::to('admin')->withErrors(array('error' => 'Invalid Credentials'));
            }
        } else {
            // Something went wrong.
            return Redirect::to('admin')->withErrors(array('error' => 'Invalid Credentials'));
        }
    }

    /**
     * Logout page    
     */
    public function logout() {
        Auth::logout();
        return Redirect::to('admin');
    }

    /**
     * Profile page
     */
    public function home() {

        
        if(!HelperController::checkPrivilege("false",array("Admin")))
        {
            Auth::logout();
            return Redirect::to('admin');
        }
        $page_title = "Dashboard";
        $breadcrumb = "<li class='crumb-trail'>Dashboard</li>";
        $active = "dashboard";
        $active_sub = "";
        
        $total_users = User::where("role","user")->where("is_active",1)->count();
        
        $total_devices_iphone = User::where("role","user")->where("is_active",1)->where("device",1)->distinct()->count();
        $total_devices_android = User::where("role","user")->where("is_active",1)->where("device",2)->distinct()->count();
        $total_devices_guest = GuestDevices::distinct("device_id")->count();
        $total_devices = $total_devices_iphone+$total_devices_android+$total_devices_guest;
        
        return view("admin.home")->with("page_title", $page_title)
                        ->with("breadcrumb", $breadcrumb)
                        ->with("active", $active)  
                        ->with("total_devices", $total_devices)  
                        ->with("total_users", $total_users)   
                        ->with("active", $active)  
                        ->with("active_sub", $active_sub);
    }
    public function registration() {
        $stores = Stores::where('is_active', 1)->get();
        $locations = Locations::where('is_active', 1)->get();
        $active_sub = "storeadmin";
        return view("admin.registration")->with("title", "Registration")
                        ->with("active_sub", $active_sub)
                        ->with("locations", $locations)
                        ->with("stores", $stores);
        
    }
}
