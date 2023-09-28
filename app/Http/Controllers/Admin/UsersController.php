<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\User;
use App\Model\Membership;
use App\Model\ContactUs;
use DB;
use Illuminate\Support\Facades\Input;
use Session;
use Illuminate\Support\Facades\Validator;
use Config;
use Illuminate\Support\Facades\Auth;
use App\Lib\Uploader;
use Illuminate\Support\Facades\Hash;
use App\Model\Category;
use App\Model\UserotherInformation;
use App\Model\UserCategroy;
use App\Model\Notificationsend;
use App\Model\Notification;
use App\Model\UserCalling;
use App\Model\Rating;
use Carbon\Carbon;
use App\Model\Cms;
use Mail;
class UsersController extends Controller {

    public function dashboard(){
        $title_page = 'Dashboard';
        $role_ids = Config::get('params.role_ids');
      
        $user_count = User::where('status','1')->where('role','2')->count();
        $expert_count = User::where('status','1')->where('role','3')->count();
        $category = Category::count();

        //last 24
        $user_count_24h = User::where('status','1')->where('role','2')->whereDate('created_at', Carbon::today())->count();
        $totalcall = UserCalling::count();
        $call24=UserCalling::whereDate('created_at', Carbon::today())->count();
        $expert_count_24h = User::where('status','1')->where('role','3')->whereDate('created_at', Carbon::today())->count();
        $TotalMinutesCall=UserCalling::where('status','1')->get();

        $workingHours=[];
        foreach($TotalMinutesCall as $value){
            $workingHours[]= (strtotime($value->end_time) - strtotime($value->start_time));
            //$workingHours[]=date('H:i:s',$data);
        }
        $AllCallmin=array_sum($workingHours);
       
        
        return view('admin.users.dashboard',compact('title_page','user_count', 'role_ids','expert_count','category','user_count_24h','totalcall','expert_count_24h','call24','AllCallmin'));
    }// end function.
    
    public function users($role) {

        $role_ids = Config::get('params.role_ids');
    
        if($role == $role_ids['Admin'] || !in_array($role, $role_ids)) {
           abort(404); 
        }
        $role_names = Config::get('params.role_names');
        $role_name = $role_names[$role];
        $title_page = $role_name;
        $breadcumb = [$title_page=>''];
        //Session::flash('success', 'User has been deleted successfully.');
        return view('admin.users.index',compact('title_page','breadcumb', 'role'));
    }

    public function usersExcel(Request $request){
        try{
            $users = User::where("role","2")->get();
            
            header('Content-type: text/csv');
            header('Content-Disposition: attachment; filename="users.csv"');

            // do not cache the file
            header('Pragma: no-cache');
            header('Expires: 0');

            // create a file pointer connected to the output stream
            $file = fopen('php://output', 'w');

           fputcsv($file, array('Name','Email','Number','Status','created_at'));

           if(!empty($users)){
            foreach($users as $user){
                $name=$user->name;
                $email=$user->email;
                $number= $user->number;
                $status=($user->status==1) ? "Active" : "Inactive";
                $created_at= date('d-m-Y', strtotime($user->created_at));
                $row = [$name,$email,$number,$status,$created_at];
                fputcsv($file, $row);
            }
        }
        exit;
        }catch(\Exception $e){

        }
    }

    public function UsersExcelExpert(Request $request){
        try{
            $users = User::where("role","3")->get();
            
            header('Content-type: text/csv');
            header('Content-Disposition: attachment; filename="users.csv"');

            // do not cache the file
            header('Pragma: no-cache');
            header('Expires: 0');

            // create a file pointer connected to the output stream
            $file = fopen('php://output', 'w');

           fputcsv($file, array('Name','Email','Number','Status','created_at'));

           if(!empty($users)){
            foreach($users as $user){
                $name= $user->name;
                $email= $user->email;
                $number= $user->number;
                $status=($user->status==1) ? "Active" : "Inactive";
                $created_at= date('d-m-Y', strtotime($user->created_at));
                $row = [$name,$email,$number,$status,$created_at];
                fputcsv($file, $row);
            }
        }
        exit;
        }catch(\Exception $e){

        }
    }

