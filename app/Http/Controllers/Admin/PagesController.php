<?php
namespace App\Http\Controllers\Admin;
use Session;
use DB;
use File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;   
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use \Config;
use App\Model\Pages;


class PagesController extends Controller
{

    public function index(){
        $title_page = 'Pages';
        $breadcumb = ['Pages'=>''];
        return view('admin.pages.index',compact('title_page','breadcumb'));
    }
    
    
    public function datatable(Request $request) {
        $columns = ['id', 'title', 'slug', 'created_at', 'action','app'];
        $totalData = Pages::count();
        $limit = $request->input('length');
        $start = $request->input('start');
        $dir = $request->input('order.0.dir');
        $order = $columns[$request->input('order.0.column')];
        $pages = Pages::select('pages.*');
        if (!empty($request->input('search.value'))) {
            $search = $request->input('search.value');
            $pages = $pages->where(function($query) use ($search) {
                $query->where('title', 'LIKE', "%{$search}%")
                    ->orWhere('slug', 'LIKE', "%{$search}%")
                    ->orWhere('description', 'LIKE', "%{$search}%");
            });
        }
        $totalFiltered = $pages->count();
        $pages = $pages->offset($start)->limit($limit)->orderBy($order, $dir)->get();
        $data = array();
        if (!empty($pages)) {
            $sno=1;
            foreach ($pages as $key => $row) {
                $nestedData['id'] = $sno++;
                $nestedData['app'] = $row->app;
                $nestedData['title'] = $row->title;
                $nestedData['slug'] = $row->slug;
                $nestedData['created_at'] = listDateFromat($row->created_at);
                $nestedData['action'] =  getButtons([
                    ['key'=>'edit','link'=>route('admin.pages.edit',$row->slug)],
                ]);

                $data[] = $nestedData;
            }
        }
        //$totalFiltered = isset($key) ? $key + 1 : 0;
        $json_data = array(
            "draw" => intval($request->input('draw')),
            "recordsTotal" => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data" => $data
        );
        echo json_encode($json_data);
    }
    
    public function edit(Request $request, $slug) {
        try {
            $entity = Pages::where([['slug',$slug]])->first();
            if(empty($entity)){
                Session::flash('warning', 'Invalid request');
                return redirect()->back();
            }
            $title_page = 'Edit Page';
            $breadcumb = ['Pages' => route('admin.pages.index'), $title_page => ''];
            
            if ($request->isMethod('post')) {
                $validatorRules = [
                    'title' => 'required',
                    'description' => 'required',
                ];

                $validator = Validator::make($request->all(), $validatorRules);
                if ($validator->fails()) {
                    Session::flash('error', 'Please correct the errors below and try again');
                    return redirect()->back()->withInput()->withErrors($validator->errors());
                } else {

                    $entity->title = $request->title;
                    $entity->description = $request->description;
                    $entity->save();
                    Session::flash('success', 'Page has been updated successfully.');
                    return redirect()->route('admin.pages.index');
                }
            }
            
           
            return view('admin.pages.edit', compact('entity','title_page', 'breadcumb'));
            
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            Session::flash('danger', $msg);
            return redirect()->back()->withInput();
        }

        
    }

}// end class.
