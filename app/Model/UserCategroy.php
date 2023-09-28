<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class UserCategroy extends Model
{
    protected $table="user_categories";

    public function category_get(){
        return $this->hasMany('App\Model\Category','id','categroy_id');
    }

     public function category_getf(){
        return $this->belongsTo('App\Model\Category','categroy_id','id');
    }

    public function UsercategroyAssing(){
        return $this->belongsTo('App\Model\User','user_id','id');
    }
}
