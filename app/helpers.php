<?php
use App\Model\User;
use App\Model\UserDevice;
use App\Model\FavtAdd;
use App\Model\Category;
use App\Model\UserCategroy;
use App\Model\UserCalling;
use App\Model\Rating;
use App\Model\Notification;
use App\Classes\AgoraDynamicKey\RtcTokenBuilder;

if (!function_exists('listDateFromat')) {
    function listDateFromat($date){
        return date('d-M-Y H:i A',strtotime($date));
    }// end function.
}

if (!function_exists('getUserImage')) {
    function getUserImage($id){
        $userData = User::where("id",$id)->first();
        return $userData->profile_image_full;
    }// end function.
}

if (!function_exists('manageDevices')) {
    function manageDevices($user_id, $device_id, $device_type, $methodName) {
        if ($methodName == 'add') {
            UserDevice::updateOrCreate(
                    ['user_id' => $user_id, 'device_id' => $device_id, 'device_type' => $device_type]
            );
        } else if ($methodName == 'delete') {
            UserDevice::where('user_id', $user_id)
                    ->where('device_id', $device_id)
                    ->where('device_type', $device_type)
                    ->delete();
        }
        return true;
    }

}

function haversineGreatCircleDistance($latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo, $earthRadius = 3959){
    // convert from degrees to radians
    $latFrom = deg2rad($latitudeFrom);
    $lonFrom = deg2rad($longitudeFrom);
    $latTo = deg2rad($latitudeTo);
    $lonTo = deg2rad($longitudeTo);
  
    $latDelta = $latTo - $latFrom;
    $lonDelta = $lonTo - $lonFrom;
  
    $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
      cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
    return $angle * $earthRadius;
  }

if (!function_exists('mailSend')) {

    function mailSend($data) {
        try {
            $site_title = Config('params.site_title');
            $from_email = Config::get('params.mail_username');
            $subject = !empty($data['subject']) ? $data['subject'] : '';
            if (is_array($data['email']) && count($data['email']) > 1) {
                foreach ($data['email'] as $email) {
                    Mail::send('emails.all_mail', ['data' => $data], function($message) use ($data, $from_email, $subject, $site_title) {
                        $message->from($from_email, $site_title);
                        $message->to($email)->subject($subject);
                    });
                }
            } else {
                Mail::send('emails.all_mail', ['data' => $data], function($message) use ($data, $from_email, $subject, $site_title) {
                    $message->from($from_email, $site_title);
                    $message->to($data['email'])->subject($subject);
                });
            }
            return true;
        } catch (Exception $ex) {
            return false;
        }
    }

}

if (!function_exists('getListImage')) {
    function getListImage($image_path) {
        $html ='';
        if(!empty($image_path)){
            $html = '<img src="'.$image_path.'" width=50 />';
        }
        return $html;
    }
}

/*
 * * Button With Html
 */
if (!function_exists('getButtons')) {

    function getButtons($array = []) {
        $html = '';
        foreach ($array as $arr) {
            $html .= buttonHtml($arr['key'], $arr['link']);
        }
        return $html;
    }

}

function get_category_details($cat_id){
    return Category::where(["id"=>$cat_id])->first();
}

function get_category_value($cat_id){
    return Category::where(["id"=>$cat_id])->get();
}