    public function contactUs(){
        $title_page = 'Contact Us Enquiry';
        $breadcumb = [$title_page=>''];
        return view('admin.users.contactus',compact('title_page','breadcumb'));
    }

    public function contactusDataTable(Request $request) {
        $columns = ['id', 'name', 'created_at', 'email','zipcode','mobileNum'];
        $totalData = ContactUs::count();
        $limit = $request->input('length');
        $start = $request->input('start');
        $dir = $request->input('order.0.dir');
        $order = $columns[$request->input('order.0.column')];
        $results = ContactUs::select('contact_us.*');
        if (!empty($request->input('search.value'))) {
            $search = $request->input('search.value');
            $results = $results->where(function($query) use ($search) {
                $query->where('name', 'LIKE', "%{$search}%")->orWhere('email', 'LIKE', "%{$search}%");
            });
        }
        $totalFiltered = $results->count();
        $results = $results->offset($start)->limit($limit)->orderBy($order, $dir)->get();
        $data = array();
        if(!empty($results)) {
            $sno=0;
            foreach ($results as $row) {
                $sno++;
                $nestedData['id'] = $sno;
                $nestedData['name'] = $row->name;
                $nestedData['email'] = $row->email;
                $nestedData['zipcode'] = $row->zipcode;
                $nestedData['mobileNum'] = $row->mobileNum;
                $nestedData['created_at'] = listDateFromat($row->created_at);
                $data[] = $nestedData;
            }
        }
        $json_data = array(
            "draw" => intval($request->input('draw')),
            "recordsTotal" => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data" => $data
        );
        echo json_encode($json_data);
    }
    
    public function usersDataTable(Request $request, $role) {
        $columns = ['id','name', 'email', 'profile_image', 'number', 'status', 'created_at','action'];
        $role_ids = Config::get('params.role_ids');
        $totalData = User::where('role', $role)->count();
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');
        $users = User::where('role',  $role);
        if(!empty($request->input('search.value'))) {
            $search = $request->input('search.value');
            $users = $users->where(function($query) use ($search) {
                $query->where('name', 'LIKE', "%{$search}%")->orWhere('email', 'LIKE', "%{$search}%")->orWhere('number', 'LIKE', "%{$search}%");
            });
        }
        $totalFiltered = $users->count();
        $users = $users->offset($start)->limit($limit)->orderBy($order, $dir)->get();
        $data = [];
        if (!empty($users)) {
            $sno=0;
            foreach ($users as $key => $row) {
                $sno++;
                $nestedData['id'] = $sno;
                $nestedData['user_id']=$row->id;
                $nestedData['name'] = $row->name;
                $nestedData['email'] = $row->email;
                $nestedData['number']=$row->number;
                $nestedData['profile_image'] = !empty($row->profile_image_full) ? '<img src="'.$row->profile_image_full.'" width="50"/>' : '';
                $nestedData['created_at'] = listDateFromat($row->created_at);
                  if($role != $role_ids['Guestuser']){
                        $nestedData['status'] = getStatus($row->status,$row->id);
                  }else{
                    $nestedData['status']="";
                  }
                $nestedData['created_at'] = date('Y-m-d',strtotime($row->created_at));
                $nestedData['notification'] ='<input class="checkhour" type="checkbox" name="notification[]" value='.$row->id.'>';
                $nestedData['Users'] ='<input class="SelectOver" type="checkbox" name="Users[]" value='.$row->id.'>';
                $buttons = [['key'=>'view','link'=>route('admin.users.show',$row->id)],
                    //['key'=>'edit','link'=>route('chefs.edit',$row->id)],
                ];
                if($role == $role_ids['Users']) {
                    $buttons[] = ['key'=>'edit', 'link'=>route('admin.users.edit',[$role, $row->id])];
                }elseif($role == $role_ids['Expert']){
                    $buttons[] = ['key'=>'edit', 'link'=>route('admin.users.expert_edit',[$role, $row->id])];
                }
                $buttons[] = ['key'=>'delete','link'=>route('admin.users.destroy',$row->id)]; 
                $nestedData['action'] =  getButtons($buttons);
                $data[] = $nestedData;
            }
        }
        //$totalFiltered = isset($key) ? $key + 1 : 0;
        $json_data = array(
            "draw" => intval($request->input('draw')),
            "recordsTotal" => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data" => $data
        );
        echo json_encode($json_data);
    }
    
