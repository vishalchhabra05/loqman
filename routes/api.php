<?php
//header("Access-Control-Allow-Origin:*");
header("Access-Control-Allow-Headers: Authorization, Content-Type:multipart/form-data");


use Illuminate\Http\Request;
/*
header ("Access-Control-Allow-Origin: *");
header ("Access-Control-Expose-Headers: Content-Length, X-JSON");
header ("Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS");
header ("Access-Control-Allow-Headers: *");
*/

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::group(['namespace' => 'Api'], function () {
    
    //Seekers User
    Route::Post('option-search','UsersController@option_search');
    Route::Post('search-categroy','UsersController@search_categroy');
    Route::get('seekers-get-categroy','UsersController@categroyget');
    // Route::Post('get_expert','UsersController@expert_get');
    Route::Post('get_expert_details','UsersController@details_expert');
    Route::post('user-register','UsersController@userRegister');
    Route::post("verify-opt","UsersController@verifyOtp");
    Route::Post('feedback','UsersController@share_feedback');
    Route::Post('contact-us','UsersController@contactus');
    Route::post('cms','UsersController@cmspage');


    Route::post("resend-otp","UsersController@resendOtp");
    Route::post('forgot-password', 'UsersController@forgotPassword');
    Route::post('reset-password','UsersController@resetPassword');
    Route::post('login', 'UsersController@login');
    Route::post("social-login",'UsersController@socialLogin');
    Route::post('otpVerify',"UsersController@otpVerify");

    //Guest User//
    Route::Post('guest-user','GuestuserController@guestLoing');
    Route::Post('guest-make-device','UsersController@guest_make_device_type');
    Route::Post('guest-user-call','GuestuserController@guestusercall');
    Route::Post('guest-accpet-call','GuestuserController@guestaccpetcall');
    Route::Post('guest-user-call','GuestuserController@guestusercall');
    Route::post('cancel-call-get','GuestuserController@guestcancelcall');
    Route::post('decline-call-get','GuestuserController@guestdeclinecall');
    Route::post('poke-notification-get','GuestuserController@guestuserPoke');
    Route::post('guest-rating','GuestuserController@userrating');
    Route::get('bages','GuestuserController@bages');
    Route::post('guest_notification','GuestuserController@guset_notification_list');
    Route::Post('guest-notification-status','GuestuserController@notification_status');
    Route::post('guest-notification-update','GuestuserController@notification_update');
    
    //Expert User
    Route::post('expert-register','ExpertsController@expertRegister');
    Route::post('expert-verify-otp','ExpertsController@otp_verify');
    Route::get('expert_get_categroy','ExpertsController@getcategory');
    Route::Post('expert_select_categroy','ExpertsController@select_categroy');
    Route::post('expert_categroy','ExpertsController@addcategroy');
    Route::Post('expert_other_info','ExpertsController@user_other_info');
    Route::post('expert-login','ExpertsController@login');
    Route::post('expert-resend_otp','ExpertsController@resend_otp');

    Route::post('expert_otp_verify','ExpertsController@singotp_verify');

    Route::post('last-activity-update','ExpertsController@last_activity_update');

    //Cron job //
    Route::get('last-activity','ExpertsController@last_activity');
});

Route::group(['middleware' => ['assign.guard:expert','jwt.auth'], 'namespace' => 'Api'], function () {
    Route::Post('make-mobile-type','ExpertsController@make_mobile_type');
    Route::post('expert-notification-call','ExpertsController@push_notification_call');
    Route::post('profile-get','ExpertsController@profileget');
    Route::Post('profile-edit','ExpertsController@profile_edit');
    Route::Post('expert_online','ExpertsController@expert_online');
    Route::post('expert-logout', 'ExpertsController@expert_logout');
    Route::Post('expert-accpet-call','ExpertsController@accpet_call');
    Route::Post('expert-cancel-call','ExpertsController@cancel_call');
    Route::Post('expert-decline-call','ExpertsController@decline_call');
    Route::Post('expert-callback-list','ExpertsController@callbacklist');
    Route::Post('expert-remove-list','ExpertsController@callbackremovelist');
    Route::Post('expert-consultion-list','ExpertsController@consultion_list');
    Route::get('notification','ExpertsController@notification_list');
    Route::Post('notification-status','ExpertsController@notification_status');
    Route::Post('notification-seen','ExpertsController@notification_seen');

    Route::get('/expert-delete','ExpertsController@expert_delete');
    // Route::post('get-profile', 'UsersController@getProfile');
    // Route::post('edit-profile', 'UsersController@editProfile');
    // Route::post('edit-password','UsersController@editPassword');
    // Route::post("userReview",'UsersController@userReview');
    
});

Route::group(['middleware' => ['assign.guard:seeker','jwt.auth'], 'namespace' => 'Api'], function () {
    Route::post('seeker-profile-get','UsersController@seeker_profile');
    Route::Post('seeker-edit-profile','UsersController@seeker_edit_profile');
    Route::Post('make-device-type','UsersController@make_device_type');
    Route::post('push-notification-call','UsersController@push_notification_call');
    Route::Post('accpet-call','UsersController@accpet_call');
    Route::Post('cancel-call','UsersController@cancel_call');
    Route::Post('decline-call','UsersController@decline_call');
    Route::post('seeker-logout', 'UsersController@logout');
    Route::post('seeker-poke','UsersController@seeker_poke');
    Route::post('expert-rating','UsersController@expertrating');
    Route::get('seeker_notification','UsersController@seeker_notification');
    Route::Post('seeker-notification-seen','UsersController@notification_seen_seeker');
    Route::Post('seeker-status','UsersController@seeker_notification_status');

    Route::get('/seeker-delete','UsersController@seeker_delete');
});





