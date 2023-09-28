<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\Notification;
use Illuminate\Support\Facades\Validator;
use Session;

class NotificationManagmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $title_page = 'Notifications Management';
        $breadcumb = [$title_page => ''];
        return view('admin.notificationmanagment.list_notification', compact('title_page','breadcumb'));
    }


    public function datatable(Request $request){
        $columns = ['id', 'title','subject','message','status','created_at', 'action'];
        $totalData = Notification::count();
        $limit = $request->input('length');
        $start = $request->input('start');
        $dir = $request->input('order.0.dir');
        $order = $columns[$request->input('order.0.column')];
        $results = Notification::select('notification.*');
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
                $nestedData['subject'] = $row->subject;
                $nestedData['message'] = $row->message;
                $nestedData['status'] = getStatus($row->status,$row->id);
                $nestedData['created_at'] = listDateFromat($row->created_at);
                $nestedData['action'] = getButtons([
                    ['key' => 'edit', 'link' => route('notification.edit', $row->id)],
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
        try {
            $entity = Notification::where('id', $id)->first();
            if (empty($entity)) {
                Session::flash('warning', 'Invalid Request');
                return redirect()->back();
            }
            $title_page = 'Edit Notification';
            $breadcumb = ['Notification' => route('notification.index'), $title_page => ''];
            return view('admin.notificationmanagment.edit_notification', compact('breadcumb','title_page', 'entity'));
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
            $entity = Notification::where('id', $id)->first();
            if (empty($entity)) {
                Session::flash('warning', 'Invalid Request');
                return redirect()->back();
            }
            $validator = Validator::make($request->all(), [
                'subject' => 'required',
                'message' => 'required',
            ]);
            if ($validator->fails()) {
                Session::flash('error', 'Please correct the errors below and try again');
                return redirect()->back()->withInput()->withErrors($validator->errors());
            }
            $entity->subject = $request->subject;
            $entity->message = $request->message;
          
            $entity->save();
            Session::flash('success', 'Notification updated successfully.');
            return redirect()->route('notification.index');
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            Session::flash('warning', $msg);
            return redirect()->back()->withInput();
        }
    }

    public function updatestatus(Request $request){
        $user_id = $request->id;
        $row = Notification::whereId($user_id)->first();
        $row->status = ($row->status == '0' ? '1' : ($row->status == '1' ? '0' : '1'));
        $row->save();
        $html = '';
        switch ($row->status) {
            case '0':
                $html = '<a data-toggle="tooltip"  class="btn btn-danger btn-xs" title="Penidng" onClick="changeStatus(' . $user_id . ')" >Inactive</a>';
                break;
            case '1':
                $html = '<a data-toggle="tooltip"  class="btn btn-success btn-xs" title="Active" onClick="changeStatus(' . $user_id . ')" >Active</a>';
                break;
            default:
                break;
        }
        return $html;
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