    public function statusUpdate(Request $request) {
        $user_id = $request->id;
        $row = User::whereId($user_id)->first();
        $row->status = ($row->status == '1' ? '2' : ($row->status == '1' ? '2' : '1'));
        $row->save();
        $html = '';
        switch ($row->status) {
            case '1':
                $html = '<a data-toggle="tooltip"  class="btn btn-success btn-xs" title="Enable" onClick="changeStatus(' . $user_id . ')" >Active</a>';
                break;
            case '0':
                $html = '<a data-toggle="tooltip"  class="btn btn-danger btn-xs" title="inactive" onClick="changeStatus(' . $user_id . ')" >inactive</a>';
                break;
            default:
                break;
        }
        return $html;
    }// end function.
    
    
    public function show($id){
        try {
            $entity = User::find($id);
            if ($entity) {
                $role_ids = Config::get('params.role_ids');
                $role_names = Config::get('params.role_names');
                $role = $entity->role;
                $role_name = $role_names[$role];
                $role_title = $role_name.'s';
                $title_page = "$role_name Details";
                $breadcumb = [$role_title => route('admin.users', ['role'=>$role]), $title_page => ''];
                $CategoryCheck="";
                $Totalbages="";
                $AllCallmin="";
                if($entity->role == "3"){
                    $totalcall = UserCalling::where('send_id',$id)->whereIn('status',array('1','2'))->count();
                    $TotalMinutesCall=UserCalling::where('send_id',$id)->where('status','1')->get();

                    $workingHours=[];
                    foreach($TotalMinutesCall as $value){
                        $workingHours[] = (strtotime($value->end_time) - strtotime($value->start_time)) / 60;
                    }
                    
                    $AllCallmin=array_sum($workingHours);

                    $userrating = Rating::where('recive_id',$id)->avg('rating');

                    // $CategoryCheck=Category::join('user_categories', 'categories.id', '=', 'user_categories.categroy_id')->get();
                    $Totalbages = Rating::where('recive_id',$id)->sum('bages');
                }elseif($entity->role == "2"){
                    $totalcall = UserCalling::where('recive_id',$id)->whereIn('status',array('1','2'))->count();
                    $userrating = Rating::where('sender_id',$id)->avg('rating');
                }elseif($entity->role == "4"){
                    $totalcall = UserCalling::where('recive_id',$id)->whereIn('status',array('1','2'))->count();
                    $userrating = Rating::where('sender_id',$id)->avg('rating');
                }
                return view('admin.users.show', compact('title_page', 'entity', 'breadcumb','totalcall','userrating','Totalbages','AllCallmin'));
            } else {
                Session::flash('warning', 'Invalid request');
                return redirect()->back();
            }
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            Session::flash('warning', $msg);
            return redirect()->back()->withInput();
        }
    }
    
    
    public function destroy($id){
        try {
            
            $role_ids = Config::get('params.role_ids');
            User::where('id', $id)->delete();
            UserotherInformation::where('user_id',$id)->delete();
            UserCategroy::where('user_id',$id)->delete();
            UserCalling::where('recive_id',$id)->delete();
            Rating::where('sender_id',$id)->delete();
            Notificationsend::where('user_id',$id)->delete();
                Session::flash('success', 'User has been deleted successfully.');
                return back();
            
        } catch (\Exception $e) {
          
            $msg = $e->getMessage();
            Session::flash('warning', $msg);
            return redirect()->back();
        }
    }// end function.
    
    
    public function myProfile(Request $request){
        $user_id = Auth::id();
        try {
            $user = User::find($user_id);
            if ($user) {
                $title_page = 'My Profile';
                $breadcumb = [$title_page => ''];
                if ($request->isMethod('post')) {
                    $rules = array(
                        'name' => 'required',
                        'email' => 'required|email|max:255|unique:users,email,' . $user->id,
                        'profile_image' => 'nullable|mimes:jpeg,jpg,png',
                    );
                    $validator = Validator::make($request->all(), $rules);
                    if ($validator->fails()) {
                        Session::flash('error', 'Please correct the errors below and try again');
                        return redirect()->back()->withInput()->withErrors($validator->errors());
                    } else {
                        $user->name = $request->name;
                        $user->email = $request->email;
                        if ($request->file('profile_image') !== null) {
                            $destinationPath = 'uploads/user/';
                            $response_data = Uploader::doUpload($request->file('profile_image'), $destinationPath);
                            if ($response_data['status'] == true) {
                                $user->profile_image = $response_data['file'];
                            }
                        }
                        if ($user->save()) {
                            Session::flash('success', 'Profile has been updated successfully.');
                            return redirect()->route('admin.dashboard');
                        }
                    }
                }
                return view('admin.users.my_profile', compact('title_page', 'user', 'breadcumb'));
            } else {
                Session::flash('warning', 'Invalid request');
                return redirect()->back();
            }
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            Session::flash('warning', $msg);
            return redirect()->back()->withInput();
        }
    }// end function.
    
