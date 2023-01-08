<?php


Route::get('/garage/install', 'Modules\Garage\Http\Controllers\InstallController@install');

Route::get('/repair-status', 'Modules\Garage\Http\Controllers\CustomerGarageStatusController@index')->name('repair-status');
Route::post('/post-repair-status', 'Modules\Garage\Http\Controllers\CustomerGarageStatusController@postRepairStatus')->name('post-repair-status');
Route::group(['middleware' => ['web', 'authh', 'auth', 'SetSessionData', 'language', 'timezone', 'AdminSidebarMenu'], 'prefix' => 'garage', 'namespace' => 'Modules\Garage\Http\Controllers'], function () {
    Route::get('edit-repair/{id}/status', 'GarageController@editRepairStatus');
    Route::post('update-repair-status', 'GarageController@updateRepairStatus');
    Route::get('delete-media/{id}', 'GarageController@deleteMedia');
    Route::get('print-label/{id}', 'GarageController@printLabel');
    Route::get('print-repair/{transaction_id}/customer-copy', 'GarageController@printCustomerCopy')->name('repair.customerCopy');
    Route::resource('/repair', 'GarageController')->except(['create', 'edit']);
    Route::resource('/status', 'GarageStatusController', ['except' => ['show']]);
    
    Route::resource('/company', 'GarageCompanyController', ['except' => ['show']]);
    Route::resource('/car-brands', 'CarBrandController', ['except' => ['show']]);
    
    Route::resource('/repair-settings', 'GarageSettingsController', ['only' => ['index', 'store']]);

    Route::get('/locations', 'GarageSettingsController@locations');
    
    
    Route::get('/addimage/{id}', 'GarageSettingsController@addimage');
    Route::post('/saveimage', 'GarageSettingsController@saveimage');
    
    
//    Route::get('/install', 'InstallController@index');
    Route::post('/install', 'InstallController@install');
    Route::get('/install/uninstall', 'InstallController@uninstall');
    Route::get('/install/update', 'InstallController@update');

    Route::get('get-device-models', 'DeviceModelController@getDeviceModels');
    

    
    Route::get('models-repair-checklist', 'DeviceModelController@getRepairChecklists');
    Route::resource('device-models', 'DeviceModelController')->except(['show']);
    Route::resource('dashboard', 'DashboardController');

    Route::get('home/get-totals', 'DashboardController@getTotals');
    
    Route::get('home/status/{id?}', 'DashboardController@getStatusTotal');
    
    
    Route::get('home/status-total/{status}/{id?}', 'DashboardController@getSTotal');
    Route::get('home/getallTotal/{status?}/{id?}', 'DashboardController@getallTotal');
    
    
    Route::get('home/SendEmail/{id}', 'JobSheetController@SendEmail');
    
    
  Route::get('home/get-company-totals/{id?}', 'DashboardController@getCompanyTotals');
    

    Route::post('customer/data', 'JobSheetController@getCustomer');
    Route::post('contact/store', 'ContactController@store');
    
    Route::post('job-sheet-post-upload-docs', 'JobSheetController@postUploadDocs');
    Route::get('job-sheet/{id}/upload-docs', 'JobSheetController@getUploadDocs');
    Route::get('job-sheet/print/{id}', 'JobSheetController@print');
    
    Route::get('job-sheet/printtest/{id}', 'JobSheetController@printtest');
    
    Route::get('estimation/print/{id}', 'JobSheetController@print_estimation');
    Route::get('job-sheet/delete/{id}/image', 'JobSheetController@deleteJobSheetImage');
    
    Route::get('job-sheet/{id}/status', 'JobSheetController@editStatus');
    Route::put('job-sheet-update/{id}/status', 'JobSheetController@updateStatus');
    
     Route::get('job-sheet/{id}/repair-status', 'JobSheetController@editRepairStatuses');
    Route::put('job-sheet-update/{id}/repair-status', 'JobSheetController@updateRepairStatuses');
    
     Route::get('job-sheet/{id}/car-status', 'JobSheetController@editCarStatus');
    Route::put('job-sheet-update/{id}/car-status', 'JobSheetController@updateCarStatus');
    
    Route::get('job-sheet/add-parts/{id}', 'JobSheetController@addParts');

    Route::post('job-sheet/save-parts/{id}', 'JobSheetController@saveParts');   
    
    Route::get('job-sheet/addphoto/{id}', 'JobSheetController@addphoto');

    Route::post('job-sheet/addphoto/', 'JobSheetController@savephoto'); 
    
    Route::get('job-sheet/car-work/{id}', 'JobSheetController@car_work'); 
    Route::get('job-sheet/cash/{id}', 'JobSheetController@cash'); 
    Route::get('job-sheet/insurance/{id}', 'JobSheetController@insurance'); 
    
 
    
    Route::post('job-sheet/get-part-row', 'JobSheetController@jobsheetPartRow');  
    Route::get('job-sheet/add-estimation/{id}', 'JobSheetController@create_estimation');
    Route::resource('job-sheet', 'JobSheetController');    
    
       Route::get('job-sheet/lpo/{id}', 'LpoController@addlpo');
       Route::get('job-sheet/addcash/{id}', 'LpoController@addcash');
     

    Route::post('job-sheet/lpo/{id}', 'LpoController@savelpo');
    Route::post('job-sheet/savecash/{id}', 'LpoController@savecash');
    
    
    Route::get('invoices/', 'InvoiceController@index');
    Route::get('insurance/', 'InvoiceController@insurance');
});
