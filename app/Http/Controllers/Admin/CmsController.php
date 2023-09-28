<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\Cms;
use Illuminate\Support\Facades\Validator;
use Session;

class CmsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $title_page = 'Cms list';
        $breadcumb = [$title_page => ''];
        return view('admin.cms.user_cms_list', compact('title_page','breadcumb'));
    }

    public function exportindex(){
        $title_page = 'Cms list';
        $breadcumb = [$title_page => ''];
        return view('admin.cms.export_cms_list', compact('title_page','breadcumb'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    public function datatable(Request $request){
        $columns = ['id', 'title','description','action'];
        $totalData = Cms::where('usertype','2')->count();
        $limit = $request->input('length');
        $start = $request->input('start');
        $dir = $request->input('order.0.dir');
        $order = $columns[$request->input('order.0.column')];
        $results = Cms::where('usertype','2')->select('cms.*');
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
                $nestedData['title'] =$row->title;
                $nestedData['description'] = $row->description;
                $nestedData['action'] = getButtons([
                    ['key' => 'edit', 'link' => route('cms.edit', $row->id)]
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

    public function exportdatatable(Request $request){
        $columns = ['id', 'title','description','action'];
        $totalData = Cms::where('usertype','3')->count();
        $limit = $request->input('length');
        $start = $request->input('start');
        $dir = $request->input('order.0.dir');
        $order = $columns[$request->input('order.0.column')];
        $results = Cms::where('usertype','3')->select('cms.*');
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
                $nestedData['title'] =$row->title;
                $nestedData['description'] = $row->description;
                $nestedData['action'] = getButtons([
                    ['key' => 'edit', 'link' => route('cms.edit', $row->id)]
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

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        try {
            $entity = Cms::where('id', $id)->first();
            if (empty($entity)) {
                Session::flash('warning', 'Invalid Request');
                return redirect()->back();
            }
            $title_page = 'Edit Cms';
            $breadcumb = ['Cms list' => route('cms.index'), $title_page => ''];
            return view('admin.cms.user_cms_edit', compact('breadcumb','title_page', 'entity'));
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            Session::flash('warning', $msg);
            return redirect()->back();
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $entity = Cms::where('id', $id)->first();
            if (empty($entity)) {
                Session::flash('warning', 'Invalid Request');
                return redirect()->back();
            }
            $validator = Validator::make($request->all(), [
                'title' => 'required',
                'description' => 'required',
            ]);
            if ($validator->fails()) {
                Session::flash('error', 'Please correct the errors below and try again');
                return redirect()->back()->withInput()->withErrors($validator->errors());
            }
            $entity->title = $request->title;
            $entity->description = $request->description;
          
            $entity->save();
            Session::flash('success', 'updated successfully.');
            return redirect()->route('cms.index');
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            Session::flash('warning', $msg);
            return redirect()->back()->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
