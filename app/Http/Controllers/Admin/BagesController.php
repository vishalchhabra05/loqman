<?php

namespace App\Http\Controllers\Admin;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\Bages;
use Illuminate\Support\Facades\Validator;
use DB;
use Session;
use App\Lib\Uploader;

class BagesController extends Controller {

    public function index() {
        $title_page = 'Bages';
        $breadcumb = [$title_page => ''];
        return view('admin.bages.index', compact('title_page','breadcumb'));
    }

    public function datatable(Request $request) {
        $columns = ['id', 'title','price','status','created_at', 'action'];
        $totalData = Bages::count();
        $limit = $request->input('length');
        $start = $request->input('start');
        $dir = $request->input('order.0.dir');
        $order = $columns[$request->input('order.0.column')];
        $results = Bages::select('bages.*');
        if (!empty($request->input('search.value'))) {
            $search = $request->input('search.value');
            $results = $results->where(function($query) use ($search) {
                $query->where('title', 'LIKE', "%{$search}%");
            });
        }
        $totalFiltered = $results->count();
        $results = $results->offset($start)->limit($limit)->orderBy($order, $dir)->get();
        $data = array();
        if(!empty($results)) {
            $sno=1;
            foreach ($results as $row) {
                $nestedData['id'] = $sno++;
                $nestedData['title'] = $row->title;
                $nestedData['price'] = $row->price;
                $nestedData['status'] = getStatus($row->status,$row->id);
                $nestedData['created_at'] = listDateFromat($row->created_at);
                $nestedData['action'] = getButtons([
                    ['key' => 'edit', 'link' => route('bages.edit', $row->id)],
                    // ['key' => 'delete', 'link' => route('bages.destroy', $row->id)]
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

    public function updatestatus(Request $request){
        $user_id = $request->id;
        $row = Bages::whereId($user_id)->first();
        $row->status = ($row->status == '0' ? '1' : ($row->status == '1' ? '2' : '1'));
        $row->save();
        $html = '';
        switch ($row->status) {
            case '0':
                $html = '<a data-toggle="tooltip"  class="btn btn-danger btn-xs" title="Penidng" onClick="changeStatus(' . $user_id . ')" >Pending</a>';
                break;
            case '1':
                $html = '<a data-toggle="tooltip"  class="btn btn-success btn-xs" title="Active" onClick="changeStatus(' . $user_id . ')" >Active</a>';
                break;
            case '2':
                $html = '<a data-toggle="tooltip"  class="btn btn-danger btn-xs" title="Inactive" onClick="changeStatus(' . $user_id . ')" >Inactive</a>';
                break;
            default:
                break;
        }
        return $html;
    }

    public function create() {
        $title_page = 'Add Bages';
        $breadcumb = ['Bages' => route('bages.index'), $title_page => ''];
        $entity = new Bages;
        return view('admin.bages.create', compact('breadcumb','title_page', 'entity'));
    }

    public function store(Request $request) {
        try {
            $validator = Validator::make($request->all(), [
                'title' => 'required',
                'price' => 'required',
                
            ]);
            if ($validator->fails()) {
                Session::flash('error', 'Please correct the errors below and try again');
                return redirect()->back()->withInput()->withErrors($validator->errors());
            }
            $entity = new Bages();
            $entity->title = $request->title;
            $entity->price = $request->price;
            
            $entity->save();
            Session::flash('success', 'Bages has been added successfully.');
            return redirect()->route('bages.index');
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            Session::flash('warning', $msg);
            return redirect()->back()->withInput();
        }
    }

    public function show($id) {
    }

    public function edit(Request $request, $id) {
        try {
            $entity = Bages::where('id', $id)->first();
            if (empty($entity)) {
                Session::flash('warning', 'Invalid Request');
                return redirect()->back();
            }
            $title_page = 'Edit Bage';
            $breadcumb = ['Bages' => route('bages.index'), $title_page => ''];
            return view('admin.bages.create', compact('breadcumb','title_page', 'entity'));
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            Session::flash('warning', $msg);
            return redirect()->back();
        }
    }

    public function update(Request $request, $id) {
        try {
            $entity = Bages::where('id', $id)->first();
            if (empty($entity)) {
                Session::flash('warning', 'Invalid Request');
                return redirect()->back();
            }
            $validator = Validator::make($request->all(), [
                'title' => 'required',
                'price' => 'required',
            ]);
            if ($validator->fails()) {
                Session::flash('error', 'Please correct the errors below and try again');
                return redirect()->back()->withInput()->withErrors($validator->errors());
            }
            $entity->title = $request->title;
            $entity->price = $request->price;
          
            $entity->save();
            Session::flash('success', 'Price has been updated successfully.');
            return redirect()->route('bages.index');
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            Session::flash('warning', $msg);
            return redirect()->back()->withInput();
        }
    }

    public function destroy(Request $request, $id) {
        try {
            $row = Bages::where('id', $id)->delete();
            Session::flash('success', 'Bages has been deleted successfully.');
            return redirect()->back();
        } catch (\Exception $e) {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }
}
