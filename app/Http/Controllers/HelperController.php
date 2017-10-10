<?php

namespace App\Http\Controllers;

use Auth;
use Input;
use Session;
// use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;
use Hash;
use URL;
use DB;

/** Models */
use App\User;
use App\Page;
use App\Category;
use App\ForumCategory;
use App\AllFile;
use App\Menu;
use App\Credentials;

class HelperController extends Controller {

    /**
     * Function to check user role and privilege
     */
    public static function checkPrivilege($check_role = false, $active = false,$post = false, $roles = ["Admin","StoreAdmin","StoreUser"]) {
        if ($post == false) {
            if (Session::has('redirect')) {
                Session::forget('redirect');
            }
            Session::put('redirect', URL::full());
        }
        if ($roles == false) {

            if (!Auth::check()) {
                Auth::logout();
                return false;
            }
        } else {
            if (!Auth::check()) {
                Auth::logout();
                return false;
            }
            if (!in_array(Auth::user()->role, $roles)) {
                Session::flash("fail", "You are not authorized to access this page");
                Auth::logout();
                return false;
            }
        }
        
//        if (Auth::user()->role == "user") {
//            header("location:" . asset("/"));
//            die();
//        }
//        else {
//            //Only to check if role is enabled or not.
//            if($check_role != false && $active == false) {
//                if($check_role->is_active == 0) {
//                    Auth::logout();
//                    return false;
//                }
//            }
//
//            // If function called with parameters
//            if($check_role != false && $active != false) {        
//                if(Auth::user()->role != "Admin") {
//                    if(is_null($check_role)) {
//                        header("location:" . asset("admin/home"));
//                        die();
//                    }
//                    else {
//                        if($check_role->is_active == 1) {
//                            $menu = explode(",", $check_role->available_menu);                    
//                            $active_data = explode("@@@", $active);           
//
//                            // Only for deli - In tables
//                            if(isset($active_data[1])) {
//                                if(!in_array($active_data[1], $menu)) {
//                                    header("location:" . asset("admin/home"));
//                                    die();
//                                }
//                            }                    
//                            else if(!in_array($active, $menu)) {
//                                header("location:" . asset("admin/home"));
//                                die();
//                            }
//                        }
//                        else {                        
//                            Auth::logout();
//                            return false;
//                        }
//                    }
//                }
//            }
//        }
        return true;
    }

