<?php

namespace App\Model;
use Illuminate\Database\Eloquent\Model;

class Category extends Model  {
    protected $appends = ['category_image_full'];
    protected $fillable = ['title'];


    public function getCategoryImageFullAttribute() {
        if(!empty($this->category_image)) {
            return asset('public/'.$this->category_image);
        } else {
            return  null;
        }
    }

    public function selectcategroy(){
        return $this->hasMany('App\Model\UserCategroy','categroy_id','id');
    }

    
}
