<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use App\Model\User;
use App\Model\Category;
use App\Model\UserCategroy;
use App\Model\UserotherInformation;
use JWTAuth;
use JWTAuthException;
use Illuminate\Support\Facades\Validator;
use Config;
use App\Lib\Uploader;
use App\Model\Notificationsend;
use App\Model\Notification;
use App\Model\UserCalling;
use App\Model\Rating;
use Illuminate\Support\Facades\Hash;
use Auth;
use App\Classes\AgoraDynamicKey\RtcTokenBuilder;

class ExpertsController extends Controller
{
    public function __construct(Notificationsend $Notificationsend,User $User,Notification $Notification)
    {
        $this->Notificationsend = $Notificationsend;
        $this->User = $User;
        $this->Notification = $Notification;
    }

    public function expertRegister(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'number' => 'required'  
            ],$message=[
                'name.required'=>'حقل الاسم مطلوب',
                'number.required'=>'حقل الرقم مطلوب',
            ]);
            if ($validator->fails()) {
                $error = $this->validationHandle($validator->messages());
                return response()->json(['status' => false, 'message' => $error],Config::get('params.validation_status'));
            } else {
                $otp = rand(1111,9999);

                $Usercheck=User::where('number',$request->number)->first();
                if(!empty($Usercheck)){
                    if($Usercheck->number_verified == 1){
                        return response()->json([
                            'status_code'=>501,
                           'status'=>false,
                           'message'=>'الرقم مسجل مسبقاً',
                        ]);
                    }elseif($Usercheck->number_verified == 0){
                        $uservalue['name']=$request->name;
                        $uservalue['number']=$request->number;
                        $uservalue['otp']=$otp;
                        $number = $request->number;
                         Smsotp($otp,$number);
                        $uservalue['email'] = $request->email;
                        if ($request->file('profile_image')) {
                         $destinationPath = '/uploads/user/';
                         $response_data = Uploader::doUpload($request->file('profile_image'), $destinationPath);
                            if ($response_data['status'] == true) {
                                $uservalue['profile_image'] = $response_data['file'];
                            }
                        }
                         User::where('number',$request->number)->update($uservalue);
                         $usservalue=User::where('number',$request->number)->first();
                                return response()->json([
                                   'status'=>true,
                                   'message'=>"تم إنشاء الحساب بنجاح",
                                   'data'   =>$usservalue,
                                ]);

                    }
                }

                $user = new User();
                $user->name = $request->name;
                $user->number=$request->number;
                $user->email = $request->email;
                $user->number_verified="0";
                $user->role = Config::get('params.role_ids.Expert');

                if ($request->file('profile_image')) {
                 $destinationPath = '/uploads/user/';
                 $response_data = Uploader::doUpload($request->file('profile_image'), $destinationPath);
                    if ($response_data['status'] == true) {
                        $user->profile_image = $response_data['file'];
                    }
                }
                $user->otp=$otp;
                $user->save();
                $number = $request->number;
                Smsotp($otp,$number);
                $message ='تم إنشاء الحساب بنجاح';

                return response()->json([
                   'status'=>true,
                   'message'=>$message,
                   'data'   =>$user,
                ]);
            }
        }catch(\Exception $e){
            return response()->json([
                'status'=>false,
                'message'=>"something went wrong",],
                Config::get('params.error_status'));
        }
    }//end function.

    public function otp_verify(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'otp' => 'required',
            ],$message=[
                'otp.required'=>'مطلوب حقل الرمز',
            ]);
            if ($validator->fails()) {
                $error = $this->validationHandle($validator->messages());
                return response()->json(['status' => false, 'message' => $error],Config::get('params.validation_status'));
            } else {
                $userInfo = User::where(['otp'=>$request->otp,'number'=>$request->number])->first();
                if($userInfo){
                    $userInfo->status = '0';
                    $userInfo->otp='';
                    $userInfo->number_verified="1";
                    $userInfo->otp_time=NULL;
                    $userInfo->save();
                    return response()->json([
                        'status' => true, 
                        'message' => 'تم التحقق من الرمز',
                        'data'=>$userInfo],
                    Config::get('params.success_status'));
                }
                return response()->json([
                    'status' => false, 
                    'message' => 'الرمز خطأ',
                    'data'=>[]],
                Config::get('params.validation_status'));
            }
        }catch(\Exception $e){
            $data = [];
            return response()->json(
                ['status'=>false,
                'message'=>"something went wrong",
                'data'=>$data],
            Config::get('params.error_status'));
        }
    }


    public function make_mobile_type(Request $request){
        $validatorRules = [
            'mobile_type' => 'required',
            'fcm_token'=>'required',
            'device_id'=>'required',
        ];
        
        try{
            $validator = Validator::make($request->all(), $validatorRules);
            if ($validator->fails()) {
                $error = $this->validationHandle($validator->messages());
                return response()->json(['status' => false, 'message' => $error],Config::get('params.validation_status'));
            } else {
               $user=JWTAuth::user()->id;

               User::where('id',$user)->update(['mobile_type'=>$request->mobile_type,'fcm_token'=>$request->fcm_token,'device_id'=>$request->device_id]);

               return response()->json([
                   'status'=>true,
                   'message'=>"تم الإضافة",
                ]);
               
            }
        }catch(\Exception $e){
            return response()->json([
                'status'=>false,
                'message'=>"something went wrong",
                ],Config::get('params.error_status'));
        }
    }

    public function push_notification_call(Request $request){
        try{
            if($request->title == "request call"){
                $sender_id=$request->sender_id;
                $recvier_id=$request->recvier_id;

                User::where('id',$request->sender_id)->update(['online_status'=>'2']);
                
                $user=User::where('id',$recvier_id)->first();
                $Username=$user->name;
                $Userprofile=$user->profile_image;
                $fcm_token=$user->fcm_token;

                $sendervalue=User::where('id',$sender_id)->first();
                $callerid=rand(1111,9999);
                $pool = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                $length=4;
                $channelname = substr(str_shuffle(str_repeat($pool, 5)), 0, $length);
                // $liveSchedule['title']='ravi';
                // $liveSchedule['id']='1';

                $appID = "fb6c5fec14334bb29f86b1a3652d9697";
                $appCertificate ="ee49340af0e0438c94ca932a81648b99";
                $channelName =$channelname;
                $uid=0;
                $role =RtcTokenBuilder::RolePublisher;
                $maxDuration=User::where('id','1')->first()->live_max_duration;

                $expireTimeInSeconds =$maxDuration * 60;
                $currentTimestamp = now()->getTimestamp();
                $privilegeExpiredTs = $currentTimestamp + $expireTimeInSeconds;
                $ch_name=$sendervalue->name.$sendervalue->id;
                $agoratoken= RtcTokenBuilder::buildTokenWithUserAccount($appID, $appCertificate, $channelName, $privilegeExpiredTs, $role, $uid);

                $usercall=new UserCalling;
                $usercall->recive_id=$request->recvier_id;
                $usercall->send_id=$request->sender_id;
                $usercall->save();

                $senderData=[
                   "Name" =>$user->name,
                   "Image"=>$user->profile_image,
                   "AgorachannalName"=>$channelname,
                   "channel_id"=>$channelname,
                   "appid"=>$appID,
                   "type"=>'calling',
                   "AgoraToken"=>$agoratoken,
                   "caller_id"=>$callerid,
                   "recvier_id"=>$user->id,
                   "sender_id"=>$sendervalue->id,
                ];

            }elseif($request->title == "notification"){
                $sender_id=$request->sender_id;
                $recvier_id=$request->recvier_id;

                $use=User::where('id',$recvier_id)->first();
                $fcm_token=$use->fcm_token;
                $senderData=[];
            }

            HurryAndroid($fcm_token,$recvier_id,$sender_id,$Username,$agoratoken,$Userprofile,$channelname,$appID,$callerid,$usercall->id);
            return response()->json([
                'status'=>true,
                'message'=>"successfully notification send",
                'data'=>$senderData,
                'usercallingid'=>$usercall->id,
            ]);
        }catch(\Exception $e){
             //dd($e->getMessage());
            return response()->json([
                'status' => false, 
                'message' =>"something went wrong"]);
            }
    }


    public function notification_list(Request $request){
        try{
            $user_id = JWTAuth::user()->id;
            if(Notificationsend::where('user_id',$user_id)->exists()){
                $data=Notificationsend::where('user_id',$user_id)->orderBy('id','DESC')->get();
            }else{
                $data="";
            }
            return response()->json([
                'status'=>true,
                'message'=>"successfully notification get",
                'data'=>$data,
            ]);

        }catch(\Exception $e){
            //dd($e->getMessage());
           return response()->json([
               'status' => false, 
               'message' =>"something went wrong"]);
        }
    }

    public function notification_seen(Request $request){
        try{
            $user_id = JWTAuth::user()->id;

            $data=Notificationsend::where('user_id',$user_id)->update(['status'=>'1']);
            return response()->json([
                'status'=>true,
                'message'=>"تحديث التنبيه",
                'data'=>'',
            ]);

        }catch(\Exception $e){
            //dd($e->getMessage());
           return response()->json([
               'status' => false, 
               'message' =>"something went wrong"]);
        }
    }

    public function notification_status(Request $request){
        try{
            $user_id = JWTAuth::user()->id;

            $data=Notificationsend::where('user_id',$user_id)->orderBy('id','DESC')->first();
            $status="";
            if(!empty($data)){
                if($data->status ==0){
                    $status=0; 
                }else{
                    $status=1;
                }
            }
            return response()->json([
                'status'=>true,
                'message'=>"update notification",
                'data'=>$status,
            ]);

        }catch(\Exception $e){
            //dd($e->getMessage());
           return response()->json([
               'status' => false, 
               'message' =>"something went wrong"]);
        }
    }
    //
    
    public function select_categroy(Request $request){
        $validatorRules = [
            'categroy' => 'required',
            'user_id'  =>  'required',
        ];
        try{
            $validator = Validator::make($request->all(), $validatorRules);
            if ($validator->fails()) {
                $error = $this->validationHandle($validator->messages());
                return response()->json(['status' => false, 'message' => $error],Config::get('params.validation_status'));
            } 

            $cat_arr = [];

            UserCategroy::where('user_id',$request->user_id)->delete();

            foreach($request->categroy as $value){
                $data=new UserCategroy;
                $data->categroy_id=$value;
                $data->user_id=$request->user_id;
                $data->save();
                $cat_id[] = $data->id;
            }

            if(!empty($cat_id)){

                $user=User::find($request->user_id);
                return response()->json([
                    'status'=>true,
                    'message'=>"تم إضافة المجال بنجاح",
                    'data'=>$user,
                ]);
            }else{
                return response()->json([
                    'status'=>false,
                    'message'=>"لايمكن إضافة مجال آخر",
                    'data'=>[],
                ]);
            }
        
        }catch(\Exception $e){
            $data = [];
            return response()->json(['status'=>false,'message'=>$e->getMessage(),'data'=>$data],Config::get('params.error_status'));
        }
    }
    public function getCategory(){
        try{
            $datas = Category::get();
            return response()->json([
                'status'=>true,
                'message'=>'Category Data',
                'data'=>$datas
            ]);
        }catch (\Exception $e) {
            return response()->json([
                'status' => false, 
                'message' =>"something went wrong", 
                'data' => []
            ]);
        }
    }


    public function addcategroy(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'category_name' => 'required',
            ],$message=[
                'category_name.required'=>'مطلوب حقل اسم الفئة',
            ]);
            if ($validator->fails()) {
                $error = $this->validationHandle($validator->messages());
                return response()->json(['status' => false, 'message' => $error],Config::get('params.validation_status'));
            }

                foreach($request->category_name as $value){
                    $data=new Category;
                    $data->category_name=$value;
                    $data->save();
                    // $value=new UserCategroy;
                    // $value->user_id=$request->user_id;
                    // $value->categroy_id=$data->id;
                    // $value->save();
                }

                $user=user::find($request->user_id);

                return response()->json([
                    'status'=>true,
                    'message'=>"تم إضافة المجال بنجاح",
                    'data'=>$user,
                ]);
        }catch (\Exception $e) {
            $data = [];
            return response()->json([
                'status'=>false,
                'message'=>$e->getMessage(),
                'data'=>$e->getMessage(),
            ],Config::get('params.error_status'));
        }
    }

    public function user_other_info(Request $request){
        $validator = Validator::make($request->all(), [
            'bio' => 'required',
            'user_id'=>'required'
        ],$message=[
            'bio.required'=>'مطلوب السيرة الذاتية',
            'user_id.required'=> 'معرف المستخدم مطلوب'
        ]);
        try{
            //$validator = Validator::make($request->all(), $validatorRules);
            if ($validator->fails()) {
                $error = $this->validationHandle($validator->messages());
                return response()->json(['status' => false, 'message' => $error],Config::get('params.validation_status'));
            }
                User::where('id',$request->user_id)->update(['bio'=>$request->bio,'status'=>'1','online_status'=>'0']);

                UserotherInformation::where('user_id',$request->user_id)->delete();

                // foreach($request->tags as $value){
                //     $data=new UserotherInformation;
                //     $data->tags=$value;
                //     $data->user_id=$request->user_id;
                //     $data->save();
                // }

                $user=User::find($request->user_id);
                return response()->json([
                    'status'=>true,
                    'message'=>"تم إضافة المعلومات الإضافية",
                    'data'=>$user,
                ]);
        }catch (\Exception $e) {
            $data = [];
            return response()->json([
                'status'=>false,
                'message'=>"something went wrong",
                'data'=>$data
            ],Config::get('params.error_status'));
        }
    }

    public function login(Request $request){
            $validatorRules = [
                'number' => 'required',
            ];
        try{
            $validator = Validator::make($request->all(), $validatorRules);
            if ($validator->fails()) {
                $error = $this->validationHandle($validator->messages());
                return response()->json(['status' => false, 'message' => $error],Config::get('params.validation_status'));
            }
            $user = User::where('number',$request->number)->where('role','3')->first();

            if(!$user){
                return response()->json([
                    'stats_code'=>501,
                    'status'=>false,
                    'message'=>"الرقم غير مسجل",
                    'data'=>"",
                ]);

            }elseif($user->status == 1){
                $data = Auth::loginUsingId($user->id, TRUE);
                if($data){
                    if($request->number === '9875481548' || $request->number === '8555236985'){
                       // $otp = rand(1111,9999);
                        $otp = 1234;
                        // Smsotp($otp,$number);
                    }else{
                        $otp = rand(1111,9999);
                        $number = $request->number;
                        Smsotp($otp,$number);
                    }
                    
                    $user_details=User::where('id',$data->id)->update(['otp'=>$otp]);
                    $userdatas=User::where('id',$data->id)->first();

                    return response()->json([
                        'status'=>true,
                        'message'=>"تحقق من الرمز",
                        'data'=>$userdatas,
                    ]);
                }

            }elseif($user->status == 2){
                return response()->json([
                    'status_code'=>502,
                    'status'=>false,
                    'message'=>"حسابك معطل من قبل المشرف"]); 

            }elseif($user->status != 1){
                return response()->json([
                    'status'=>false,
                    'message'=>"من فضلك أكمل التسجيل",
                    'data'=>$user]);
            }

            

        }catch(\Exception $e){
            $data = [];
            return response()->json([
                'status'=>false,
                'message'=>$e->getMessage(),
                'data'=>$data
            ],Config::get('params.error_status'));
        }
    }

    function singotp_verify(Request $request){
        $validatorRules = [
            'otp' => 'required',
        ];
        try{
            $validator = Validator::make($request->all(), $validatorRules);
            if ($validator->fails()) {
                $error = $this->validationHandle($validator->messages());
                return response()->json(['status' => false, 'message' => $error],Config::get('params.validation_status'));
            } else {
                $userInfo = User::where(['otp'=>$request->otp,'number'=>$request->number])->first();
                if($userInfo){
                    $userInfo->otp='';
                    $userInfo->save();
                    $token = JWTAuth::fromUser($userInfo);
                    $userInfo->security_token = $token;
                    return response()->json(
                        ['status' => true, 
                        'message' => 'تم التحقق من الرمز',
                        'data'=>$userInfo],
                        Config::get('params.success_status'));
                }
                return response()->json([
                    'status' => true, 
                    'message' => 'الرمز خطأ',
                    'data'=>[]],Config::get('params.validation_status'));
            }
        }catch(\Excpetion $e){
            $data = [];
            return response()->json([
                'status'=>false,
                'message'=>$e->getMessage(),
                'data'=>$data
            ],Config::get('params.error_status'));
        }
    }


    function resend_otp(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'number' => 'required',
            ],$message=[
                'number.required'=>'حقل الرقم مطلوب'
            ]);
            if ($validator->fails()) {
                $error = $this->validationHandle($validator->messages());
                return response()->json(['status' => false, 'message' => $error],Config::get('params.validation_status'));
            } 

            $user=User::where('number',$request->number)->first();
            if($user){
                $otp = rand(1111,9999);
                $number = $request->number;
                $data=User::where('number',$request->number)->update(['otp'=>$otp]);
                $user_details=User::where('number',$request->number)->first();
                Smsotp($otp,$number);
            }

            return response()->json(
                    ['status' => true, 
                    'message' => 'تم إعادة إرسال الرمز',
                    'data'=>$user_details
                   ]);

        }catch(\Excpetion $e){
            $data = [];
            return response()->json([
                'status'=>false,
                'message'=>$e->getMessage(),
                'data'=>$data
            ],Config::get('params.error_status'));
        }

    }


    function profileget(Request $request){
        try{
            $user_id = JWTAuth::user()->id;
            $user_details = User::where(["id"=>$user_id])->with("selectcategroy")->first();
            // $user_value=User::where('id',$user_id)->first();
            
            //$user_details = User::where(["id"=>$request->user_id])->with("selectcategroy")->first();
            $user_value=User::where('id',$user_id)->first();
            $categories = [];
            foreach($user_details->selectcategroy as $cat){
                $cat_details = get_category_details($cat->categroy_id);
                $categories[]=[
                    'categroy_id'=>$cat_details->id,
                    'categroy_name'=>$cat_details->category_name
                ];
            }

            $tags=UserotherInformation::where('user_id',$user_id)->get();
            $tag_value =[];
            foreach($tags as $value){
                $tag_value[]=$value->tags;
            }
            
            $ratingavg=Rating::where('recive_id',$user_id)->avg('rating');
            $totalrating=Rating::where('recive_id',$user_id)->count();

                return response()->json([
                    'status' => true, 
                    'message' => 'user profile data',
                    'data'=>[
                        'tags'=>$tag_value,
                        'categroy'=>$categories,
                        'user_details'=>$user_value,
                        'ratingavg'=>$ratingavg,
                        'totalrating'=>$totalrating
                    ]
                ]);
                

        }catch(\Exception $e){
            return response()->json([
                'status'=>false,
                'message'=>"something went wrong",
            ],Config::get('params.error_status'));
        }
    }

    function expert_delete(Request $request){
        try{

            User::where('id', JWTAuth::user()->id)->delete();
            UserotherInformation::where('user_id',JWTAuth::user()->id)->delete();
            UserCategroy::where('user_id',JWTAuth::user()->id)->delete();
            UserCalling::where('recive_id',JWTAuth::user()->id)->delete();
            Rating::where('sender_id',JWTAuth::user()->id)->delete();
            Notificationsend::where('user_id',JWTAuth::user()->id)->delete();

            return response()->json([
                'status' => true, 
                'message' => 'user delete successfully.',
                'data'=>''
            ]);

        }catch(\Exception $e){
            return response()->json([
                'status'=>false,
                'message'=>"something went wrong",
            ],Config::get('params.error_status'));
        }
    }


    public function profile_edit(Request $request){
        try{
            $user_id = JWTAuth::user()->id;
            $user_details = User::where('id',$user_id)->first();
            
            //$data = $request->all();
            $data = [];
            $data['name']=$request->name;
            $data['email']=$request->email;
            $data['bio']=$request->bio;

            if ($request->file('profile_image')) {
                 $destinationPath = '/uploads/user/';
                 $response_data = Uploader::doUpload($request->file('profile_image'), $destinationPath);
                    if ($response_data['status'] == true) {
                         $data['profile_image'] = $response_data['file'];
                    }
            }

            User::where('id',$user_id)->update($data);

            if($request->categroy_id){
                UserCategroy::where('user_id',$user_id)->delete();
                foreach($request->categroy_id as $value){
                    $categroy=new UserCategroy;
                    $categroy->categroy_id=$value;
                    $categroy->user_id=$user_id;
                    $categroy->save();
                }
            }

            // if($request->tags){
            //     UserotherInformation::where('user_id',$user_id)->delete();

            //     foreach($request->tags as $tags_name){
            //         $tag=new UserotherInformation;
            //         $tag->user_id=$user_id;
            //         $tag->tags=$tags_name;
            //         $tag->save();
            //     }
            // }
            return response()->json([
                'status' => true, 
                'message' => 'تم التحديث']);

        }catch(\Exception $e){
            return response()->json([
                'status'=>false,
                'message'=>"something went wrong",
            ],Config::get('params.error_status'));
        }
    }

    public function expert_online(Request $request){
        $validatorRules = [
            'status_online' => 'required',
        ];
        try{
         $validator = Validator::make($request->all(), $validatorRules);
            if ($validator->fails()) {
                $error = $this->validationHandle($validator->messages());
                return response()->json(['status' => false, 'message' => $error],Config::get('params.validation_status'));
            }else{

                $user=User::where('id',$request->user_id)->update(['online_status'=>$request->status_online]);
                $user_value=User::where('id',$request->user_id)->first();
                 return response()->json([
                'status' => true, 
                'message' => 'تم تحديث الحالة',
                'data'=>$user_value,
                 ]);

            } 
        }catch(\Exception $e){
            return response()->json([
                'status'=>false,
                'message'=>"Something went wrong",
            ],Config::get('params.error_status'));
        }

    }

    public function expert_logout(){
         try{
            $user_id = JWTAuth::user()->id;
            User::where('id', $user_id)->update(['online_status' => '0','fcm_token'=>'','mobile_type'=>'']);
            $token = JWTAuth::getToken();
            if ($token) {
                JWTAuth::setToken($token)->invalidate();
            }
            return response()->json([
                'status'=>true,
                'message'=>'تم الخروج بنجاح',
                'data'=>[]
            ]);
        }catch (\Exception $e) {
            return response()->json([
                'status' => false, 
                'message' => "something went wrong", 
                'data' => []
            ]);
        }
    }


    public function accpet_call(Request $request){
        try{
            $data=UserCalling::where('id',$request->usercalling_id)->update(['start_date'=>$request->start_date,'start_time'=>$request->start_time,'status'=>'1']);

            return response()->json([
                'status'=>true,
                'message'=>"successfully accpet calling",
                'data'=>'',
            ]);
        }catch(\Exception $e){
            return response()->json([
                'status'=>false,
                'message'=>"something went wrong",
                ],Config::get('params.error_status'));
            }
    }

    function cancel_call(Request $request){
        try{
             
            $data=UserCalling::where('id',$request->usercalling_id)->update(['start_date'=>$request->start_date,'status'=>'2']);
            $usecalling=UserCalling::where('id',$request->usercalling_id)->first();
            $userrecive=User::where('id',$usecalling->send_id)->first();
            User::where('id',$usecalling->send_id)->update(['online_status'=>'1']);

            $subject="Call cancelled";
            $message="Call cancelled";
            $notification_type="provider_call_cancel";
            $user=User::where('id',$usecalling->recive_id)->first();
            $diduser="false";
            if($user->fcm_token){
                ExpertpushAndroid($user->fcm_token,$user->id,$subject,$message,$notification_type,$diduser,$userrecive->id,$usecalling->id);
            }

            return response()->json([
                'status'=>true,
                'message'=>"cencel calling",
                'data'=>'',
            ]);

        }catch(\Exception $e){
            return response()->json([
                'status'=>false,
                'message'=>"something went wrong",
                ],Config::get('params.error_status'));
            } 
    }

    function decline_call(Request $request){
        try{
              UserCalling::where('id',$request->usercalling_id)->update(['end_time'=>$request->end_time]);

            $usecalling=UserCalling::where('id',$request->usercalling_id)->first();
            $userrecive=User::where('id',$usecalling->send_id)->first();
            User::where('id',$usecalling->send_id)->update(['online_status'=>'1']);

            $notificationValue=Notificationsend::where('send_notification','1')->get();
            $notification=Notification::find(7);
            foreach($notificationValue as $Notificas){
                $valus=new Notificationsend;
                $valus->user_id=$Notificas->send_id;
                $valus->message=$notification->message;
                $valus->status="0";
                $valus->save();

                $User=User::where('id',$Notificas->send_id)->where('status','1')->first();
                $notification_type="hurry";
                 $diduser="false";
                 if($User->fcm_token){
                    PushNotficationAndroid($User->fcm_token,$User->id,$notification->subject, $notification->message,$notification_type,$diduser);
                 }

                Notificationsend::where('id',$Notificas->id)->update(['send_notification'=>'2']);
            }

            $UserCheck=User::where('id',$usecalling->recive_id)->first();
            $subject="Call declined";
            $message="Call declined";
            $notification_type="provider_decline_cancel";
            $diduser="true";
            if($UserCheck->fcm_token){
                ExpertpushAndroid($UserCheck->fcm_token,$UserCheck->id,$subject,$message,$notification_type,$diduser,$userrecive->id,$usecalling->id);
            }

            return response()->json([
                'status'=>true,
                'message'=>"successfully decline calling",
                'data'=>'',
            ]);

        }catch(\Exception $e){
            echo $e->getMessage();die;
            return response()->json([
                'status'=>false,
                'message'=>"something went wrong",
                ],Config::get('params.error_status'));
        } 
    }

    public function callbacklist(Request $request){
        try{
            $user_id = JWTAuth::user()->id;
            $user=UserCalling::where('send_id',$user_id)->where('status','3')->orderBy('id','DESC')->get();

            $data=[];
            foreach($user as $key=>$value){
                $data[$key]['id']=$value->id;
                $data[$key]['send_id']=$value->send_id;
                $data[$key]['recive_id']=$value->recive_id;
                if($value->start_date){
                    $data[$key]['start_date']=date('l,d F Y', strtotime($value->start_date));
                }
                if($value->start_time){
                    $data[$key]['start_time']=$value->start_time;
                }	
                $data[$key]['end_time']=$value->end_time;
            }

            return response()->json([
                'status'=>true,
                'message'=>"successfully Call api",
                'data'=>$data,
            ]);

        }catch(\Exception $e){
            return response()->json([
                'status'=>false,
                'message'=>"something went wrong",
                ],Config::get('params.error_status'));
        }
    }

    function callbackremovelist(Request $request){
        try{
            UserCalling::where('id',$request->usercalling_id)->delete();

            return response()->json([
                'status'=>true,
                'message'=>"تم الإزالة",
                'data'=>'',
            ]);
        }catch(\Exception $e){
            return response()->json([
                'status'=>false,
                'message'=>"something went wrong",
            ],Config::get('params.error_status'));
        }
    }

    function consultion_list(Request $request){
        try{
            $user_id = JWTAuth::user()->id;
            $usercall=UserCalling::where('send_id',$user_id)->where('status','1')->orderBy('id','DESC')->get();
            $calltotal=$usercall->count();
            $rating=Rating::where('recive_id',$user_id)->get();
            $totalvalue=$rating->sum('bages');

            $usercalls=[];
            foreach($usercall as $key=>$value){
                $usercalls[$key]['recive_id']=$value->recive_id;
                if($value->start_date){
                    $usercalls[$key]['start_date']=date('l,d F Y', strtotime($value->start_date));
                }
                if($value->start_time){
                    $usercalls[$key]['start_time']=$value->start_time;
                }   
                $usercalls[$key]['end_time']=$value->end_time;
                $check=Getratings($value->id);
                $usercalls[$key]['ratings']=$check->rating ?? '';
            }

            return response()->json([
                'status'=>true,
                'message'=>"successfully consultion list",
                'data'=>$usercalls,
                'totalcall'=>$calltotal,
                'totalvalue'=>$totalvalue,
            ]);

        }catch(\Exception $e){
            return response()->json([
                'status'=>false,
                'message'=>"something went wrong",
            ],Config::get('params.error_status'));
        }
    }

    function last_activity(Request $request){
        try{
           echo "Hello sir";
        $data = $this->User->where('status','1')->whereIn('role',array('2','3'))->get();
        $notification = $this->Notification->where('status','1')->where('id','6')->first();
        $notification_type="hurry";
        $diduser="false";
        foreach($data as $value){
            $Fivedate=date('Y-m-d', strtotime($value->last_activity. ' + 5 days'));
            if($Fivedate == date("Y-m-d")){
                PushNotficationAndroid($value->fcm_token,$value->id,$notification->subject, $notification->message,$notification_type,$diduser);

                $this->Notificationsend->user_id = $value->id;
                $this->Notificationsend->message = $notification->message;
                $this->Notificationsend->status = "0";
                $this->Notificationsend->save();
            }
        }

        }catch(\Exception $e){
            return response()->json([
                'status'=>false,
                'message'=>"something went wrong",
            ],Config::get('params.error_status'));
        }
    }

    function last_activity_update(Request $request){
        try{

            $data = $this->User->where('id',$request->id)->update(['last_activity'=>$request->last_activity]);
            return response()->json([
                'status'=>true,
                'message'=>"User last activity update successfully",
                'data'   =>'',
             ]);
    
            }catch(\Exception $e){
                dd($e->getMessage());
                return response()->json([
                    'status'=>false,
                    'message'=>"something went wrong",
                ],Config::get('params.error_status'));
            }
    }


 
    // public function login(Request $request) {
    //     $data = $request->all();
    //     $validator = Validator::make($request->all(), [
    //         'number' => 'required',
    //         //'password' => 'required',
    //         'device_type' => 'required|in:IPHONE,ANDROID',
    //         'device_id' => 'required',
    //     ]);
    //     if ($validator->fails()) {
    //         $response['status'] = false;
    //         $response['message'] = $this->validationHandle($validator->messages());
    //         $response['data'] = [];
    //         $code =Config::get('params.validation_status');
    //         return response()->json([$response],$code);
    //     } else {
    //         $data['password']='123456';
    //         $response = $this->makeLogin($data);
    //         $code = Config::get('params.success_status');
    //         return response()->json([$response],$code);
    //     }
    // }//end function.


    protected function makeLogin($data=[]){
        $response = [];
        $roles_arr = Config::get('params.role_ids');
        $user = User::where([['number' , $data['number']],['role',$roles_arr['Expert']]])->first();
        if (!$user) {
            $response['status'] = false;
            $response['message'] = "User does not exists.";
            $response['data'] = [];
            $code = Config::get('params.validation_status');
            
        } else {
            if (Hash::check($data['password'], $user->password)) {
                $status_arr = \Config::get('params.status');
                if ($user->status == $status_arr['pending']) {
                    $response['status'] = false;
                    $response['message'] = "Your account is not verified.";
                    $response['data'] = $user;
                    $code = Config::get('params.validation_status');
                } else if($user->status == $status_arr['inactive']) {
                    $response['status'] = false;
                    $response['message'] = "Your account is inactive.";
                    $response['data'] = $user;
                    $code = Config::get('params.validation_status');
                } 
                // else if($user->is_login == '1'){
                //     $response['status'] = false;
                //     $response['message'] = "User already logged in another device”";
                //     $response['data'] = $user;
                // }  
                else if($user->status == $status_arr['active']) {
                    $token = JWTAuth::fromUser($user);
                    $user_id = $user->id;
                    $token = randomToken();
                    $token = '1234';
                    $user->otp = $token;
                    $user->otp_time = date('Y-m-d H:i:s');
                    $user->save();
                      Smsotp($token,$data['number']);
                    // User::where('id', $user_id)->update(['is_login' => '1']);
                    manageDevices($user->id, $data['device_id'], $data['device_type'], 'add');
                    $user->security_token = $token;
                    //$user->is_social_login = 0;
                    $response = ['status' => true, 'message' => 'Login successful.', 'data' => $user];
                    $code = Config::get('params.success_status');
                }
            }
            else {
                $response['status'] = false;
                $response['message'] = "Password Incorrect!.";
                $response['data'] = [];
                $code = Config::get('params.validation_status');
            }
        }
        return $response;
        
    }// end function.


    public function saveMoreCategory()
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
        ]);
        if ($validator->fails()) {
            $error = $this->validationHandle($validator->messages()); 
            return response()->json(['status'=>false,'message'=>$error],Config::get('params.validation_status'));
        }else{
            try{
            foreach($request->title as $catValue){
                $Category = new Category();
                $Category->title = $catValue;
                $Category->user_id = Auth()->User()->id;
                $Category->save();

                }
            }catch (\Exception $e) {
                $response['status'] = false;
                $response['message'] = $e->getMessage();
                $response['data'] = [];
                return response()->json([$response],Config::get('params.error_status'));
           } 
        }
    }
    
}
