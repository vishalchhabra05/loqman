<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class UserCalling extends Model
{
    protected $table="usercalling";

    public function GetRatings(){
        return $this->hasOne('App\Model\Rating','usercalling_id','id');
    }

    public function Getsenduser(){
        return $this->hasOne('App\Model\User','id','send_id');
    }

    public function Getreciveuser(){
        return $this->hasOne('App\Model\User','id','recive_id');
    }
}
