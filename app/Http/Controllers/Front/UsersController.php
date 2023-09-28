<?php

namespace App\Http\Controllers\Front;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\User;
use App\Model\Add;
use App\Model\FavtAdd;
use App\Model\UserReview;
use App\Model\UserPlan;
use App\Model\ContactUs;
use DB;
use Mail;
use Illuminate\Support\Facades\Input;
use Session;
use Illuminate\Support\Facades\Validator;
use Config;
use Illuminate\Support\Facades\Auth;
use App\Lib\Uploader;
use App\Model\Pages;
use Illuminate\Support\Facades\Hash;

class UsersController extends Controller {

    public function index(){
        // $data = array('name'=>"Virat Gandhi");
        // Mail::send([], $data, function($message) {
        //  $message->to('akhil.kumawat@arkasoftwares.com', 'Tutorials Point')->subject
        //     ('Laravel Basic Testing Mail');
        // //  $message->from('xyz@gmail.com','Virat Gandhi');
        // });
        // echo "Basic Email Sent. Check your inbox.";
        // die;
        $pageTitle = "Blinkers - Home";
        $datas = Add::where('status','1')->with("addImages")->latest()->get();
        return view('front.users.index',compact(['pageTitle','datas']));
    }

    public function login(Request $request){
        $userInfo = User::where("email",$request->email)->first();
            $role_ids = \Config::get('params.role_ids');
            $userInfo = User::where("email",$request->email)->first();
            if($userInfo){
                if($userInfo->role == $role_ids['Users'] || $userInfo->role == $role_ids['Seller']){
                    if (Auth::guard('web')->attempt(['email' => $request->email, 'password' => $request->password], $request->get('remember'))) {
                        Session::flash('success', "Welcome $userInfo->name");
                        return redirect()->route('index');
                    } else {
                        Session::flash('error', 'Invalid username or password.');
                    }
                    return back()->withInput($request->only('email', 'remember'));
                }else{
                    Session::flash('error', 'Please check your roll permission to login or contact to admin.');
                    return back()->withInput($request->only('email', 'remember'));
                }
            }else{
                Session::flash('error', 'No user is associate with this email address.');
                return back()->withInput($request->only('email', 'remember'));
            }
    }

