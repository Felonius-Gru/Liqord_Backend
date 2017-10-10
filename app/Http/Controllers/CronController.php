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
use App\Billing;
use App\Authorize;
use App\BillingInfo;

class CronController extends Controller {

    public function paynow() {
        
        $results = StoreLocation::get();
        foreach ($results as $result) {
            $date1 = $result->created_at;
            $date2 = date('Y-m-d');
            $ts1 = strtotime($date1);
            $ts2 = strtotime($date2);
            $year1 = date('Y', $ts1);
            $year2 = date('Y', $ts2);

            $month1 = date('m', $ts1);
            $month2 = date('m', $ts2);

            $diff = (($year2 - $year1) * 12) + ($month2 - $month1);
            $bill_date = date('Y-m-d', strtotime($result->created_at . '+' . $diff . 'month'));
            if (strtotime(date("Y-m-d")) == strtotime($bill_date)) {

                $user = User::where('store_id', $result->store_id)
                                ->where('role','StoreAdmin')
                                ->where('is_active', 1)->first(); {
                    if (count($user) > 0) {               
                    $data['user_id'] = $user->id;
                    $data['card_num'] = $user->card_num;
                    $data['cvv'] = $user->cvv;
                    $data['exp_date'] = $user->expiry_date;
                    }
                    $data['store_id'] = $result->store_id;
                    $data['location_id'] = $result->location_id;
                    
                }

                $bill = Billing::where('store_id', $result->store_id)
                                ->where('location_id', $result->location_id)->first();
                
                if ((count($bill) > 0) && (count($user) > 0)) {
                    $bil_date = date('Y-m-d', strtotime($result->created_at . '+' . $diff . 'month'));
                    $bill_info = BillingInfo::where('store_id', $result->store_id)
                                ->where('location_id', $result->location_id)
                                ->where('bill_date', $bil_date)
                                ->where('billing_status',1)
                                ->first();
                    
                    $data['bill_amount'] = $bill->monthly_cost;
                    $data['bill_date'] = $bil_date;
                    if (count($bill_info) == 0) {
                    CronController::PaymentPost($data);}
                }
                 if(count($bill) == 0){
                    $billing = new BillingInfo();
                    $billing->user_id = $user->id;
                    $billing->type = 0;
                    $billing->store_id = $result->store_id;
                    $billing->location_id = $result->location_id;
                    $billing->bill_date = $bill_date;
                    $billing->error = 'Monthly cost not yet set';
                    $billing->save(); 
                }
            }
        }
    }

     public function updateCard(Request $request, $id = false) {
          
        $data = $request->all();
       
        $rules = array(
            'card_num' => 'required',
            'cvv' => 'required',
            'exp_date' => 'required'
        );
            $user = User::where('id',$data['user_id'])->first();
            if (is_null($user)) {
                Session::flash("fail", "Store not found");
                $request->flash();
                return Redirect::to("admin/declined_charges");
            }
       
        $valid = Validator::make($data, $rules);

        if ($valid->passes()) {
            $user->card_num = HelperController::clean($data['card_num']);
            $user->cvv = HelperController::clean($data['cvv']);
            $user->expiry_date = HelperController::clean($data['exp_date']);
            $save = $user->save();
            CronController::PaymentPost($data);

            if ($save) {
                    Session::flash("success", "Credit card information updated");
                    return Redirect::to("admin/billing_history");
            } else {
//                $request->flash();
                Session::flash("fail", "Opps... Something went wrong");
            }
            return Redirect::back();
        } else {
            return Redirect::back()->withErrors($valid);
        }
    }
    
    public function PaymentPost($data) {

        $billing = new BillingInfo();
        $billing->user_id = $data['user_id'];
        $billing->type = 0;
        $billing->store_id = $data['store_id'];
        $billing->location_id = $data['location_id'];
        $billing->last4 = 'XXXXXXXXXXXX' . substr($data['card_num'], -4);
        $billing->expiration_date = $data['exp_date'];
        $billing->cvv = $data['cvv'];
        $billing->orginal_price = $data["bill_amount"];
        $billing->bill_date = $data["bill_date"];
        $billing->payment_type = "AUTH.NET";
        $billing->save();

        $data["bill_id"] = $billing->id;

        $authorize_response = CronController::payThroughAuthorize($data);
        if ($authorize_response->response_code == '1') {
            $billing->card_type = $authorize_response->card_type;
            $billing->transaction_id = $authorize_response->transaction_id;
            $billing->billing_status = $authorize_response->approved;
            $billing->response = $authorize_response->response;
            $billing->payment_status = $authorize_response->approved;
            $billing->save();
            
            BillingInfo::where('billing_status', '')
                    ->where('store_id', $billing->store_id)
                    ->where('location_id', $billing->location_id)
                    ->whereDate('bill_date', '=', $billing->bill_date)
                    ->update(['payment_status' => 1]);
        } else {
            $billing->error = $authorize_response->response_reason_text;
            $billing->save();
        }
    }

    /**
     * Function to pay through Auth.net and return return response
     */
    public static function payThroughAuthorize($data) {
        // Define Constants if not defined
        $authorize = Authorize::find(1);
        // Define Constants if not defined
        if (!defined('AUTHORIZENET_API_LOGIN_ID')) {
            if ($authorize->production == 1) {
                define("AUTHORIZENET_API_LOGIN_ID", $authorize->production_login_id);
            } else {
                define("AUTHORIZENET_API_LOGIN_ID", $authorize->sandbox_login_id);
            }
        }
        if (!defined('AUTHORIZENET_TRANSACTION_KEY')) {
            if ($authorize->production == 1) {
                define("AUTHORIZENET_TRANSACTION_KEY", $authorize->production_transaction_id);
            } else {
                define("AUTHORIZENET_TRANSACTION_KEY", $authorize->sandbox_transaction_id);
            }
        }
        if (!defined('AUTHORIZENET_SANDBOX')) {
            if ($authorize->production == 1)
                define("AUTHORIZENET_SANDBOX", false);
            else
                define("AUTHORIZENET_SANDBOX", true);
        }

        $sale = new \AuthorizeNetAIM;
        $sale->amount = $data["bill_amount"];
        $sale->card_num = $data['card_num'];
        $sale->exp_date = $data['exp_date'];
//        $customer = (object) array();
//        $customer->first_name = $data["first_name"];
//        $customer->last_name = $data["last_name"];
//        $customer->address = $data["address"];
//        $customer->state = $data["state"];
//        $customer->zip = $data["zip"];
//        $sale->setFields($customer);
        $response = $sale->authorizeAndCapture();
        return $response;
    }

}
