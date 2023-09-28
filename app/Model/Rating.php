<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    protected $table="userrating";

    public function Getratinguser(){
        return $this->hasOne('App\Model\User','id','sender_id');
    }

    public function Getexpetratinguser(){
        return $this->hasOne('App\Model\User','id','recive_id');
    }
}
