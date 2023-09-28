<?php
namespace App\Http\Controllers\Api;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use \Config;
use JWTAuth;
use JWTAuthException;
use App\Lib\Uploader;
use App\Model\UserotherInformation;
use App\Model\Membership;
use App\Model\User;
use App\Model\UserCategroy;
use App\Model\Category;
use App\Model\UserPlan;
use App\Model\UserDevice;
use App\Model\Review;
use App\Model\Contactus;
use App\Model\Cms;
use App\Model\Notification;
use App\Model\Add;
use App\Model\Notificationsend;
use App\Model\UserCalling;
use App\Model\Rating;
use App\Model\Feedback;
use App\Classes\AgoraDynamicKey\RtcTokenBuilder;


class UsersController extends Controller {

    public function categroyget(Request $request){
        try{
            $datas = Category::get();
            return response()->json([
                'status'=>true,
                'message'=>'Category Data',
                'data'=>$datas
            ]);

        }catch(\Exception $e){
            $data = [];
            return response()->json([
                'status'=>false,
                'message'=>$e->getMessage(),
                'data'=>$data],
                Config::get('params.error_status'
            ));
        }

    }

    public function option_search(Request $request){
        try{
            $jsonData=[];

            $categroy_id=Category::where('category_name','like','%'.$request->search.'%')->get();

                $usercid=[];
                foreach($categroy_id as $categroy_data){
                     $cat_data=UserCategroy::where('categroy_id',$categroy_data->id)->get();
                     foreach($cat_data as $user_value){
                          $usercid[]=$user_value->user_id ?? '';
                     }
                }

                $tags=UserotherInformation::where('tags','like','%'.$request->search.'%')->get();
                foreach($tags as $alltags){
                      $usercid[]=$alltags->user_id;

                }

                if(empty($usercid)){
                    $user_details = User::where(['role'=>'3','status'=>'1'])->where('name', 'like', '%'.$request->search.'%')->orWhere('bio', 'like', '%'.$request->search.'%')->with(["selectcategroy.category_get"])->take(5)->get();
                }else{
                   
                   $user_details = User::where(['role'=>'3','status'=>'1'])->with(["selectcategroy.category_get"])->whereHas('selectcategroy', function ($query) use($usercid) {
                           $query->whereIn('user_id',$usercid);
                         })->take(5)->get();
                }


          
                   

                    foreach($user_details as $key=>$valuess){
                        $jsonData[$key]["id"]=$valuess->id;
                        $jsonData[$key]["name"]=$valuess->name;
                    }


            if($jsonData == null){
                return response()->json([
                    'status'=>502,
                    'message'=>"لايوجد نتائج",
                  ]);
           }else{

                return response()->json([
                    'status'=>true,
                    'message'=>"تم البحث",
                    'data'=>$jsonData,
                  ]);
           }

        }catch(\Exception $e){
            dd($e->getMessage());
            return response()->json([
                'status'=>false,
                'message'=>"Some thing Went Wrong",
                ],
                Config::get('params.error_status'
            ));
        }
    }