    public function changePassword(Request $request){
        $user_id = Auth::id();
        try {
            $user = User::find($user_id);
            if ($user) {
                $title_page = 'Change Password';
                $breadcumb = ['Profile'=>route('admin.myprofile'), $title_page => ''];
                if ($request->isMethod('post')) {
                    $rules = [
                        'old_password' => 'required',
                        'new_password' => 'required|min:8',
                        'confirm_password' => 'required|same:new_password',
                    ];
                    $validator = Validator::make($request->all(), $rules);
                    if ($validator->fails()) {
                        Session::flash('error', 'Please correct the errors below and try again');
                        return redirect()->back()->withInput()->withErrors($validator->errors());
                    } else {
                        if (Hash::check($request->old_password, $user->password)){
                            $user->password = Hash::make($request->new_password);
                            $user->save();
                            Session::flash('success', 'Password has been updated successfully.');
                            return redirect()->route('admin.dashboard');
                        }
                        else {
                            Session::flash('error', 'Current password is incorrect.');
                            return redirect()->back();
                        }
                    }
                }
                return view('admin.users.change_password', compact('title_page', 'user', 'breadcumb'));
            } else {
                Session::flash('warning', 'Invalid request');
                return redirect()->back();
            }
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            Session::flash('warning', $msg);
            return redirect()->back()->withInput();
        }
    }
    
