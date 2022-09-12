<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\AppDetailRequest;
use Illuminate\Support\Facades\Storage;
use App\Models\AppDetail;

class AppDetailController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $app_details = AppDetail::all();
        return view('admin.pages.app-details.create',compact('app_details'))->with(['custom_title' => 'App Details']);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(AppDetailRequest $request)
    {
        $app_details = AppDetail::all();

        if( $request->has('verification_image_male') ) {
            Storage::delete($app_details[0]->value);

            $path_image_male = $request->file('verification_image_male')->store('general/files');
            $app_details[0]['value'] = $path_image_male;
            $app_details[0]->save();
        }
        if( $request->has('verification_video_male') ) {
            Storage::delete($app_details[1]->value);

            $path_video_male = $request->file('verification_video_male')->store('general/files');
            $app_details[1]['value'] = $path_video_male;
            $app_details[1]->save();
        }

        if( $request->has('verification_image_female') ) {

            Storage::delete($app_details[2]->value);
            $path_image_female = $request->file('verification_image_female')->store('general/files');
            $app_details[2]['value'] = $path_image_female;
            $app_details[2]->save();
        }
        if( $request->has('verification_video_female') ) {

            Storage::delete($app_details[3]->value);
            $path_video_female = $request->file('verification_video_female')->store('general/files');
            $app_details[3]['value'] = $path_video_female;
            $app_details[3]->save();
        }

        flash('App details updated successfully!')->success();
        return redirect(route('admin.app-details.index'));
    }
}
