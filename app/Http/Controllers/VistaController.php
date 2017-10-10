<?php 

namespace App\Http\Controllers;

use Auth;
use Input;
use SoapClient;
use Session;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;
use Hash;
/** Models */
use App\User;
use App\AuthorizedLoyaltyUser;
use App\CinemaAuthCredentials;
use App\Reward;
use App\Common;

class VistaController extends Controller {

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('guest');
    }

    /**
     * GET CINEMA LIST USING VISTA API
     */
    public static function getCinemaList($OptionalMovieName="",$OptionalCinemaId = "", $OptionalIncludeOperator = '0', $OptionalOrderByOperator = '0', $OptionalIncludeGiftStores = '0') {
        $client = new SoapClient(DATA_SERVICE);

        $response = $client->GetCinemaList(array(
            'OptionalMovieName' => $OptionalMovieName,
            'OptionalCinemaId' => $OptionalCinemaId,
            'OptionalBizStartTimeOfDay' => 0,
            'OptionalIncludeOperator' => $OptionalIncludeOperator,
            'OptionalOrderByOperator' => $OptionalOrderByOperator,
            'OptionalIncludeGiftStores' => $OptionalIncludeGiftStores
        ));
        $json = json_encode($response);
        $array = array();
        if ($response->Result == "OK") {
            $xmlstring = $response->DatasetXML;
            $xml = simplexml_load_string($xmlstring);
            $json = json_encode($xml);
            $array = json_decode($json, TRUE);
            if (array_key_exists('Table', $array)) {
                $result = $array['Table'];
            } else {
                $result = $array;
            }
            if(!isset($result[0]))
            $result = array($result);

            foreach ($result as $key=>$forconcatimage)
            {
                if(isset($forconcatimage['Cinema_strID']))
                    $result[$key]["image_url"] = CINEMA_IMAGE_URL."{$forconcatimage['Cinema_strID']}?width=160&height=160";

            }
            return $result;
        }else{
            return $array;
        }



    }

    /**
     * GET CINEMA LIST ALL USING VISTA API
     */
    public static function getCinemaListAll($OptionalIncludeOperator = '', $OptionalOrderByOperator = '', $OptionalIncludeGiftStores = '0') {

        header('Content-Type: text/plain');

        $options = array(
               'soap_version'=>SOAP_1_1,
               'exceptions'=>true,
               'trace'=>1,
               'cache_wsdl'=>WSDL_CACHE_NONE
        );
        $client = new SoapClient(DATA_SERVICE,$options);

        $response = $client->GetCinemaListAll(array(
            'OptionalIncludeOperator' => $OptionalIncludeOperator,
            'OptionalOrderByOperator' => $OptionalOrderByOperator,
            'OptionalIncludeGiftStores' => $OptionalIncludeGiftStores
        ));
        //var_dump($response);die();
        $array = array();
        $json = json_encode($response);
        if ($response->Result == "OK") {
            $xmlstring = $response->DatasetXML;
            $xml = simplexml_load_string($xmlstring);
            $json = json_encode($xml);
            $array = json_decode($json, TRUE);
            if (array_key_exists('Table', $array)) {
                $result = $array['Table'];
            } else {
                $result = $array;
            }
            foreach ($result as $key=>$forconcatimage)
            {
                if(isset($forconcatimage['Cinema_strID']))
                    $result[$key]["image_url"] = CINEMA_IMAGE_URL."{$forconcatimage['Cinema_strID']}?width=160&height=160";

            }
            return $result;
        }

        return $array;
    }

    /**
     * GET Movie LIST USING VISTA API
     */
    public static function getMovieList($OptionalCinemaId = '', $OptionalOrderByOperator = 'FALSE', $OptionalBizStartTimeOfDay = '0', $OptionalIncludeGiftStores = false) {
        $client = new SoapClient(DATA_SERVICE);

        $response = $client->GetMovieList(array(
            'OptionalCinemaId' => $OptionalCinemaId,
            'OptionalOrderByOperator' => $OptionalOrderByOperator,
            'OptionalOrderByOperator' => $OptionalOrderByOperator,
            'OptionalBizStartTimeOfDay' => $OptionalBizStartTimeOfDay,
            'OptionalIncludeGiftStores' => $OptionalIncludeGiftStores
        ));

        $array = array();
        $json = json_encode($response);
        if ($response->Result == "OK") {
            $xmlstring = $response->DatasetXML;
            $xml = simplexml_load_string($xmlstring);
            $json = json_encode($xml);
            $array = json_decode($json, TRUE);
            if (array_key_exists('Table', $array)) {
                $result = $array['Table'];
            } else {
                $result = $array;
            }
            if(!isset($result[0]))
                    $result = array($result);
            $remove_array_values = array();
            foreach ($result as $key=>$forconcatimage)
            {
                if(isset($forconcatimage['Movie_strID'])){
                    $result[$key]["image_url"] = MOVIE_IMAGE_URL."{$forconcatimage['Movie_strID']}?referenceScheme=HeadOffice&allowPlaceHolder=true&height=500";
                }else{
                    array_push($remove_array_values, $key);
                }
            }
            foreach ($remove_array_values as $remove_array_value)
            {
                unset($result[$remove_array_value]); // remove item at index 0
            }

            $reindex_result = array_values($result); // 'reindex' array
            return $reindex_result;
        }

        return $array;
    }

    /**
     * GET Movie INFO LIST USING VISTA API
     */
    public static function getMovieInfoList($OptionalTypeFlag = 'NowShowing',$OptionalMovieName="") {
        $client = new SoapClient(DATA_SERVICE);

        $response = $client->GetMovieInfoList(array(
            'OptionalMovieName' => $OptionalMovieName,
            'OptionalTypeFlag' => $OptionalTypeFlag,
        ));

        $array = array();
        $json = json_encode($response);
        if ($response->Result == "OK") {
            $xmlstring = $response->DatasetXML;
            $xml = simplexml_load_string($xmlstring);
            $json = json_encode($xml);
            $array = json_decode($json, TRUE);
            $result = array();
            if (array_key_exists('Table', $array)) {
                $result =  $array['Table'];
            } else {
                $result = $array;
            }
            if(!isset($result[0]))
                    $result = array($result);

            if(count($result)>0){
                $remove_array_values = array();
                foreach ($result as $key=>$forconcatimage)
                {
                    if(isset($forconcatimage['Film_strCode'])){
                        $result[$key]["image_url"] = MOVIE_IMAGE_URL."{$forconcatimage['Film_strCode']}?referenceScheme=HeadOffice&allowPlaceHolder=true&height=500";
                    }else{
                        array_push($remove_array_values, $key);
                    }

                }
                foreach ($remove_array_values as $remove_array_value)
                {
                    unset($result[$remove_array_value]); // remove item at index 0
                }
            }
            $reindex_result = array_values($result); // 'reindex' array
            return $reindex_result;
        }
        return $array;
    }

    /**
     * GET Movie INFO LIST USING VISTA API
     */
    public static function getCinemaSiteGroups($OptionalTypeFlag = 'NowShowing') {
        $client = new SoapClient(DATA_SERVICE);

        $response = $client->GetCinemaSiteGroups(array(
        ));

        $array = $response; //json_encode( (array)$response );
        return $array;

        $array = array();
        $json = json_encode($response);

        $res = $response->GetCinemaSiteGroupsResult;
        $json = json_encode($res);
        $array = json_decode($json, TRUE);
        return $array;
    }

    /**
     * GET Movie INFO LIST USING VISTA API
     */
    public static function getMovieShowtimes($CinemaId, $BizDate, $BizStartTimeOfDay = 0, $OptionalClientClass = '', $OrderMode, $OptionalSessionDisplayCutOffInMins = 0, $AllSalesChannels = "False") {
        $client = new SoapClient(DATA_SERVICE);

        $response = $client->GetMovieShowtimes(array(
            'CinemaId' => $CinemaId,
            'BizDate' => $BizDate,
            'BizStartTimeOfDay' => $BizStartTimeOfDay,
            'OptionalClientClass' => $OptionalClientClass,
            'OrderMode' => $OrderMode,
            'OptionalSessionDisplayCutOffInMins' => $OptionalSessionDisplayCutOffInMins,
            'AllSalesChannels' => $AllSalesChannels
        ));

        $array = array();
        $json = json_encode($response);
        if ($response->Result == "OK") {
            $xmlstring = $response->DatasetXML;
            $xml = simplexml_load_string($xmlstring);
            $json = json_encode($xml);
            $array = json_decode($json, TRUE);
            if (array_key_exists('Table', $array)) {
                $result =  $array['Table'];
            } else {
                $result =  $array;
            }
            return $result;
        }
//        echo "<pre/>";
//        print_r($result);
//        die();
        return $array;
    }

    public static function getSessionSeatPlan($CinemaId, $SessionId) {
        $client = new SoapClient(DATA_SERVICE_3);

        $response = $client->GetSessionSeatPlan(array(
            'CinemaId' => $CinemaId,
            'SessionId' => $SessionId
        ));

        $json = json_encode($response);
        $array = json_decode($json, TRUE);
        return $array;
    }

    public static function getTicketTypeList($CinemaId, $SessionId,$OptionalUserSessionIdForLoyaltyTickets = '', $OptionalLoyaltyTicketMatchesHOCode = '0', $OptionalShowNonATMTickets = '0', $OptionalReturnAllRedemptionAndCompTickets = '1', $OptionalReturnAllLoyaltyTickets = '1', $OptionalAreaCategoryCode = '', $OptionalClientClass = '', $OptionalReturnLoyaltyRewardFlag = '0', $OptionalSeparatePaymentBasedTickets = '0', $OptionalShowLoyaltyTicketsToNonMembers = '0', $OptionalEnforceChildTicketLogic = '0', $OptionalIncludeZeroValueTickets = '0') {
        $client = new SoapClient(DATA_SERVICE,array('trace'=>1));

        $response = $client->GetTicketTypeList(array(
            'OptionalUserSessionIdForLoyaltyTickets' => $OptionalUserSessionIdForLoyaltyTickets,
            'OptionalLoyaltyTicketMatchesHOCode' => $OptionalLoyaltyTicketMatchesHOCode,
            'CinemaId' => $CinemaId,
            'SessionId' => $SessionId,
            'OptionalShowNonATMTickets' => $OptionalShowNonATMTickets,
            'OptionalReturnAllRedemptionAndCompTickets' => $OptionalReturnAllRedemptionAndCompTickets,
            'OptionalReturnAllLoyaltyTickets' => $OptionalReturnAllLoyaltyTickets,
            'OptionalAreaCategoryCode' => $OptionalAreaCategoryCode,
            'OptionalClientClass' => $OptionalClientClass,
            'OptionalReturnLoyaltyRewardFlag' => $OptionalReturnLoyaltyRewardFlag,
            'OptionalSeparatePaymentBasedTickets' => $OptionalSeparatePaymentBasedTickets,
            'OptionalShowLoyaltyTicketsToNonMembers' => $OptionalShowLoyaltyTicketsToNonMembers,
            'OptionalEnforceChildTicketLogic' => $OptionalEnforceChildTicketLogic,
            'OptionalIncludeZeroValueTickets' => $OptionalIncludeZeroValueTickets
        ));
        $array = array();
        $json = json_encode($response);      
        //EmagineLogController::saveLog("Ticket Types Req and res", $client->__getLastRequest(), $json);
        if ($response->Result == "OK") {
            $xmlstring = $response->DatasetXML;
            $xml = simplexml_load_string($xmlstring);
            $json = json_encode($xml);
            $array = json_decode($json, TRUE);
            if (array_key_exists('Table', $array)) {
                $result = $array['Table'];
            } else {
                $result = $array;
            }
//            $message = "Tickets: " . print_r($result, true);
//            @mail("prasanthka01@gmail.com","Set Seat Selected",$message);
            return $result;
        }
        return $array;
    }
    public static function getMoviePeople($MovieId) {
        $client = new SoapClient(DATA_SERVICE);

        $response = $client->GetMoviePeople(array(
            'MovieId' => $MovieId
        ));

        $array = array();
        $json = json_encode($response);
        if ($response->Result == "OK") {
            $xmlstring = $response->DatasetXML;
            $xml = simplexml_load_string($xmlstring);
            $json = json_encode($xml);
            $array = json_decode($json, TRUE);
            if (array_key_exists('Table', $array)) {
                return $array['Table'];
            } else {
                return $array;
            }
        }
        return $array;
    }
    public static function getSessionList($CinemaId,$MovieId,$OptionalBizStartHourOfDay=0,$OptionalSessionDisplayCutOff=0) {
        $client = new SoapClient(DATA_SERVICE);

        $response = $client->GetSessionList(array(
            'OptionalBizStartHourOfDay' => $OptionalBizStartHourOfDay,
            'OptionalSessionDisplayCutOff' => $OptionalSessionDisplayCutOff,
            'CinemaId' => $CinemaId,
            'MovieId' => $MovieId
        ));

        $array = array();
        $json = json_encode($response);
        if ($response->Result == "OK") {
            $xmlstring = $response->DatasetXML;
            $xml = simplexml_load_string($xmlstring);
            $json = json_encode($xml);
            $array = json_decode($json, TRUE);
            if (array_key_exists('Table', $array)) {
                return $array['Table'];
            } else {
                return $array;
            }
        }
        return $array;
    }
    public static function getSessionInfo($CinemaId,$SessionId) {
        $client = new SoapClient(DATA_SERVICE);

        $response = $client->GetSessionInfo(array(
            'CinemaId' => $CinemaId,
            'SessionId' => $SessionId
        ));

        $array = array();
        $json = json_encode($response);
        if ($response->Result == "OK") {
            $xmlstring = $response->DatasetXML;
            $xml = simplexml_load_string($xmlstring);
            $json = json_encode($xml);
            $array = json_decode($json, TRUE);
            if (array_key_exists('Table', $array)) {
                return $array['Table'];
            } else {
                return $array;
            }
        }
        return $array;
    }
    public static function getTicketTypeFromBarcode($CinemaId,$SessionId,$Barcode) {
        $client = new SoapClient(DATA_SERVICE);

        $response = $client->GetTicketTypeFromBarcode(array(
            'CinemaId' => $CinemaId,
            'Barcode' => $Barcode,
            'SessionId' => $SessionId
        ));

        $array = array();
        $json = json_encode($response);
        if ($response->Result == "OK") {
            $xmlstring = $response->DatasetXML;
            $xml = simplexml_load_string($xmlstring);
            $json = json_encode($xml);
            $array = json_decode($json, TRUE);
            if (array_key_exists('Table', $array)) {
                return $array['Table'];
            } else {
                return $array;
            }
        }
        return $array;
    }
    public static function getTicketTypePackage($CinemaId,$SessionId,$TicketTypeCode) {
        $client = new SoapClient(DATA_SERVICE);
        $response = $client->GetTicketTypePackage(array(
            'CinemaId' => $CinemaId,
            'SessionId' => $SessionId,
            'TicketTypeCode' => $TicketTypeCode
        ));

        $array = array();
        $json = json_encode($response);
        if ($response->Result == "OK") {
            $xmlstring = $response->DatasetXML;
            $xml = simplexml_load_string($xmlstring);
            $json = json_encode($xml);
            $array = json_decode($json, TRUE);
            if (array_key_exists('Table', $array)) {
                return $array['Table'];
            } else {
                return $array;
            }
        }
        return $array;
    }
    public static function getShowtimeDateList($CinemaId,$OptionalBizStartHourOfDay=0) {
        $client = new SoapClient(DATA_SERVICE);

        $response = $client->GetShowtimeDateList(array(
            'OptionalBizStartHourOfDay' => $OptionalBizStartHourOfDay,
            'CinemaId' => $CinemaId
        ));

        $array = array();
        $json = json_encode($response);
        if ($response->Result == "OK") {
            $xmlstring = $response->DatasetXML;
            $xml = simplexml_load_string($xmlstring);
            $json = json_encode($xml);
            $array = json_decode($json, TRUE);
            if (array_key_exists('Table', $array)) {
                return $array['Table'];
            } else {
                return $array;
            }
        }
        return $array;
    }
    public static function getEventList($CinemaId,$TypeFlag) {
        $client = new SoapClient(DATA_SERVICE,array('trace'=>1));
        $response = $client->GetEventList(array(
            'CinemaId' => $CinemaId,
            'TypeFlag' => $TypeFlag
        ));
        //@mail("prasanthka01@gmail.com","EVENT REQ:",$client->__getLastRequest());
        $array = array();
        $result = array();
        $json = json_encode($response);
        if ($response->Result == "OK") {
            $xmlstring = $response->DatasetXML;
            $xml = simplexml_load_string($xmlstring);
            $json = json_encode($xml);
            $array = json_decode($json, TRUE);
            if (array_key_exists('Table', $array)) {
                $result = $array['Table'];
            } else {
                $result = $array;
            }
        }
        if(count($result)==0){
            return $result;
        }
        if (count($result) != count($result, COUNT_RECURSIVE)) {
            foreach ($result as $key=>$forconcatimage)
            {
                if(isset($result['Event_strCode']))
                    $result[$key]["image_url"] = EVENT_IMAGE_URL."{$forconcatimage['Event_strCode']}?width=121&height=180";

            }
        }
        else if (count($result) == count($result, COUNT_RECURSIVE)) {
            if(isset($result['Event_strCode']))
                $result["image_url"] = CINEMA_IMAGE_URL."{$result['Event_strCode']}?width=160&height=160";
        }
        if(!isset($result[0]))
                    $result = array($result);
        
        return $result;
    }
    public static function getEventMovieList($CinemaId,$EventCode) {
        $client = new SoapClient(DATA_SERVICE);
        $response = $client->GetEventMovieList(array(
            'CinemaId' => $CinemaId,
            'EventCode' => $EventCode
        ));

        $array = array();
        $json = json_encode($response);
        if ($response->Result == "OK") {
            $xmlstring = $response->DatasetXML;
            $xml = simplexml_load_string($xmlstring);
            $json = json_encode($xml);
            $array = json_decode($json, TRUE);
            if (array_key_exists('Table', $array)) {
                return $array['Table'];
            } else {
                return $array;
            }
        }
        return $array;
    }
    public static function getCinemaOpForSession($CinemaId,$SessionId) {
        $client = new SoapClient(DATA_SERVICE);
        $response = $client->GetCinemaOpForSession(array(
            'CinemaId' => $CinemaId,
            'SessionId' => $SessionId
        ));

        $array = array();
        $json = json_encode($response);
        if ($response->Result == "OK") {
            $xmlstring = $response->DatasetXML;
            $xml = simplexml_load_string($xmlstring);
            $json = json_encode($xml);
            $array = json_decode($json, TRUE);
            if (array_key_exists('Table', $array)) {
                return $array['Table'];
            } else {
                return $array;
            }
        }
        return $array;
    }
    public static function getPrintStream($UserSessionId,$PrintDocumentType,$PrintDocumentCode) {
        $client = new SoapClient(DATA_SERVICE);
        $response = $client->GetPrintStream(array(
            'UserSessionId' => $UserSessionId,
            'PrintDocumentType' => $PrintDocumentType,
            'PrintDocumentCode' => $PrintDocumentCode
        ));

        $array = array();
        $json = json_encode($response);
        if ($response->Result == "OK") {
            $xmlstring = $response->DatasetXML;
            $xml = simplexml_load_string($xmlstring);
            $json = json_encode($xml);
            $array = json_decode($json, TRUE);
            if (array_key_exists('Table', $array)) {
                return $array['Table'];
            } else {
                return $array;
            }
        }
        return $array;
    }
    public static function getConfirmationDetails($ConfirmationDetailsType,$CinemaId,$VistaBookingNumber,$VistaTransNumber) {
        $client = new SoapClient(DATA_SERVICE);
        $response = $client->GetConfirmationDetails(array(
            'ConfirmationDetailsType' => $ConfirmationDetailsType,
            'CinemaId' => $CinemaId,
            'VistaBookingNumber' => $VistaBookingNumber,
            'VistaTransNumber' => $VistaTransNumber
        ));

        $array = array();
        $json = json_encode($response);
        if ($response->Result == "OK") {
            $xmlstring = $response->DatasetXML;
            $xml = simplexml_load_string($xmlstring);
            $json = json_encode($xml);
            $array = json_decode($json, TRUE);
            if (array_key_exists('Table', $array)) {
                return $array['Table'];
            } else {
                return $array;
            }
        }
        return $array;
    }
    public static function getConcessionItemsList($CinemaId,$ClientId,$OrderUserId) {
        $client = new SoapClient(DATA_SERVICE);
        $response = $client->GetConcessionItemsList(array(
            'CinemaId' => $CinemaId,
            'ClientId' => $ClientId,
            'OrderUserId' => $OrderUserId
        ));

        $array = array();
        $json = json_encode($response);
        if ($response->Result == "OK") {
            $xmlstring = $response->DatasetXML;
            $xml = simplexml_load_string($xmlstring);
            $json = json_encode($xml);
            $array = json_decode($json, TRUE);
            if (array_key_exists('Table', $array)) {
                return $array['Table'];
            } else {
                return $array;
            }
        }
        return $array;
    }

    public static function createClient($ClientId,$WorkstationCode,$Description,$SalesChannel,$ClientClass) {
        $client = new SoapClient(DATA_SERVICE);
        $response = $client->CreateClient(array(
            'ClientId' => $ClientId,
            'WorkstationCode' => $WorkstationCode,
            'Description' => $Description,
            'SalesChannel' => $SalesChannel,
            'ClientClass' => $ClientClass
        ));

        $array = array();
        $json = json_encode($response);
        if ($response->Result == "OK") {
            $xmlstring = $response->DatasetXML;
            $xml = simplexml_load_string($xmlstring);
            $json = json_encode($xml);
            $array = json_decode($json, TRUE);
            if (array_key_exists('Table', $array)) {
                return $array['Table'];
            } else {
                return $array;
            }
        }
        return $array;
    }
    public static function getClientList($OptionalClientId,$OptionalDescription) {
        $client = new SoapClient(DATA_SERVICE);
        $response = $client->GetClientList(array(
            'OptionalClientId' => $OptionalClientId,
            'OptionalDescription' => $OptionalDescription
        ));

        $array = array();
        $json = json_encode($response);
        if ($response->Result == "OK") {
            $xmlstring = $response->DatasetXML;
            $xml = simplexml_load_string($xmlstring);
            $json = json_encode($xml);
            $array = json_decode($json, TRUE);
            if (array_key_exists('Table', $array)) {
                return $array['Table'];
            } else {
                return $array;
            }
        }
        return $array;
    }
    public static function updateClient($Status,$ConfigXml,$ClientId,$WorkstationCode,$Description,$SalesChannel,$ClientClass) {
        $client = new SoapClient(DATA_SERVICE);
        $response = $client->UpdateClient(array(
            'Status' => $Status,
            'ConfigXml' => $ConfigXml,
            'ClientId' => $ClientId,
            'WorkstationCode' => $WorkstationCode,
            'Description' => $Description,
            'SalesChannel' => $SalesChannel,
            'ClientClass' => $ClientClass
        ));

        $array = array();
        $json = json_encode($response);
        if ($response->Result == "OK") {
            $xmlstring = $response->DatasetXML;
            $xml = simplexml_load_string($xmlstring);
            $json = json_encode($xml);
            $array = json_decode($json, TRUE);
            if (array_key_exists('Table', $array)) {
                return $array['Table'];
            } else {
                return $array;
            }
        }
        return $array;
    }
    public static function getLtyMembershipConcessionItem($ClientId) {
        $client = new SoapClient(DATA_SERVICE);
        $response = $client->GetLtyMembershipConcessionItem(array(
            'ClientId' => $ClientId
        ));

        $array = array();
        $json = json_encode($response);
        if ($response->Result == "OK") {
            $xmlstring = $response->DatasetXML;
            $xml = simplexml_load_string($xmlstring);
            $json = json_encode($xml);
            $array = json_decode($json, TRUE);
            if (array_key_exists('Table', $array)) {
                return $array['Table'];
            } else {
                return $array;
            }
        }
        return $array;
    }
    public static function getOrder($UserSessionId,$ProcessOrderValue,$BookingMode,$OptionalClientClass,$OptionalClientId,$OptionalClientName) {
        $client = new SoapClient(TICKETING_SERVICE);
        $response = $client->GetOrder(array(
            'UserSessionId' => $UserSessionId,
            'ProcessOrderValue' => $ProcessOrderValue,
            'BookingMode' => $BookingMode,
            'OptionalClientClass' => $OptionalClientClass,
            'OptionalClientId' => $OptionalClientId,
            'OptionalClientName' => $OptionalClientName
        ));
        $json = json_encode($response);
        $array = json_decode($json, TRUE);
        return $array;
    }
    public static function getOrderHistory($LoyaltyMemberId,$MemberEmail="") {
        $client = new SoapClient(DATA_SERVICE);
        $response = $client->GetOrderHistory(array(
            'MemberEmail' => $MemberEmail,
            'LoyaltyMemberId' => $LoyaltyMemberId
        ));
        $json = json_encode($response);
        $array = json_decode($json, TRUE);
        return $array;
    }


    public static function addTickets($UserSessionId,$CinemaId, $SessionId, $TicketTypes, $OptionalLoyaltyTicketMatchesHOCode = 'False', $OptionalShowNonATMTickets = 'False', $OptionalReturnAllRedemptionAndCompTickets = 'False', $OptionalReturnAllLoyaltyTickets = 'False', $OptionalAreaCategoryCode = '', $OptionalClientClass = '', $OptionalReturnLoyaltyRewardFlag = 'False', $OptionalSeparatePaymentBasedTickets = 'False', $OptionalShowLoyaltyTicketsToNonMembers = 'False', $OptionalEnforceChildTicketLogic = 'False', $OptionalIncludeZeroValueTickets = 'False') {
        $client = new SoapClient(TICKETING_SERVICE,array('trace'=>1));
       
        $response = $client->AddTickets(array(
            'UserSessionId' => $UserSessionId,
            'CinemaId' => $CinemaId,
            'SessionId' => $SessionId,
            'TicketTypes' => $TicketTypes,
            'ReturnOrder' => "true",
            'ReturnSeatData' => "false",
            'ProcessOrderValue' => "false",
            'UserSelectedSeatingSupported' => "1",
            'SkipAutoAllocation' => "0",
            'IncludeAllSeatPriorities' => "false",
            'IncludeSeatNumbers' => "false",
            'ExcludeAreasWithoutTickets' => 0,
            'ReturnDiscountInfo' => "true",
            'BookingFeeOverride' => "",
            'BookingMode' => "1",
            'ReorderSessionTickets' => "false",
            'ReturnSeatDataFormat' => "0",
            'IncludeCompanionSeats' => "1",
            'OptionalClientClass' => "",
            'OptionalClientId' => "",
            'OptionalClientName' => ""
        ));

        $res = $response;
        $json = json_encode($res);

        EmagineLogController::saveLog("Add Ticket Request And Response", $client->__getLastRequest(), $json,$UserSessionId);
                 
        $array = json_decode($json, TRUE);
        return $array;
    }
    public static function addTicketsCheck($UserSessionId,$CinemaId, $SessionId,$TicketTypes,$OptionalLoyaltyTicketMatchesHOCode = 'False', $OptionalShowNonATMTickets = 'False', $OptionalReturnAllRedemptionAndCompTickets = 'False', $OptionalReturnAllLoyaltyTickets = 'False', $OptionalAreaCategoryCode = '', $OptionalClientClass = '', $OptionalReturnLoyaltyRewardFlag = 'False', $OptionalSeparatePaymentBasedTickets = 'False', $OptionalShowLoyaltyTicketsToNonMembers = 'False', $OptionalEnforceChildTicketLogic = 'False', $OptionalIncludeZeroValueTickets = 'False') {
        $client = new SoapClient(TICKETING_SERVICE);

        $response = $client->AddTickets(array(
            'UserSessionId' => $UserSessionId,
            'CinemaId' => $CinemaId,
            'SessionId' => $SessionId,
            'TicketTypes' => $TicketTypes,
            'ReturnOrder' => "true",
            'ReturnSeatData' => "false",
            'ProcessOrderValue' => "false",
            'UserSelectedSeatingSupported' => "true",
            'SkipAutoAllocation' => "false",
            'IncludeAllSeatPriorities' => "false",
            'IncludeSeatNumbers' => "false",
            'ExcludeAreasWithoutTickets' => "False",
            'ReturnDiscountInfo' => "true",
            'BookingFeeOverride' => "",
            'BookingMode' => "1",
            'ReorderSessionTickets' => "false",
            'ReturnSeatDataFormat' => "0",
            'OptionalClientClass' => "",
            'OptionalClientId' => "",
            'OptionalClientName' => ""
        ));


        $res = $response;
        $json = json_encode($res);
        $array = json_decode($json, TRUE);
        return $array;
    }
    public static function getSessionSeatData($CinemaId,$SessionId,$UserSessionId,$OptionalClientClass="",$OptionalClientId="",$OptionalClientName="",$ReturnOrder="false",$ExcludeAreasWithoutTickets="false",$IncludeBrokenSeats="false",$IncludeHouseSpecialSeats="false",$IncludeGreyAndSofaSeats="false",$IncludeAllSeatPriorities="false",$IncludeSeatNumbers="true",$IncludeCompanionSeats="false") {
        $client = new SoapClient(TICKETING_SERVICE);
        $response = $client->GetSessionSeatData(array(
            'CinemaId' => $CinemaId,
            'SessionId' => $SessionId,
            'UserSessionId' => $UserSessionId,
            'ReturnOrder' => "true",
            'ExcludeAreasWithoutTickets' => "false",
            'IncludeBrokenSeats' => "false",
            'IncludeHouseSpecialSeats' => "false",
            'IncludeGreyAndSofaSeats' => "false",
            'IncludeAllSeatPriorities' => "false",
            'IncludeSeatNumbers' => "true",
            'IncludeCompanionSeats' => "false",
            'OptionalClientClass' => "",
            'OptionalClientId' => "",
            'OptionalClientName' => ""
        ));
        $res = $response;
        $json = json_encode($res);
        $array = json_decode($json, TRUE);
        return $array;

    }
    public static function setSelectedSeats($CinemaId,$SessionId,$UserSessionId,$SelectedSeats) {
        $option=array('trace'=>1);
        $client = new SoapClient(TICKETING_SERVICE,$option);
        $response = $client->SetSelectedSeats(array(
            'CinemaId' => $CinemaId,
            'SessionId' => $SessionId,
            'UserSessionId' => $UserSessionId,
            'SeatData' => "",
            'ReturnOrder' => "1",
            'SelectedSeats' => $SelectedSeats,
            'OptionalClientClass' => "",
            'OptionalClientId' => "",
            'OptionalClientName' => ""

        ));
       

        //@mail("prasanthka01@gmail.com,josh@thetunagroup.com","Set Seat Selected",$client->__getLastRequest());

        $res = $response;
        $json = json_encode($res);
        $array = json_decode($json, TRUE);
        return $array;
    }
    public static function applyDiscounts($UserSessionId,$ItemId,$DiscountCode) {
        $client = new SoapClient(TICKETING_SERVICE);
        $response = $client->ApplyDiscounts(array(
            'UserSessionId' => $UserSessionId,
            'ConcessionDiscounts' => array(
                'ConcessionDiscountApplied' => array(
                    'Quantity' => "0",
                    'ItemId' => "0",
                    'DiscountCode' => ""
                ),
            ),
            'TicketDiscounts' => array(
                'TicketDiscountApplied' => array(
                    'ItemId' => $ItemId,
                    'DiscountCode' => $DiscountCode
                ),
            ),
            'ReturnOrder' => "1",
            'OptionalClientClass' => "",
            'OptionalClientId' => "",
            'OptionalClientName' => ""
        ));

        $res = $response;
        $json = json_encode($res);
        $array = json_decode($json, TRUE);
        return $array;

    }
    public static function cancelOrder($UserSessionId) {
        $client = new SoapClient(TICKETING_SERVICE);
        $response = $client->CancelOrder(array(
            'UserSessionId' => $UserSessionId,
            'OptionalClientClass' => "",
            'OptionalClientId' => "",
            'OptionalClientName' => ""
        ));
        $res = $response;
        $json = json_encode($res);
        $array = json_decode($json, TRUE);
        return $array;
    }
    public static function completeOrder($UserSessionId,$PaymentInfo,$CustomerName,$CustomerEmail,$CustomerPhone,$PerformPayment=0) {
        $client = new SoapClient(TICKETING_SERVICE, array('trace' => 1));
        $response = $client->CompleteOrder(array(
            'UserSessionId' => $UserSessionId,
            'PaymentInfo' => $PaymentInfo,
            'PerformPayment' => $PerformPayment,
            'CustomerEmail' => $CustomerEmail,
            'CustomerPhone' => $CustomerPhone,
            'CustomerName' => $CustomerName,
            'GeneratePrintStream' => "false",
            'ReturnPrintStream' => "false",
            'UnpaidBooking' => "1",
            'PrintTemplateName' => "",
            'OptionalMemberId' =>"",
            'OptionalReturnMemberBalances' =>"false",
            'CustomerZipCode' =>"",
            'BookingMode' => "0",
            'PrintStreamType' => "0",
            'GenerateConcessionVoucherPrintStream' =>false,
            'PassTypesRequestedForOrder' => array(
                'IncludeApplePassBook' => "false",
                'IncludeICal' => "false",
                'AdditionalAttachmentTypes' =>array(
                    "String" =>array()
                )
            ),
            'UseAlternateLanguage' => "false",
            'OptionalClientClass' => "",
            'OptionalClientId' => "",
            'OptionalClientName' => ""
        ));
        $message = "REQUEST:\n" . $client->__getLastRequest() . "\n";
        //@mail("prasanthka01@gmail.com", "Emagine Vista Complete Order", $message);
         
        $res = $response;
        $json = json_encode($res);
        $array = json_decode($json, TRUE);
        EmagineLogController::saveLog("Complete Order Request And Response", $client->__getLastRequest(), $json,$UserSessionId);
        if(isset($array["VistaBookingNumber"]) && isset($array["VistaBookingId"]) ){
            $message = "VistaBookingNumber : ".$array["VistaBookingNumber"]." VistaBookingId: ".$array["VistaBookingId"];
            //@mail("prasanthka01@gmail.com", "Emagine Vista Number:ID", $message);
        }
        
        return $array;
    }
    public static function removeTickets($UserSessionId) {
        $client = new SoapClient(TICKETING_SERVICE);

        $response = $client->RemoveTickets(array(
            'OptionalClientClass' => "",
            'OptionalClientId' => "",
            'OptionalClientName' => "",
            'UserSessionId' => $UserSessionId,
            'ReturnOrder' => "true"
        ));
        $json = json_encode($response);
        $array = json_decode($json, TRUE);
        
        return $array;
    }
    public static function removeConcessions($UserSessionId,$ConcessionRemovals) {
        $client = new SoapClient(TICKETING_SERVICE);

        $response = $client->RemoveConcessions(array(
            'UserSessionId' => $UserSessionId,
            'ConcessionRemovals' => $ConcessionRemovals,
            'ReturnOrder' => "true"
        ));

        $res = $response;
        $json = json_encode($res);
        $array = json_decode($json, TRUE);
        return $array;
    }
    public static function resetOrderExpiry($UserSessionId) {
        $client = new SoapClient(TICKETING_SERVICE);

        $response = $client->ResetOrderExpiry(array(
            'UserSessionId' => $UserSessionId
        ));

        $json = json_encode($response);
        $array = json_decode($json, TRUE);
        return $array;
    }
    public static function removeCardFromWallet($UserSessionId,$AccessToken,$MemberId) {
        $client = new SoapClient(TICKETING_SERVICE);

        $response = $client->RemoveCardFromWallet(array(
            'UserSessionId' => $UserSessionId,
            'AccessToken' => $AccessToken,
            'MemberId' => $MemberId
        ));

        $array = array();
        $json = json_encode($response);
        if ($response->Result == "OK") {
            $xmlstring = $response->DatasetXML;
            $xml = simplexml_load_string($xmlstring);
            $json = json_encode($xml);
            $array = json_decode($json, TRUE);
            if (array_key_exists('Table', $array)) {
                return $array['Table'];
            } else {
                return $array;
            }
        }
        return $array;
    }
    public static function externalPaymentStarting($UserSessionId) {
        $client = new SoapClient(TICKETING_SERVICE);

        $response = $client->ExternalPaymentStarting(array(
            'UserSessionId' => $UserSessionId
        ));

        $array = array();
        $json = json_encode($response);
        if ($response->Result == "OK") {
            $xmlstring = $response->DatasetXML;
            $xml = simplexml_load_string($xmlstring);
            $json = json_encode($xml);
            $array = json_decode($json, TRUE);
            if (array_key_exists('Table', $array)) {
                return $array['Table'];
            } else {
                return $array;
            }
        }
        return $array;
    }
    public static function getCardWallet($UserSessionId) {
        $client = new SoapClient(TICKETING_SERVICE);

        $response = $client->GetCardWallet(array(
            'UserSessionId' => $UserSessionId
        ));

        $array = array();
        $json = json_encode($response);
        if ($response->Result == "OK") {
            $xmlstring = $response->DatasetXML;
            $xml = simplexml_load_string($xmlstring);
            $json = json_encode($xml);
            $array = json_decode($json, TRUE);
            if (array_key_exists('Table', $array)) {
                return $array['Table'];
            } else {
                return $array;
            }
        }
        return $array;
    }
    public static function validateMember($UserSessionId,$MemberId,$IncludeAdvanceBooking="false",$ReturnMember="1") {
        $client = new SoapClient(LOYALTY_SERVICE);

        $response = $client->ValidateMember(array(
            'UserSessionId' => $UserSessionId,
            'MemberId' => $MemberId,
            'ReturnMember' => $ReturnMember,
            'IncludeAdvanceBooking' => $IncludeAdvanceBooking
        ));


        $res = $response;
        $json = json_encode($res);
        $array = json_decode($json, TRUE);
        //Creating card number barcode 
        if(isset($array["LoyaltyMember"]["CardNumber"])) {
            $code = $array["LoyaltyMember"]["CardNumber"];
            if (!file_exists("uploads/barcode/loyalty/".$code.".png")) {
                try{
                    include(app_path() . '/Barcode/barcode_loyalty.php');
                    $array["LoyaltyMember"]["card_number_barcode"] = asset($barcode_image_loyalty);
                } catch (\Exception $ex) {
                    $array["LoyaltyMember"]["card_number_barcode"] = "";
                }
            }else{
                $barcode_image_loyalty = "uploads/barcode/loyalty/".$code.".png";
                $array["LoyaltyMember"]["card_number_barcode"] = asset($barcode_image_loyalty);
            }

        }
        //Ends
        return $array;
    }
    public static function LoayaltyLogin($UserSessionId,$MemberLogin,$MemberPassword,$IncludeAdvanceBooking="0",$ReturnMember="1") {
        //Login with user name
        $client = new SoapClient(LOYALTY_SERVICE, array('trace' => 1));

        $response = $client->ValidateMember(array(
            'UserSessionId' => $UserSessionId,
            'MemberLogin' => $MemberLogin,
            'MemberPassword' => $MemberPassword,
            'ReturnMember' => $ReturnMember,
            'IncludeAdvanceBooking' => $IncludeAdvanceBooking
        ));
        $res = $response;
        $json = json_encode($res);
        $array = json_decode($json, TRUE);
        if (isset($array["Result"]) && $array["Result"] != "OK") {
            //Login with email
            if (!filter_var($MemberLogin, FILTER_VALIDATE_EMAIL) === false) {
                $client = new SoapClient(LOYALTY_SERVICE, array('trace' => 1));
                $response = $client->ValidateMember(array(
                    'UserSessionId' => $UserSessionId,
                    'MemberEmail' => $MemberLogin,
                    'MemberPassword' => $MemberPassword,
                    'ReturnMember' => $ReturnMember,
                    'IncludeAdvanceBooking' => $IncludeAdvanceBooking
                ));
                $res = $response;
                $json = json_encode($res);
                $array = json_decode($json, TRUE);
            } 
        }
        //EmagineLogController::saveLog("Login", $client->__getLastRequest(), $json);
        return $array;
    }
    public static function createMember($user_session_id,$email,$user_name,$password,$first_name,$last_name,$club_id,$dob="0001-01-01",$phone="") {
        $expiry = date('Y-m-d', strtotime('+1 years'));
        $client = new SoapClient(LOYALTY_SERVICE, array('trace' => 1));
        $response = $client->CreateMember(array(
            "LoyaltyMember" =>array(
                'FirstName' => $first_name,
                'LastName' => $last_name,
                'FullName' => "",
                'CardNumber' => "",
                'MobilePhone' => $phone,
                'HomePhone' => $phone,
                'Email' => $email,
                'ClubID' => $club_id,
                'BalanceList'=>array(
                    'MemberBalance'=>array(
                        'BalanceTypeID' => "",
                        'Name' => "",
                        'Message' => "",
                        'PointsRemaining' => "0",
                        'LifetimePointsBalanceDisplay' => "0",
                        'IsDefault' => "0",
                        'NameAlt' => "",
                        'NameTranslations' => array(
                            "Translation" => array(
                                "LanguageTag" => "",
                                "Text" => ""
                            ),
                            'RedemptionRate' => "0"
                        )
                    )
                ),
                'UserName' => $user_name,
                'Password' => $password,
                'MiddleName' => "",
                'Address1' => "",
                'State' => "",
                'City' => "",
                'ZipCode' => "",
                'SendNewsletter' =>"0",
                'EducationLevel' =>"0",
                'HouseholdIncome' =>"0",
                'PersonsInHousehold' =>"0",
                'DateOfBirth' =>"{$dob}T00:00:00",
                'Status' =>"",
                'Suburb' =>"",
                'Gender' =>"",
                'PickupComplex' =>"0",
                'PreferredComplex' => "0",
                'PreferredComplexList' => array(
                    "Int32"=>"0"
                ),
                'PreferenceList' => array(
                    "Int32"=>"0"
                ),
                'ClubName' =>"",
                'ContactByThirdParty' =>"0",
                'ExpiryDate' =>"{$expiry}T00:00:00",
                'WishToReceiveSMS' =>"0",
                'WorkZipCode' =>"",
                'CardList' =>array(
                    "String"=>""
                ),
                'PreferredGenres' => array(
                    "Int32"=>"0"
                ),
                'Occupation' =>"0",
                'MaritalStatus' =>"",
                'MailingFrequency' =>"",
                'Pin' =>"",
                'Memberships'=>array(
                    'LoyaltyMembership'=>array(
                        'Id' => "",
                        'CardNumber' => "",
                        'ClubId' => "",
                        'ClubName' => "",
                        'ExpiryDate' => "{$expiry}T00:00:00"
                    )
                ),
                'ExpiryPointsList'=>array(
                    'ExpiryPoints'=>array(
                        'PointsExpiring' => "0",
                        'ExpireOn' => "{$expiry}T00:00:00",
                        'BalanceTypeId' => "0"
                    )
                ),
                'MemberLevelId' =>"0",
                'MemberLevelName' =>"",
                'LoyaltySessionExpiry' =>"0001-01-01T00:00:00",
                'GiftCard' =>"0",
                'GiftCardBalance' =>"0",
                'IsBannedFromMakingUnpaidBookingsUntil' =>"0001-01-01T00:00:00",
                'MembershipActivated' =>"0",
                'MemberItemId' =>"",
                'ExternalID' =>"",
                'NationalID' =>"",
                'PushNotificationSubscription' =>array(
                    "PushToken" => ""
                ),
                'IsAnonymous' =>"0"
            ),
            'UserSessionId' => $user_session_id,
            'CustomerLanguageTag' => "",
            'OptionalClientClass' => "",
            'OptionalClientId' => "",
            'OptionalClientName' => ""
        ));

        //echo "REQUEST:\n" . $client->__getLastRequest() . "\n";die();
        $res = $response;
        $json = json_encode($res);
        $array = json_decode($json, TRUE);
        return $array;
    }
    public static function deleteMember($member_id) {
        $client = new SoapClient(LOYALTY_SERVICE);

        $response = $client->DeleteMember(array(
            'MemberId' => $member_id,
            'OptionalUserSessionId' => "",
            'CustomerLanguageTag' => "",
        ));


        $res = $response;
        $json = json_encode($res);
        $array = json_decode($json, TRUE);
        return $array;
    }
    public static function setMemberToActivateOnComplete($array) {
        $client = new SoapClient(LOYALTY_SERVICE, array('trace' => 1));
        $response = $client->SetMemberToActivateOnComplete($array);

        //echo "REQUEST:\n" . $client->__getLastRequest() . "\n";die();
        $res = $response;
        $json = json_encode($res);
        $array = json_decode($json, TRUE);
        return $array;
    }
    public static function AddConcessions($array) {
        $member_concession_info = json_encode($array);
        $url = BASE_URL_API . 'RESTTicketing.svc/order/concessions';
        $member_concession_order = VistaController::httpPostJson($url, $member_concession_info);
        if ($member_concession_order->Result == 0) {
            return true;
        }else{
            return false;
        }
    }
    

    public static function getMember($UserSessionId) {
        $client = new SoapClient(LOYALTY_SERVICE);

        $response = $client->GetMember(array(
            'UserSessionId' => $UserSessionId
        ));

        $res = $response;
        $json = json_encode($res);
        $array = json_decode($json, TRUE);
        return $array;
    }
    public static function getMemberID($MemberLogin,$MemberPassword) {
        $client = new SoapClient(LOYALTY_SERVICE);

        $response = $client->GetMemberID(array(
            'MemberLogin' => $MemberLogin,
            'MemberPassword' => $MemberPassword,
            'MemberCardNumber' => ""
        ));

        $res = $response;
        $json = json_encode($res);
        $array = json_decode($json, TRUE);
        return $array;
    }
    public static function logOffMember($UserSessionId) {
        $client = new SoapClient(LOYALTY_SERVICE);

        $response = $client->LogOffMember(array(
            'UserSessionId' => $UserSessionId
        ));

        $res = $response;
        $json = json_encode($res);
        $array = json_decode($json, TRUE);
        return $array;
    }

    public static function getMemberItemList($UserSessionId,$remove=true) {
        $client = new SoapClient(LOYALTY_SERVICE);

        $response = $client->GetMemberItemList(array(
            'UserSessionId' => $UserSessionId,
            'SelectedSessionDateTime' => date('Y-m-d')."T00:00:00",
            'GetTicketTypes' => 1,
            'GetDiscounts' => 1,
            'GetDiscounts' => 1,
            'GetConcessions' => "false",
            'SupressSelectedSessionDateTimeFilter' => "false",
            'GetAdvanceBookings' => "false",
            'GetAdvanceBookings' => "false",
            'GetAdvanceSeatingRecognitions' => 1,
        ));

        $res = $response;
        $json = json_encode($res);
        $array = json_decode($json, TRUE);
        $all_rewards = $array;

        if($array["Result"] == "OK"){
            $upgrade_items = Common::where("id","upgrade_items")->first();
            $upgrade_items_array =[];
            
            if(!is_null($upgrade_items))
                $upgrade_items_array = explode(",", $upgrade_items->value);
            
            $rewards_table = Reward::where("is_active",0)->get();
            $reward_array=[];
            $all_rewards=[];
            
            $TicketTypeList=[];
            $DiscountList=[];
            $ConcessionList=[];
            $AdvanceBookingList=[];
            $AdvanceSeatingList=[];
            if($remove){
                foreach($rewards_table as $reward_table){
                    array_push($reward_array, $reward_table->item_id);
                }
            }
            if(isset($array["TicketTypeList"]["LoyaltyItem"])){
                $TicketLoyaltyItems = $array["TicketTypeList"]["LoyaltyItem"];
                $i=0;
                $rewards = [];
                $LoyaltyItems = [];
                
                if(!isset($TicketLoyaltyItems[0])){
                    $LoyaltyItems[0] = $array["TicketTypeList"]["LoyaltyItem"];
                }else{
                    $LoyaltyItems = $array["TicketTypeList"]["LoyaltyItem"];
                }
                
                foreach($LoyaltyItems as $LoyaltyItem){
                    $rewards[$i]=$LoyaltyItem;
                    if(in_array($rewards[$i]["RecognitionID"], $reward_array)){
                        unset($rewards[$i]);
                        $i++;
                        continue;
                    }
                    else if(in_array($rewards[$i]["Description"], $reward_array)){
                        unset($rewards[$i]);
                        $i++;
                        continue;
                    }
                    $rewards[$i]["UpgradingItem"] = 0;
                    if(in_array($rewards[$i]["RecognitionID"], $upgrade_items_array)){
                        $rewards[$i]["UpgradingItem"] = 1;
                    }
                    
                    $i++;
                }
                $TicketTypeList["LoyaltyItem"] = array_values($rewards);
            }
            if(isset($array["DiscountList"]["LoyaltyItem"])){
                $DiscounLoyaltyItems = $array["DiscountList"]["LoyaltyItem"];
                $i=0;
                $rewards = [];
                $LoyaltyItems = [];
                
                if(!isset($DiscounLoyaltyItems[0])){
                    $LoyaltyItems[0] = $array["DiscountList"]["LoyaltyItem"];
                }else{
                    $LoyaltyItems = $array["DiscountList"]["LoyaltyItem"];
                }
                
                
                foreach($LoyaltyItems as $LoyaltyItem){
                    $rewards[$i]=$LoyaltyItem;
                    if(in_array($rewards[$i]["RecognitionID"], $reward_array)){
                        unset($rewards[$i]);
                        $i++;
                        continue;
                    }
                    else if(in_array($rewards[$i]["Description"], $reward_array)){
                        unset($rewards[$i]);
                        $i++;
                        continue;
                    }
                    $rewards[$i]["UpgradingItem"] = 0;
                    if(in_array($rewards[$i]["RecognitionID"], $upgrade_items_array)){
                        $rewards[$i]["UpgradingItem"] = 1;
                    }
                    
                    $i++;
                }
                $DiscountList["LoyaltyItem"] = array_values($rewards);
            }
            if(isset($array["ConcessionList"]["LoyaltyItem"])){
                $ConcessionLoyaltyItems = $array["ConcessionList"]["LoyaltyItem"];
                $i=0;
                $rewards = [];
                $LoyaltyItems = [];
                
                if(!isset($ConcessionLoyaltyItems[0])){
                    $LoyaltyItems[0] = $array["ConcessionList"]["LoyaltyItem"];
                }else{
                    $LoyaltyItems = $array["ConcessionList"]["LoyaltyItem"];
                }
                
                
                foreach($LoyaltyItems as $LoyaltyItem){
                    $rewards[$i]=$LoyaltyItem;
                    if(in_array($rewards[$i]["RecognitionID"], $reward_array)){
                        unset($rewards[$i]);
                        $i++;
                        continue;
                    }
                    else if(in_array($rewards[$i]["Description"], $reward_array)){
                        unset($rewards[$i]);
                        $i++;
                        continue;
                    }
                    $rewards[$i]["UpgradingItem"] = 0;
                    if(in_array($rewards[$i]["RecognitionID"], $upgrade_items_array)){
                        $rewards[$i]["UpgradingItem"] = 1;
                    }
                    
                    $i++;
                }
                $ConcessionList["LoyaltyItem"] = array_values($rewards);
            }
            if(isset($array["AdvanceBookingList"]["LoyaltyItem"])){
                $AdvanceLoyaltyItems = $array["AdvanceBookingList"]["LoyaltyItem"];
                $i=0;
                $rewards = [];
                $LoyaltyItems = [];
                
                if(!isset($AdvanceLoyaltyItems[0])){
                    $LoyaltyItems[0] = $array["AdvanceBookingList"]["LoyaltyItem"];
                }else{
                    $LoyaltyItems = $array["AdvanceBookingList"]["LoyaltyItem"];
                }
                
                foreach($LoyaltyItems as $LoyaltyItem){
                    $rewards[$i]=$LoyaltyItem;
                    if(in_array($rewards[$i]["RecognitionID"], $reward_array)){
                        unset($rewards[$i]);
                        $i++;
                        continue;
                    }
                    else if(in_array($rewards[$i]["Description"], $reward_array)){
                        unset($rewards[$i]);
                        $i++;
                        continue;
                    }
                    $rewards[$i]["UpgradingItem"] = 0;
                    if(in_array($rewards[$i]["RecognitionID"], $upgrade_items_array)){
                        $rewards[$i]["UpgradingItem"] = 1;
                    }
                    
                    $i++;
                }
                $AdvanceBookingList["LoyaltyItem"] = array_values($rewards);
            }
            if(isset($array["AdvanceSeatingList"]["LoyaltyItem"])){
                $AdvanceSeatingLoyaltyItems = $array["AdvanceSeatingList"]["LoyaltyItem"];
                
                $i=0;
                $rewards = [];
                $LoyaltyItems = [];
                
                if(!isset($AdvanceSeatingLoyaltyItems[0])){
                    $LoyaltyItems[0] = $array["AdvanceSeatingList"]["LoyaltyItem"];
                }else{
                    $LoyaltyItems = $array["AdvanceSeatingList"]["LoyaltyItem"];
                }
                foreach($LoyaltyItems as $LoyaltyItem){
                    $rewards[$i]=$LoyaltyItem;
                    
                    if(in_array($rewards[$i]["RecognitionID"], $reward_array)){
                        unset($rewards[$i]);
                        $i++;
                        continue;
                    }
                    else if(in_array($rewards[$i]["Description"], $reward_array)){
                        unset($rewards[$i]);
                        $i++;
                        continue;
                    }
                    $rewards[$i]["UpgradingItem"] = 0;
                    if(in_array($rewards[$i]["RecognitionID"], $upgrade_items_array)){
                        $rewards[$i]["UpgradingItem"] = 1;
                    }
                    
                    $i++;
                }
                $AdvanceSeatingList["LoyaltyItem"] = array_values($rewards);
            }
            $all_rewards["Result"]="OK";
            $all_rewards["TicketTypeList"]=$TicketTypeList;
            $all_rewards["DiscountList"]=$DiscountList;
            $all_rewards["ConcessionList"]=$ConcessionList;
            $all_rewards["AdvanceBookingList"]=$AdvanceBookingList;
            $all_rewards["AdvanceSeatingList"]=$AdvanceSeatingList;
            
        }
        return $all_rewards;
    }
    public static function getReferenceData($UserSessionId) {
        $client = new SoapClient(LOYALTY_SERVICE);

        $response = $client->GetReferenceData(array(
            'UserSessionId' => $UserSessionId,
            'CustomerLanguageTag' => ""
        ));

        $res = $response;
        $json = json_encode($res);
        $array = json_decode($json, TRUE);
        return $array;
    }
    public static function verifyNewMembershipDetails($Email,$UserName,$CardNumber,$SalesChannel,$ClubId) {
        $client = new SoapClient(LOYALTY_SERVICE);

        $response = $client->VerifyNewMembershipDetails(array(
            'Email' => $Email,
            'UserName' => $UserName,
            'CardNumber' => $CardNumber,
            'SalesChannel' => $SalesChannel,
            'ClubId' => $ClubId,
            'CustomerLanguageTag' => ""
        ));

        $res = $response;
        $json = json_encode($res);
        $array = json_decode($json, TRUE);
        return $array;
    }
    public static function memberSearch($UserSessionId) {
        $client = new SoapClient(LOYALTY_SERVICE);

        $response = $client->MemberSearch(array(
            'UserSessionId' => $UserSessionId,
            'CustomerLanguageTag' => ""
        ));

        $res = $response;
        $json = json_encode($res);
        $array = json_decode($json, TRUE);
        return $array;
    }
    public static function getMemberTransactionList($UserSessionId) {
        $client = new SoapClient(LOYALTY_SERVICE);

        $response = $client->GetMemberTransactionList(array(
            'UserSessionId' => $UserSessionId,
            'CustomerLanguageTag' => ""
        ));

        $res = $response;
        $json = json_encode($res);
        $array = json_decode($json, TRUE);
        return $array;
    }
    public static function getMemberRedemptionList($UserSessionId) {
        $client = new SoapClient(LOYALTY_SERVICE,array('trace'=>1));

        $response = $client->GetMemberRedemptionList(array(
            'UserSessionId' => $UserSessionId,
            'CustomerLanguageTag' => ""
        ));

        $res = $response;
        $json = json_encode($res);
        
        $array = json_decode($json, TRUE);
        return $array;
    }
    public static function getMemberTransactionHistory($UserSessionId,$MaxResults,$DateFrom,$DateTo,$ReturnMemberTransactionDetails) {
        
        $from = date("Y-m", strtotime("-1 month"))."-01T00:00:00";
        $to = date("Y-m", strtotime("+3 month"))."-01T00:00:00";
            
        ini_set('default_socket_timeout', 500000);
        $client = new SoapClient(LOYALTY_SERVICE,array('trace'=>1));

        $response = $client->GetMemberTransactionHistory(array(
            'UserSessionId' => $UserSessionId,
            'MaxResults' => $MaxResults,
            'DateFrom' => $from,
            'DateTo' => $to,
            'ReturnMemberTransactionDetails' => $ReturnMemberTransactionDetails,
            'CustomerLanguageTag' => ""
        ));

        $res = $response;
        $json = json_encode($res);
        //EmagineLogController::saveLog("Transaction History", $client->__getLastRequest(), $json);
        $array = json_decode($json, TRUE);
        return $array;
    }


    public static function httpGet($url)
    {
        $ch = curl_init();

        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
        curl_setopt($ch,CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        $output=curl_exec($ch);
        curl_close($ch);
//        var_dump($output);die();
        $output = json_decode($output);
        return $output;
    }
    public static function httpPost($url,$params)
    {
      $postData = '';
       //create name value pairs seperated by &
       foreach($params as $k => $v)
       {
          $postData .= $k . '='.$v.'&';
       }
       $postData = rtrim($postData, '&');

        $ch = curl_init();

        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
        curl_setopt($ch,CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_POST, count($postData));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);

        $output=curl_exec($ch);

        curl_close($ch);
        $output = json_decode($output);
        return $output;

    }
    public static function httpPostJson($url,$params)
    {
        
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER,
                array("Content-type: application/json"));
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $params);

        $json_response = curl_exec($curl);