    public function forgot(Request $request) {
       if ($request->isMethod('post')) {
            try {
                $rules = ['email' => 'required|email'];
                $validator = Validator::make($request->all(), $rules);
                if ($validator->fails()) {
                    Session::flash('error', 'Please correct the errors below and try again');
                    return redirect()->back()->withInput()->withErrors($validator);
                } else {
                    $status_arr = Config::get('params.status');
                    $role_arr = Config::get('params.role_ids');
                    $user = User::where('email', '=', $request->email)->where('role', $role_arr['Admin'])->first();
                    if(empty($user)) {
                        Session::flash('error', 'The Email you entered does not exist.');
                        return redirect()->back()->withInput();
                    } else if($user->status == $status_arr['pending']) {
                        Session::flash('error', 'Your account is not verified.');
                        return redirect()->back()->withInput();
                    } else if($user->status == $status_arr['inactive']) {
                        Session::flash('error', 'Your account is not active.');
                        return redirect()->back()->withInput();
                    }
                    $token = randomToken();
                    $user->token = $token;
                    $user->save();

                    $data = ['email' => $user->email,'url'=>route('admin.reset',$token)];

                    Mail::send('emails.all_mail', $data, function ($message) use ($data){
                        $message->from('no-reply@demo.com', 'Loqman');
                        $message->to($data["email"]);
                        $message->subject('Loqman: Reset Password Request');
                    });
                    Session::flash('success', 'Reset password link has been sent successfully to ' . $request['email']);
                    return redirect()->route('admin.forgot');
                }
            } catch (\Exception $e) {
                Session::flash('error', $e->getMessage());
                return redirect()->back();
            }
        }
        $title_page = 'Forgot Password';
        return view('admin.users.forgot',  compact('title_page'));
    }// end function.
    
    public function reset(Request $request, $token) {
        $status_arr = Config::get('params.status');
        $user_data = User::where('token', $token)->first();
        if(empty($user_data)){
            Session::flash('error', 'The request page was not found');
            return redirect()->route('admin.login');
        } else if($user_data->status == $status_arr['pending']) {
            Session::flash('error', 'Your account is not verified.');
            return redirect()->route('admin.login');
        } else if($user_data->status == $status_arr['inactive']) {
            Session::flash('error', 'Your account is not active.');
            return redirect()->route('admin.login');
        }
        if ($request->isMethod('post')) {
            try {
                $rules = [
                    'password' => 'required|min:12|max:20|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[!@#\$%\^&\*])/',
                    'confirm_password' => 'required|required_with:password|same:password',
                ];
                $validator = Validator::make($request->all(), $rules);
                if ($validator->fails()) {
                    Session::flash('error', 'Please correct the errors below and try again');
                    return redirect()->back()->withInput()->withErrors($validator->errors());
                } else {
                    $password = Hash::make($request->password);
                    $user_data->update(['password'=>$password,'token'=>'']);
                    Session::flash('success', 'Password has been updated successfully.');
                    return redirect()->route('admin.login');
                }
            } catch (\Exception $e) {
                Session::flash('danger', $e->getMessage());
                return redirect()->back();
            }
        }
        $title_page = 'Reset Password';
        return view('admin.users.reset',  compact('title_page'));
    }
    
    public function create($role) {
       
        $role_ids = Config::get('params.role_ids');
     
        $title_page = 'Add User';
        $breadcumb = ['Admin' => route('admin.users', $role), $title_page => ''];
        $entity = new User();
        return view('admin.users.create', compact('breadcumb','title_page', 'entity', 'role'));
    }

    public function store($role, Request $request) {
       
        $role_ids = Config::get('params.role_ids');
        
        try {
            $rules = [
                'name' => 'required',
                'number' => 'required|unique:users',
            ];
            //echo Request::get('lat'); die;
            $validator = Validator::make($request->all(), $rules);
            
            if ($validator->fails()) {
                Session::flash('error', 'Please correct the errors below and try again.');
                return redirect()->back()->withInput()->withErrors($validator->errors());
            }
            $entity = new User;
            $entity->name = $request->name;
            $entity->email = $request->email;
            $entity->number = $request->number;
            $entity->role = $role;
            if ($request->file('profile_image') !== null) {
                $destinationPath = 'uploads/user/';
                $response_data = Uploader::doUpload($request->file('profile_image'), $destinationPath);
                if ($response_data['status'] == true) {
                    $entity->profile_image = $response_data['file'];
                }
            }
            $entity->save();
            Session::flash('success', 'User has been added successfully.');
            return redirect()->route('admin.users', $role);
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            Session::flash('warning', $msg);
            return redirect()->back()->withInput();
        }
    }
    public function edit($role, $id) {
        $role_ids = Config::get('params.role_ids');
        if($role != $role_ids['Users']) {
            abort(404);
        }
        $entity = User::where('id', $id)->first();
        if(empty($entity) || $entity->role != $role_ids['Users']){
            abort(404);
        }
        $entity->name = $entity->name;
        $title_page = 'Edit User';
        $breadcumb = ['User' => route('admin.users', $role), $title_page => ''];
        return view('admin.users.create', compact('breadcumb','title_page', 'entity', 'role'));
    }

