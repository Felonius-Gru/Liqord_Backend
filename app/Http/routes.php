<?php

/*
  |--------------------------------------------------------------------------
  | Application Routes
  |--------------------------------------------------------------------------
  |
  | Here is where you can register all of the routes for an application.
  | It's a breeze. Simply tell Laravel the URIs it should respond to
  | and give it the controller to call when that URI is requested.
  |
 */

/**
 * Max execution time set to infinite
 */
set_time_limit(0);
define("IMAGE_STARTS_WITH", "liqord_");
define("PRODUCT_WIDTH", "300");
define("PRODUCT_HEIGHT", "300");
define("PRODUCT_THUMB_WIDTH", "480");
define("PRODUCT_THUMB_HEIGHT", "185");
define("PROFILE_PIC_WIDTH", "300");
define("PROFILE_PIC_HEIGHT", "300");

//Root Page
Route::get('/', 'HomeController@index');
Route::group(array('prefix' => 'admin'), function() {

    /**
     * Home Controller
     */
    // Login Page
    Route::get('/', 'HomeController@index');
    // Login Post
    Route::post('login', 'HomeController@loginPost');
    Route::get('logout', 'HomeController@logout');

    // Home Page
    Route::get('home', 'HomeController@home');
    //Profile Page
    Route::get('profile', 'ProfileController@profile');
    //Profile Page Post
    Route::post('profile', 'ProfileController@profilePost');
    
    Route::get('registration', 'HomeController@registration');
    Route::post('registration', 'UserController@update');

    Route::get('importExport', 'LiqordController@importExport');
    Route::post('importExcel', 'LiqordController@importExcel');

    Route::get('upcExport', 'LiqordController@upcExport');
    Route::post('upcExcel', 'LiqordController@upcExcel');

    /**
     * User Controller
     */
    // Users Page
    Route::get('users', 'UserController@users');
    Route::get('users/result', 'UserController@usersResults');
    Route::get('users/update/{id}', 'UserController@view');
    Route::post('users/update/{id}', 'UserController@update');
    Route::get('users/refillcard/{id}', 'UserController@refillcardview');
    Route::post('users/refillcard/{id}', 'UserController@refillcard');

    // Users Logs
    Route::get('users/logs', 'UserController@logs');
    Route::get('users/logs/results', 'UserController@logResults');

    // Register Users
    Route::get('users/register', 'UserController@view');
    Route::post('users/register', 'UserController@update');

    // Enable
    Route::get('users/enable/{id}', 'UserController@enableUser');
    // Disable
    Route::get('users/disable/{id}', 'UserController@disableUser');
    // Delete
    Route::get('users/delete/{id}', 'UserController@deleteUser');
    // Forgot password
    Route::post('users/forgotPassword', 'UserController@forgotPassword');

    Route::get('items', 'ItemsController@items');
    Route::get('items/result', 'ItemsController@itemsResults');
    Route::get('items/delete/{id}', 'ItemsController@deleteItem');
    Route::get('items/edit/{id}', 'ItemsController@view');
    Route::post('items/edit/{id}', 'ItemsController@update');

    Route::get('locations', 'LocationController@locations');
    Route::get('locations/result', 'LocationController@locationResults');
    Route::post('location/add', 'LocationController@update');
    Route::post('location/edit', 'LocationController@update');
    Route::get('location/viewdetail', 'LocationController@viewdetail');
    Route::post('location/viewdetail', 'LocationController@viewdetail');
    // Enable
    Route::get('locations/enable/{id}', 'LocationController@enableLocation');
    // Disable
    Route::get('locations/disable/{id}', 'LocationController@disableLocation');

    //Location--->Stores
    Route::get('locations/store/view/{id}', 'LocationController@locationStore');

    Route::get('stores', 'StoreController@stores');
    Route::get('stores/result', 'StoreController@storeResults');

    //Add Stores
    Route::get('stores/add', 'StoreController@view');
    Route::post('stores/add', 'StoreController@update');
    //Update Stores
    Route::get('stores/update/{id}', 'StoreController@view');
    Route::post('stores/update/{id}', 'StoreController@update');

//    Route::post('stores/add', 'StoreController@update');
//    Route::post('stores/edit', 'StoreController@update');

    Route::get('stores/viewdetail', 'StoreController@viewdetail');
    Route::post('stores/viewdetail', 'StoreController@viewdetail');
    // Enable
    Route::get('stores/enable/{id}', 'StoreController@enableStore');
    // Disable
    Route::get('stores/disable/{id}', 'StoreController@disableStore');

    //Product Locations
    Route::get('storelocations', 'StoreLocController@index');
    Route::get('storelocations/result', 'StoreLocController@results');
    Route::get('storelocations/viewlocation', 'StoreLocController@viewlocation');
    Route::post('storelocations/viewlocation', 'StoreLocController@viewlocation');
    // Add View
//    Route::get('storelocations/add', 'StoreLocController@view');
//    // Add submit
    Route::post('storelocations/add', 'StoreLocController@update');
//    Route::get('storelocations/update/{id}', 'StoreLocController@view');
//    Route::post('storelocations/update/{id}', 'StoreLocController@update');
    // Enable
    Route::get('storelocations/enable/{id}', 'StoreLocController@enable');
    // Disable
    Route::get('storelocations/disable/{id}', 'StoreLocController@disable');

    Route::get('storelocations/printenable/{id}', 'StoreLocController@printenable');
    // Disable
    Route::get('storelocations/printdisable/{id}', 'StoreLocController@printdisable');
    Route::get('storelocations/inventoryenable/{id}', 'StoreLocController@inventoryenable');
    // Disable
    Route::get('storelocations/inventorydisable/{id}', 'StoreLocController@inventorydisable');
    //Liquors For Sale
    Route::get('product/{location_id}', 'ProductController@index');
    Route::get('product/result/{location_id}', 'ProductController@results');

    Route::get('products/add/{location_id}', 'ProductController@view');
    Route::post('products/add/{location_id}', 'ProductController@update');

    Route::get('products/importExport/{location_id}', 'ProductController@viewImport');
    Route::post('products/importExport/{location_id}', 'ProductController@importProduct');

    Route::get('product/update/{id}/{location_id}', 'ProductController@view');
    Route::post('product/update/{id}/{location_id}', 'ProductController@update');
    // Enable
    Route::get('product/enable/{id}', 'ProductController@enable');
    // Disable
    Route::get('product/disable/{id}', 'ProductController@disable');

    Route::get('autocomplete', 'ProductController@autocomplete');
    Route::post('autocomplete', 'ProductController@autocomplete');

    //Shelves
    Route::get('shelves/{location_id}', 'ShelfController@index');
    Route::get('shelves/result/{location_id}', 'ShelfController@results');

    Route::get('shelves/add/{location_id}', 'ShelfController@view');
    Route::post('shelves/add/{location_id}', 'ShelfController@update');

    Route::get('shelves/update/{id}/{location_id}', 'ShelfController@view');
    Route::post('shelves/update/{id}/{location_id}', 'ShelfController@update');
    // Enable
    Route::get('shelves/enable/{id}', 'ShelfController@enable');
    // Disable
    Route::get('shelves/disable/{id}', 'ShelfController@disable');

    Route::get('cutofftime', 'CutOffTimeController@cutofftime');
    Route::get('cutofftime/result', 'CutOffTimeController@cutofftimeResults');

    //Add Stores
    Route::get('cutofftime/add', 'CutOffTimeController@view');
    Route::post('cutofftime/add', 'CutOffTimeController@update');
    //Update Stores
    Route::get('cutofftime/update/{id}', 'CutOffTimeController@view');
    Route::post('cutofftime/update/{id}', 'CutOffTimeController@update');

    // Enable
    Route::get('cutofftime/enable/{id}', 'CutOffTimeController@enablecutofftime');
    // Disable
    Route::get('cutofftime/disable/{id}', 'CutOffTimeController@disablecutofftime');

    Route::get('cutofftime/viewstorelocation', 'CutOffTimeController@viewstorelocation');
    Route::post('cutofftime/viewstorelocation', 'CutOffTimeController@viewstorelocation');
    // Users Page
    Route::get('tabusers', 'TabUserController@users');
    Route::get('tabusers/result', 'TabUserController@usersResults');
    Route::get('tabusers/register', 'TabUserController@view');
    Route::post('tabusers/register', 'TabUserController@update');
    Route::get('tabusers/update/{id}', 'TabUserController@view');
    Route::post('tabusers/update/{id}', 'TabUserController@update');
    // Enable
    Route::get('tabusers/enable/{id}', 'TabUserController@enableUser');
    // Disable
    Route::get('tabusers/disable/{id}', 'TabUserController@disableUser');

    Route::get('tabusers/viewstorelocation', 'TabUserController@viewstorelocation');
    Route::post('tabusers/viewstorelocation', 'TabUserController@viewstorelocation');


    Route::get('orders', 'OrderController@index');

    //Orders
//    Route::get('orders/result', 'OrderController@results');
    Route::get('orders/result', 'OrderController@results');
    Route::get('orders/result/{search}/{storeid}/{locationid}/{startdate}/{enddate}', 'OrderController@results');
    // Order details View
    Route::get('orders/view/{id}', 'OrderController@view');
    Route::get('orders/item/update/{id}', 'OrderController@itemUpdate');
    Route::post('orders/item/update/{id}', 'OrderController@itemUpdatePost');

    Route::get('order/item/status/instock/{id}', 'OrderController@instock');
    Route:get('order/item/status/outofstock/{id}', 'OrderController@outofstock');



    Route::get('shelf/tagorders', 'ShelfTagOrderController@tagorders');
    Route::get('shelf/tagorders/result', 'ShelfTagOrderController@tagordersResults');
    Route::get('shelf/tagorders/result/{search}/{storeid}/{locationid}/{startdate}/{enddate}', 'ShelfTagOrderController@tagordersResults');

    Route::get('shelf/tagorders/delete/{id}', 'ShelfTagOrderController@deleteItem');
    Route::get('shelf/tagorders/edit/{id}', 'ShelfTagOrderController@view');
    Route::post('shelf/tagorders/edit/{id}', 'ShelfTagOrderController@update');
    Route::get('shelf/tagorders/view/{id}', 'ShelfTagOrderController@view');

    Route::get('shelf/tagorders/print/{id}', 'ShelfTagOrderController@printall');
    Route::get('shelf/tagorders/printitem/{id}', 'ShelfTagOrderController@printItem');
    Route::get('shelf/tagorders/csv/{id}', 'ShelfTagOrderController@csvDownload');
    
   Route::get('shelf/taginventory', 'InventoryTagController@tagorders');
   Route::get('shelf/taginventory/result', 'InventoryTagController@tagordersResults');
   Route::get('shelf/taginventory/result/{search}/{storeid}/{locationid}/{startdate}/{enddate}', 'InventoryTagController@tagordersResults');
   Route::get('shelf/taginventory/view/{store_id}/{location_id}', 'InventoryTagController@view');
   Route::get('shelf/taginventory/printitem/{id}', 'InventoryTagController@printItem');
   
   Route::post('store/card', 'UserController@addcard');
   Route::post('store/editcard', 'UserController@addcard');
   
   Route::get('billing', 'BillingController@index');
   Route::get('billing/result', 'BillingController@Results');
   Route::get('billinglocation/{id}', 'BillingController@viewlocation');
   Route::get('billinglocation/result/{id}', 'BillingController@resultlocation');
   Route::post('billing/add', 'BillingController@update');
   Route::post('billing/edit', 'BillingController@update');
   
   Route::get('billing_section', 'BillingController@billing');
   Route::get('billing_section/result', 'BillingController@billingResults');
   Route::get('billing_history', 'BillingController@billing_history');
   Route::get('billinghistory/{id}', 'BillingController@billing_history');
   Route::get('billing_history/result/{id}', 'BillingController@historyResults');
   
   Route::get('decline_charges', 'BillingController@decline_charges');
   Route::get('decline_charges/result', 'BillingController@declineResults');
   
   Route::get('charges_month', 'BillingController@charges_month');
   Route::get('charges_month/result', 'BillingController@monthResults');
   
   Route::get('declined_charges', 'BillingController@declined_charges');
   Route::get('declined_charges/result', 'BillingController@declinedResults');
   
   Route::get('editcard/{id}', 'BillingController@viewcard');
   Route::post('editcard/{id}', 'CronController@updatecard');
   
   Route::get('autopayment', 'CronController@paynow');
    
});
Route::group(array('prefix' => 'api'), function() {
    //fetching stores
    Route::post('stores/getall', 'ApiController@getAllStores');
    Route::post('login', 'ApiController@loginPost');
    //fetch store-locations
    Route::post('getstore/locations', 'ApiController@getstoreLocations');
    //search
    Route::post('search/product', 'ApiController@searchProducts');
    Route::post('category/product', 'ApiController@getProducts');

//   Route::post('categories','ApiController@productbycategories');
    Route::post('product/quantities', 'ApiController@productquantity');

    Route::post('get/product', 'ApiController@getProduct');
    //Products shelves
    Route::post('get/shelves', 'ApiController@getShelves');
    //get product by shelf number and bottleposition
    Route::post('product/locate', 'ApiController@locateProducts');
    Route::post('cart', 'ApiController@cartPost');
    Route::post('cartview', 'ApiController@Cartview');
    Route::post('deletecart', 'ApiController@Deletecart');
    Route::post('update/quantity', 'ApiController@updateQuantity');
    Route::post('adjust/price', 'ApiController@adjustPrice');
    Route::post('placeorder', 'ApiController@Placeorder');
    Route::post('getproduct/barcode', 'ApiController@scannedproduct');
    Route::post('orders/listing', 'ApiController@ordersListing');
    Route::post('orders/items/listing', 'ApiController@orderItems');
    Route::post('add/shelves', 'ApiController@addshelf');
    Route::post('get/newproduct', 'ApiController@getNew');
    Route::post('get/productlist', 'ApiController@getShelfProduct');
    Route::post('deleteitem', 'ApiController@DeleteItem');
    Route::post('clearitem', 'ApiController@clearItem');
    Route::post('moveitem', 'ApiController@moveItem');
    Route::post('sendlabel', 'ApiController@sendLabel');
 // Route::post('add/product', 'ApiController@addProduct');
 // Route::post('add/shelfproduct', 'ApiController@addshelfProduct');
    Route::post('add/product', 'ApiController@addshelfProduct');
    Route::post('moveposition', 'ApiController@movePosition');
//  Route::get('storebottle', 'ApiController@storebottle');
});

//Route::any('{slug}', function($url) {
//        $controller = App::make('App\Http\Controllers\HomePageController');
//        return $controller->callAction('index', array($url));
//    })->where('slug', '(.*)?');