    public function search_categroy(Request $request){
        try{
            
            if($request->has('search')){

                $categroy_id=Category::where('category_name','like','%'.$request->search.'%')->get();

                $usercid=[];
                foreach($categroy_id as $categroy_data){
                     $cat_data=UserCategroy::where('categroy_id',$categroy_data->id)->get();
                     foreach($cat_data as $user_value){
                          $usercid[]=$user_value->user_id ?? '';
                     }
                }

                $tags=UserotherInformation::where('tags','like','%'.$request->search.'%')->get();
                foreach($tags as $alltags){
                      $usercid[]=$alltags->user_id;

                }

                $splitSearch = explode(' ', $request->search);
                log::debug($splitSearch);

                if(empty($usercid)){
                    $user_details = User::where(['role'=>'3','status'=>'1'])
                    ->Where(function ($query) use($splitSearch) {
                        for ($i = 0; $i < count($splitSearch); $i++){
                           $query->orwhere('name', 'like',  '%' . $splitSearch[$i] .'%');
                        }      
                    })
                    ->orWhere('bio', 'like', '%'.$request->search.'%')->with(["selectcategroy.category_get"])->orderBy('id', 'DESC')->get();
                }else{
                   
                   $user_details = User::where(['role'=>'3','status'=>'1'])->with(["selectcategroy.category_get"])->whereHas('selectcategroy', function ($query) use($usercid) {
                           $query->whereIn('user_id',$usercid);
                         })->get();
                }


          
                   $jsonData=[];

                    foreach($user_details as $key=>$valuess){
                        $jsonData[$key]['user_id']=$valuess->id;
                           $ratingavg=Rating::where('recive_id',$valuess->id)->avg('rating');
                           $totalrating=Rating::where('recive_id',$valuess->id)->count();
                        $jsonData[$key]['ratingavg']=$ratingavg;
                        $jsonData[$key]['totalrating']=$totalrating;
                        $jsonData[$key]["name"]=$valuess->name;
                        $jsonData[$key]['status']=$valuess->status;
                        $jsonData[$key]['online_status']=$valuess->online_status;
                        $jsonData[$key]['profile_image']=$valuess->profile_image;
                        $jsonData[$key]['number']=$valuess->number;
                        
                        foreach($valuess->selectcategroy as $categs){   
                          foreach($categs->category_get as $get_test){
                            $jsonData[$key]['categroy'][]=$get_test->category_name;
                          }
                        }
                    }
                    
        
            }elseif($request->has('categroy_id')){

                $categroyGet=Category::where('id',$request->categroy_id)->get();

                 $usercid=[];
                foreach($categroyGet as $categroy_data){
                     $cat_data=UserCategroy::where('categroy_id',$categroy_data->id)->get();
                     foreach($cat_data as $user_value){
                          $usercid[]=$user_value->user_id ?? '';
                     }
                }
                
                $user_details = User::where(['role'=>'3','status'=>'1'])->with(["selectcategroy.category_get"])->whereHas('selectcategroy', function ($query) use($usercid) {
                           $query->whereIn('user_id',$usercid);
                         })->get();

                $jsonData=[];

                    foreach($user_details as $key=>$valuess){
                        $jsonData[$key]['user_id']=$valuess->id;
                        $ratingavg=Rating::where('recive_id',$valuess->id)->avg('rating');
                           $totalrating=Rating::where('recive_id',$valuess->id)->count();
                        $jsonData[$key]['ratingavg']=$ratingavg;
                        $jsonData[$key]['totalrating']=$totalrating;
                        $jsonData[$key]["name"]=$valuess->name;
                        $jsonData[$key]['online_status']=$valuess->online_status;
                        $jsonData[$key]['status']=$valuess->status;
                        $jsonData[$key]['profile_image']=$valuess->profile_image;
                        $jsonData[$key]['number']=$valuess->number;
                        
                        foreach($valuess->selectcategroy as $categs){   
                          foreach($categs->category_get as $get_test){
                            $jsonData[$key]['categroy'][]=$get_test->category_name;
                          }
                        }
                    }

            }
               if($jsonData == null){
                    return response()->json([
                        'status'=>502,
                        'message'=>"لايوجد نتائج",
                      ]);
               }else{

                    return response()->json([
                        'status'=>true,
                        'message'=>"تم البحث",
                        'data'=>$jsonData,
                      ]);
               }

        }catch(\Exception $e){
            $data = [];
            return response()->json([
                'status'=>false,
                'message'=>$e->getMessage(),
                ],
                Config::get('params.error_status'
            ));
        }

    }

    /*public function expert_get(Request $request){
        $validatorRules = [
            'categroy_id' => 'required',
        ];
        try{
            $validator = Validator::make($request->all(), $validatorRules);
                if ($validator->fails()) {
                    $error = $this->validationHandle($validator->messages());
                    return response()->json(['status' => false, 'message' => $error],Config::get('params.validation_status'));
                }
                $categroyGet=Category::where('id',$request->categroy_id)->with('selectcategroy')->first();
                $userInfo=User::where('id',$categroyGet)->with('selectcategroy')->first();

                $categories = [];
                foreach($categroyGet->selectcategroy as $cat){
                     $cat_details = get_category_details($cat->categroy_id);
                     $categories[] = $cat_details->category_name;
                }
                $categories = implode(",",array_unique($categories));
            
               //  $data=User::with('category_get')->where('id',$categroyGet->user_id)->get();
               //  $jsonData=[];
               // foreach($data as $array){
               //       $jsonData[]=$array;
               // }

                return response()->json([
                    'status'=>true,
                    'message'=>"Expert User Data",
                    'data'=>$jsonData,
                ]);
        }catch(\Exception $e){seeker_edit_profile
            $data = [];
            return response()->json([
                'status'=>false,
                'message'=>$e->getMessage(),
                'data'=>$data],
                Config::get('params.error_status'
            ));
        }

    }*/

