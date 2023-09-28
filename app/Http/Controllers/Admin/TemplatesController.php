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
use App\Model\EmailTemplate;


class TemplatesController extends Controller
{

    public function index(){
        
        $title_page = 'Email Templates';
        $breadcumb = [$title_page=>''];
        
        return view('admin.templates.index',compact('title_page','breadcumb'));
    }
    
    
    public function datatable(Request $request) {
        $columns = ['id', 'title', 'slug', 'created_at', 'action'];
        $totalData = EmailTemplate::count();
        $limit = $request->input('length');
        $start = $request->input('start');
        $dir = $request->input('order.0.dir');
        $order = $columns[$request->input('order.0.column')];
        $pages = EmailTemplate::select('email_templates.*');
        
        if (!empty($request->input('search.value'))) {
            $search = $request->input('search.value');
            $pages = $pages->where(function($query) use ($search) {
                $query->where('title', 'LIKE', "%{$search}%")
                    ->orWhere('slug', 'LIKE', "%{$search}%")
                    ->orWhere('description', 'LIKE', "%{$search}%")
                    ->orWhere('subject', 'LIKE', "%{$search}%");
            });
        }
        
        $totalFiltered = $pages->count();
        $pages = $pages->offset($start)->limit($limit)->orderBy($order, $dir)->get();


        $data = [];
        if (!empty($pages)) {
            foreach ($pages as $key => $row) {
                $nestedData['id'] = null;
                $nestedData['title'] = $row->title;
                $nestedData['subject'] = $row->slug;
                $nestedData['created_at'] = listDateFromat($row->created_at);
                $nestedData['action'] =  getButtons([
                    ['key'=>'edit','link'=>route('admin.templates.edit',$row->slug)],
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
            $entity = EmailTemplate::where([['slug',$slug]])->first();
            if(empty($entity)){
                Session::flash('warning', 'Invalid request');
                return redirect()->back();
            }
            $title_page = 'Edit Email Template';
            $breadcumb = ['Email Templates' => route('admin.templates.index'), $title_page => ''];
            
            if ($request->isMethod('post')) {

                $validatorRules = [
                    'title' => 'required',
                    'description' => 'required',
                ];

                $validator = Validator::make($request->all(), $validatorRules);
                if ($validator->fails()) {
                    return redirect()->back()->withInput()->withErrors($validator->errors());
                } else {
                    $entity->title = $request->title;
                    $entity->subject = $request->subject;
                    $entity->description = $request->description;
                    $entity->save();
                    Session::flash('success', 'Email templates has been updated successfully.');
                    return redirect()->route('admin.templates.index');
                }
            }
            
           
            return view('admin.templates.edit', compact('entity','title_page', 'breadcumb'));
            
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            Session::flash('danger', $msg);
            return redirect()->back()->withInput();
        }
    }

}// end class.
