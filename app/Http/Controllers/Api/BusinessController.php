<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use DB;
use \Config;
use JWTAuth;
use JWTAuthException;
use App\Model\User;
use App\Model\Category;
use App\Model\SubCategory;
use App\Model\Add;
use App\Model\AddImage;
use App\Model\Plans;
use App\Lib\Uploader;
use App\Model\FavtAdd;
use App\Model\GygJob;
use App\Model\Setting;
use App\Model\UserCard;
use App\Model\GygJobCategory;
use App\Model\UserCategory;
use App\Model\Review;
use App\Model\Payment;
use App\Model\Membership;
use Illuminate\Support\Facades\Validator;

class BusinessController extends Controller {

    public function getCategory(){
        try{
            $datas = Category::get();
            foreach ($datas as $key => $data) {
                $datas[$key]['totalProduct'] = Add::where("category_id",$data->id)->where("status","1")->count();
            }
            return response()->json(['status'=>true,'message'=>'Category Data','data'=>$datas]);
        }catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage(), 'data' => []]);
        }
    }

    public function getSubCategory(Request $request){
        $validatorRules = [
            'category_id' => 'required',
        ];
        try{
            $validator = Validator::make($request->all(), $validatorRules);
            if ($validator->fails()) {
                $error = $this->validationHandle($validator->messages());
                return response()->json(['status' => false, 'message' => $error]);
            } else {
                $data = SubCategory::where('category_id',$request->category_id)->with('categoryInfo')->get();
                return response()->json(['status'=>true,'message'=>'Category Data','data'=>$data]);
            }
        }catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage(), 'data' => []]);
        }
    }

   
    public function createAdds(Request $request){
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
        $response = [];
        try {
            $validator = Validator::make($request->all(), $validatorRules);
            if ($validator->fails()) {
                $error = $this->validationHandle($validator->messages());
                $response['status'] = false;
                $response['message'] = $error;
            } else {
                $data = $request->all();
                $data['user_id'] = JWTAuth::user()->id;
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
                $response['status'] = true;
                $response['message'] = "Add created successfully";
                $response['data']=$addData;
            }
        } catch (\Throwable $th) {
            $response['status'] = false;
            $response['message'] = $th->getMessage().' - '.$th->getLine();
            $response['data'] = [];
        }
        return response()->json($response);
    }

    public function deleteAdds(Request $request){
        $validatorRules=['id' => 'required|exists:adds,id'];
        $response = [];
        try {
            $validator = Validator::make($request->all(), $validatorRules);
            if ($validator->fails()) {
                $error = $this->validationHandle($validator->messages());
                $response['status'] = false;
                $response['message'] = $error;
            } else {
                Add::where('user_id',JWTAuth::user()->id)->where('id',$request->id)->delete();
                $response['status'] = true;
                $response['message'] = 'Add deleted successfully.';
                $response['data'] = [];
            }
        } catch (\Throwable $th) {
            $response['status'] = false;
            $response['message'] = $th->getMessage().' - '.$th->getLine();
            $response['data'] = [];
        }
        return response()->json($response);
    }

    public function plans(){
        $response = [];
        try {
            $datas = Plans::all();
            foreach ($datas as $key => $value) {
                $datas[$key]['description'] = explode(",",$value->description);
            }
            $response['data'] = $datas;
            $response['status'] = true;
            $response['message'] ="Plans Data";
        } catch (\Exception $th) {
            $response['status'] = false;
            $response['message'] =$th->getMessage();
            $response['data'] = [];
        }
        return response()->json($response);
    }

    public function productByCategory(Request $request){
        $validatorRules=[
            'category_id'=>'required|exists:categories,id',
            'sub_category_id'=>'required|exists:sub_categories,id',
        ];
        try {
            $validator = Validator::make($request->all(), $validatorRules);
            if ($validator->fails()) {
                $error = $this->validationHandle($validator->messages());
                $response['status'] = false;
                $response['message'] = $error;
            } else {
                $datas = Add::where('category_id',$request->category_id)->where('status','1')->whereIn('sub_category_id',$request->sub_category_id)->with('addImages')->latest()->get();
                foreach ($datas as $key => $data) {
                    $datas[$key]['favtAdd'] = getFavtProd($data->id,JWTAuth::user()->id);
                }
                $response['status'] = true;
                $response['message'] = 'Adds by category.';
                $response['data'] = $datas;
            }
        } catch (\Throwable $th) {
            $response['status'] = false;
            $response['message'] = $th->getMessage().' - '.$th->getLine();
            $response['data'] = [];
        }
        return response()->json($response);
    }

    public function trendingProductByCategory(Request $request){
        $validatorRules=[
            'category_id'=>'required|exists:categories,id',
            "login_id"=>"required",
        ];
        try {
            $validator = Validator::make($request->all(), $validatorRules);
            if ($validator->fails()) {
                $error = $this->validationHandle($validator->messages());
                $response['status'] = false;
                $response['message'] = $error;
            } else {
                if(!empty($request->keyword)){
                    $datas = Add::where('status','1')->where('category_id',$request->category_id)->where('title','like','%'. $request->keyword .'%')->latest()->get();
                }else{
                    $datas = Add::where('status','1')->where('category_id',$request->category_id)->latest()->get();
                }
            foreach ($datas as $key => $data) {
                $datas[$key]['id'] = $data->id;
                $datas[$key]['user_id'] = $data->user_id;
                $datas[$key]['category_id'] = $data->category_id;
                $datas[$key]['status'] = $data->status;
                $datas[$key]['sub_category_id'] = $data->sub_category_id;
                $datas[$key]['title'] = $data->title;
                $datas[$key]['price'] = $data->price;
                $datas[$key]['discount_price'] = $data->discount_price;
                $datas[$key]['description'] = $data->description;
                $datas[$key]['pickup_address'] = $data->pickup_address;
                $datas[$key]['pickup_lat'] = $data->pickup_lat;
                $datas[$key]['created_at'] = $data->created_at;
                $datas[$key]['updated_at'] = $data->updated_at;
                if($request->login_id){
                    $datas[$key]['favtAdd'] = getFavtProd($data->id,$request->login_id);
                }else{
                    $datas[$key]['favtAdd'] = false ;
                }
                $datas[$key]['add_images'] = AddImage::where("add_id", $data->id)->get();
            }
                $response['status'] = true;
                $response['message'] = 'Adds by category.';
                $response['data'] = $datas;
            }
        } catch (\Throwable $th) {
            $response['status'] = false;
            $response['message'] = $th->getMessage().' - '.$th->getLine();
            $response['data'] = [];
        }
        return response()->json($response);
    }

    public function searchSubCategory(Request $request){
        $validatorRules=['keyword' => 'required'];
        try {
            $validator = Validator::make($request->all(), $validatorRules);
            if ($validator->fails()) {
                $error = $this->validationHandle($validator->messages());
                $response['status'] = false;
                $response['message'] = $error;
            } else {
                $response['status'] = true;
                $response['message'] = "Product by keyword";
                $response['data'] = Add::where('status','1')->where('title','like','%'. $request->keyword .'%')->latest()->get();
                //$response['data'] = SubCategory::where('title', 'like', '%' . $request->keyword . '%')->with('categoryInfo')->get();
            }
        }catch (\Throwable $th) {
            $response['status'] = false;
            $response['message'] = $th->getMessage().' - '.$th->getLine();
            $response['data'] = [];
        }
        return response()->json($response);
    }
    
    public function trandingProduct(){
        try {
            $datas = Add::where('status','1')->latest()->get();
            $productData=[];
            foreach ($datas as $key => $data) {
                $datas[$key]['id'] = $data->id;
                $datas[$key]['user_id'] = $data->user_id;
                $datas[$key]['category_id'] = $data->category_id;
                $datas[$key]['status'] = $data->status;
                $datas[$key]['sub_category_id'] = $data->sub_category_id;
                $datas[$key]['title'] = $data->title;
                $datas[$key]['price'] = $data->price;
                $datas[$key]['discount_price'] = $data->discount_price;
                $datas[$key]['description'] = $data->description;
                $datas[$key]['pickup_address'] = $data->pickup_address;
                $datas[$key]['pickup_lat'] = $data->pickup_lat;
                $datas[$key]['created_at'] = $data->created_at;
                $datas[$key]['updated_at'] = $data->updated_at;
                $datas[$key]['favtAdd'] = false;
                $datas[$key]['add_images'] = AddImage::where("add_id", $data->id)->get();
            }
            $response['status'] = true;
            $response['message'] = 'My Adds List.';
            $response['data'] = $datas;
        } catch (\Throwable $th) {
            $response['status'] = false;
            $response['message'] = $th->getMessage().' - '.$th->getLine();
            $response['data'] = [];
        }
        return response()->json($response);
    }

    public function relatedAds(Request $request){
        $validatorRules=[
            'id' => 'required|exists:adds,id',
            
        ];
        try {
            $validator = Validator::make($request->all(), $validatorRules);
            if ($validator->fails()) {
                $error = $this->validationHandle($validator->messages());
                $response['status'] = false;
                $response['message'] = $error;
            } else {
                $productInfo = Add::where('id',$request->id)->first();
                $data = Add::where('id','<>',$request->id)->where('category_id',$productInfo->category_id)->with('addImages')->orderBy('id','DESC')->limit('5')->get();
                $response['status'] = true;
                $response['message'] = 'Add details.';
                $response['data'] = $data;
            }
        } catch (\Throwable $th) {
            $response['status'] = false;
            $response['message'] = $th->getMessage().' - '.$th->getLine();
            $response['data'] = [];
        }
        return response()->json($response);
    }

    public function webProductDetail(Request $request){
        $validatorRules=['id' => 'required|exists:adds,id'];
        try {
            $validator = Validator::make($request->all(), $validatorRules);
            if ($validator->fails()) {
                $error = $this->validationHandle($validator->messages());
                $response['status'] = false;
                $response['message'] = $error;
            } else {
                $data = Add::where('id',$request->id)->with('addImages')->with('addUserInfo')->first();
                $response['status'] = true;
                $response['message'] = 'Add details.';
                $response['data'] = $data;
            }
        } catch (\Throwable $th) {
            $response['status'] = false;
            $response['message'] = $th->getMessage().' - '.$th->getLine();
            $response['data'] = [];
        }
        return response()->json($response);
    }

    public function addDetails(Request $request){
        $validatorRules=[
            'id'=>'required|exists:adds,id',
            'userLat'=>'required',
            'userLng'=>'required',
        ];
        try {
            $validator = Validator::make($request->all(), $validatorRules);
            if ($validator->fails()) {
                $error = $this->validationHandle($validator->messages());
                $response['status'] = false;
                $response['message'] = $error;
            } else {
                $data = Add::where('id',$request->id)->with('addImages')->with("addUserInfo")->first();
                $distanceInMiles = haversineGreatCircleDistance($request->userLat,$request->userLng,$data->pickup_lat,$data->pickup_lng);
                $data['distance'] = number_format((float)$distanceInMiles/0.62137, 2, '.', '');
                $data['favtAdd'] = getFavtProd($data->id,JWTAuth::user()->id);
                $response['status'] = true;
                $response['message'] = 'Add details.';
                $response['data'] = $data;
            }
        } catch (\Throwable $th) {
            $response['status'] = false;
            $response['message'] = $th->getMessage().' - '.$th->getLine();
            $response['data'] = [];
        }
        return response()->json($response);
    }

    public function myAdds(){
        try {
            $datas = Add::where('user_id',JWTAuth::user()->id)->with('addImages')->get();
            foreach ($datas as $key => $data) {
                $datas[$key]['favtAdd'] = getFavtProd($data->id,JWTAuth::user()->id);
            }
            $response['status'] = true;
            $response['message'] = 'My Adds List.';
            $response['data'] = $datas;
        } catch (\Throwable $th) {
            $response['status'] = false;
            $response['message'] = $th->getMessage().' - '.$th->getLine();
            $response['data'] = [];
        }
        return response()->json($response);
    }

    public function myFavtAdds(){
        try {
            $datas = FavtAdd::where('user_id',JWTAuth::user()->id)->with('favtAddInfo.addImages')->get();
            foreach ($datas as $key => $data) {
                $datas[$key]['favtAdd'] = getFavtProd($data->add_id,JWTAuth::user()->id);
            }
            $response['status'] = true;
            $response['message'] = 'My favourite add List.';
            $response['data'] = $datas;
        } catch (\Throwable $th) {
            $response['status'] = false;
            $response['message'] = $th->getMessage().' - '.$th->getLine();
            $response['data'] = [];
        }
        return response()->json($response);
    }

     public function getProducts(Request $request){
        try {
            if(!empty($request->keyword)){
                $datas = Add::where('status','1')->where('title','like','%'. $request->keyword .'%')->latest()->get();
            }else{
                $datas = Add::where('status','1')->latest()->get();
            }
            $productData=[];
            foreach ($datas as $key => $data) {
                $datas[$key]['id'] = $data->id;
                $datas[$key]['user_id'] = $data->user_id;
                $datas[$key]['category_id'] = $data->category_id;
                $datas[$key]['status'] = $data->status;
                $datas[$key]['sub_category_id'] = $data->sub_category_id;
                $datas[$key]['title'] = $data->title;
                $datas[$key]['price'] = $data->price;
                $datas[$key]['discount_price'] = $data->discount_price;
                $datas[$key]['description'] = $data->description;
                $datas[$key]['pickup_address'] = $data->pickup_address;
                $datas[$key]['pickup_lat'] = $data->pickup_lat;
                $datas[$key]['created_at'] = $data->created_at;
                $datas[$key]['updated_at'] = $data->updated_at;
                if($request->login_id){
                    $datas[$key]['favtAdd'] = getFavtProd($data->id,$request->login_id);
                }else{
                    $datas[$key]['favtAdd'] = false;
                }
                
                $datas[$key]['add_images'] = AddImage::where("add_id", $data->id)->get();
            }
            $response['status'] = true;
            $response['message'] = 'My Adds List.';
            $response['data'] = $datas;
        } catch (\Throwable $th) {
            $response['status'] = false;
            $response['message'] = $th->getMessage().' - '.$th->getLine();
            $response['data'] = [];
        }
        return response()->json($response);
    }
    
    
    public function getProductReviews(Request $request){
        $response = [];
        try {
            $validatorRules=[
                'add_id'=>'required|exists:adds,id',
            ];
            $validator = Validator::make($request->all(), $validatorRules);
            if ($validator->fails()) {
                $error = $this->validationHandle($validator->messages());
                $response['status'] = false;
                $response['message'] = $error;
            } else {
                $response['status'] = true;
                $response['message'] = "Review List";
                $response['data'] = Review::where("add_id",$request->add_id)->with("reviewUserInfo")->get();
            }
        } catch (\Throwable $th) {
            $response['status'] = false;
            $response['message'] = $th->getMessage().' - '.$th->getLine();
            $response['data'] = [];
        }
        return response()->json($response);
    }

    public function editAdds(Request $request){
        $validatorRules=[
            'id'=>'required|exists:adds,id',
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
        $response = [];
        try {
            $validator = Validator::make($request->all(), $validatorRules);
            if ($validator->fails()) {
                $error = $this->validationHandle($validator->messages());
                $response['status'] = false;
                $response['message'] = $error;
            } else {
                if($request->discount_price){
                    Add::where('user_id',JWTAuth::user()->id)->where('id',$request->id)->update([
                        'category_id' => $request->category_id,
                        'sub_category_id'=>$request->sub_category_id,
                        'title'=>$request->title,
                        'price'=>$request->price,
                        'discount_price'=>$request->discount_price,
                        'description'=>$request->description,
                        'pickup_address'=>$request->pickup_address,
                        'pickup_lat'=>$request->pickup_lat,
                        'pickup_lng'=>$request->pickup_lng,
                    ]);
                }else{
                    Add::where('user_id',JWTAuth::user()->id)->where('id',$request->id)->update([
                        'category_id' => $request->category_id,
                        'sub_category_id'=>$request->sub_category_id,
                        'title'=>$request->title,
                        'price'=>$request->price,
                        'description'=>$request->description,
                        'pickup_address'=>$request->pickup_address,
                        'pickup_lat'=>$request->pickup_lat,
                        'pickup_lng'=>$request->pickup_lng,
                    ]);
                }
                AddImage::where('add_id',$request->id)->delete();
                if ($request->file('add_image')) {
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
                $response['status'] = true;
                $response['message'] = "Add edited successfully";
                $response['data']=$request->all();
            }
        } catch (\Throwable $th) {
            $response['status'] = false;
            $response['message'] = $th->getMessage().' - '.$th->getLine();
            $response['data'] = [];
        }
        return response()->json($response);
    }
    
    public function addCard(Request $request){
        $validatorRules=[
            'card_type'=>'required',
            'card_holder_name'=>'required',
            "card_number" =>"required",
            "card_cvv" =>"required",
            "card_expiry_date" =>"required",
        ];
        $response = [];
        try {
            $validator = Validator::make($request->all(), $validatorRules);
            if ($validator->fails()) {
                $error = $this->validationHandle($validator->messages());
                $response['status'] = false;
                $response['message'] = $error;
            }else{
                $input = $request->all();
                $input['user_id'] = JWTAuth::user()->id;
                UserCard::create($input);
                $response['status'] = true;
                $response['message'] = "Card Created Successfully";
                $response['data'] = [];
            }
        } catch (\Throwable $th) {
            $response['status'] = false;
            $response['message'] = $th->getMessage().' - '.$th->getLine();
            $response['data'] = [];
        }
        return response()->json($response);
    }
    
    public function editCard(Request $request){
        $validatorRules=[
            "id"=>'required|exists:user_cards,id',
            'card_type'=>'required',
            'card_holder_name'=>'required',
            "card_number" =>"required",
            "card_cvv" =>"required",
            "card_expiry_date" =>"required",
        ];
        $response = [];
        try {
            $validator = Validator::make($request->all(), $validatorRules);
            if ($validator->fails()) {
                $error = $this->validationHandle($validator->messages());
                $response['status'] = false;
                $response['message'] = $error;
            }else{
                $input = $request->all();
                $data = UserCard::where('id', $request->id)->where("user_id",JWTAuth::user()->id)->update($input);
                $response['status'] = true;
                $response['message'] = "Card Updated Successfully";
                $response['data'] =  $request->all();
            }
        } catch (\Throwable $th) {
            $response['status'] = false;
            $response['message'] = $th->getMessage().' - '.$th->getLine();
            $response['data'] = [];
        }
        return response()->json($response);
    }
    
     public function myCards(){
        $response = [];
        try {
            $response['status'] = true;
            $response['message'] = "User Card List";
            $response['data'] = UserCard::where("user_id",JWTAuth::user()->id)->get();
        } catch (\Throwable $th) {
            $response['status'] = false;
            $response['message'] = $th->getMessage().' - '.$th->getLine();
            $response['data'] = [];
        }
        return response()->json($response);
    }
    
     public function deleteCard(Request $request){
        $validatorRules=[
            "id"=>'required|exists:user_cards,id',
        ];
        $response = [];
        try {
            $validator = Validator::make($request->all(), $validatorRules);
            if ($validator->fails()) {
                $error = $this->validationHandle($validator->messages());
                $response['status'] = false;
                $response['message'] = $error;
            }else{
                UserCard::where('id', $request->id)->where("user_id",JWTAuth::user()->id)->delete();
                $response['status'] = true;
                $response['message'] = "Card deleted successfully";
                $response['data'] =  [];
            }
        } catch (\Throwable $th) {
            $response['status'] = false;
            $response['message'] = $th->getMessage().' - '.$th->getLine();
            $response['data'] = [];
        }
        return response()->json($response);
    }

    public function markFavtProduct(Request $request){
        $validatorRules=[
            'add_id'=>'required|exists:adds,id',
            'status'=>'required|in:0,1',
        ];
        $response = [];
        try {
            $validator = Validator::make($request->all(), $validatorRules);
            if ($validator->fails()) {
                $error = $this->validationHandle($validator->messages());
                $response['status'] = false;
                $response['message'] = $error;
            } else {
                if($request->status == 1){
                    $data['user_id'] = JWTAuth::user()->id;
                    $data['add_id'] = $request->add_id;
                    FavtAdd::create($data);
                    $response['status'] = true;
                    $response['message'] = 'Add mark favourite successfully.';
                    $response['data'] = [];
                }else{
                    FavtAdd::where('add_id',$request->add_id)->where('user_id',JWTAuth::user()->id)->delete();
                    $response['status'] = true;
                    $response['message'] = 'Add unmark successfully.';
                    $response['data'] = [];
                }
            }
        } catch (\Throwable $th) {
            $response['status'] = false;
            $response['message'] = $th->getMessage().' - '.$th->getLine();
            $response['data'] = [];
        }
        return response()->json($response);
    }

    public function postRating(Request $request){
        $validatorRules = [
            'add_id' => 'required|numeric|exists:adds,id',
            'rating' => 'required|numeric|max:5|min:1',
            'review' => 'required',
        ];
        try {
            $validator = Validator::make($request->all(), $validatorRules);
            if ($validator->fails()) {
                $error = $this->validationHandle($validator->messages());
                return response()->json(['status' => false, 'message' => $error]);
            } else {
                $input = $request->all();
                $input['user_id'] = JWTAuth::user()->id;
                Review::create($input);
                return response()->json(['status'=>true,'message'=>'Review posted successfully.','data'=>[]]);
            }
        }catch (\Exception $e) {
            return response()->json(['status'=>false,'message'=>$e->getMessage(),'data'=>[]]);
        }
    }
    
    public function getProductReview(Request $request){
        $validatorRules = [
            'add_id' => 'required|numeric|exists:adds,id',
        ];
        try {
            $validator = Validator::make($request->all(), $validatorRules);
            if ($validator->fails()) {
                $error = $this->validationHandle($validator->messages());
                return response()->json(['status' => false, 'message' => $error]);
            } else {
                $data = Review::where("add_id",$request->add_id)->with("reviewUserInfo")->get();
                return response()->json(['status'=>true,'message'=>'Adds Review.','data'=>$data]);
            }
        }catch (\Exception $e) {
            return response()->json(['status'=>false,'message'=>$e->getMessage(),'data'=>[]]);
        }
    }

    public function submitPayment(Request $request){
        try {
            $validatorRules=[
                //'payment_id'=>'required',
                'amount'=>'required',
                'plan_id'=>'required|exists:plans,id',
            ];
            $response = [];
            $validator = Validator::make($request->all(), $validatorRules);
            if ($validator->fails()) {
                $error = $this->validationHandle($validator->messages());
                $response['status'] = false;
                $response['message'] = $error;
            }else{
                $data = $request->all();
                $data['user_id'] = JWTAuth::user()->id;
                $paymentData = Payment::create($data);
                $planInfo = Plans::where("id",$request->plan_id)->first();
                $membership['user_id'] = JWTAuth::user()->id;
                $membership['valid_upto'] = date('Y-m-d', strtotime("+$planInfo->duration month"));
                $membership['plan_id'] = $planInfo->id;
                Membership::create($membership);
                //convert user to seller
                User::where("id",JWTAuth::user()->id)->update(["role"=>Config::get('params.role_ids.Seller')]);
                $response['status'] = true;
                $response['message'] = "Payment done successfully";
                $response['data'] = [];
            }
        } catch (\Exception $e) {
            $response['status'] = false;
            $response['message'] = $e->getMessage().' - '.$e->getLine();
            $response['data'] = [];
        }
        return response()->json($response);
    }

}//end class.