    public function Expert_edit($role, $id) {
        $role_ids = Config::get('params.role_ids');
        if($role != $role_ids['Expert']) {
            abort(404);
        }
        $entity = User::where('id', $id)->first();
        if(empty($entity) || $entity->role != $role_ids['Expert']){
            abort(404);
        }
        $entity->name = $entity->name;
        $title_page = 'Edit Expert';
        $breadcumb = ['User' => route('admin.users', $role), $title_page => ''];
        return view('admin.users.expert_edit', compact('breadcumb','title_page', 'entity', 'role'));
    }

    public function update(Request $request, $role, $id) {
        $role_ids = Config::get('params.role_ids');
        $entity = User::where('id', $id)->first();
        try {
            $rules = [
                'name' => 'required',
                'email' => 'required|email|unique:users,email,'.$id,
                'status' => 'required',
                'number' => 'required',
            ];
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                Session::flash('error', 'Please correct the errors below and try again.');
                return redirect()->back()->withInput()->withErrors($validator->errors());
            }
            $entity->name = $request->name;
            $entity->email = $request->email;
            $entity->status = $request->status;
            $entity->number = $request->number;
            if($request->password) {
                $entity->password = Hash::make($request->password);
            }
            if ($request->file('profile_image') !== null) {
                $destinationPath = 'uploads/user/';
                $response_data = Uploader::doUpload($request->file('profile_image'), $destinationPath);
                if ($response_data['status'] == true) {
                    $entity->profile_image = $response_data['file'];
                }
            }
            $entity->save();
            Session::flash('success', 'User has been updated successfully.');
            return redirect()->route('admin.users', $role);
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            Session::flash('warning', $msg);
            return redirect()->back()->withInput();
        }
    }


    public function push_notification(Request $request){
        try{
             $notification=Notification::find(1);
             foreach($request->user_id as $Userid){
                 $User=User::where('id',$Userid)->first();
                 $notification_type="hurry";
                 $diduser="false";
                 if($User->fcm_token){
                    PushNotficationAndroid($User->fcm_token,$User->id,$notification->subject, $notification->message,$notification_type,$diduser);
                 }

                $data=new Notificationsend;
                $data->user_id=$Userid;
                $data->status="0";
                $data->message=$notification->message;
                $data->save();
             }  
        }catch (\Exception $e) {
           dd($e->getMessage());;
        }
    }

    public function selectuseractive(Request $request){
        try{
             foreach($request->user_id as $Userid){
                $Usercheck=User::where('id',$Userid)->get();
                foreach($Usercheck as $value){
                    if($value->status == 1){
                        User::where('id',$value->id)->update(['status'=>'2']);
                    }else{
                        User::where('id',$value->id)->update(['status'=>'1']);
                    }
                }
             } 
        }catch (\Exception $e) {
           dd($e->getMessage());;
        }
    }


    public function cmspage(Request $request, $slug){
        try{
            $data=Cms::where('slug',$slug)->where('usertype',3)->first();

            return view('cms_page',compact('data'));
    

        }catch(\Exception $e){
            dd($e);
            $data = [];
            return response()->json([
                'status'=>false,
                'message'=>"something went wrong",
                'data'=>$data],
                Config::get('params.error_status'
            ));
        }
    }
    
    
    

}
