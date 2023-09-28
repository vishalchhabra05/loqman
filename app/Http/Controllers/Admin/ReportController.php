<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\UserCalling;

class ReportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $title_page = 'User Report Managment';
        $breadcumb = [' User Report Managment'=>''];
        return view('admin.report.seeker_reprot',compact('title_page','breadcumb'));
    }

    public function expertindex()
    {
        $title_page = 'Expert Report Managment';
        $breadcumb = [' Expert Report Managment'=>''];
        return view('admin.report.expert_report',compact('title_page','breadcumb'));
    }


    public function datatable(Request $request){
        $columns = ['id', 'send_id', 'recive_id', 'status', 'start_date','start_time'];
        $totalData = UserCalling::join('users', 'usercalling.recive_id', '=', 'users.id')->whereIn('users.role',array('2','4'))->count();
        $limit = $request->input('length');
        $start = $request->input('start');
        $dir = $request->input('order.0.dir');
        $order = $columns[$request->input('order.0.column')];
        $pages = UserCalling::select('usercalling.*')->join('users', 'usercalling.recive_id', '=', 'users.id')->whereIn('users.role',array('2','4'));
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
                $nestedData['send_id'] = $row->Getsenduser->name;
                $nestedData['recive_id'] = $row->Getreciveuser->name;
                if($row->status == "1"){
                    $nestedData['status'] = "Call Accpet";
                }elseif($row->status == "2"){
                    $nestedData['status'] = "Call Cancel";
                }elseif($row->status == "3"){
                    $nestedData['status'] = "Poke Call";
                }
                $nestedData['start_date'] = listDateFromat($row->start_date);
                $nestedData['start_time'] = $row->start_time;
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


    function expertDatatable(Request $request){
        $columns = ['id', 'send_id', 'recive_id', 'status', 'start_date','start_time'];
        $totalData = UserCalling::select('usercalling.*')->join('users', 'usercalling.send_id', '=', 'users.id')->where('users.role','3')->count();
        $limit = $request->input('length');
        $start = $request->input('start');
        $dir = $request->input('order.0.dir');
        $order = $columns[$request->input('order.0.column')];
        $pages = UserCalling::select('usercalling.*')->join('users', 'usercalling.send_id', '=', 'users.id')->where('users.role','3');
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
                $nestedData['send_id'] = $row->Getsenduser->name;
                $nestedData['recive_id'] = $row->Getreciveuser->name;
                if($row->status == "1"){
                    $nestedData['status'] = "Call Accpet";
                }elseif($row->status == "2"){
                    $nestedData['status'] = "Call Cancel";
                }elseif($row->status == "3"){
                    $nestedData['status'] = "Poke Call";
                }
                $nestedData['start_date'] = listDateFromat($row->start_date);
                $nestedData['start_time'] = $row->start_time;
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

    function seekerExcel(Request $request){
        try{
            $users = UserCalling::join('users', 'usercalling.recive_id', '=', 'users.id')->whereIn('users.role',array('2','4'))->get();

            
            header('Content-type: text/csv');
            header('Content-Disposition: attachment; filename="users.csv"');

            // do not cache the file
            header('Pragma: no-cache');
            header('Expires: 0');

            // create a file pointer connected to the output stream
            $file = fopen('php://output', 'w');

           fputcsv($file, array('Seeker Name','Date','Status','created_at'));

           if(!empty($users)){
            foreach($users as $user){
                $seekarname=$user->name;
                $date= $user->start_date;
                $status=($user->status==1) ? "Call accpet" : "Call cancel";
                $created_at= date('d-m-Y', strtotime($user->created_at));
                $row = [$seekarname,$date,$status,$created_at];
                fputcsv($file, $row);
            }
        }
        exit;

        }catch(\Exception $e){

        }
    }

    function expertExcel(Request $request){
        try{
            $users = UserCalling::join('users', 'usercalling.send_id', '=', 'users.id')->where('users.role','3')->get();

            
            header('Content-type: text/csv');
            header('Content-Disposition: attachment; filename="users.csv"');

            // do not cache the file
            header('Pragma: no-cache');
            header('Expires: 0');

            // create a file pointer connected to the output stream
            $file = fopen('php://output', 'w');

           fputcsv($file, array('Expert Name','Date','Status','created_at'));

           if(!empty($users)){
            foreach($users as $user){
                $seekarname=$user->name;
                $date= $user->start_date;
                $status=($user->status==1) ? "Call accpet" : "Call cancel";
                $created_at= date('d-m-Y', strtotime($user->created_at));
                $row = [$seekarname,$date,$status,$created_at];
                fputcsv($file, $row);
            }
        }
        exit;

        }catch(\Exception $e){

        }
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
