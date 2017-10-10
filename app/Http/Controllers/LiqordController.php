<?php

namespace App\Http\Controllers;

use Auth;
use Input;
use Session;
use DB;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;
use Hash;
use Crypt;
use Excel;
/** Models */
use App\Items;

class LiqordController extends Controller {

    public function importExport() {
        if (!HelperController::checkPrivilege()) {
            return Redirect::to('admin');
        }

        $page_title = "Import LARA File";
        $breadcrumb = "<li class='active'>Import Excel</li>";
        $active = "import";
        $active_sub = "";

        return view("admin.importExport")->with("page_title", $page_title)
                        ->with("breadcrumb", $breadcrumb)
                        ->with("active", $active)
                        ->with("active_sub", $active_sub);
    }

    public function downloadExcel($type) {
        $data = Item::get()->toArray();
        return Excel::create('itsolutionstuff_example', function($excel) use ($data) {
                    $excel->sheet('mySheet', function($sheet) use ($data) {
                        $sheet->fromArray($data);
                    });
                })->download($type);
    }

     public function importExcel() {
        if (!HelperController::checkPrivilege()) {
            return Redirect::to('admin');
        }
//        ini_set('memory_limit','900M');
         if (Input::hasFile('import_file')) {
          $path = Input::file('import_file')->getRealPath();

          $data = Excel::load($path, function($reader) {

          })->get();
          $title = $data[0]->keys();
          if($title[0] == 'mi')
          {
               DB::table('items')->update(['is_active' => 0]);
          }

          if (!empty($data) && $data->count()) {
          $category = '';
          $date = date('Y-m-d');
          //                print_r($data);die;
         
          foreach ($data as $key => $value) {
          if (isset($value->mi)) {
          if (($value->mi == "") && ($value->brand_name == "") && ($value->proof == "" )) {

          } else {
          if ((strpos($value->mi,'(CONTINUED)')) !== false) {
          continue;
          }
          else  if (($value->brand_name == "") && ($value->proof == "")) {
          $category = $value->mi;
          }
          else {

          if (!$value->new) {
          $value->new = '';
          }

          //   Items::update(['is_active' => 0]);
          $item = new Items;
          $item->category = $category;
          $item->ada_no = $value->ada;
          $item->code = $value->liq;
          $item->brand_name = $value->brand_name;
          $item->proof = $value->proof;
          $item->size = $value->size;
          $item->pack_size = $value->pack;
          $item->base_price = $value->base;
          $item->license_price = $value->licensee;
          $item->minimum_shelf_price = $value->minimum;
          $item->new_chng = $value->new;
          $result = $item->save();
          }
          }
          }
          else {
          if (isset($value->ada)) {
                        if (($value->ada == 'NO.') || ($value->ada == "") || ( strpos($value->ada, 'NEW ITEMS:') !== false )) {
                            //    echo $value->ada;echo "\n";
                        } else if (strpos($value->ada, 'DELETED ITEMS:') !== false) {
                            break;
                        } else {
                            if (($value->code == "") || ($value->brand_name == "")) {
                                $category = $value->ada;
                            } else {
                                $result[] = ['category' => $category, 'ada_no' => $value->ada, 'code' => $value->code, 'brand_name' => $value->brand_name, 'proof' => $value->proof, 'size' => $value->size, 'pack_size' => $value->pack, 'base_price' => $value->base, 'license_price' => $value->licensee, 'minimum_shelf_price' => $value->minimum, 'created_at' => $date];
                            }
                        }
                    } 
              }
          }
          if($title[0] != 'mi')
          {
           if (!empty($result)) {
                    DB::table('items')->insert($result);
                    Session::flash("success", "Insert Record successfully");
                    return Redirect::back();
                }
          }
          }
          } 
      /**  if (Input::hasFile('sku_file')) {
            $path = Input::file('sku_file')->getRealPath();
            $title = Excel::load($path, function($reader) {
                        
                    })->first()->keys()->toArray();
             if (!empty($title)) {
            $item = Items::where("code", $title[0])->where('is_active', 1)->first();
            if ($item) {
                $item->upc = $title[5];
                $item->save();
            }
              }
            $data = Excel::load($path, function($reader) {
                        
                    })->get();
            if (!empty($data) && $data->count()) {
                foreach ($data as $key => $value) {
                    $items = Items::where("code", $value->$title[0])->where('is_active', 1)->first();
                    if ($items) {
                        $items->upc = $value->$title[5];
                       $result =  $items->save();
                    }
                }
            }
        } */
        if ($result) {
          Session::flash("success", "Insert Record successfully");
          return Redirect::back();
          }
        return back();
    }

     public function upcExport() {
        if (!HelperController::checkPrivilege()) {
            return Redirect::to('admin');
        }

        $page_title = "Import UPC File";
        $breadcrumb = "<li class='active'>Import UPC File</li>";
        $active = "upc";
        $active_sub = "";

        return view("admin.upcExport")->with("page_title", $page_title)
                        ->with("breadcrumb", $breadcrumb)
                        ->with("active", $active)
                        ->with("active_sub", $active_sub);
    }

    public function upcExcel() {
        if (!HelperController::checkPrivilege()) {
            return Redirect::to('admin');
        }

        if (Input::hasFile('sku_file')) {
            $path = Input::file('sku_file')->getRealPath();
            $title = Excel::load($path, function($reader) {
                        
                    })->first()->keys()->toArray();
                    
              
                    
             if (!empty($title)) {
            $item = Items::where("code", $title[0])->where('is_active', 1)->first();
            if ($item) {
                $item->upc = $title[5];
                $item->save();
            }
              }
            $data = Excel::load($path, function($reader) {
                        
                    })->get();
            if (!empty($data) && $data->count()) {
                foreach ($data as $key => $value) {
                    $items = Items::where("code", $value->$title[0])->where('is_active', 1)->first();
                    if ($items) {
                        $items->upc = $value->$title[5];
                       $result =  $items->save();
                    }
                }
            }
        }
        if ($result) {
          Session::flash("success", "Insert Record successfully");
          return Redirect::back();
          }
        return back();
    }
    
    public function importExcelold() {
        if (!HelperController::checkPrivilege()) {
            return Redirect::to('admin');
        }

        if (Input::hasFile('import_file')) {
            $path = Input::file('import_file')->getRealPath();
            $data = Excel::load($path, function($reader) {
                        
                    })->get();
            if (!empty($data) && $data->count()) {
                $category = '';
                $date = date('Y-m-d');
//                print_r($data);die;
                foreach ($data as $key => $value) {
                    if (isset($value->ada)) {
                        if (($value->ada == 'NO.') || ($value->ada == "") || ( strpos($value->ada, 'NEW ITEMS:') !== false )) {
                            //    echo $value->ada;echo "\n";
                        } else if (strpos($value->ada, 'DELETED ITEMS:') !== false) {
                            break;
                        } else {
                            if (($value->code == "") || ($value->brand_name == "")) {
                                $category = $value->ada;
                            } else {
                                $insert[] = ['category' => $category, 'ada_no' => $value->ada, 'code' => $value->code, 'brand_name' => $value->brand_name, 'proof' => $value->proof, 'size' => $value->size, 'pack_size' => $value->pack, 'base_price' => $value->base, 'license_price' => $value->licensee, 'minimum_shelf_price' => $value->minimum, 'created_at' => $date];
                            }
                        }
                    } else {
                        return Redirect::back();
                    }
                }
                if (!empty($insert)) {
                    DB::table('items')->insert($insert);
                    Session::flash("success", "Insert Record successfully");
                    return Redirect::back();
                }
            }
        }
        return back();
    }

}
