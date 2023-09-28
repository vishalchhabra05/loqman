<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\UserotherInformation;
use Session;

class TagsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $title_page = 'Tags Managment';
        $breadcumb = [$title_page => ''];
        return view('admin.tagsmanagment.list_tags', compact('title_page','breadcumb'));
        
    }

    public function datatable(Request $request){
        $columns = ['id', 'user_id','tags','action'];
        $totalData = UserotherInformation::count();
        $limit = $request->input('length');
        $start = $request->input('start');
        $dir = $request->input('order.0.dir');
        $order = $columns[$request->input('order.0.column')];
        $results = UserotherInformation::select('userother_information.*');
        if (!empty($request->input('search.value'))) {
            $search = $request->input('search.value');
            $results = $results->where(function($query) use ($search) {
                $query->where('tags', 'LIKE', "%{$search}%");
            });
        }
        $totalFiltered = $results->count();
        $results = $results->offset($start)->limit($limit)->orderBy($order, $dir)->get();
        $data = array();
        if(!empty($results)) {
            $sno=1;
            foreach ($results as $row) {
                $nestedData['id'] = $sno++;
                $nestedData['user_name'] =$row->UserInformation->name;
                $nestedData['tags'] = $row->tags;
                $nestedData['action'] = getButtons([
                    ['key' => 'delete', 'link' => route('tags.destroy', $row->id)]
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
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        //
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
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try{
            $checkuser=UserotherInformation::where('id',$id)->first();
            if(!empty($checkuser)){
                if($checkuser->id){
                    Session::flash('warning', 'Tag name has been not deleted Becuse user assgin.');
                    return redirect()->back();
                }
           }
            $row = UserotherInformation::where('id', $id)->delete();
            Session::flash('success', 'Tag name has been deleted successfully.');
            return redirect()->back();

        }catch(\Exception $e){
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }
}
