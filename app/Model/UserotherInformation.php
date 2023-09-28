<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class UserotherInformation extends Model
{
    protected $table="userother_information";

    public function UserInformation(){
        return $this->hasOne(User::class,'id','user_id');
    } 
}