    /**
     * Function to create formatted price
     */
    public static function price($price) {
        $price = clean($price);

        if ($price != "") {
            $new_price = number_format($price, 2);
            $split = explode(".", $new_price);
            if ($split[1] == 0)
                $new_price = $split[0];
            return "$" . $new_price;
        } else
            return $price;
    }
    public static function getMenus() {
        if(Auth::check()){
            $menu = Menu::where("is_active", "1")->where("parent", "0")->orderBy("order_no", "asc")->get();
            foreach ($menu as $item) {
                $item->sub_menu = HelperController::get_sub_menu($item->id);
            }
        }else{
            $menu = Menu::where("is_active", "1")->where("is_login", "0")->where("parent", "0")->orderBy("order_no", "asc")->get();
            foreach ($menu as $item) {
                $item->sub_menu = HelperController::get_sub_menu($item->id);
            }
        }

        return $menu;
    }
    public static function get_sub_menu($id) {
        $sql = "SELECT * FROM (SELECT id, name, url, 'menu', image, order_no, is_active FROM menu "
                        . "WHERE parent={$id} AND is_active=1 UNION "
                        . "SELECT id, menu_name,url,'page','',order_no, is_active FROM page "
                        . "WHERE parent={$id} AND is_active=1) "
                        . "dummytablename ORDER BY order_no";
                        
        $menu = DB::select($sql);
        if (!is_null($menu)) {
            foreach ($menu as $item) {
                if ($item->menu == "menu") {
                    $result = HelperController::get_sub_menu($item->id);
                    if (!is_null($result)) {
                        $item->sub_menu = $result;
                    }
                }
            }
        }
        return $menu;
    }
     public static function cleanArray($data) {
        foreach($data as $key=>$value) {
            $data[$key] = HelperController::clean($value);
        }
        
        return $data;
    }
    /**
     * Function to clean a value
     */
    public static function clean($value) {
        // Replace special curved quotes with normal quotes.    
        $chr_map = array(
            // Windows codepage 1252
            "\xC2\x82" => "'", // U+0082⇒U+201A single low-9 quotation mark
            "\xC2\x84" => '"', // U+0084⇒U+201E double low-9 quotation mark
            "\xC2\x8B" => "'", // U+008B⇒U+2039 single left-pointing angle quotation mark
            "\xC2\x91" => "'", // U+0091⇒U+2018 left single quotation mark
            "\xC2\x92" => "'", // U+0092⇒U+2019 right single quotation mark
            "\xC2\x93" => '"', // U+0093⇒U+201C left double quotation mark
            "\xC2\x94" => '"', // U+0094⇒U+201D right double quotation mark
            "\xC2\x9B" => "'", // U+009B⇒U+203A single right-pointing angle quotation mark
            // Regular Unicode     // U+0022 quotation mark (")
            // U+0027 apostrophe     (')
            "\xC2\xAB" => '"', // U+00AB left-pointing double angle quotation mark
            "\xC2\xBB" => '"', // U+00BB right-pointing double angle quotation mark
            "\xE2\x80\x98" => "'", // U+2018 left single quotation mark
            "\xE2\x80\x99" => "'", // U+2019 right single quotation mark
            "\xE2\x80\x9A" => "'", // U+201A single low-9 quotation mark
            "\xE2\x80\x9B" => "'", // U+201B single high-reversed-9 quotation mark
            "\xE2\x80\x9C" => '"', // U+201C left double quotation mark
            "\xE2\x80\x9D" => '"', // U+201D right double quotation mark
            "\xE2\x80\x9E" => '"', // U+201E double low-9 quotation mark
            "\xE2\x80\x9F" => '"', // U+201F double high-reversed-9 quotation mark
            "\xE2\x80\xB9" => "'", // U+2039 single left-pointing angle quotation mark
            "\xE2\x80\xBA" => "'", // U+203A single right-pointing angle quotation mark
        );
        $chr = array_keys($chr_map); // but: for efficiency you should
        $rpl = array_values($chr_map); // pre-calculate these two arrays
        $value = str_replace($chr, $rpl, html_entity_decode($value, ENT_QUOTES, "UTF-8"));

        $text = preg_replace(array(
            // Remove invisible content
            '@<head[^>]*?>.*?</head>@siu',
            '@<script[^>]*?.*?</script>@siu',
            '@<object[^>]*?.*?</object>@siu',
            '@<embed[^>]*?.*?</embed>@siu',
            '@<applet[^>]*?.*?</applet>@siu',
            '@<noframes[^>]*?.*?</noframes>@siu',
            '@<noscript[^>]*?.*?</noscript>@siu',
            '@<noembed[^>]*?.*?</noembed>@siu'
                ), array('', '', '', '', '', '', '', ''), $value);
        $value = strip_tags($text);

        return trim($value);
    }

