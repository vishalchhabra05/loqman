<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Model\Plans;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
//use \Config;

class User extends Authenticatable implements JWTSubject {
    //use Notifiable;
    protected $appends = ['profile_image_full'];
    protected $hidden = ['password'];
    protected $fillable = ['name', 'email','password','remember_token','status','role','number','dob','profile_image','otp','otp_time'];


    public function getJWTIdentifier() {
        return $this->getKey();
    }
    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims() {
        return [];
    }

    public function userCategoryInfos(){
        return $this->hasMany('App\Model\UserCategory', 'user_id');
    }
    
    public function ratings() {
        return $this->hasMany('App\Model\Review', 'user_id');
    }

    public function getAverageRatingAttribute() {
        return $this->ratings()->average('rating');
    }

    public function getProfileImageFullAttribute() {
        if(!empty($this->profile_image)) {
            return asset('public/'.$this->profile_image);
        } else {
            return  null;
        }
    }

    public function getAuthPassword()
    {
      return $this->password;
    }

    public function category_get(){
        return $this->hasMany('App\Model\Category','user_id','id');
    }

    public function selectcategroy(){
        return $this->hasMany('App\Model\UserCategroy','user_id','id');
    }

    public function CategroySelect(){
        return $this->belongsTo('App\Model\UserCategroy','id','user_id');
    }

    public function getselect(){
        return $this->hasMany('App\Model\UserCategroy','user_id','id');
    }

    public function getExpetname(){
        return $this->hasMany('App\Model\UserCalling','send_id','id');
    }

  

   

}
