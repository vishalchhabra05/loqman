<?php
namespace App\Lib;
use Illuminate\Database\Eloquent\Model;
use Image;
use Illuminate\Support\Facades\Storage;
use File;
use Imagine\Image\Box;
use Imagine\Gd\Imagine;
/**
 * 
 * 
 * This Library use for image upload and resizing.
 *  
 * 
 **/

class Uploader
{
    
    public static function doUpload($file,$path,$pre=false){
        $response = [];
        $image = $file;
        //dd($image->getClientOriginalExtension());
        //$file = $pre.time().rand(100000,10000000).'.'.$image->getClientOriginalExtension();
        $file = $pre.time().rand(100000,10000000).'.'.$image->getClientOriginalExtension();
        $destinationPath = public_path().'/'.$path;
        if(!is_dir($destinationPath)) {
            @\File::makeDirectory($destinationPath, 0777, true);
        }
        if($uploaded = $image->move($destinationPath, $file)){
            $response['status']     = true;
            $response['file']       = $path.$file;
            $response['file_name']  = $file;
            $response['path']       = $path;
        }
        else{
            
            $response['status']     = false;
        }
        return $response;

    }
    public static function doUploadWeb($file,$path,$pre=false, $is_thumb=false, $thumb_size=['w'=>200,'h'=>200]){
        $response['status']     = false;
        $image = $file;
        $file = $pre.time().rand(100000,10000000).'.'.$image->getClientOriginalExtension();
        $destinationPath = public_path().$path;
        if(!is_dir($destinationPath)) {
            @\File::makeDirectory($destinationPath, 0777, true);
        }
        if($uploaded = $image->move($destinationPath, $file)){
            $response['status']     = true;
            $response['file']       = $path.$file;
            $response['file_name']  = $file;
            $response['path']       = $path;
            if($is_thumb){
                
            }
        }
        
        return $response;

    }
    
    
    public static function imgaeResizeCustom($size=[], $file_exist_path='', $file_save_folder=''){
        
        $file_path = $file_path_copy = public_path().$file_exist_path;
        
        $file_path_exp = explode('/', $file_path_copy);
        $file_name = end($file_path_exp);
        array_pop($file_path_exp);
        $file_path_name = implode('/', $file_path_exp);
        $file_save_folder = $file_path_name.'/'.$file_save_folder;
        $file_path_save = $file_save_folder.'/'.$file_name;
        if(!is_dir($file_save_folder)){
            @\File::makeDirectory($file_save_folder, 0777, true);
        }
        $imagine = new Imagine();
        $image = $imagine->open($file_path);
        $image->resize(new Box(70,70))
        ->save($file_path_save, ['quality' => 100]);
        
        return true;
    }
    
    public static function saveImageFromUrl($image_url, $path, $pre=''){
        $response = [];
        $file = $pre.time().rand(100000,10000000).'.jpg';
        $save_path = public_path().$path.$file;
        @copy($image_url, $save_path);
        
        $response['status'] = true;
        $response['file'] = $path.$file;
        $response['file_name'] = $file;
        $response['path'] = $path;
        
        return $response;
        
        /*
        $ch = curl_init($image_url);
        $fp = fopen($save_path, 'wb');
        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_exec($ch);
        curl_close($ch);
        fclose($fp);
        return true;
        */
    }
}