    /**
     * Function to clean quotes from a value
     */
    public static function cleanQuotes($value) {
        // Replace special curved quotes with normal quotes.    
        $chr_map = array(
            // Windows codepage 1252
            "\xC2\x82" => "'", // U+0082⇒U+201A single low-9 quotation mark
            "\xC2\x84" => '"', // U+0084⇒U+201E double low-9 quotation mark
            "\xC2\x8B" => "'", // U+008B⇒U+2039 single left-pointing angle quotation mark
            "\xC2\x91" => "'", // U+0091⇒U+2018 left single quotation mark
            "\xC2\x92" => "'", // U+0092⇒U+2019 right single quotation mark
            "\xC2\x93" => '"', // U+0093⇒U+201C left double quotation mark
            "\xC2\x94" => '"', // U+0094⇒U+201D right double quotation mark
            "\xC2\x9B" => "'", // U+009B⇒U+203A single right-pointing angle quotation mark
            // Regular Unicode     // U+0022 quotation mark (")
            // U+0027 apostrophe     (')
            "\xC2\xAB" => '"', // U+00AB left-pointing double angle quotation mark
            "\xC2\xBB" => '"', // U+00BB right-pointing double angle quotation mark
            "\xE2\x80\x98" => "'", // U+2018 left single quotation mark
            "\xE2\x80\x99" => "'", // U+2019 right single quotation mark
            "\xE2\x80\x9A" => "'", // U+201A single low-9 quotation mark
            "\xE2\x80\x9B" => "'", // U+201B single high-reversed-9 quotation mark
            "\xE2\x80\x9C" => '"', // U+201C left double quotation mark
            "\xE2\x80\x9D" => '"', // U+201D right double quotation mark
            "\xE2\x80\x9E" => '"', // U+201E double low-9 quotation mark
            "\xE2\x80\x9F" => '"', // U+201F double high-reversed-9 quotation mark
            "\xE2\x80\xB9" => "'", // U+2039 single left-pointing angle quotation mark
            "\xE2\x80\xBA" => "'", // U+203A single right-pointing angle quotation mark
        );
        $chr = array_keys($chr_map); // but: for efficiency you should
        $rpl = array_values($chr_map); // pre-calculate these two arrays
        $value = str_replace($chr, $rpl, html_entity_decode($value, ENT_QUOTES, "UTF-8"));
        return $value;
    }

    /**
     * Function to create image for content in text editor
     */
    public static function createImageForTextEditorContent($content) {
        if (!is_dir("uploads/page_content_images"))
            mkdir("uploads/page_content_images", 0777);

        $content = HelperController::cleanQuotes($content);

        preg_match_all('/(?<=src=")[^"]+(?=")/', $content, $srcs, PREG_PATTERN_ORDER);
        $i = 0;

        foreach ($srcs[0] as $src) {
            // for image uploaded
            if (strpos($src, "data:image") !== false) {
                $original_src = $src;
                $array = explode(",", $src);
                $first = array_shift($array);
                $first = str_replace("data:image/", "", $first);
                $ext = str_replace(";base64", "", $first);
                $src = implode(",", $array);
                $img = "uploads/page_content_images/" . IMAGE_STARTS_WITH . time() . "{$i}.{$ext}";
                file_put_contents($img, base64_decode($src));
                $content = str_replace($original_src, asset($img), $content);
            }
//            // for image from url
//            else if (strpos($src, $_SERVER['HTTP_HOST']) == false) {
//                $image = file_get_contents($src);
//                $ext = pathinfo($src, PATHINFO_EXTENSION);
//                $img = "uploads/page_content_images/" . IMAGE_STARTS_WITH . time() . "{$i}.{$ext}";
//                file_put_contents($img, $image);
//                $content = str_replace($src, asset($img), $content);
//            }
            $i++;
        }

        return $content;
    }

    /**
     * Function to get all categories in hierarchial order
     */
    public static function getCategory() {
        $main_categories = Category::where("parent", 0)->where("is_active", 1)->get();
        $category_set = array();
        foreach ($main_categories as $main_category) {
            $category_set[] = array("id" => $main_category->id, "name" => $main_category->name,
                "parent" => $main_category->parent);
            $category_set_sub = HelperController::getSubCategory($main_category->id, " ");
            $category_set = array_merge($category_set, $category_set_sub);
        }

        return $category_set;
    }
    
    /**
     * Supporting function for getCategory()
     */
    public static function getSubCategory($id, $name) {
        $name = $name . "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
        $main_categories = Category::where("parent", $id)->where("is_active", 1)->get();
        $category_set = array();
        if (!is_null($main_categories)) {
            foreach ($main_categories as $main_category) {
                $main_category->name = $name . " " . $main_category->name;
                $category_set[] = array("id" => $main_category->id, "name" => $main_category->name,
                    "parent" => $main_category->parent);
                $category_set_sub = HelperController::getSubCategory($main_category->id, $name);
                $category_set = $category_set;//array_merge($category_set, $category_set_sub);
            }
        }

        return $category_set;
    }
    
