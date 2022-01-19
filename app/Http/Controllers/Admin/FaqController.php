<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Faq;
use App\Models\Language;
use App\Http\Requests\Admin\FaqRequest;

class FaqController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.pages.faqs.index')->with(['custom_title' => 'Faqs']);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $languages = Language::whereIsActive('y')->get();
        return view('admin.pages.faqs.create',compact('languages'))->with(['custom_title' => 'Faq', 'default_lang' => config('utility.default_lang_code')]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(FaqRequest $request)
    {                    
        $data = $this->getLangStoreData($request);
        $data['custom_id'] = getUniqueString('faqs');
        $faq = Faq::create($data);

        if ($faq->save()) {
            flash('Faq created successfully!')->success();
        } else {
            flash('Unable to save faq. Please try again later.')->error();
        }
        return redirect(route('admin.faqs.index'));
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
    public function edit(Faq $faq)
    {
        $languages = Language::whereIsActive('y')->get();
        return view('admin.pages.faqs.edit', compact('faq','languages'))->with(['custom_title' => 'Faq', 'default_lang' => config('utility.default_lang_code')]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(FaqRequest $request, Faq $faq)
    {
        if(!empty($request->action) && $request->action == 'change_status') {
            $content = ['status'=>204, 'message'=>"something went wrong"];
            if($faq) {
                $faq->is_active = $request->value;
                if($faq->save()) {
                    $content['status']=200;
                    $content['message'] = "Status updated successfully.";
                }
            }
            return response()->json($content);
        } else {
            $data = $this->getLangStoreData($request);
            $faq->update($data);
            if( $faq->save() ) {
                flash('Faq details updated successfully!')->success();
            } else {
                flash('Unable to faq. Try again later')->error();
            }
            return redirect(route('admin.faqs.index'));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        if(!empty($request->action) && $request->action == 'delete_all'){
            $content = ['status'=>204, 'message'=>"something went wrong"];
            $faqs = Faq::select('id')->whereIn('custom_id',explode(',',$request->ids))->get();
            foreach($faqs as $faq){
                $faq->faqTranslations()->delete();
                $faq->delete();
            }
            // Faq::whereIn('custom_id',explode(',',$request->ids))->delete();
            $content['status']=200;
            $content['message'] = "Faq deleted successfully.";
            $content['count'] = Faq::all()->count();
            return response()->json($content);
        }else{
            $faq = Faq::where('custom_id', $id)->firstOrFail();
            $faq->faqTranslations()->delete();
            $faq->delete();
            if(request()->ajax()){
                $content = array('status'=>200, 'message'=>"Faq deleted successfully.", 'count' => Faq::all()->count());
                return response()->json($content);
            }else{
                flash('Faq deleted successfully.')->success();
                return redirect()->route('admin.faqs.index');
            }
        }
    }

    public function listing(Request $request)
    {
        extract($this->DTFilters($request->all()));
        $records = [];
        $faqs = Faq::with('faqTranslations')->orderBy($sort_column, $sort_order);

        if ($search != '') {
            $faqs->where(function ($query) use ($search) {
                $query->where('custom_id', 'like', "%{$search}%")
                    ->orWhereHas('faqTranslations', function ($query) use ($search) {
                        $query->where('question', 'like', "%{$search}%")
                             ->orWhere('answer', 'like', "%{$search}%");
                    });
            });
        }

        $count = $faqs->count();
        $records['recordsTotal'] = $count;
        $records['recordsFiltered'] = $count;
        $records['data'] = [];

        $faqs = $faqs->offset($offset)->limit($limit)->orderBy($sort_column, $sort_order);
        $faqs = $faqs->get();

        foreach ($faqs as $faq) {
            $params = [
                'checked'       =>  ($faq->is_active == 'y' ? 'checked' : ''),
                'getaction'     =>  $faq->is_active,
                'class'         =>  '',
                'id'            =>  $faq->custom_id,
            ];

            $records['data'][] = [
                'id'            =>  $faq->id,
                'question'      =>  $faq->translate(config('utility.default_lang_code')) ? $faq->translate(config('utility.default_lang_code'))->question : "",
                'active'        =>  view('admin.layouts.includes.switch', compact('params'))->render(),
                'action'        =>  view('admin.layouts.includes.actions')->with(['custom_title' => 'Faq', 'id' => $faq->custom_id], $faq)->render(),
                'checkbox'      =>  view('admin.layouts.includes.checkbox')->with('id', $faq->custom_id)->render(),
            ];
        }
        return $records;
    }
}
