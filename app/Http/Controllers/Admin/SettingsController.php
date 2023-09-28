<?php

namespace App\Http\Controllers\Admin;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\Setting;
use DB;
use Illuminate\Support\Facades\Input;
use Session;
use Illuminate\Support\Facades\Validator;
use Config;
use Illuminate\Support\Facades\Auth;
use App\Lib\Uploader;

class SettingsController extends Controller {

    public function settings(){
        try {
            $setting_field_arr = Config::get('params.setting_field_arr');
            $settings = Setting::whereIn('slug', $setting_field_arr)->get();
            $title_page = 'Settings';
            $breadcumb = [$title_page => ''];
            return view('admin.settings.settings',compact('title_page', 'breadcumb', 'settings'));
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            Session::flash('warning', $msg);
            return redirect()->back()->withInput();
        }
    }// end function.
    
    public function settingsUpdate(Request $request){
        try {
            $data = $request->all();
            $setting_field_arr = Config::get('params.setting_field_arr');
            $settings_sess = Session::get('settings');
            foreach($setting_field_arr as $slug){
                if(isset($data[$slug])) {
                    Setting::where('slug', $slug)->update(['value'=>$data[$slug]]);
                    if(isset($settings_sess[$slug])){
                        $settings_sess[$slug] = $data[$slug];
                    }
                }
            }
            Session::put('settings', $settings_sess);
            Session::flash('success', 'Settings has been updated succesfully.');
            return redirect()->route('admin.dashboard');
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            Session::flash('warning', $msg);
            return redirect()->back()->withInput();
        }
    }

}
