<?php

namespace App\Http\Controllers\Front;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\User;
use App\Model\Add;
use App\Model\AddImage;
use App\Model\Category;
use App\Model\SubCategory;
use App\Model\Review;
use App\Model\UserReview;
use App\Model\ContactUs;
use App\Model\FavtAdd;
use DB;
use Illuminate\Support\Facades\Input;
use Session;
use Illuminate\Support\Facades\Validator;
use Config;
use Illuminate\Support\Facades\Auth;
use App\Lib\Uploader;
use App\Model\Pages;
use Illuminate\Support\Facades\Hash;

class ProductController extends Controller {

    public function details($id){
        $pageTitle = "Blinkers - Product Details";
        $data = Add::where('id',$id)->with("addImages")->with("addUserInfo")->first();
        $productUserReview = UserReview::where("to_user_id", $data->user_id)->with("fromUserInfo")->get();
        $relatedAds =  Add::where('id','!=',$id)->where("category_id",$data->category_id)->with("addImages")->with("addUserInfo")->get();
        $totalReview = Review::where("add_id",$id)->count();
        return view('front.product.details',compact(['pageTitle','data','relatedAds','productUserReview','totalReview']));
    }

    public function createAds(){
        $pageTitle = "Blinkers - Create Ads";
        $category = Category::pluck('title', 'id');
        $firstCategory = Category::orderBy("id","ASC")->first();
        $subCategory = SubCategory::where("category_id",$firstCategory->id)->pluck('title', 'id');
        return view('front.product.create',compact(['pageTitle',"category","subCategory"]));
    }

    public function editAds($id){
        $addData= Add::where('id',$id)->with("addImages")->first();
        $pageTitle = "Blinkers - edit".$addData->title;
        $category = Category::pluck('title', 'id');
        $sub_category = SubCategory::where("category_id",$addData->category_id)->pluck('title', 'id');
        return view('front.product.editadd',compact(['pageTitle','addData','category','sub_category']));
    }

    public function productListing(){
        $pageTitle = "Blinkers - Product Listing";
        $addData= Add::where('status','1')->with("addImages")->orderBy("id","DESC")->get();
        return view('front.product.listing',compact(['pageTitle','addData']));
    }

    public function sortingProduct(Request $request){
        $pageTitle = "Blinkers - Product Listing";
        $sortingValue= $request->sortingmenu;
        if($request->sortingmenu == 'date'){
            $addData= Add::where('status','1')->with("addImages")->orderBy("id","DESC")->get();
        }elseif($request->sortingmenu == 'price'){
            $addData= Add::where('status','1')->with("addImages")->orderBy("price","DESC")->get();
        }elseif($request->sortingmenu == 'price-desc'){
            $addData= Add::where('status','1')->with("addImages")->orderBy("price","ASC")->get();
        }
        return view('front.product.listing',compact(['pageTitle','addData','sortingValue']));
    }

    public function getsubCateogry(Request $request){
        $datas = SubCategory::where("category_id",$request->id)->get();
        $html='';
        foreach ($datas as $key => $value) {
            $html .= "<option value=".$value->id."> ".$value->title." </option>";
        }
        return $html;
    }

    public function storeAds(Request $request){
        $validatorRules=[
            'category_id'=>'required|exists:categories,id',
            'sub_category_id'=>'required|exists:sub_categories,id',
            'title'=>'required',
            'price'=>'required|integer',
            //'discount_price'=>'required|integer',
            'description'=>'required',
            'pickup_address'=>'required',
            'pickup_lat'=>'required',
            'pickup_lng'=>'required',
        ];
        try {
            $validator = Validator::make($request->all(), $validatorRules);
            if ($validator->fails()) {
                Session::flash('error', 'Please correct the errors below and try again');
                return redirect()->back()->withInput()->withErrors($validator->errors());
            } else {
                $data = $request->all();
                $data['user_id'] = Auth::user()->id;
                $addData = Add::create($data);
                if ($request->hasFile('add_image')) {
                    $destinationPath = '/uploads/adds/';
                    foreach ($request->file('add_image') as $key => $value) {
                        $response_data = Uploader::doUpload($value, $destinationPath);
                        if ($response_data['status'] == true) {
                            $addImageData['add_image'] = $response_data['file'];
                            $addImageData['add_id'] = $addData->id;
                            AddImage::create($addImageData);
                        }
                    }
                }
                Session::flash('success', 'Add created successfully.');
                return redirect()->route('my-ads');
            }
        } catch (\Throwable $e) {
            $msg = $e->getMessage();
            Session::flash('warning', $msg);
            return redirect()->back()->withInput();
        }    
    }

    public function updateAds(Request $request){
        $validatorRules=[
            'category_id'=>'required|exists:categories,id',
            'sub_category_id'=>'required|exists:sub_categories,id',
            'title'=>'required',
            'price'=>'required|integer',
            //'discount_price'=>'required|integer',
            'description'=>'required',
            'pickup_address'=>'required',
            'pickup_lat'=>'required',
            'pickup_lng'=>'required',
        ];
        try {
            $validator = Validator::make($request->all(), $validatorRules);
            if ($validator->fails()) {
                Session::flash('error', 'Please correct the errors below and try again');
                return redirect()->back()->withInput()->withErrors($validator->errors());
            } else {
                $data = $request->all();
                $data['user_id'] = Auth::user()->id;
                unset($data["_token"]);
                unset($data["imageid"]);
                unset($data["add_image"]);
                $addData = Add::where("id",$request->id)->update($data);
                if($request->imageid){
                    AddImage::where("add_id",$request->id)->whereNotIn("id", $request->imageid)->delete();
                }else{
                    AddImage::where("add_id",$request->id)->delete();
                }
                if ($request->hasFile('add_image')) {
                    $destinationPath = '/uploads/adds/';
                    foreach ($request->file('add_image') as $key => $value) {
                        $response_data = Uploader::doUpload($value, $destinationPath);
                        if ($response_data['status'] == true) {
                            $addImageData['add_image'] = $response_data['file'];
                            $addImageData['add_id'] = $request->id;
                            AddImage::create($addImageData);
                        }
                    }
                }
                Session::flash('success', 'Add update successfully.');
                return redirect()->route('my-ads');
            }
        } catch (\Throwable $e) {
            $msg = $e->getMessage();
            Session::flash('warning', $msg);
            return redirect()->back()->withInput();
        }   
    }

    public function deleteAds($id){
        Add::where('id',$id)->delete();
        Session::flash('success', 'Add deleted successfully.');
        return redirect()->route('my-ads');
    }

    public function markFavt(Request $request){
        $favtData = FavtAdd::where("user_id",Auth::user()->id)->where("add_id",$request->id)->first();
        if($favtData){
            FavtAdd::where("user_id",Auth::user()->id)->where("add_id",$request->id)->delete();
            $data["status"]= false;
            $data["message"] = "Add removed from favourites successfully.";
        }else{
            $requestData['user_id'] = Auth::user()->id;
            $requestData['add_id'] = $request->id;
            FavtAdd::create($requestData);
            $data["status"]= true;
            $data["message"] = "Add mark to favourites successfully.";
        }
        return $data;
    }
 
}
