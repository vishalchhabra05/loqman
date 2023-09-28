<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use App\Model\User;
use App\Model\Setting;
use \Config;
use DB;

use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    
    function validationHandle($validation) {
        foreach ($validation->getMessages() as $field_name => $messages) {
            if (!isset($firstError)) {
                $firstError = $messages[0];
            }
        }
       
        return $firstError;
    }// end function.

    function getNearByUser($userId){
        $userInfo  = User::where('id',$userId)->first();
        $searchRole = 3;
        if($userInfo->role == 3){
            $searchRole = 4;
        }
        $SettingKmData = Setting::where('slug','search_in_radius')->first();
        $filterKm = $SettingKmData->value;
        $users =  DB::table("users");
        $users =  $users->select(DB::raw("*, ( 6371 * acos( cos( radians('$userInfo->lat') ) * cos( radians( lat ) ) * cos( radians( lng ) - radians('$userInfo->lng') ) + sin( radians('$userInfo->lat') ) * sin( radians( lat ) ) ) ) AS distance"));
        $users = $users->having('distance', '<', $filterKm); 
        $users = $users->where('role', '!=' , $searchRole);
        $users = $users->orderBy('distance', 'asc');
        $users = $users->pluck('id')->toArray();
        return $users;
    }
    
}
