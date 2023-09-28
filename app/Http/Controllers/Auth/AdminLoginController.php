<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use App\Model\User;
use Auth;
use Session;
use Illuminate\Support\Facades\Validator;

class AdminLoginController extends Controller {
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/admin/login';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('guest')->except('logout');
    }
    
    public function showLoginForm() {
        if(Auth::guard('admin')->check()){
            return redirect()->route('admin.dashboard');
        }
        return view('auth.admin_login');
    }
    
    
    
    public function login(Request $request) {
        $rules = [
            'email'   => 'required|email',
            'password' => 'required'
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            Session::flash('error', 'Please correct the errors below and try again');
            return redirect()->back()->withInput()->withErrors($validator->errors());
        }
        $role_ids = \Config::get('params.role_ids');
        $userInfo = User::where("email",$request->email)->first();
        if($userInfo){
        if($userInfo->role == $role_ids['Admin']){
            if (Auth::guard('admin')->attempt(['email' => $request->email, 'password' => $request->password], $request->get('remember'))) {
                return redirect()->intended(route('admin.dashboard'));
            } else {
                Session::flash('error', 'Invalid username or password.');
            }
            return back()->withInput($request->only('email', 'remember'));
        }else{
            Session::flash('error', 'Please check your roll permission to login or contact to admin.');
            return back()->withInput($request->only('email', 'remember'));
        }
      }else{
        Session::flash('error', 'no search found any user this email');
        return back()->withInput($request->only('email', 'remember'));
      }
    }
    
    public function logout() {
        Auth::guard('admin')->logout();
        return redirect()->route('admin.login');
    }
    
    
}