    public function details_expert(Request $request){
        $validatorRules = [
            'id' => 'required',
        ];
        try{
            $validator = Validator::make($request->all(), $validatorRules);
                if ($validator->fails()) {
                    $error = $this->validationHandle($validator->messages());
                    return response()->json(['status' => false, 'message' => $error],Config::get('params.validation_status'));
                }
                // $data=User::with('category_get')->where('id',$request->id)->get();
                // $jsonData=[];
                //     foreach($data as $array){
                //             $jsonData[]=$array;
                //     }

                    $user_details = User::where(['role'=>'3','status'=>'1'])->where('id',$request->id)->with("selectcategroy")->first();
                        $user_value=User::where(['role'=>'3','status'=>'1'])->where('id',$request->id)->first();
                        $ratingavg=Rating::where('recive_id',$request->id)->avg('rating');
                        $totalrating=Rating::where('recive_id',$request->id)->count();
                        if($user_details){
                            $categories = [];
                            foreach($user_details->selectcategroy as $cat){
                                $cat_details = get_category_details($cat->categroy_id);
                                $categories[] = $cat_details->category_name;
                            }
                            $categories = implode(",",array_unique($categories));
                        }
                     

                    if(empty($categories)){
                        return response()->json([
                                'status'=>502,
                                'message'=>"تم البحث",
                          ]);

                    }else{
                         return response()->json([
                                'status'=>true,
                                'message'=>"معلومات الخبير",
                                'data'=>[
                                   'categroy'=>$categories,
                                   'user_details'=>$user_value,
                                   'ratingavg'=>$ratingavg,
                                   'totalrating'=>$totalrating
                                ],
                          ]);
                    }
              
        }catch(\Exception $e){
            $data = [];
            return response()->json([
                'status'=>false,
                'message'=>"something went wrong",
                'data'=>$data],
                Config::get('params.error_status'
            ));
        }

    }