//        $old_url = BASE_URL_API . 'RESTTicketing.svc/order/concessions';
//        
//        if($url == $old_url){
//            
//            $message = "Request : ".$params."<br/>Response:".$json_response;
//            @mail("prasanthka01@gmail.com","Concession Order",$message);
//        }
        curl_close($curl);
//        echo "<pre>";
//        var_dump($json_response);die();
        $output = json_decode($json_response);
        return $output;

    }

    // public static function saveLoyaltyUserCard($billing_data) {
    //     $settings = AuthorizeController::getAuthorizeConfig();
    //     if (count($settings) == 0) {
    //         return false;
    //     }
    //      $customer_data = array("description" => "{$billing_data['first_name']} {$billing_data['last_name']}",
    //                     "merchantCustomerId" => "emagine_" . $billing_data['user_id'],
    //                     "email" => $billing_data['email'],
    //                     "cardNumber" => $billing_data['card_num'],
    //                     "expirationDate" => $billing_data['exp_date'],
    //                     "firstName" => $billing_data['first_name'],
    //                     "address" => "",
    //                     "city" => "",
    //                     "state" => "",
    //                     "zip" => "");
    //     $authorized_user = AuthorizedLoyaltyUser::where("user_id", $billing_data['user_id'])->where("production", $settings['enable_production'])->first();
    //     $authorized_user_update = new AuthorizedLoyaltyUser;
    //     if (!is_null($authorized_user)) {
    //         $authorized_user_update = AuthorizedLoyaltyUser::find($authorized_user->id);

    //         $customer_data["profile_id"] = $authorized_user->profile_id;
    //         $customer_data["payment_id"] = $authorized_user->payment_id;
    //         AuthorizeController::updateCustomerProfile($settings, $customer_data);
    //         $authorized_user_update->card_number = 'XXXXXXXXXXXX' . substr($billing_data['card_num'], -4);
    //         $authorized_user_update->save();
    //         return true;
    //     } else {
    //         $auth_customer = AuthorizeController::createNewCustomerProfile($settings, $customer_data);
    //         if (!isset($auth_customer["error"])) {
    //             // Update user info
    //             $authorized_user_update->user_id = $billing_data['user_id'];
    //             $authorized_user_update->profile_id = $auth_customer['profile_id'];
    //             $authorized_user_update->payment_id = $auth_customer['payment_id'];
    //             $authorized_user_update->shipping_id = $auth_customer['shipping_id'];
    //             $authorized_user_update->production = $settings['enable_production'];
    //             $authorized_user_update->card_number = 'XXXXXXXXXXXX' . substr($billing_data['card_num'], -4);
    //             $authorized_user_update->save();
    //             return true;
    //         } else {
    //             return false;
    //         }
    //     }
    // }

    public static function saveLoyaltyUserCard($billing_data) {
        $settings = AuthorizeController::getAuthorizeConfig();
        if (count($settings) == 0) {
            return false;
        }
        $cinema_id = $billing_data["cinema_id"];
        $cinema_auth_creds = CinemaAuthCredentials::where("cinema_id",$cinema_id)->where("is_active",1)->first();
        if(!is_null($cinema_auth_creds)){
            if($cinema_auth_creds->auth_login_id != "" && $cinema_auth_creds->auth_transaction_id != ""){
                $settings["enable_production"] = 1;
                $settings["auth_login_id"] = $cinema_auth_creds->auth_login_id;
                $settings["auth_transaction_id"] = $cinema_auth_creds->auth_transaction_id;
            }
        }

        $authorized_user = AuthorizedLoyaltyUser::where("user_id", $billing_data['user_id'])->where("cinema_id", $billing_data['cinema_id'])->where("production", $settings['enable_production'])->first();
        if (is_null($authorized_user)) {
            $authorized_user = new AuthorizedLoyaltyUser;
            $authorized_user->user_id = $billing_data['user_id'];
            $authorized_user->cinema_id = $billing_data['cinema_id'];
            $authorized_user->name = $billing_data['first_name'];
            $authorized_user->email = $billing_data['email'];
            $authorized_user->phone = $billing_data['phone'];
            $authorized_user->card_number = "";
            $authorized_user->save();
        }
        else {
            $authorized_user->name = $billing_data['first_name'];
            $authorized_user->email = $billing_data['email'];
            $authorized_user->phone = $billing_data['phone'];
            $authorized_user->save();
        }

        if(isset($billing_data['save_this_card'])) {
            $customer_data = array("description" => "{$billing_data['first_name']} {$billing_data['last_name']}",
                        "merchantCustomerId" => "emagine_" . $billing_data['user_id'],
                        "email" => $billing_data['email'],
                        "cardNumber" => $billing_data['card_num'],
                        "expirationDate" => $billing_data['exp_date'],
                        "firstName" => $billing_data['first_name'],
                        "address" => "",
                        "city" => "",
                        "state" => "",
                        "zip" => $billing_data["customer_zip"]);

            // Update existing card
            if ($authorized_user->card_number != "") {
                $customer_data["profile_id"] = $authorized_user->profile_id;
                $customer_data["payment_id"] = $authorized_user->payment_id;

                AuthorizeController::updateCustomerProfile($settings, $customer_data);

                $authorized_user->card_type = $billing_data['card_type'];
                $authorized_user->cinema_id = $billing_data['cinema_id'];
                $authorized_user->card_number = 'XXXXXXXXXXXX' . substr($billing_data['card_num'], -4);
                $authorized_user->vista_card = substr($billing_data['card_num'],0,6).'0000000000' . substr($billing_data['card_num'], -4);
                $authorized_user->customer_zip = $billing_data["customer_zip"];
                $authorized_user->save();
                return true;
            }
            // New card
            else {
                $auth_customer = AuthorizeController::createNewCustomerProfile($settings, $customer_data);
                if (!isset($auth_customer["error"])) {
                    // Update user info
                    $authorized_user->cinema_id = $billing_data['cinema_id'];
                    $authorized_user->profile_id = $auth_customer['profile_id'];
                    $authorized_user->payment_id = $auth_customer['payment_id'];
                    $authorized_user->shipping_id = $auth_customer['shipping_id'];
                    $authorized_user->production = $settings['enable_production'];
                    $authorized_user->card_type = $billing_data['card_type'];
                    $authorized_user->card_number = 'XXXXXXXXXXXX' . substr($billing_data['card_num'], -4);
                    $authorized_user->vista_card = substr($billing_data['card_num'],0,6).'000000' . substr($billing_data['card_num'], -4);
                    $authorized_user->customer_zip = $billing_data["customer_zip"];
                    $authorized_user->save();
                    return true;
                }
                else {
                    return false;
                }
            }
        }

        return true;
    }

    public static function getSpecificShowtimes($cinema_id,$movie_id,$date) {

        $optr1 = "gt";
        $optr2 = "lt";

        $date1 = $date;
        $date = strtotime($date);
        $date = strtotime("+1 day", $date);
        $date2 = date('Y-m-d', $date);

        $url = ODATA_URL . "OData.svc/Sessions?%24format=json&%24filter=Showtime%20{$optr1}%20DateTime%27{$date1}%27and%20Showtime%20{$optr2}%20DateTime%27{$date2}%27and%20CinemaId%20eq%20%27{$cinema_id}%27and%20ScheduledFilmId%20eq%20%27{$movie_id}%27";
        $result = VistaController::httpGet($url);
        if (isset($result->value)) {
            foreach ($result->value as $res) {
                $res->cinema_image_url = CINEMA_IMAGE_URL . "{$res->CinemaId}?width=160&height=160";
                $res->movie_image_url = MOVIE_IMAGE_URL . "{$res->ScheduledFilmId}?width=160&height=160";
                $res->time = date("h:i A", strtotime($res->Showtime));
                $res->date = date("D, M d Y", strtotime($res->Showtime));
                $res->date_f = date("Y-m-d", strtotime($res->Showtime));

            }
            return $result->value;
        } else {
            return [];
        }
    }
    public static function getNowShowingScheduledFilms($cinema_id) {

        //$url = ODATA_URL . "OData.svc/GetNowShowingScheduledFilms?%24format=json&cinemaId=%27{$cinema_id}%27";
        $url = ODATA_URL . "OData.svc/GetScheduledFilms?%24format=json&cinemaId=%27{$cinema_id}%27";
        $result = VistaController::httpGet($url);
        if (isset($result->value)) {
            return $result->value;
        } else {
            return [];
        }
    }
    public static function getShowtimes($cinema_id,$movie_id,$date_plus=7) {
        $start_optr = "gt";
        $start_date = date('Y-m-d');
        $end_optr = "lt";
        increment_date_plus:
        $date_plus_7 = date('Y-m-d', strtotime("+{$date_plus} day"));
        $end_date = $date_plus_7;
        
        $date_array = [];
        $show_times = [];
        $url = ODATA_URL . "OData.svc/Sessions?%24format=json&%24filter=Showtime%20{$start_optr}%20DateTime%27{$start_date}%27and%20Showtime%20{$end_optr}%20DateTime%27{$end_date}%27and%20CinemaId%20eq%20%27{$cinema_id}%27and%20ScheduledFilmId%20eq%20%27{$movie_id}%27";
        $result = VistaController::httpGet($url);
        if (isset($result->value)) {
            foreach ($result->value as $res) {
                $res->cinema_image_url = CINEMA_IMAGE_URL . "{$res->CinemaId}?width=160&height=160";
                $res->movie_image_url = MOVIE_IMAGE_URL . "{$res->ScheduledFilmId}?width=160&height=160";
                $res->time = date("h:i A", strtotime($res->Showtime));
                $res->date = date("D, M d Y", strtotime($res->Showtime));
                $res->date_f = date("Y-m-d", strtotime($res->Showtime));
                if (!in_array($res->date_f, $date_array)) {
                        if ($res->date_f >= date('Y-m-d'))
                            $date_array[] = $res->date_f;
                }
                
                $show_times[$res->date_f][$res->SessionId] = $res->time;
            }
           
        }
        if(count($date_array)==0){
            $date_plus = $date_plus+7;
            goto increment_date_plus;
        }
        return [$date_array,$show_times];
    }
    public static function getLoyaltyUserCreditCard($user_id,$cinema_id) {
        $settings = AuthorizeController::getAuthorizeConfig();
        $loyalty_user_card = [];
        if (count($settings) == 0) {
            return $loyalty_user_card;
        }else{
            $authorized_user = AuthorizedLoyaltyUser::where("user_id", $user_id)->where("cinema_id", $cinema_id)->where("production", $settings['enable_production'])->first();
            if(!is_null($authorized_user)){
                $loyalty_user_card = $authorized_user;
                return $loyalty_user_card;
            }else{
                return $loyalty_user_card;
            }
        }

    }
    public static function getLoyaltylocationNumber($cinema_id) {
        
        $url = ODATA_URL."OData.svc/Cinemas?%24format=json";
        $result = VistaController::httpGet($url);
        $location_code = "S";
        if(isset($result->value)){
            foreach ($result->value as $res)
            {
                if($res->ID == $cinema_id){
                    $LoyaltyCode = $res->LoyaltyCode;
                    if(strlen($res->LoyaltyCode) ==1){
                        $LoyaltyCode = "0".$res->LoyaltyCode;
                    }
                    $location_code = $location_code.$LoyaltyCode;
                }
            }
        }
        return $location_code;
    }
    public static function resetPassword($email) {
        
        $EmailData = [
                    "InternetTicketingResetPasswordUrlFormat" => "http://tickets.emagine-entertainment.com/loyalty/resetPassword?code={0}",
                    "InternetTicketingHomeUrl" => BROWSING . "Loyalty/Home",
                    "IsAltLang" => false,
                    "CustomerLanguageTag" => "en"
        ];
        $InitiatePasswordReset = [
            "Username" => null,
            "EmailAddress" => $email,
            "EmailData" => $EmailData
        ];
        $InitiatePasswordResetRequest = json_encode($InitiatePasswordReset);
        $url = BASE_URL_API . 'RESTLoyalty.svc/member/initiatepasswordreset';
        $InitiatePasswordResetResult = VistaController::httpPostJson($url, $InitiatePasswordResetRequest);
        
        if ( isset($InitiatePasswordResetResult->PasswordResetCode) && $InitiatePasswordResetResult->PasswordResetCode != "") {
            return true;
        } else {
            return false;
        }
    }
    
    public static function validatePasswordResetCode($ResetCode) {
        $ValidatePasswordReset = [
            "ResetCode" => $ResetCode
        ];
        $ValidatePasswordResetRequest = json_encode($ValidatePasswordReset);
        $url = BASE_URL_API . 'RESTLoyalty.svc/member/validatepasswordresetcode ';
        $ValidatePasswordResetResult = VistaController::httpPostJson($url, $ValidatePasswordResetRequest);
        $member_id = 0;
        if(isset($ValidatePasswordResetResult->IsResetCodeValid) && $ValidatePasswordResetResult->IsResetCodeValid==true){
            $member_id = $ValidatePasswordResetResult->Member->MemberId;
            return $member_id;
        }else{
            return $member_id;
        }
    }
    
    public static function completePasswordReset($MembershipId,$NewPassword,$ResetCode) {
        $completePasswordReset = [
            "MembershipId" => $MembershipId,
            "NewPassword" => $NewPassword,
            "ResetCode" => $ResetCode
        ];
        $completePasswordResetRequest = json_encode($completePasswordReset);
        $url = BASE_URL_API . 'RESTLoyalty.svc/member/completepasswordreset';
        $completePasswordResetResult = VistaController::httpPostJson($url, $completePasswordResetRequest);
        if (isset($completePasswordResetResult->IsResetCodeValid) && $completePasswordResetResult->IsResetCodeValid) {
            return true;
        } else {
            return false;
        }
    }
    public static function getTicketPrice($cinema_id,$session_id,$user_session_id) {
        $ticket_type_list = VistaController::getTicketTypeList($cinema_id, $session_id, $user_session_id);
        $ticket_types_array = [];
        foreach($ticket_type_list as $ticket_types_codes_p){
            if(isset($ticket_types_codes_p["Price_strTicket_Type_Code"])){
                $ticket_types_array[$ticket_types_codes_p["Price_strTicket_Type_Code"]] =   $ticket_types_codes_p["Price_intTicket_Price"] + $ticket_types_codes_p["Price_intSurcharge"];
            }
            
        }
        return $ticket_types_array;
    }
    public static function getTicketPriceOrder($user_session_id) {
        $get_order = VistaController::getOrder($user_session_id, "0", "0", "", "", "");
        $get_order_value = false;
        $price_in_cents = 0;
        if (isset($get_order["Result"]) && $get_order["Result"] == "OK") {
            if (isset($get_order["Order"]["Sessions"]["Session"])) {
                if (!isset($get_order["Order"]["Sessions"]["Session"][0])) {
                    $order_sessions = array($get_order["Order"]["Sessions"]["Session"]);
                }else{
                    $order_sessions = $get_order["Order"]["Sessions"]["Session"];
                }
                foreach($order_sessions as $sessions){
                    if (isset($sessions["Tickets"]["Ticket"])) {
                        if (!isset($sessions["Tickets"]["Ticket"][0])) {
                            $tickets = array($sessions["Tickets"]["Ticket"]);
                        }else{
                            $tickets = $sessions["Tickets"]["Ticket"];
                        }
                        foreach($tickets as $ticket){
                            $price_in_cents = $price_in_cents+$ticket["PriceCents"];    
                            $get_order_value = true;
                        }
                    }
                }
            }
        }
        if($get_order_value){
            return $price_in_cents;
        }else{
            return -1;
        }
        
    }
    
    



}
