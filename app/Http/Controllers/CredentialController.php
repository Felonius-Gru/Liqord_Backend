<?php
namespace App\Http\Controllers;

use Auth;
use Input;
use Session;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
//use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;
use Yajra\Datatables\Facades\Datatables;
use DateTime;

use Hash;
use Response;

use File;
use Image;
/** Models */
use App\User;
use App\PaypalCredentials;
use App\Credentials;
class CredentialController extends Controller {

 
    public function payPalCreds() {
        
         if (HelperController::checkPrivilege()) {
            return Redirect::to('admin');
        }
          $result=PaypalCredentials::find(1);
          if(is_null($result))
          {
              
          }
            $page_title="Paypal Credentials";
            $breadcrumb = "<li class='active'>Paypal Credentials</li>";
            $header = "<header>Paypal Credentials</header>";
            $active = "paypal";
            $active_sub = "";
        return view("admin.paypalCreds")->with("page_title",$page_title)
                        ->with("breadcrumb", $breadcrumb)
                        ->with("active", $active)
                        ->with("header", $header)
                        ->with("active_sub", $active_sub)
                        ->with("result", $result);
  
    }

    /**
     * Profile page - Update
     */
    
      public function updatepayPalCreds(Request $request) {
        if(HelperController::checkPrivilege()) {
            return Redirect::to("admin");
        }

        $data = $request->all();

        $rules = array('sandbox_client_id' => 'required','sandbox_client_secret' => 'required','production_client_id' => 'required','production_client_secret' => 'required');
        
       
               $result=PaypalCredentials::find(1);
            if(is_null($result)) {
                Session::flash("fail", "Something went wrong");
                return Redirect::back();
            }
        
        
        $valid = Validator::make($data, $rules);

        if ($valid->passes()) {
            $result->sandbox_client_id = HelperController::clean($data['sandbox_client_id']);
            $result->sandbox_client_secret  = HelperController::clean($data['sandbox_client_secret']);
            $result->production_client_id  =  HelperController::clean($data['production_client_id']);
            $result->production_client_secret  =  HelperController::clean($data['production_client_secret']);
            if(isset($data['is_production']))
            $result->is_production=1;
            else
             $result->is_production=0;   
            
            $save = $result->save();
            
            if ($save) {
                
                    Session::flash("success", "Paypal creds updated");  
                    return Redirect::back();
                                            
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
    
    
        public function authorizeView() {
            
             if (!HelperController::checkPrivilege()) {
            return Redirect::to('admin');
        }
          $result = Credentials::find(1);
          if(is_null($result))
          {
              
          }
            $page_title="Authorize Credentials";
            $breadcrumb = "<li class='active'>Authorize Credentials</li>";
            $header = "<header>Authorize Credentials</header>";
            $active = "authorize";
            $active_sub = "";
        return view("admin.authDotNetCreds")->with("page_title",$page_title)
                        ->with("breadcrumb", $breadcrumb)
                        ->with("active", $active)
                        ->with("header", $header)
                        ->with("active_sub", $active_sub)
                        ->with("result", $result);
  
            
        }
        
         /**
     * Authorize.Net details page - Update
     */
    public function authorizeUpdate(Request $request) {
       if (!HelperController::checkPrivilege()) {
            return Redirect::to('admin');
        }
        $data = $request->all();      
        if($request->has("auth_is_production")) {
            $rules = array('auth_production_url' => 'required',                      
                            'auth_production_login_id' => 'required',
                            'auth_production_transaction_id' => 'required');
            $messages = array('auth_production_url.required' => 'The production url field is required.',
                            'auth_production_login_id.required' => 'The production login id field is required.',
                            'auth_production_transaction_id.required' => 'The production transaction id field is required.');
        }
        else {
            $rules = array('auth_sandbox_url' => 'required',
                            'auth_sandbox_login_id' => 'required',
                            'auth_sandbox_transaction_id' => 'required');
            $messages = array('auth_sandbox_url.required' => 'The sandbox url field is required.',
                            'auth_sandbox_login_id.required' => 'The sandbox login id field is required.',
                            'auth_sandbox_transaction_id.required' => 'The sandbox transaction id field is required.');
        }
        
        $valid = Validator::make($data, $rules, $messages);

        if ($valid->passes()) {            
            $credentials = Credentials::find(1);
            
            $credentials->auth_is_production = ($request->has("auth_is_production")) ? 1 : 0;
            $credentials->auth_production_url = HelperController::clean($data['auth_production_url']);
            $credentials->auth_production_login_id = HelperController::clean($data['auth_production_login_id']);
            $credentials->auth_production_transaction_id = HelperController::clean($data['auth_production_transaction_id']);
            $credentials->auth_sandbox_url = HelperController::clean($data['auth_sandbox_url']);
            $credentials->auth_sandbox_login_id = HelperController::clean($data['auth_sandbox_login_id']);
            $credentials->auth_sandbox_transaction_id = HelperController::clean($data['auth_sandbox_transaction_id']);
            
            $save = $credentials->save();

            if ($save) {
                Session::flash("success", "Details updated");
                return back();
            } else {
                // Something went wrong.
                $request->flash();
                Session::flash("fail", "Opps... Something went wrong");
                return back();
            }
        }
        else {
            // Something went wrong.
            $request->flash();
            return back()->withErrors($valid);
        }
    }

    
}