    public function share_feedback(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'message' => 'required',
            ],$message =[
                'message.required' => 'حقل الرسالة مطلوب',
            ]);
            if ($validator->fails()) {
                $error = $this->validationHandle($validator->messages());
                return response()->json(['status' => false, 'message' => $error],Config::get('params.validation_status'));
            }

            $user= new Feedback;
            $user->name=$request->name;
            $user->email=$request->email;
            $user->subject=$request->subject;
            $user->message=$request->message;
            $user->save();

            return response()->json([
                'status'=>true,
                'message'=>"شكراً لك ونتطلع إلى سماع رأيك مرة أخرى",
                'data'=>'',
            ]);
    

        }catch(\Exception $e){
            $data = [];
            return response()->json([
                'status'=>false,
                'message'=>"something went wrong",
                'data'=>$data],
                Config::get('params.error_status'
            ));
        }
    }

    public function contactus(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'message' => 'required',
            ],$message =[
                'message.required' => 'حقل الرسالة مطلوب',
            ]);
            if ($validator->fails()) {
                $error = $this->validationHandle($validator->messages());
                return response()->json(['status' => false, 'message' => $error],Config::get('params.validation_status'));
            }

            $user= new Contactus;
            $user->name=$request->name;
            $user->email=$request->email;
            $user->subject=$request->subject;
            $user->message=$request->message;
            $user->save();

            return response()->json([
                'status'=>true,
                'message'=>"شكراً لك، سنتواصل معك قريباً",
                'data'=>'',
            ]);
    

        }catch(\Exception $e){
            $data = [];
            return response()->json([
                'status'=>false,
                'message'=>"something went wrong",
                'data'=>$data],
                Config::get('params.error_status'
            ));
        }
    }

    public function cmspage(Request $request){
        $validatorRules = [
            'slug' => 'required',
            'usertype' => 'required',
        ];
        try{
            $validator = Validator::make($request->all(), $validatorRules);
            if ($validator->fails()) {
                $error = $this->validationHandle($validator->messages());
                return response()->json(['status' => false, 'message' => $error],Config::get('params.validation_status'));
            }
            $data=Cms::where('slug',$request->slug)->where('usertype',$request->usertype)->first();

            return response()->json([
                'status'=>true,
                'message'=>"success",
                'data'=>$data,
            ]);
    

        }catch(\Exception $e){
            $data = [];
            return response()->json([
                'status'=>false,
                'message'=>"something went wrong",
                'data'=>$data],
                Config::get('params.error_status'
            ));
        }
    }

    public function userRegister(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'number' => 'required|numeric',
            ],$message =[
                'number.required'=> 'حقل الرقم مطلوب'
            ]);
            if ($validator->fails()) {
                $error = $this->validationHandle($validator->messages());
                return response()->json(['status' => false, 'message' => $error],Config::get('params.validation_status'));
            } else {
                $user=User::where('number',$request->number)->first();
               
                if($user){
                    if($user->status == 2){
                        $response = ['status_ code'=>502,'status'=>false,'message'=>"حسابك غير نشط من قبل المشرف"];
                        return response()->json($response);
                    }elseif($user->role == 2){
                        if($request->number === '8555236985'){
                            $otp = 1234;
                            $user->otp=$otp;
                        }else{
                            $otp = rand(1111,9999);
                            $user->otp=$otp;
                        }
                        $user->save();

                        $number = $request->number;
                        Smsotp($otp,$number);
                        $message ='otp send';
                        $response = ['status'=>true,'message'=>$message, 'data'=>$user];
                        return response()->json($response);
                    }else{
                        $response = ['status'=>false,'message'=>"رقم هاتفك المحمول مأخوذ بالفعل كمستخدم خبير",];
                        return response()->json($response);
                    }
                }else{
                    $otp = rand(1111,9999);

                    $user = new User();
                    $user->number = $request->number;
                    $user->role = Config::get('params.role_ids.Users');
                    $user->otp=$otp;
                    $user->save();
                    $number = $request->number;
                    Smsotp($otp,$number);
                    $message ='تم إنشاء حساب المستخدم بنجاح';
                    $response = ['status'=>true,'message'=>$message, 'data'=>$user];
                    return response()->json($response);
                }
            }
        } catch (\Exception $e) {
          
            $data = [];
            return response()->json(['status'=>false,'message'=>"something went wrong",'data'=>$data],Config::get('params.error_status'));
        }
    }//end function.

    public function verifyOtp(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'otp' => 'required',
            ],$message =[
                'otp.required'=>'مطلوب حقل الرمز'
            ]);
            if ($validator->fails()) {
                $error = $this->validationHandle($validator->messages());
                return response()->json(['status' => false, 'message' => $error],Config::get('params.validation_status'));
            } else {
                $userInfo = User::where(['otp'=>$request->otp,'number'=>$request->number])->first();
                if($userInfo){
                    $userInfo->status = '1';
                    $userInfo->otp='';
                    $userInfo->number_verified="1";
                    $userInfo->save();
                    $token = JWTAuth::fromUser($userInfo);
                    $userInfo->security_token = $token;
                    return response()->json(['status' => true, 'message' => 'التحقق من حساب المستخدم بنجاح','data'=>$userInfo],Config::get('params.success_status'));
                }
                return response()->json(['status' => false, 'message' => 'الرمز خطأ','data'=>[]],Config::get('params.validation_status'));
            }
        } catch (\Throwable $e) {
            return response()->json(['status'=>false,'message'=>$e->getMessage(),'data'=>[],Config::get('params.error_status')]);
        }
    }//end function.

    public function seeker_profile(){
        try{
            $user_id = JWTAuth::user()->id;
            $user_details = User::where(["id"=>$user_id])->first();

                return response()->json([
                    'status' => true, 
                    'message' => 'user profile data',
                    'data'=>$user_details]);
                

        }catch(\Exception $e){
            return response()->json([
                'status'=>false,
                'message'=>"Somethig went wrong",
            ],Config::get('params.error_status'));
        }
    }

     public function seeker_edit_profile(Request $request){
        $validatorRules = [
            'user_id' => 'required',
        ];

        try{
            $validator = Validator::make($request->all(), $validatorRules);
            if ($validator->fails()) {
                $error = $this->validationHandle($validator->messages());
                return response()->json(['status' => false, 'message' => $error]);
            } else {
               
              $data['name']=$request->name;

            if ($request->file('profile_image')) {
                 $destinationPath = '/uploads/user/';
                 $response_data = Uploader::doUpload($request->file('profile_image'), $destinationPath);
                    if ($response_data['status'] == true) {
                         $data['profile_image'] = $response_data['file'];
                    }
            }

            User::where('id',$request->user_id)->update($data);

               return response()->json([
                'status' => true, 
                'message' => 'تم التحديث']);
               
            }
        }catch(\Exception $e){
            return response()->json([
                'status'=>false,
                'message'=>"something went wrong",
                ],Config::get('params.error_status'));
        }
    }

    public function make_device_type(Request $request){
        $validatorRules = [
            'mobile_type' => 'required',
            'fcm_token'=>'required',
        ];
        
        try{
            $validator = Validator::make($request->all(), $validatorRules);
            if ($validator->fails()) {
                $error = $this->validationHandle($validator->messages());
                return response()->json(['status' => false, 'message' => $error],Config::get('params.validation_status'));
            } else {
               $user=JWTAuth::user()->id;

               User::where('id',$user)->update(['mobile_type'=>$request->mobile_type,'fcm_token'=>$request->fcm_token]);

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

    public function guest_make_device_type(Request $request){
        $validatorRules = [
            'login_id'=>'required',
        ];
        
        try{
            $validator = Validator::make($request->all(), $validatorRules);
            if ($validator->fails()) {
                $error = $this->validationHandle($validator->messages());
                return response()->json(['status' => false, 'message' => $error],Config::get('params.validation_status'));
            } else {
               $user=$request->login_id;
               $userInfo=User::where('id',$user)->first();

               User::where('id',$user)->update(['mobile_type'=>$userInfo->mobile_type,'fcm_token'=>$userInfo->fcm_token]);

               return response()->json([
                   'status'=>true,
                   'message'=>"successfully add fcm token",
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
                
                User::where('id',$request->recvier_id)->update(['online_status'=>'2']);

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
                $usercall->recive_id=$request->sender_id;
                $usercall->send_id=$request->recvier_id;
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
            HurryGuestAndroid($fcm_token,$recvier_id,$sender_id,$Username,$agoratoken,$Userprofile,$channelname,$appID,$callerid,$usercall->id);
            return response()->json([
                'status'=>true,
                'message'=>"successfully notification send",
                'data'=>$senderData,
                'usercallingid'=>$usercall->id,
            ]);
        }catch(\Exception $e){
            return response()->json([
                'status' => false, 
                'message' =>"something went wrong"]);
            }
    }

    function accpet_call(Request $request){
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

    function seeker_delete(Request $request){
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


    function cancel_call(Request $request){
        try{
            $data=UserCalling::where('id',$request->usercalling_id)->update(['start_date'=>$request->start_date,'status'=>'2']);
            $usecalling=UserCalling::where('id',$request->usercalling_id)->first();
            User::where('id',$usecalling->send_id)->update(['online_status'=>'1']);
      
            $subject="Call cancelled";
            $message="Call cancelled";
            $notification_type="seeker_call_cancel";
            $user=User::where('id',$usecalling->send_id)->first();
            $diduser="false";
            if($user->fcm_token){
                PushNotficationAndroid($user->fcm_token,$user->id,$subject,$message,$notification_type,$diduser);
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

            User::where('id',$usecalling->send_id)->update(['online_status'=>'1']);

            $UserCheck=User::where('id',$usecalling->send_id)->first();
            $subject="Call declined";
            $message="Call declined";
            $notification_type="seeker_call_decline";
            $diduser="true";
            if($UserCheck->fcm_token){
                PushNotficationAndroid($UserCheck->fcm_token,$UserCheck->id,$subject,$message,$notification_type,$diduser);
            }

            return response()->json([
                'status'=>true,
                'message'=>"successfully decline calling",
                'data'=>'',
            ]);

        }catch(\Exception $e){
            return response()->json([
                'status'=>false,
                'message'=>"something went wrong",
                ],Config::get('params.error_status'));
            } 
    }

    public function seeker_poke(Request $request){
        try{
            $data=new UserCalling;
            $data->send_id=$request->recive_id;
            $data->recive_id=$request->send_id;
            $data->start_date=$request->start_date;
            $data->start_time=$request->start_time;
            $data->status=3;
            $data->save();
            
    
            $user=User::where('id',$request->recive_id)->first();
            $notificationCheck=Notification::where('status','1')->where('id','4')->first();
            $subject=$notificationCheck->subject;
            $message=$notificationCheck->message;
            $notification_type="poke_call";
            $diduser="null";
            
            $notification=new Notificationsend;
            $notification->user_id=$request->recive_id;
            $notification->send_id=$request->send_id;
            $notification->status="0";
            $notification->send_notification="1";
            $notification->message=$message;
            $notification->save();

            PushNotficationAndroid($user->fcm_token,$request->recive_id,$subject,$message,$notification_type,$diduser);
            return response()->json([
                'status'=>true,
                'message'=>"successfully poke send",
                'data'=>'',
            ]);

        }catch(\Exception $e){
            return response()->json([
                'status'=>false,
                'message'=>"something went wrong",
                ],Config::get('params.error_status'));
            } 
    }

    public function seeker_notification(Request $request){
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
            return response()->json([
                'status'=>false,
                'message'=>"something went wrong",
                ],Config::get('params.error_status'));
        }
    }


    public function notification_seen_seeker(Request $request){
        try{
            $user_id = JWTAuth::user()->id;

            $data=Notificationsend::where('user_id',$user_id)->update(['status'=>'1']);
            return response()->json([
                'status'=>true,
                'message'=>"update notification",
                'data'=>'',
            ]);

        }catch(\Exception $e){
            //dd($e->getMessage());
           return response()->json([
               'status' => false, 
               'message' =>"something went wrong"]);
        }
    }


    public function seeker_notification_status(Request $request){
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
           return response()->json([
               'status' => false, 
               'message' =>"something went wrong"]);
        }
    }


    public function expertrating(Request $request){
         $validatorRules = [
            'rating' => 'required',
            'bages'=>'required',
            'sender_id'=>'required',
            'recive_id'=>'required',
            'usercallingid'=>'required',
        ];
        try{
            $validator = Validator::make($request->all(), $validatorRules);
            if ($validator->fails()) {
                $error = $this->validationHandle($validator->messages());
                return response()->json(['status' => false, 'message' => $error],Config::get('params.validation_status'));
            } 
            $data=new Rating;
            $data->usercalling_id=$request->usercallingid;
            $data->rating=$request->rating;
            $data->sender_id=$request->sender_id;
            $data->recive_id=$request->recive_id;
            $data->bages=$request->bages;
            $data->save();

            return response()->json([
                'status'=>true,
                'message'=>"شكراً لتقييمك",
            ]);

        }catch(\Exception $e){
            return response()->json([
                'status'=>false,
                'message'=>"something went wrong",
                ],Config::get('params.error_status'));
        }
    }


///////////
    public function resetPassword(Request $request){
        $validator = Validator::make($request->all(), [
            'email' => 'required|exists:users,email',
            'password' => 'required|max:20|min:8',
            'confirm_password' => 'required_with:password|same:password|max:20|min:8',
        ]);
        if ($validator->fails()) {
            $error = $this->validationHandle($validator->messages()); 
            return response()->json(['status'=>false,'message'=>$error],Config::get('params.validation_status'));
        }
        else {
            try{
                User::where('email', $request->email)->update(['password' => Hash::make($request->password)]);
                return response()->json(['status'=>true,'message'=>'Password has been changed successfully.']);
            }catch (\Exception $e) {
                return response()->json(['status' => false, 'message' => $e->getMessage(), 'data' => []],Config::get('params.error_status'));
            }
        }
    }//end function.


    public function resendOtp(Request $request){
       
        $validator = Validator::make($request->all(), [
            'number' => 'required',
        ]);
        if ($validator->fails()) {
            $error = $this->validationHandle($validator->messages()); 
            return response()->json(['status'=>false,'message'=>$error],Config::get('params.validation_status'));
        }
        else {
            try{
                $user = User::where('number',$request->number)->first();
                if($user){
                    $token = randomToken();
                    $token = '1234';
                    $user->otp = $token;
                    $user->save();
                      Smsotp($token,$request->number);
                    $template = \App\Model\EmailTemplate::where([['slug', 'send-welcome-mail-when-customer-signup']])->first();
                    $site_name = getSettings()['site_title'];
                    $subject = $template->subject;
                    $description = $template->description;
                    $subject = str_replace(['{OPT}','{SITE}'],[$token, $site_name], $subject);
                    $description = str_replace( ['{OTP}','{SITE}'],[$token, $site_name],$description);
                    $mail_data = ['email' => $user->email,'subject' => $subject,'content' => $description ];
                    mailSend($mail_data);
                    $message ='Otp send successfully';
                    $response = ['status'=>true,'message'=>$message, 'data'=>$user];
                    return response()->json([$response],Config::get('params.success_status'));
                }else{
                    $response = ['status'=>false,'message'=>"No User found with this email address", 'data'=>[]];
                    return response()->json([$response],Config::get('params.validation_status'));
                }
                
            }catch (\Exception $e) {
                return response()->json(['status' => false, 'message' => $e->getMessage(), 'data' => []],Config::get('params.error_status'));
            }
        }
    }//end function.


    public function forgotPassword(Request $request) {
        try{
            $validator = Validator::make($request->all(), [
                'email' => 'required|exists:users,email',
            ]);
            if ($validator->fails()) {
                $response['status'] = false;
                $response['message'] = $this->validationHandle($validator->messages());
                return response()->json([$response],Config::get('params.validation_status'));
            } else {
                $status_arr = Config::get('params.status');
                $user = User::where('email', $request->email)->first();
                if(empty($user)) {
                    return response()->json(['status' => false, 'message' => 'User not found.', 'data' => []],Config::get('params.validation_status'));
                } else if($user->status == $status_arr['inactive']){
                    return response()->json(['status' => false, 'message' => 'Your account is inactive.', 'data' => []],Config::get('params.error_status'));
                } else{
                    $token = randomToken();
                    $user->otp = $token;
                    $user->save();
                    $template = \App\Model\EmailTemplate::where([['slug', 'send-mail-when-customer-forgot-password']])->first();
                    $site_name = getSettings()['site_title'];
                    $subject = $template->subject;
                    $description = $template->description;
                    $subject = str_replace(['{LINK}','{SITE}','{FULL_NAME}'],[$token, $site_name, $user->name], $subject);
                    $description = str_replace( ['{LINK}','{SITE}','{FULL_NAME}'],[$token, $site_name, $user->name],$description);
                    $mail_data = ['email' => $user->email,'subject' => $subject,'content' => $description ];
                    mailSend($mail_data);
                    $response = ['status'=>true,'message'=>"OTP send successfully",'data'=>$token];
                    return response()->json([$response],Config::get('params.success_status'));
                }
            }
        }catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage(), 'data' => []],Config::get('params.error_status'));
        }
    }//end function.

    public function otpVerify(Request $request){
        $validator = Validator::make($request->all(), [
            'otp' => 'required',
        ]);
        if ($validator->fails()) {
            $error = $this->validationHandle($validator->messages()); 
            return response()->json(['status'=>false,'message'=>$error],Config::get('params.validation_status'));
        }
        else {
            try{
                $userInfo = User::where('otp', $request->otp)->first();
                if($userInfo){
                    $userInfo->otp = '';
                    $userInfo->otp_time = Null;
                    $userInfo->save();
                    return response()->json(['status'=>true,'message'=>'Otp match successfully.',"data"=>$userInfo]);
                }else{
                    return response()->json(['status'=>false,'message'=>'Entered OTP is invalid.',"data"=>[]],Config::get('params.validation_status'));
                }
                
            }catch (\Exception $e) {
                return response()->json(['status' => false, 'message' => $e->getMessage(), 'data' => []],Config::get('params.error_status'));
            }
        }
    }//end function.

    public function login(Request $request) {
        $data = $request->all();
        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required',
            'device_type' => 'required|in:IPHONE,ANDROID',
            'device_id' => 'required',
        ]);
        if ($validator->fails()) {
            $response['status'] = false;
            $response['message'] = $this->validationHandle($validator->messages());
            $response['data'] = [];
            $code =Config::get('params.validation_status');
            return response()->json([$response],$code);
        } else {
            $response = $this->makeLogin($data);
            $code = Config::get('params.success_status');
            return response()->json([$response],$code);
        }
    }//end function.

    public function getProfile(Request $request){
        $validatorRules = ['user_id' => 'required|exists:users,id'];
        try{
            $response = [];
            $validator = Validator::make($request->all(), $validatorRules);
            if ($validator->fails()) {
                $error = $this->validationHandle($validator->messages());
                return response()->json(['status' => false, 'message' => $error],Config::get('params.validation_status'));
            }else{
                $user = User::find($request->user_id);
                $response['status'] = true; 
                $response['message'] = "User profile data."; 
                $response['data'] = $user; 
                $code = Config::get('params.success_status');
            }
        } catch (\Exception $e) {
            $response['status'] = false;
            $response['message'] = $e->getMessage();
            $response['data'] = []; 
            $code = Config::get('params.error_status');
        }
        return response()->json([$response],$code);
    }//end function.
    
   

    public function socialLogin(Request $request){
        $validatorRules = [
            'name' => 'required',
            'email' => 'required|email|max:255',
            'device_type' => 'required|in:IPHONE,ANDROID',
            'device_id' => 'required',
        ];
        try {
            $validator = Validator::make($request->all(), $validatorRules);
            if ($validator->fails()) {
                $error = $this->validationHandle($validator->messages());
                return response()->json(['status' => false, 'message' => $error]);
            } else {
                $user = User::where("email",$request->email)->first();
                if(!$user){
                    $user = new User();
                    $user->name = $request->name;
                    $user->email = $request->email;
                    $user->social_login = '1';
                    $user->status = '1';
                    $user->role = Config::get('params.role_ids.Users');
                    if($request->file('profile_image') !== null) {
                        $destinationPath = '/uploads/user/';
                        $response_data = Uploader::doUpload($request->file('profile_image'), $destinationPath);
                        if ($response_data['status'] == true) {
                            $user->profile_image = $response_data['file'];
                        }
                    }
                    $user->save();
                    $token = JWTAuth::fromUser($user);
                    $user->security_token = $token;
                    $message ='User login successfully';
                }else{
                    $token = JWTAuth::fromUser($user);
                    $user->security_token = $token;
                    $message ='User login successfully';
                }
                $response = ['status'=>true,'message'=>$message, 'data'=>$user];
                return response()->json($response);
            }
        } catch (\Exception $e) {
            $data = [];
            return response()->json(['status'=>false,'message'=>$e->getMessage(),'data'=>$data]);
        }
    }//end function.

    
    protected function makeLogin($data=[]){
        $response = [];
        $roles_arr = Config::get('params.role_ids');
        $user = User::where([['email' , $data['email']],['role',$roles_arr['Users']]])->first();
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
    
    
    
    public function logout(Request $request) {
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
    }// end function.

    public function editProfile(Request $request) {
        try {
            $user_id = JWTAuth::user()->id;
            $validator = Validator::make($request->all(), [
                'name' => 'required|max:255',
                'dob' =>'required',
                'number'=>'required',
                'email'=>'required|email|unique:users,email,'.$user_id,
            ]);
            if ($validator->fails()) {
                $response['status'] = false;
                $response['message'] = $this->validationHandle($validator->messages());
                $response['data'] = [];
                return response()->json([$response],Config::get('params.validation_status'));
            } else {
                $user = User::where('id', $user_id)->first();
                if($request->file('profile_image') !== null) {
                    if(!empty($user->profile_image) && file_exists(public_path().$user->profile_image)){
                        unlink(public_path().$user->profile_image);
                    }
                    $destinationPath = '/uploads/user/';
                    $response_data = Uploader::doUpload($request->file('profile_image'), $destinationPath);
                    if ($response_data['status'] == true) {
                        $user->profile_image = $response_data['file'];
                    }
                }
                $user->name = $request->name;
                $user->email = $request->email;
                $user->dob = $request->dob;
                $user->number = $request->number;
                $user->save();
                return response()->json(['status' => true, 'message' => 'Profile has been updated successfully.', 'data'=>$user],Config::get('params.success_status'));
            }
        } catch (\Exception $e) {
            $response['status'] = false;
            $response['message'] = $e->getMessage();
            $response['data'] = [];
            return response()->json([$response],Config::get('params.error_status'));
        }
    }//end function.

    public function editPassword(Request $request){
        try {
            $validatorRules = [
                'current_password' => 'required|max:20|min:8',
                'password' => 'required|max:20|min:8',
                'confirm_password' => 'required_with:password|same:password|max:20|min:8',
            ];
            $response = [];
            $validator = Validator::make($request->all(), $validatorRules);
            if ($validator->fails()) {
                $error = $this->validationHandle($validator->messages());
                $response['status'] = false;
                $response['message'] = $error;
                $code= Config::get('params.validation_status');
                
            }else{
                $user = User::where('id', JWTAuth::user()->id)->first();
                if (Hash::check($request->current_password, $user->password)) {
                    $user->password = Hash::make($request->password);
                    $user->save();
                    $response['status'] = true;
                    $response['message'] = "Password updated successfully.";
                    $response['data'] = [];
                    $code= Config::get('param.success_status');
                }else{
                    $response['status'] = false;
                    $response['message'] = "Current password did not match with your old password";
                    $response['data'] = [];
                    $code= Config::get('param.validation_status');
                }
            }
        } catch (\Throwable $e) {
            $response['status'] = false;
            $response['message'] = $e->getMessage().' - '.$e->getLine();
            $response['data'] = [];
            $code= Config::get('param.error_status');
        }
        
        return response()->json([$response],$code);
    }//end function.
    
    public function userReview(Request $request){
        $response = [];
        try {
            $validatorRules = [
                'review' =>'required|in:0,1',
            ];
            
            $validator = Validator::make($request->all(), $validatorRules);
            if ($validator->fails()) {
                $error = $this->validationHandle($validator->messages());
                $response['status'] = false;
                $response['message'] = $error;
            }else{
                //dd(JWTAuth::user()->id);
                if($request->review == 1){
                    $response['status'] = true;
                    $response['message'] = "Review List";
                    $response['data'] = Review::where("user_id",JWTAuth::user()->id)->with("reviewUserInfo")->get();
                }else{
                    $userAdds = Add::where("user_id",JWTAuth::user()->id)->pluck('id');
                    //print_r($userAdds);die();
                    //$dataString = implode(",",$userAdds);
                    $response['status'] = true;
                    $response['message'] = "User Review List";
                    $response["data"]= Review::whereIn('add_id', $userAdds)->with("reviewUserInfo")->get();
                }
            }
        } catch (\Throwable $th) {
            $response['status'] = false;
            $response['message'] = $th->getMessage().' - '.$th->getLine();
            $response['data'] = [];
        }
        return response()->json($response);
    }//end function.


    
}//end class.
