<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\User;
use Illuminate\Support\Facades\Validator;
use App\Classes\AgoraDynamicKey\RtcTokenBuilder;
use App\Model\UserCalling;
use App\Model\Rating;
use App\Model\Notificationsend;
use App\Model\Notification;
use App\Model\Bages;
use Config;

class GuestuserController extends Controller
{
    public function guestLoing(Request $request){
        $validatorRules = [
            'device_id' => 'required',
            'fcm_token'=>'required',
        ];
        
        try{
            $validator = Validator::make($request->all(), $validatorRules);
            if ($validator->fails()) {
                $error = $this->validationHandle($validator->messages());
                return response()->json(['status' => false, 'message' => $error],Config::get('params.validation_status'));
            } else {
               if(User::where('name',$request->device_id)->exists()){
                  $data=User::where('name',$request->device_id)->first();

               }else{
                   $data=new User;
                   $data->name=$request->device_id;
                   $data->status="1";
                   $data->role="4";
                   $data->fcm_token=$request->fcm_token;
                   $data->save();

                   $data=User::where('name',$request->device_id)->first();
               }
               return response()->json([
                       'status'=>true,
                       'message'=>"guest user successfully add",
                       'data'=>$data,
                    ]);
               
            }
        }catch(\Exception $e){
            return response()->json([
                'status'=>false,
                'message'=>"something went wrong",
                ],Config::get('params.error_status'));
        }
    }

    public function guestusercall(Request $request){
        try{
                $sender_id=$request->guest_id;
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
                $usercall->recive_id=$request->guest_id;
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
                  
                  //HurryIos($user->name,"472f626e667f20248bb16f1554fc893cc2ab4863365cf29741e8693545390b8b"); 
                  HurryGuestAndroid($fcm_token,$recvier_id,$sender_id,$Username,$agoratoken,$Userprofile,$channelname,$appID,$callerid, $usercall->id);
                return response()->json([
                    'status'=>true,
                    'message'=>"successfully notification send",
                    'data'=>$senderData,
                    'usercallingid'=>$usercall->id,
                ]);
        }catch(\Exception $e){
            dd($e->getMessage());
            return response()->json([
                'status'=>false,
                'message'=>"something went wrong",
                ],Config::get('params.error_status'));
        }
    }

    function guestcancelcall(Request $request){
        try{
            $data=UserCalling::where('id',$request->usercalling_id)->update(['start_date'=>$request->start_date,'status'=>'2']);
            $usecalling=UserCalling::where('id',$request->usercalling_id)->first();
            User::where('id',$usecalling->send_id)->update(['online_status'=>'1']);

            $subject="Call cancelled";
            $message="Call cancelled";
            $notification_type="guest_call_cancel";
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

    function guestdeclinecall(Request $request){
        try{
            UserCalling::where('id',$request->usercalling_id)->update(['end_time'=>$request->end_time]);

            $usecalling=UserCalling::where('id',$request->usercalling_id)->first();

            User::where('id',$usecalling->send_id)->update(['online_status'=>'1']);

            $UserCheck=User::where('id',$usecalling->send_id)->first();

            $subject="Call declined";
            $message="Call declined";
            $notification_type="guest_call_decline";
            $diduser="true";
            if($UserCheck->fcm_token){
                PushNotficationAndroid($UserCheck->fcm_token,$UserCheck->id,$subject,$message,$notification_type,$diduser);
            }
            return response()->json([
                'status'=>true,
                'message'=>"تم الرفض",
                'data'=>'',
            ]);

        }catch(\Exception $e){
            return response()->json([
                'status'=>false,
                'message'=>"something went wrong",
                ],Config::get('params.error_status'));
        }
    }

    function guestaccpetcall(Request $request){
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


    function guestuserPoke(Request $request){
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
            $notification_type="poke guest call";
            $diduser="null";

            $notification=new Notificationsend;
            $notification->user_id=$request->recive_id;
            $notification->status="0";
            $notification->message=$message;
            $notification->save();

            PushNotficationAndroid($user->fcm_token,$request->recive_id,$subject,$message,$notification_type,$diduser);
            return response()->json([
                'status'=>true,
                'message'=>"تم إرسال طلب اتصال",
                'data'=>'',
            ]);
        }catch(\Exception $e){
            return response()->json([
                'status'=>false,
                'message'=>"something went wrong",
                ],Config::get('params.error_status'));
        }
    }

    function bages(Request $request){
        try{
            $data=Bages::all();
            return response()->json([
                'status'=>true,
                'message'=>"bages",
                'data'=>$data,
            ]);
        }catch(\Exception $e){
            return response()->json([
                'status'=>false,
                'message'=>"something went wrong",
                ],Config::get('params.error_status'));
        }
    }

    function userrating(Request $request){
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
            dd($e->getMessage());
            return response()->json([
                'status'=>false,
                'message'=>"something went wrong",
                ],Config::get('params.error_status'));
        }
    }


    function guset_notification_list(Request $request){
        try{
            if(Notificationsend::where('user_id',$request->user_id)->exists()){
                $data=Notificationsend::where('user_id',$request->user_id)->orderBy('id','DESC')->get();
            }else{
                $data="";
            }
            return response()->json([
                'status'=>true,
                'message'=>"successfully notification get",
                'data'=>$data,
            ]);

        }catch(\Exception $e){
            dd($e->getMessage());
            return response()->json([
                'status'=>false,
                'message'=>"something went wrong",
                ],Config::get('params.error_status'));
        }
    }


    function notification_status(Request $request){
        try{
            $data=Notificationsend::where('user_id',$request->user_id)->orderBy('id','DESC')->first();
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
                'message'=>"successfully notification get",
                'data'=>$status,
            ]);

        }catch(\Exception $e){
            echo $e->getMessage(); die;
            return response()->json([
                'status'=>false,
                'message'=>"something went wrong",
                ],Config::get('params.error_status'));
        }
    }

    function notification_update(Request $request){
        try{

            $data=Notificationsend::where('user_id',$request->user_id)->update(['status'=>'1']);
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
}