    public static function getMainCategoryList() {
        $main_categories = Category::where("is_active", 1)->get();  
        return $main_categories;
    }
    public static function getCategoryEduList($catogory,$file_type) {
        
        if($file_type=="article"){
            $edu_archive_list = AllFile::
                where("file_type", $file_type)
                ->where("is_active", 1)
                ->where("category", $catogory)
                ->get();
        }else{
            $edu_archive_list = AllFile::join(
                        "uploaded_files", "all_files.id", "=", "uploaded_files.file_id"
                )
                ->join("categories","all_files.category","=","categories.id")
                ->where("all_files.file_type", $file_type)
                ->where("uploaded_files.file_extension", $file_type)
                ->where("all_files.is_active", 1)
                ->where("all_files.category", $catogory)
                ->select("uploaded_files.id as id","all_files.title","all_files.id as main_id")
                ->get();
        }
        
        return $edu_archive_list;
    }
    
    /**
     * Function to get all categories in hierarchial order Forum
     */
    public static function getCategoryForum() {
        $main_categories = ForumCategory::where("category_id", 0)->where("is_active", 1)->get();
        $category_set = array();
        foreach ($main_categories as $main_category) {
            $category_set[] = array("id" => $main_category->id, "title" => $main_category->title,
                "category_id" => $main_category->category_id);
            $category_set_sub = HelperController::getSubCategoryForum($main_category->id, " ");
            $category_set = array_merge($category_set, $category_set_sub);
        }

        return $category_set;
    }

    /**
     * Supporting function for getCategory() Forum
     */
    public static function getSubCategoryForum($id, $name) {
        $name = $name . "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
        $main_categories = ForumCategory::where("category_id", $id)->where("is_active", 1)->get();
        $category_set = array();
        if (!is_null($main_categories)) {
            foreach ($main_categories as $main_category) {
                $main_category->title = $name . " " . $main_category->title;
                $category_set[] = array("id" => $main_category->id, "title" => $main_category->title,
                    "category_id" => $main_category->category_id);
                $category_set_sub = HelperController::getSubCategoryForum($main_category->id, $name);
                $category_set = array_merge($category_set, $category_set_sub);
            }
        }

        return $category_set;
    }

    /**
     * Function to get all child categories of a category
     */
    public static function getCategoryChild($id) {
        $main_categories = Category::where("parent", $id)->where("is_active", 1)->get();
        $category_set = array();
        foreach ($main_categories as $main_category) {
            $category_set_sub = HelperController::getSubCategoryChild($main_category->id);
            $category_set = array_merge($category_set, $category_set_sub);
        }

        return $category_set;
    }
    

    /**
     * Supporting function for getCategoryChild()
     */
    public static function getSubCategoryChild($id) {
        $main_categories = Category::where("parent", $id)->where("is_active", 1)->get();
        $category_set = array();
        foreach ($main_categories as $main_category) {
            $category_set[] = $main_category->id;
            $category_set_sub = HelperController::getSubCategoryChild($main_category->id);
            $category_set = array_merge($category_set, $category_set_sub);
        }

        return $category_set;
    }
    /**
     * Function to get all child categories of a category For Forum
     */
    public static function getCategoryChildForum($id) {
        $main_categories = ForumCategory::where("category_id", $id)->where("is_active", 1)->get();
        $category_set = array();
        foreach ($main_categories as $main_category) {
            $category_set_sub = HelperController::getSubCategoryChildForum($main_category->id);
            $category_set = array_merge($category_set, $category_set_sub);
        }

        return $category_set;
    }
    /**
     * Supporting function for getCategoryChild()For Forum
     */
    public static function getSubCategoryChildForum($id) {
        $main_categories = ForumCategory::where("category_id", $id)->where("is_active", 1)->get();
        $category_set = array();
        foreach ($main_categories as $main_category) {
            $category_set[] = $main_category->id;
            $category_set_sub = HelperController::getCategoryChildForum($main_category->id);
            $category_set = array_merge($category_set, $category_set_sub);
        }

        return $category_set;
    }

