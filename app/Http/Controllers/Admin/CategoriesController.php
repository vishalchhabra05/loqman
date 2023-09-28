<?php

namespace App\Http\Controllers\Admin;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\Category;
use App\Model\UserCategroy;
use App\Model\Notification;
use App\Model\Notificationsend;
use App\Model\User;
use Illuminate\Support\Facades\Validator;
use DB;
use Session;
use App\Lib\Uploader;

class CategoriesController extends Controller {

    public function index() {
        $title_page = 'Categories';
        $breadcumb = [$title_page => ''];
        return view('admin.categories.index', compact('title_page','breadcumb'));
    }

    public function datatable(Request $request) {
        $columns = ['id', 'category_name','category_image','created_at', 'action'];
        $totalData = Category::count();
        $limit = $request->input('length');
        $start = $request->input('start');
        $dir = $request->input('order.0.dir');
        $order = $columns[$request->input('order.0.column')];
        $results = Category::select('categories.*');
        if (!empty($request->input('search.value'))) {
            $search = $request->input('search.value');
            $results = $results->where(function($query) use ($search) {
                $query->where('category_name', 'LIKE', "%{$search}%");
            });
        }
        $totalFiltered = $results->count();
        $results = $results->offset($start)->limit($limit)->orderBy($order, $dir)->get();
        $data = array();
        if(!empty($results)) {
            $sno=1;
            foreach ($results as $row) {
                $nestedData['id'] = $sno++;
                $nestedData['category_name'] = $row->category_name;
                $nestedData['category_image'] = !empty($row->category_image_full) ? '<img src="'.$row->category_image_full.'" width="50"/>' : '';
                $nestedData['notification'] ='<input class="checkhour" type="checkbox" name="notification[]" value='.$row->id.'>';
                $nestedData['category'] ='<input class="CategroyValue" type="checkbox" name="category[]" value='.$row->id.'>';
                $nestedData['created_at'] = listDateFromat($row->created_at);
                $nestedData['action'] = getButtons([
                    ['key' => 'edit', 'link' => route('categories.edit', $row->id)],
                    ['key' => 'delete', 'link' => route('categories.destroy', $row->id)],
                    ['key' => 'view', 'link' => route('categories.show', $row->id)]
                ]);
                $data[] = $nestedData;
            }
        }
        $json_data = array(
            "draw" => intval($request->input('draw')),
            "recordsTotal" => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data" => $data
        );
        echo json_encode($json_data);
    }

    public function create() {
        $title_page = 'Add Category';
        $breadcumb = ['Categories' => route('categories.index'), $title_page => ''];
        $entity = new Category;
        return view('admin.categories.create', compact('breadcumb','title_page', 'entity'));
    }

    public function categroy_push(Request $request){
        try{
            $CategoryCheck=Category::join('user_categories', 'categories.id', '=', 'user_categories.categroy_id')->get();
            $notification=Notification::find(2);
            foreach($CategoryCheck as $value){
                $User=User::where('id',$value->user_id)->first();
                $notification_type="hurry";
                 $diduser="false";
                 if($User->fcm_token){
                    PushNotficationAndroid($User->fcm_token,$User->id,$notification->subject, $notification->message,$notification_type,$diduser);
                 }
                $data=new Notificationsend;
                $data->user_id=$User->id;
                $data->status="0";
                $data->message=$notification->message;
                $data->save();
            }
    
        }catch(\Exception $e){
            $msg = $e->getMessage();
            Session::flash('warning', $msg);
            return redirect()->back()->withInput();
        }
    }

    public function delete_categroy(Request $request){
        try{
            foreach($request->category_id as $categroy){
                $Blog = "DELETE
                categories, user_categories
                from
                categories
                LEFT join user_categories on categories.id = user_categories.categroy_id
                WHERE
                categories.id =" .$categroy;
                DB::select(DB::raw($Blog));
            }
    
        }catch(\Exception $e){
            $msg = $e->getMessage();
            Session::flash('warning', $msg);
            return redirect()->back()->withInput();
        }
    }

    public function store(Request $request) {
        try {
            $validator = Validator::make($request->all(), [
                'category_name' => 'required|unique:categories,category_name',
                'category_image' => 'image|mimes:jpeg,png,jpg'  
            ]);
            if ($validator->fails()) {
                return redirect()->back()->withInput()->withErrors($validator->errors());
            }
            $entity = new Category();
            $entity->category_name	 = $request->category_name;
            
            if ($request->file('category_image')) {
                $destinationPath = '/uploads/categories/';
                $response_data = Uploader::doUpload($request->file('category_image'), $destinationPath);
                if ($response_data['status'] == true) {
                    $entity->category_image = $response_data['file'];
                }
            }
            $entity->save();
            Session::flash('success', 'Category has been added successfully.');
            return redirect()->route('categories.index');
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            Session::flash('warning', $msg);
            return redirect()->back()->withInput();
        }
    }

    public function show($id) {
        try{
            $entity = Category::find($id);
            if ($entity) {
                $title_page = 'Categories';
                $breadcumb = [$title_page => ''];
                $assignuser=UserCategroy::where('categroy_id',$entity->id)->get();
                return view('admin.categories.show', compact('title_page','breadcumb','entity','assignuser'));
            } else {
                Session::flash('warning', 'Invalid request');
                return redirect()->back();
            }
        }catch(\Exception $e){

        }
    }

    public function edit(Request $request, $id) {
        try {
            $entity = Category::where('id', $id)->first();
            if (empty($entity)) {
                Session::flash('warning', 'Invalid Request');
                return redirect()->back();
            }
            $title_page = 'Edit Category';
            $breadcumb = ['Categories' => route('categories.index'), $title_page => ''];
            return view('admin.categories.create', compact('breadcumb','title_page', 'entity'));
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            Session::flash('warning', $msg);
            return redirect()->back();
        }
    }

    public function update(Request $request, $id) {
        try {
            $entity = Category::where('id', $id)->first();
            if (empty($entity)) {
                Session::flash('warning', 'Invalid Request');
                return redirect()->back();
            }
            $validator = Validator::make($request->all(), [
                'category_name' => 'required',
            ]);
            if ($validator->fails()) {
                Session::flash('error', 'Please correct the errors below and try again');
                return redirect()->back()->withInput()->withErrors($validator->errors());
            }
            $entity->category_name = $request->category_name;
            if ($request->file('category_image')) {
                if(!empty($entity->image) && file_exists('public'.$entity->image)){
                    unlink('public'.$entity->image);
                }
                $destinationPath = 'uploads/categories/';
                $response_data = Uploader::doUpload($request->file('category_image'), $destinationPath);
                if ($response_data['status'] == true) {
                    $entity->category_image = $response_data['file'];
                }
            }
            $entity->save();
            Session::flash('success', 'Category has been updated successfully.');
            return redirect()->route('categories.index');
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            Session::flash('warning', $msg);
            return redirect()->back()->withInput();
        }
    }

    public function destroy(Request $request, $id) {
        try {
            $checkuser=UserCategroy::where('categroy_id',$id)->first();
            if(!empty($checkuser)){
                if($checkuser->user_id){
                    Session::flash('warning', 'Category has been not deleted Becuse user assgin.');
                    return redirect()->back();
                }
           }
            $row = Category::where('id', $id)->delete();
            Session::flash('success', 'Category has been deleted successfully.');
            return redirect()->back();
        } catch (\Exception $e) {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }
}
