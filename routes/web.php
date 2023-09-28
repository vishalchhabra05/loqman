<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::get('/',function () {
    return redirect()->route('admin.login');
});
Route::get('clear-cache',function() {
    
    $exitCode = Artisan::call('cache:clear');
    $exitCode = Artisan::call('config:cache');
    //$exitCode = Artisan::call('route:cache');
    $exitCode = Artisan::call('route:clear');
    
    return 'DONE';
});
Route::get('migrate-run',function(){
    Artisan::call("migrate");
    Artisan::call('db:seed');
    echo "migration run successful";
    die;
});

Route::get('admin/login','Auth\AdminLoginController@showLoginForm')->name('admin.login');
Route::post('admin/login','Auth\AdminLoginController@login')->name('admin.login');
Route::get('admin/logout', 'Auth\AdminLoginController@logout')->name('admin.logout');
Route::group(['prefix'=>'admin', 'namespace' => 'Admin'], function () {
    Route::get('/forgot', 'UsersController@forgot')->name('admin.forgot');
    Route::post('/forgot', 'UsersController@forgot')->name('admin.forgot');
    Route::get('/reset/{token}', 'UsersController@reset')->name('admin.reset');
    Route::post('/reset/{token}', 'UsersController@reset')->name('admin.reset');
    Route::get('/cms-page/{slug}','UsersController@cmspage');
});

Route::group(['prefix'=>'admin', 'namespace' => 'Admin', 'middleware'=> ['auth:admin']], function () {
    Route::get('/', 'UsersController@dashboard')->name('admin.dashboard');
    Route::get('users/{role}', 'UsersController@users')->name('admin.users');
    Route::get('users/create/{role}', 'UsersController@create')->name('admin.users.create');
    Route::post('users/create/{role}', 'UsersController@store')->name('admin.users.create');
    Route::get('/my-profile', 'UsersController@myProfile')->name('admin.myprofile');
    Route::post('/my-profile', 'UsersController@myProfile')->name('admin.myprofile');
    Route::get('users/view/{id}', 'UsersController@show')->name('admin.users.show');
    Route::post('users-datatable/{role}', 'UsersController@usersDataTable')->name('admin.usersDataTable');
    Route::Post('seeker-users-excel','UsersController@usersExcel')->name('admin.UsersExcel');
    Route::Post('expert-users-excel','UsersController@UsersExcelExpert')->name('admin.UsersExpertExcel');
    Route::get('users/edit/{role}/{id}', 'UsersController@edit')->name('admin.users.edit');
    Route::get('users/expert_edit/{role}/{id}','UsersController@Expert_edit')->name('admin.users.expert_edit');
    Route::patch('users/edit/{role}/{id}', 'UsersController@update')->name('admin.users.edit');
    Route::delete('users/delete/{id}', 'UsersController@destroy')->name('admin.users.destroy');
    Route::post('users/status-update', 'UsersController@statusUpdate')->name('admin.users.statusUpdate');
    Route::get('/change-password', 'UsersController@changePassword')->name('admin.changePassword');
    Route::post('/change-password', 'UsersController@changePassword')->name('admin.changePassword');
    Route::post('push-notification','UsersController@push_notification')->name('admin.push_notification');
    Route::post('select-user-active','UsersController@selectuseractive')->name('admin.useractives');

    Route::get('/settings', 'SettingsController@settings')->name('admin.settings');
    Route::post('/settings', 'SettingsController@settingsUpdate')->name('admin.settings');

   
    Route::resource('/categories', 'CategoriesController');
    Route::post('/categroy-push','CategoriesController@categroy_push')->name('admin.category_push_notification');
    Route::post('/select-category-delete','CategoriesController@delete_categroy')->name('admin.categroydelete');
    Route::post('/categories/datatables', 'CategoriesController@datatable')->name('admin.categories.datatables');

    Route::resource('/bages', 'BagesController');
    Route::post('/bages/datatables', 'BagesController@datatable')->name('admin.bages.datatables');
    Route::post('/bages/status-update', 'BagesController@updatestatus')->name('admin.bages.updatestatus');

    Route::get('/pages', 'PagesController@index')->name('admin.pages.index');
    Route::any('pages/datatable', 'PagesController@datatable')->name('admin.pages.datatable');
    Route::any('/pages/edit/{slug}', 'PagesController@edit')->name('admin.pages.edit');

    Route::resource('/tags','TagsController');
    Route::post('/tags/TagsController','TagsController@datatable')->name('admin.tags.datatable');

    Route::resource('/cms','CmsController');
    Route::any('/cms/datatable','CmsController@datatable')->name('admin.cms.datatable');
    Route::get('/cms-export','CmsController@exportindex')->name('admin.cms.exportcms');
    Route::any('/cms/exportdatatable','CmsController@exportdatatable')->name('admin.cms.exportdatatable');

    Route::resource('/notification','NotificationManagmentController');
    Route::post('/notification/datatable','NotificationManagmentController@datatable')->name('admin.notification.datatable');
    Route::post('notification/status-update','NotificationManagmentController@updatestatus')->name('admin.notification.updatestatus');

    Route::resource('/feedback','Feedbackcontroller');
    Route::any('feedback/datatable', 'Feedbackcontroller@datatable')->name('admin.feedback.datatable');

    Route::resource('/report','ReportController');
    Route::get('/report-expert','ReportController@expertindex')->name('admin.expertreport');
    Route::any('/report-expert-datatable','ReportController@expertDatatable')->name('admin.report.expertdatatable');
    Route::post('/seeker-excel-report','ReportController@seekerExcel')->name('admin.seeker_report_excel');
    Route::post('/seeker-expert-report','ReportController@expertExcel')->name('admin.seeker_expert_excel');
    Route::any('report/datatable', 'ReportController@datatable')->name('admin.report.datatable');

    Route::resource('/contact-us','ContactusController');
    Route::any('contact-us/datatable', 'ContactusController@datatable')->name('admin.contact-us.datatable');

   Route::resource('/rating','RatingController');
   Route::get('/rating-expert','RatingController@rating_expert')->name('admin.rating_expert');
   Route::any('/raing-expert-datatable','RatingController@ratingDatatable')->name('admin.raing_expert_datatable');
   Route::any('rating/datatable', 'RatingController@datatable')->name('admin.rating.datatable');
}); 
Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

?>