    /**
     * Function to get specific categories sub-category in hierarchial order
     */
    public static function getCategorySpecific($id) {
        $main_categories = Category::where("id", $id)->get();
        $category_set = array();
        foreach ($main_categories as $main_category) {
            $category_set[] = array("id" => $main_category->id, "name" => $main_category->name,
                "parent" => $main_category->parent);
            $category_set_sub = HelperController::getSubCategory($main_category->id, " ");
            $category_set = array_merge($category_set, $category_set_sub);
        }

        return $category_set;
    }
    public static function cardType($number)
    {
        $number=preg_replace('/[^\d]/','',$number);

        // VISA
        if (preg_match('/^4[0-9]{12}(?:[0-9]{3})?$/',$number))
        {
            return 1;
        }
        // MasterCard
        elseif (preg_match('/^5[1-5][0-9]{14}$/',$number))
        {
            return 2;
        }
        // American Express
        elseif (preg_match('/^3[47][0-9]{13}$/',$number))
        {
            return 3;
        }
        // Discover
        elseif (preg_match('/^6(?:011|5[0-9][0-9])[0-9]{12}$/',$number))
        {
            return 4;
        }
        // Diners Club
        elseif (preg_match('/^3(?:0[0-5]|[68][0-9])[0-9]{11}$/',$number))
        {
            return 5;
        }
        // JCB
        elseif (preg_match('/^(?:2131|1800|35\d{3})\d{11}$/',$number))
        {
            return 6;
        }
        else
        {
            return 0;
        }
    }
    
    
    public static function employeecardType($number)
    {
        $number=preg_replace('/[^\d]/','',$number);

        // VISA
        if (preg_match('/^4[0-9]{12}(?:[0-9]{3})?$/',$number))
        {
            return "VISA";
        }
        // MasterCard
        elseif (preg_match('/^5[1-5][0-9]{14}$/',$number))
        {
            return "MasterCard";
        }
        // American Express
        elseif (preg_match('/^3[47][0-9]{13}$/',$number))
        {
            return "AmericanExpress";
        }
        // Discover
        elseif (preg_match('/^6(?:011|5[0-9][0-9])[0-9]{12}$/',$number))
        {
            return "Discover";
        }
        // Diners Club
        elseif (preg_match('/^3(?:0[0-5]|[68][0-9])[0-9]{11}$/',$number))
        {
            return "DinersClub";
        }
        // JCB
        elseif (preg_match('/^(?:2131|1800|35\d{3})\d{11}$/',$number))
        {
            return "JCB";
        }
        else
        {
            return "InvalidCardnumber";
        }
    }
    public static function pdfVersion($filename)
    {
        $fp = @fopen($filename, 'rb');
 
        if (!$fp) {
            return 0;
        }

        /* Reset file pointer to the start */
        fseek($fp, 0);

        /* Read 20 bytes from the start of the PDF */
        preg_match('/\d\.\d/',fread($fp,20),$match);

        fclose($fp);

        if (isset($match[0])) {
            return $match[0];
        } else {
            return 0;
        }
    }
    
    public static function shopSettings() {
       $shop_settings = [];
       $credentials = Credentials::find(1);
       $shop_settings["auth_url"] = $credentials->auth_sandbox_url;
       $shop_settings["auth_login_id"] = $credentials->auth_sandbox_login_id;
       $shop_settings["auth_transaction_id"] = $credentials->auth_sandbox_transaction_id;
       if ($credentials->auth_is_production == 1) {
           $shop_settings["auth_url"] = $credentials->auth_production_url;
           $shop_settings["auth_login_id"] = $credentials->auth_production_login_id;
           $shop_settings["auth_transaction_id"] = $credentials->auth_production_transaction_id;
       }
       $shop_settings["is_production"] = $credentials->auth_is_production;

       return $shop_settings;
   }

}
