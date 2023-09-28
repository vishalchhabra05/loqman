<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\Rating;

class RatingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $title_page = 'User Review & Rating Management';
        $breadcumb = [' User Review & Rating Management'=>''];
        return view('admin.rating.list_rating',compact('title_page','breadcumb'));
    }


    public function rating_expert(){
        $title_page = 'Expert Review & Rating Management';
        $breadcumb = ['Expert Review & Rating Management'=>''];
        return view('admin.rating.expert_list_rating',compact('title_page','breadcumb'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */


    public function datatable(Request $request){
        $columns = ['id', 'recive_id', 'sender_id', 'bages', 'rating','created_at'];
        $totalData = Rating::join('users', 'userrating.sender_id', '=', 'users.id')->whereIn('users.role',array('2','4'))->count();
        $limit = $request->input('length');
        $start = $request->input('start');
        $dir = $request->input('order.0.dir');
        $order = $columns[$request->input('order.0.column')];
        $pages = Rating::select('userrating.*')->join('users', 'userrating.sender_id', '=', 'users.id')->whereIn('users.role',array('2','4'));
        if (!empty($request->input('search.value'))) {
            $search = $request->input('search.value');
            $pages = $pages->where(function($query) use ($search) {
                $query->where('users.name', 'LIKE', "%{$search}%");
            });
        }
        $totalFiltered = $pages->count();
        $pages = $pages->offset($start)->limit($limit)->orderBy($order, $dir)->get();
        $data = array();
        if (!empty($pages)) {
            $sno=1;
            foreach ($pages as $key => $row) {
                $nestedData['id'] = $sno++;
                $nestedData['sender_id'] = $row->Getratinguser->name;
                $nestedData['recive_id'] = $row->Getexpetratinguser->name;
                $nestedData['bages'] = $row->bages;
                $nestedData['rating'] = $row->rating;
                $nestedData['created_at'] = date('Y-m-d',strtotime($row->created_at));
                $nestedData['action'] =  getButtons([
                   
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

    public function ratingDatatable(Request $request){
        $columns = ['id', 'recive_id', 'sender_id', 'bages', 'rating','created_at'];
        $totalData = Rating::join('users', 'userrating.recive_id', '=', 'users.id')->where('users.role','3')->count();
        $limit = $request->input('length');
        $start = $request->input('start');
        $dir = $request->input('order.0.dir');
        $order = $columns[$request->input('order.0.column')];
        $pages = Rating::select('userrating.*')->join('users', 'userrating.recive_id', '=', 'users.id')->where('users.role','3');
        if (!empty($request->input('search.value'))) {
            $search = $request->input('search.value');
            $pages = $pages->where(function($query) use ($search) {
                $query->where('users.name', 'LIKE', "%{$search}%");
            });
        }
        $totalFiltered = $pages->count();
        $pages = $pages->offset($start)->limit($limit)->orderBy($order, $dir)->get();
        $data = array();
        if (!empty($pages)) {
            $sno=1;
            foreach ($pages as $key => $row) {
                $nestedData['id'] = $sno++;
                $nestedData['sender_id'] = $row->Getratinguser->name;
                $nestedData['recive_id'] = $row->Getexpetratinguser->name;
                $nestedData['bages'] = $row->bages;
                $nestedData['rating'] = $row->rating;
                $nestedData['created_at'] = date('Y-m-d',strtotime($row->created_at));
                $nestedData['action'] =  getButtons([
                   
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

    public function create()
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
        //
    }
}