    public function verifyAccount(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'token' => 'required',
            ]);
            if ($validator->fails()) {
                Session::flash('error', 'Please correct the errors below and try again');
                return redirect()->back()->withInput()->withErrors($validator->errors());
            } else {
                $user = User::where('token', $request->token)->first();
                if(empty($user)) {
                    Session::flash('error', 'No user associate with this OTP');
                    return redirect()->back()->withInput()->withErrors($validator->errors());
                } else{
                    $user->status = '1';
                    $user->token='';
                    $user->save();
                    Session::flash('success', 'Your account has been activated please login to continue');
                    return redirect()->route('index');
                }
            }
        }catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage(), 'data' => []]);
        }
    }

    public function forgotPassword(Request $request) {
        try{
            $validator = Validator::make($request->all(), [
                'email' => 'required|exists:users,email',
            ]);
            if ($validator->fails()) {
                Session::flash('error', 'Please correct the errors below and try again');
                return redirect()->back()->withInput()->withErrors($validator->errors());
            } else {
                $status_arr = Config::get('params.status');
                $user = User::where('email', $request->email)->first();
                if(empty($user)) {
                    Session::flash('error', 'No user associate with this email address');
                    return redirect()->back()->withInput()->withErrors($validator->errors());
                } else if($user->status == $status_arr['inactive']){
                    Session::flash('error', 'Your account is inactive');
                    return redirect()->back()->withInput()->withErrors($validator->errors());
                } else{
                    $token = randomToken();
                    $user->token = $token;
                    $user->save();
                    $template = \App\Model\EmailTemplate::where([['slug', 'send-mail-when-customer-forgot-password']])->first();
                    $site_name = getSettings()['site_title'];
                    $subject = $template->subject;
                    $description = $template->description;
                    $subject = str_replace(['{LINK}','{SITE}','{FULL_NAME}'],[$token, $site_name, $user->name], $subject);
                    $description = str_replace( ['{LINK}','{SITE}','{FULL_NAME}'],[$token, $site_name, $user->name],$description);
                    $mail_data = ['email' => $user->email,'subject' => $subject,'content' => $description ];
                    mailSend($mail_data);
                    Session::flash('success', 'OPT send on email please check your email.');
                    return redirect()->route('index');
                }
            }
        }catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage(), 'data' => []]);
        }
    }

    public function signup(Request $request){
        $validatorRules = [
            'name' => 'required',
            'number' => 'required|numeric|unique:users,number',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|max:20|min:8',
        ];
        try {
            $validator = Validator::make($request->all(), $validatorRules);
            if ($validator->fails()) {
                Session::flash('error', 'Please correct the errors below and try again');
                return redirect()->back()->withInput()->withErrors($validator->errors());
            } else {
                $user = new User();
                $user->name = $request->name;
                $user->email = $request->email;
                $user->number = $request->number;
                $user->password = Hash::make($request->password);
                $user->role = Config::get('params.role_ids.Users');
                $token = randomToken();
                $user->token = $token;
                $user->save();
                $template = \App\Model\EmailTemplate::where([['slug', 'send-welcome-mail-when-customer-signup']])->first();
                $site_name = getSettings()['site_title'];
                $subject = $template->subject;
                $description = $template->description;
                $subject = str_replace(['{OPT}','{SITE}'],[$token, $site_name], $subject);
                $description = str_replace( ['{OTP}','{SITE}'],[$token, $site_name],$description);
                $mail_data = ['email' => $user->email,'subject' => $subject,'content' => $description ];
                mailSend($mail_data);
                Session::flash('success', 'User has been added successfully.');
                return redirect()->route('index');
            }
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            Session::flash('warning', $msg);
            return redirect()->back()->withInput();
        }
    }

    public function myAds(){
        $pageTitle = "Blinkers - Privacy Policy";
        $myads = Add::where("user_id",Auth::user()->id)->with("addImages")->get();
        return view('front.users.myads',compact('pageTitle','myads'));
    }

    public function myFavorites(){
        $pageTitle = "Blinkers - My Favorites";
        $favtAdds = FavtAdd::where("user_id",Auth::user()->id)->with(['favtAddInfo.addImages'])->get();
        return view('front.users.favtads',compact('pageTitle','favtAdds'));
    }

    public function chat(){
        $pageTitle = "Blinkers - My chat";
        return view('front.users.chat',compact('pageTitle'));
    }

    public function settings(){
        $pageTitle = "Blinkers - Setting";
        return view('front.users.setting',compact('pageTitle'));
    }

    public function userReview(){
        $pageTitle = "Blinkers - User Review";
        $receivedReviews = UserReview::where('from_user_id',Auth::user()->id)->with("toUserInfo")->get();
        $postedReviews = UserReview::where('to_user_id',Auth::user()->id)->with("fromUserInfo")->get();
        return view('front.users.userreview',compact('pageTitle','receivedReviews','postedReviews'));
    }

    public function myProfile(){
        $pageTitle = "Blinkers - My Profile";
        $userInfo = User::where("id",Auth::user()->id)->first();
        return view('front.users.myprofile',compact('pageTitle','userInfo'));
    }

    public function myPlan(){
        $pageTitle = "Blinkers - My Plan";
        $date = today()->format('Y-m-d');
        $currentPlan = UserPlan::where('plan_expire', '>=', $date)->where("created_at","<=",$date)->with("userPlanInfo")->first();
        $plans = UserPlan::where("user_id",Auth::user()->id)->with("userPlanInfo")->get();
        return view('front.users.myplan',compact('pageTitle','plans','currentPlan'));
    }

    public function updateProfile(Request $request){
        $userId = Auth::user()->id;
        $validatorRules = [
            'name' => 'required',
            'number' => 'required|numeric|unique:users,number,'.$userId,
            'email' => 'required|email|max:255|unique:users,email,'.$userId,
            'dob' => 'required',
        ];
        try {
            $validator = Validator::make($request->all(), $validatorRules);
            if ($validator->fails()) {
                Session::flash('error', 'Please correct the errors below and try again');
                return redirect()->back()->withInput()->withErrors($validator->errors());
            } else {
                $user = User::where('id',$userId)->first();
                $user->name = $request->name;
                $user->email = $request->email;
                $user->number = $request->number;
                $user->dob = date("Y-m-d",strtotime($request->dob));
                if ($request->file('image')) {
                    $destinationPath = 'uploads/user/';
                    $response_data = Uploader::doUpload($request->file('image'), $destinationPath);
                    if ($response_data['status'] == true) {
                        $user->profile_image = $response_data['file'];
                    }
                }
                $user->save();
                Session::flash('success', 'Profile information updated successfully.');
                return redirect()->route('my-profile');
            }
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            Session::flash('warning', $msg);
            return redirect()->back()->withInput();
        }
    }

    public function updatePassword(Request $request){
        $userId = Auth::user()->id;
        $validatorRules = [
            'current_password' => 'required|max:20|min:8',
            'password' => 'required|max:20|min:8',
            'confirm_password' => 'required_with:password|same:password|max:20|min:8',
        ];
        try {
            $validator = Validator::make($request->all(), $validatorRules);
            if ($validator->fails()) {
                Session::flash('error', 'Please correct the errors below and try again');
                return redirect()->back()->withInput()->withErrors($validator->errors());
            } else {$user = User::where('id', Auth::user()->id)->first();
                if (Hash::check($request->current_password, $user->password)) {
                    $user->password = Hash::make($request->password);
                    $user->save();
                    Session::flash('success', 'Password updated successfully.');
                }else{
                    Session::flash('error', 'Current password did not match with your old password');
                }
                return redirect()->route('my-profile');
            }
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            Session::flash('warning', $msg);
            return redirect()->back()->withInput();
        }
    }

    public function logout(){
        Auth::guard('web')->logout();
        return redirect()->route('index');
    }



    public function privacyPolicy(){
        $pageTitle = "Blinkers - Privacy Policy";
        return view('front.users.privacy',compact('pageTitle'));
    }

    public function staticPage($slug){
        $pageData = Pages::where("slug",$slug)->first();
        $pageTitle = "Blinkers - $pageData->title";
        return view('front.users.pages',compact('pageTitle','pageData'));
    }

    public function contactUs(){
        $pageTitle = "Blinkers - Contact Us";
        return view('front.users.contactus',compact('pageTitle'));
    }

    public function aboutUs(){
        $pageTitle = "Blinkers - About Us";
        return view('front.users.aboutus',compact('pageTitle'));
    }

    public function contactForm(Request $request){
        $validatorRules = [
            'name' => 'required',
            'mobileNum' => 'required|numeric',
            'email' => 'required|email|max:255',
            'subject' => 'required',
            'message' => 'required',
        ];
        try {
            $validator = Validator::make($request->all(), $validatorRules);
            if ($validator->fails()) {
                Session::flash('error', 'Please correct the errors below and try again');
                return redirect()->back()->withInput()->withErrors($validator->errors());
            } else {
                $data=$request->all();
                unset($data["_token"]);
                ContactUs::create($data);
                Session::flash('success', 'Message send successfully.');
                return redirect()->route('contact-us');
            }
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            Session::flash('warning', $msg);
            return redirect()->back()->withInput();
        }
    }
 
}