if (!function_exists('getStatus')) {

    function getStatus($current_status, $id) {
        $html = '';
        switch ($current_status) {
            
            case '0':
                $html = '<span class="f-left margin-r-5" id = "status_' . $id . '"><a data-toggle="tooltip"  class="btn btn-danger btn-xs" title="Pending" onClick="changeStatus(' . $id . ')" >Pending</a></span>';
                break;
            case '1':
                $html = '<span class="f-left margin-r-5" id = "status_' . $id . '"><a data-toggle="tooltip"  class="btn btn-success btn-xs" title="Active" onClick="changeStatus(' . $id . ')" >Active</a></span>';
                break;
            case '2':
                $html = '<span class="f-left margin-r-5" id = "status_' . $id . '"><a data-toggle="tooltip"  class="btn btn-danger btn-xs" title="Inactive" onClick="changeStatus(' . $id . ')" >Inactive</a></span>';
                break;

            default:

                break;
        }

        return $html;
    }


if (!function_exists('buttonHtml')) {
    function buttonHtml($key, $link) {
        $array = [
            "edit" => "<span class='f-left margin-r-5'><a data-toggle='tooltip'  class='btn btn-primary btn-xs' title='Edit' href='" . $link . "'><i class='fa fas fa-edit ' aria-hidden='true'></i></a></span>",
            "view" => "<span class='f-left margin-r-5'><a data-toggle='tooltip'  class='btn btn-primary btn-xs' title='View' href='" . $link . "' ><i class='fa fas fa-eye' aria-hidden='true'></i></a></span>",
            "delete" =>"<span class='f-left margin-r-5'><form style='display: inline-block;' title='Trash' action='$link' method='POST' onsubmit=' return confirmDelete(event,this);'><input type='hidden' name='_method' value='DELETE'><input type='hidden' name='_token' value='".csrf_token()."'><button type='submit' class='btn btn-danger btn-xs'><i class='fa fas fa-trash' aria-hidden='true'></i></button></form></span>",
        ];
        if (isset($array[$key])) {
            return $array[$key];
        }
        return '';
    }

}

if (!function_exists('getSettings')) {
    function getSettings() {
        return \App\Model\Setting::pluck('value', 'slug')->toArray();
    }
}

if (!function_exists('getCategory')) {
    function getCategory() {
        return \App\Model\Category::pluck('category_image', 'id')->toArray();
    }
}

if (!function_exists('getFavtProd')) {
    function getFavtProd($id, $user_id) {
        $favtCount=FavtAdd::where('user_id',$user_id)->where('add_id',$id)->count();
        if(0 < $favtCount){
            return true;
        }else{
            return false;
        }
    }
}


if (!function_exists('randomToken')) {
    function randomToken() {
        $token =rand(1111, 9999);
        //$token = base64_encode($token);
        return $token;
    }
}

if(!function_exists('Smsotp')){
 function Smsotp($otp=NULL,$number){
    $password = 'Bh0164$577';
    $email    = 'Aazerapp@gmail.com';
    $otp      = 'الرقم السري لمرة واحدة للدخول إلى تطبيق لقمان هو'.' '.$otp;
    $number  = '+966'.$number;
    $url      = 'http://el.cloud.unifonic.com/wrapper/sendSMS.php?userid='.$email.'&password='.$password.'&msg='.$otp.'&sender=Dosor&to='.$number.'';
    $client = new \GuzzleHttp\Client();
    $res = $client->get($url);
    return $res;
 }
 
}
//Push notification//

function HurryAndroid($fcm_token, $recvier_id,$sender_id,$Username,$agoratoken,$Userprofile,$channelname,$appID,$callerid,$UserCalling){
        $url = "https://fcm.googleapis.com/fcm/send";
        $token = $fcm_token;
        $user_id=$recvier_id;
        $serverKey="AAAAO9eiX2Q:APA91bHyM2nNqxcL46YXsPZRhZbdKoj2G_bEp218Erm5HHy3KnV9IfC3LtKXBGSKbW2GK5VBL8RpDG6ai7-RYIAVKVwG1v-rRjfTeOeueseSmo8cFd_4LgKZmi7iV74q9faTdSj_-TwN";
        $notificationCheck = Notification::where('status','1')->where('id','3')->first();
        $message = $notificationCheck->message;
        $payload = array (
            "message" => $message,
            "reciver_id" =>$user_id,
            "sender_id"=>$sender_id,
            "agoratoken"=>$agoratoken,
            "AppId"=>$appID,
            "ChannelName"=>$channelname,
            "Channel_id"=>$channelname,
            "Name"=>$Username,
            "Image"=>$Userprofile,
            "caller_id"=>$callerid,
            "title"=>"call",
            "Usercallingid"=>$UserCalling,
            'notificationType' => 'expert_call_back'
        );
        $body = $message;
        // $notification = array('text' => $body, 'sound' => 'default', 'badge' => '1','notificationType' => 'hurry_sessions');
        $notification = array('body' => $message, 'sound' => 'default');
        $arrayToSend = array('to' => $token, 'notification' => $notification,'priority'=>'high','notificationType'=>'hurry_sessions','data'=>$payload);
        $json = json_encode($arrayToSend);
        $headers = array();
        $headers[] = 'Content-Type: application/json';
        $headers[] = 'Authorization: key='. $serverKey;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST,
        "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
        curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        //Send the request
        $response = curl_exec($ch);
        //Close request
        if ($response === FALSE) {
        // die('FCM Send Error: ' . curl_error($ch));
        }
        curl_close($ch);
        return $response;
}

function HurryGuestAndroid($fcm_token, $recvier_id,$sender_id,$Username,$agoratoken,$Userprofile,$channelname,$appID,$callerid,$UserCalling){
    $url = "https://fcm.googleapis.com/fcm/send";
    $token = $fcm_token;
    $user_id=$recvier_id;
    $serverKey="AAAAO9eiX2Q:APA91bHyM2nNqxcL46YXsPZRhZbdKoj2G_bEp218Erm5HHy3KnV9IfC3LtKXBGSKbW2GK5VBL8RpDG6ai7-RYIAVKVwG1v-rRjfTeOeueseSmo8cFd_4LgKZmi7iV74q9faTdSj_-TwN";
    $notificationCheck = Notification::where('status','1')->where('id','5')->first();
    $message = $notificationCheck->message;
    $payload = array (
        "message" => $message,
        "reciver_id" =>$user_id,
        "sender_id"=>$sender_id,
        "agoratoken"=>$agoratoken,
        "AppId"=>$appID,
        "ChannelName"=>$channelname,
        "Channel_id"=>$channelname,
        "Name"=>$Username,
        "Image"=>$Userprofile,
        "caller_id"=>$callerid,
        "title"=>"call",
        "Usercallingid"=>$UserCalling,
        'notificationType' => 'hurry_sessions'
    );
    $body = $message;
    // $notification = array('text' => $body, 'sound' => 'default', 'badge' => '1','notificationType' => 'hurry_sessions');
    $notification = array('body' => $message, 'sound' => 'default');
    $arrayToSend = array('to' => $token, 'notification' => $notification,'priority'=>'high','notificationType'=>'hurry_sessions','data'=>$payload);
    $json = json_encode($arrayToSend);
    $headers = array();
    $headers[] = 'Content-Type: application/json';
    $headers[] = 'Authorization: key='. $serverKey;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST,
    "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
    curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    //Send the request
    $response = curl_exec($ch);
    //Close request
    if ($response === FALSE) {
    // die('FCM Send Error: ' . curl_error($ch));
    }
    curl_close($ch);
    return $response;
}

function PushNotficationAndroid($fcm_token, $recvier_id, $subject, $message, $notification_type,$diduser){
    $url = "https://fcm.googleapis.com/fcm/send";
    $token=$fcm_token;
    $user_id=$recvier_id;
    $serverKey="AAAAO9eiX2Q:APA91bHyM2nNqxcL46YXsPZRhZbdKoj2G_bEp218Erm5HHy3KnV9IfC3LtKXBGSKbW2GK5VBL8RpDG6ai7-RYIAVKVwG1v-rRjfTeOeueseSmo8cFd_4LgKZmi7iV74q9faTdSj_-TwN";
    $payload = array (
        "subject"=> $subject,
        "message" => $message,
        "user_id" =>$user_id,
        "did_user_join"=>$diduser,
        'notificationType' =>$notification_type,
    );
    $body = $message;
    // $notification = array('text' => $body, 'sound' => 'default', 'badge' => '1','notificationType' => 'hurry_sessions');
    $notification = array('body' => $message, 'sound' => 'default');
    $arrayToSend = array('to' => $token, 'notification' => $notification,'priority'=>'high','notificationType'=>'hurry_sessions','data'=>$payload);
    $json = json_encode($arrayToSend);
    $headers = array();
    $headers[] = 'Content-Type: application/json';
    $headers[] = 'Authorization: key='. $serverKey;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST,
    "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
    curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    //Send the request
    $response = curl_exec($ch);
    //Close request
    if ($response === FALSE) {
    // die('FCM Send Error: ' . curl_error($ch));
    }
    curl_close($ch);
    return $response;
}

function ExpertpushAndroid($fcm_token, $recvier_id, $subject, $message, $notification_type,$diduser, $reciveid,$usercalingid){
    $url = "https://fcm.googleapis.com/fcm/send";
    $token=$fcm_token;
    $user_id=$recvier_id;
    $serverKey="AAAAO9eiX2Q:APA91bHyM2nNqxcL46YXsPZRhZbdKoj2G_bEp218Erm5HHy3KnV9IfC3LtKXBGSKbW2GK5VBL8RpDG6ai7-RYIAVKVwG1v-rRjfTeOeueseSmo8cFd_4LgKZmi7iV74q9faTdSj_-TwN";
    $payload = array (
        "subject"=> $subject,
        "message" => $message,
        "user_id" =>$user_id,
        "did_user_join"=>$diduser,
        'notificationType' =>$notification_type,
        'usercallingId'=>$usercalingid,
        'recive_id'=>$reciveid,
    );
    $body = $message;
    // $notification = array('text' => $body, 'sound' => 'default', 'badge' => '1','notificationType' => 'hurry_sessions');
    $notification = array('body' => $message, 'sound' => 'default');
    $arrayToSend = array('to' => $token, 'notification' => $notification,'priority'=>'high','notificationType'=>'hurry_sessions','data'=>$payload);
    $json = json_encode($arrayToSend);
    $headers = array();
    $headers[] = 'Content-Type: application/json';
    $headers[] = 'Authorization: key='. $serverKey;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST,
    "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
    curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    //Send the request
    $response = curl_exec($ch);
    //dd($response);
    //Close request
    if ($response === FALSE) {
    // die('FCM Send Error: ' . curl_error($ch));
    }
    curl_close($ch);
    return $response;
}

function Getratings($id){
   $data=Rating::where('usercalling_id',$id)->first();
   return $data;
}

/*function HurryIos($name,$deviceId){
     $passphrase = "123456";
     // $liveMode = TRUE;
     $url ='ssl://gateway.push.apple.com:2195';
     // Put your alert message here:
     //$message = 'Driver accept your request He will come soon!';
     $ctx = stream_context_create();
     stream_context_set_option($ctx, 'ssl', 'local_cert', $_SERVER['DOCUMENT_ROOT'].'/loqman-app/loqmanDeve.pem');
     stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase);
     // Open a connection to the APNS server
     $fp = stream_socket_client($url, $err, $errstr, 60, STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT, $ctx);
     if (!$fp) {
         echo "Failed to connect: {$err} {$errstr}" . PHP_EOL;
         die;
     } else {
         // Create the payload body
         $body['aps'] = array('badge' => $badge, 'alert' => (string) $alert, 'sound' => "default");
         // Encode the payload as JSON
         $payload = json_encode($body);
         // Build the binary notification
         $msg = chr(0) . pack('n', 32) . pack('H*', $device_token) . pack('n', strlen($payload)) . $payload;
         // echo $device_token;die;
         // Send it to the server
         $result = fwrite($fp, $msg, strlen($msg));
         fclose($fp);
     }
}*/

function HurryIos($name,$deviceToken){
 
    /* We are using the sandbox version of the APNS for development. For production
        environments, change this to ssl://gateway.push.apple.com:2195 */
        $apnsServer = 'ssl://api.push.apple.com:443';
        /* Make sure this is set to the password that you set for your private key
        when you exported it to the .pem file using openssl on your OS X */
        $privateKeyPassword = '1234';
        /* Put your own message here if you want to */
        $message = 'Welcome to iOS 7 Push Notifications';
        /* Pur your device token here */
        $deviceToken =$deviceToken;
        /* Replace this with the name of the file that you have placed by your PHP
        script file, containing your private key and certificate that you generated
        earlier */
        $pushCertAndKeyPemFile = 'notification.pem';
        $stream = stream_context_create();
        stream_context_set_option($stream,
        'ssl',
        'passphrase',
        $privateKeyPassword);
        stream_context_set_option($stream,
        'ssl',
        'local_cert',
        $pushCertAndKeyPemFile);

        $connectionTimeout = 20;
        $connectionType = STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT;
        $connection = stream_socket_client($apnsServer,
        $errorNumber,
        $errorString,
        $connectionTimeout,
        $connectionType,
        $stream);
        if (!$connection){
        echo "Failed to connect to the APNS server. Error no = $errorNumber<br/>";
        exit;
        } else {
        echo "Successfully connected to the APNS. Processing...</br>";
        }
        $messageBody['aps'] = array('alert' => $message,
        'sound' => 'default',
        'badge' => 2,
        );
        $payload = json_encode($messageBody);
        $notification = chr(0) .
        pack('n', 32) .
        pack('H*', $deviceToken) .
        pack('n', strlen($payload)) .
        $payload;
        $wroteSuccessfully = fwrite($connection, $notification, strlen($notification));
        //dd($wroteSuccessfully);
        if (!$wroteSuccessfully){
        echo "Could not send the message<br/>";
        }
        else {
        echo "Successfully sent the message<br/>";
        }
        fclose($connection);
    /*$passphrase = '';
    
    // Put your alert message here:
    $message = 'My first push notification!';
    
    
    
    $ctx = stream_context_create();
    stream_context_set_option($ctx, 'ssl', 'local_cert', $_SERVER['DOCUMENT_ROOT'].'/loqman-app/notification.pem'); // Replace with your pem file
    stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase);
    
    // Open a connection to the APNS server
    $fp = stream_socket_client(
    //  'ssl://gateway.push.apple.com:2195', $err,
        'ssl://api.push.apple.com:443', $err,
        $errstr, 60, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx);
    
    if (!$fp)
        exit("Failed to connect: $err $errstr" . PHP_EOL);
    
    echo 'Connected to APNS' . PHP_EOL;
    
    // Create the payload body
    
    
    
    $body = 
        [
           'aps' => 
               [
                 'content-available'=> 1,
                 'apns-push-type'=>'background',
                 'apns-expiration' => 0
               ],
            'data' =>
                [
                    'uuid'=> $deviceToken, //uuid must be uuid format otherwise it manually created
                    'name' => 'RNVoip',
                    'handle' => '123213782123', // phone number 
                    'hasVideo' => true , // u are trying to audio call. please remove hasVideo key and value 
                    'handleType' => 'generic' // options are `generic`, `number` and `email`
                ]
        
        ];
                      
    
    // Encode the payload as JSON
    
    $payload = json_encode($body);
    
    // Build the binary notification
    $msg = chr(0) . pack('n', CRC32(32)) . pack('H*', CRC32($deviceToken)) . pack('n', strlen($payload)) . $payload;


    
    
    // Send it to the server
    $result = fwrite($fp, $msg, strlen($msg));

    
    if (!$result)
        $data= 'Message not delivered' . PHP_EOL;
    else
    $data= 'Message successfully delivered'.$deviceToken . PHP_EOL;
   
    echo $data; die;
    // Close the connection to the server
    fclose($fp);*/
}
/* function HurryIos($name,$deviceId)
   {

        $passphrase = '';
      
        $message = 'Hurry Up seats has been  filled up Join Now!!';
        $payload = array (
           "message" => $message,
            "Title" => $name,
           'notificationType' => 'hurry_sessions'
        );

        $deviceToken = "" . $deviceId . "";
        
        $ctx = stream_context_create();
        stream_context_set_option($ctx, 'ssl', 'local_cert', $_SERVER['DOCUMENT_ROOT'].'/notification.pem');
        stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase);
     
        $fp = stream_socket_client('ssl://api.push.apple.com:443', 
            $err, 
            $errstr, 
            60, 
            STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, 
            $ctx);
       

            if(!$fp) {
                echo "ERROR $errcode: $errstr\n";
                return;
            }
       
       //echo 'Connected to APNS' . PHP_EOL;
        
       // Create the payload body
       $body['aps'] = array(
            'badge' => +1,
            "alert"=> array(
                    "title"=> $name,
                    "body"=> $message
               ),
           "data"=> $payload,
           'sound' => 'default',
            'content-available' => 1,
            'mutable-content' => 1,
        );
       
        
        // $body['aps'] = $fields;
        
        $payload = json_encode($body);
        
        // Build the binary notification
        $msg = chr(0) . pack('n', 32) . pack('H*', $deviceToken) . pack('n', strlen($payload)) . $payload;
        
        // Send it to the server
        $result = @fwrite($fp, $msg, strlen($msg));
       if (!$result){
          return 'Message not delivered '  .$deviceToken. PHP_EOL;
        }else{
            return 'Message successfully delivered '.$deviceToken. PHP_EOL;
        }
       
        // Close the connection to the server
        fclose($fp);          
} */


function CategroyCheck($user_id){
    return UserCategroy::where('user_id',$user_id)->get();
}


}
?>