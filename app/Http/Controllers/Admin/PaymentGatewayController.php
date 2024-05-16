<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PaymentGateway;

class PaymentGatewayController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.pages.payment-gateway.index')->with(['custom_title' => 'Payment Gateway']);
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
        if(!empty($request->action) && $request->action == 'change_status') {
            $content = ['status'=>204, 'message'=>"something went wrong"];
            $status = PaymentGateway::find($id);
            if($status) {
                $status->flag_value = $request->value=='y'?1:0;
                if($status->save()) {
                    if($request->value === "n"){
                        PaymentGateway::where('id',$status->id)->update(['flag_value'=>'0']);
                    }
                    $content['status']=200;
                    $content['message'] = "Status updated successfully.";
                }
            }
            return response()->json($content);
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

    public function listing(Request $request){
        extract($this->DTFilters($request->all()));
        $statusListing = PaymentGateway::select('id','flag_constant','flag_value');
        if ($search != '') {
             $statusListing->where("flag_constant","like",'%'.$search.'%');
             $statusListing->orWhere("flag_constant","like",'%'.$search.'%');
       
        }
        $records['recordsTotal'] = $statusListing->count();
        $records['recordsFiltered'] = $statusListing->count();
        $statusListing = $statusListing->offset($offset)->limit($limit)->orderBy("id", "asc");
        $statusListing = $statusListing->get();
        $records['data'] = [];
        foreach ($statusListing as $status) {
         $params = [
             'checked'       =>  ((int)$status->flag_value == 1 ? 'checked' : ''),
             'getaction'     =>  $status->flag_value,
             'class'         =>  '',
             'id'            =>  $status->id,
         ];
           
         $records['data'][] = [
             'id'            =>  $status->id,
             'name'          =>  $status->flag_constant ?? "",
             'status'         =>  (int)$status->flag_value?'True':'false',
             'active'        =>  view('admin.layouts.includes.switch', compact('params'))->render(),
             // 'action'        =>  view('admin.layouts.includes.actions')->with(['custom_title' => 'Status', 'id' => $status->id,'type'], $status)->render(),
             'checkbox'      =>  view('admin.layouts.includes.checkbox')->with('id', $status->id)->render(),
         ];
     }
     // dd($records);
       return $records;
     }
}
