<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use \Config;

class Setting extends Model
{
    
    protected $fillable = [
        'site_title'
    ];
    
    public static function getSettings(){
        $setting_id = Config::get('params.setting_id');
        $setting_data = Setting::find($setting_id)->toArray();
        return $setting_data;
    }// end function.
}
