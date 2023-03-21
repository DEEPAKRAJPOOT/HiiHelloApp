<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Coupon,CouponVendor,SubscriptionPlan};
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;
use App\Http\Requests\Admin\CouponRequest;
use Illuminate\Support\Facades\Storage;

class CouponController extends Controller
{
    public function index()
    {
        $coupon_vendors = CouponVendor::whereIsActive('y')->orderBy('name','asc')->get();
        $subscription_plans = SubscriptionPlan::whereIsActive('y')->with(['subscriptionPlanTransDefault'])->get();
        return view('admin.pages.coupon.index',compact('coupon_vendors','subscription_plans'))->with(['custom_title' => 'Coupons']);
    }
    public function show(Coupon $coupon)
    {
        return view('admin.pages.coupon.view', compact('coupon'))->with(['custom_title' => 'Coupon']);
    }
    
    public function create()
    {
        $coupon_vendors = CouponVendor::whereIsActive('y')->orderBy('name','asc')->get();
        $subscription_plans = SubscriptionPlan::whereIsActive('y')->with(['subscriptionPlanTransDefault'])->get();
        return view('admin.pages.coupon.create',compact('coupon_vendors','subscription_plans'))->with(['custom_title' => 'Coupon']);
    }

    public function edit(Coupon $coupon)
    {
        $coupon_vendors = CouponVendor::whereIsActive('y')->orderBy('name','asc')->get();
        $subscription_plans = SubscriptionPlan::whereIsActive('y')->with(['subscriptionPlanTransDefault'])->get();
        return view('admin.pages.coupon.edit',compact('coupon','coupon_vendors','subscription_plans'))->with(['custom_title' => 'Coupon']);
    }

    public function store(CouponRequest $request)
    {
        $data['plan_id'] = $request->plan_id ? $request->plan_id : NULL;
        $data['vendor_id'] = $request->vendor_id ? $request->vendor_id : NULL;
        $data['custom_id'] = getUniqueString('coupons');
        $data['coupon'] = $request->coupon;
        $data['title'] = $request->title;
        $data['value'] = ($request->value && $request->type == 'percentage') ? $request->value : NULL;
        $data['expired_at'] = now()->create($request->expired_at)->format('Y-m-d H:i:s');
        $data['description'] = $request->description ? $request->description : NULL;
        $data['type'] = $request->type;
        $data['is_universal'] = $request->is_universal;
        $data['is_reusable'] = $request->is_reusable;
        $data['is_self_hosted'] = $request->is_self_hosted;
        $data['is_active'] = $request->is_active;
        if($request->hasFile('coupon_image')){
            $data['image'] = $request->file('coupon_image')->store('coupon');
        }
        $coupon = Coupon::create($data);
        $coupon->is_active = $request->is_active;

        if ($coupon->save()) {
            flash('Coupon created successfully!')->success();
        } else {
            flash('Unable to save Coupon. Please try again later.')->error();
        }
        return redirect(route('admin.coupons.index'));
    }

    public function listing(Request $request)
    {
        extract($this->DTFilters($request->all()));
        $records = [];
        $coupons = Coupon::with(['subscriptionPlanTransDefault'])->orderBy($sort_column, $sort_order);

        if ($search != '') {
            $coupons->where(function ($query) use ($search) {
                $query->where('custom_id', 'like', "%{$search}%")
                    ->orWhere('coupon', 'like', "%{$search}%")
                    ->orWhere('title', 'like', "%{$search}%")
                    ->orWhere('value', 'like', "%{$search}%")
                    ->orWhereHas('subscriptionPlanTranslations', function ($query) use ($search) {
                        $query->where('name', 'like', "%{$search}%")
                            ->orWhere('description', 'like', "%{$search}%")
                            ->orWhere('note', 'like', "%{$search}%");
                    })
                    ->orWhereHas('vendor', function ($query) use ($search) {
                        $query->where('name', 'like', "%{$search}%");
                    });
            });
        }
        if(!empty($request->search_expiry_after)){
            $coupons->where('expired_at','>=',now()->create($request->search_expiry_after)->format('Y-m-d H:i:s'));
        }
        if(!empty($request->search_expiry_before)){
            $coupons->where('expired_at','<=',now()->create($request->search_expiry_before)->format('Y-m-d H:i:s'));
        }
        if(!empty($request->search_plan)){
            $coupons->where('plan_id',$request->search_plan);
        }
        if(!empty($request->search_vendor)){
            $coupons->where('vendor_id',$request->search_vendor);
        }

        $count = $coupons->count();

        $records['recordsTotal'] = $count;
        $records['recordsFiltered'] = $count;
        $records['data'] = [];

        $coupons->offset($offset)->limit($limit)->orderBy($sort_column, $sort_order);

        $coupons = $coupons->get();

        foreach ($coupons as $coupon) {

            $params = [
                'id' => $coupon->custom_id,
                'checked' => ($coupon->is_active == 'y' ? 'checked' : ''),
                'getaction' => $coupon->is_active,
                'class' => '',
            ];

            $records['data'][] = [
                'id' => $coupon->id,
                'image' => view('admin.layouts.includes.table_image')->with(['image' => $coupon->image, 'image_type' => 'coupon'])->render(),
                'plan' => $coupon->subscriptionPlanTransDefault ? $coupon->subscriptionPlanTransDefault->name : "",
                'vendor' => $coupon->vendor ? $coupon->vendor->name : "",
                'title' => $coupon->title,
                'coupon' => $coupon->coupon,
                'type' => ucfirst($coupon->type),
                'is_universal' => $coupon->is_universal,
                'is_reusable' => $coupon->is_reusable,
                'is_self_hosted' => $coupon->is_self_hosted,
                'expired_at' => $coupon->expired_at ? now()->create($coupon->expired_at)->format('Y-m-d') : 'N/A',
                'is_active' => view('admin.layouts.includes.switch', compact('params'))->render(),
                'action' => view('admin.layouts.includes.actions')->with(['custom_title' => 'Coupons', 'id' => $coupon->custom_id], $coupon)->render(),
                'checkbox' => view('admin.layouts.includes.checkbox')->with('id', $coupon->custom_id)->render(),
            ];
        }
        return $records;
    }

    public function update(CouponRequest $request, Coupon $coupon)
    {
        if (!empty($request->action) && $request->action == 'change_status') {
            $content = ['status' => 204, 'message' => "Something went wrong"];
            if ($coupon) {
                $coupon->is_active = $request->value;
                if ($coupon->save()) {
                    $content['status'] = 200;
                    $content['message'] = "Status updated successfully.";
                }
            }
            return response()->json($content);
        } else {
            $data['plan_id'] = $request->plan_id ? $request->plan_id : NULL;
            $data['vendor_id'] = $request->vendor_id ? $request->vendor_id : NULL;
            $data['coupon'] = $request->coupon;
            $data['title'] = $request->title;
            $data['value'] = ($request->value && $request->type == 'percentage') ? $request->value : NULL;
            $data['expired_at'] = now()->create($request->expired_at)->format('Y-m-d H:i:s');
            $data['description'] = $request->description ? $request->description : NULL;
            $data['type'] = $request->type;
            $data['is_universal'] = $request->is_universal;
            $data['is_reusable'] = $request->is_reusable;
            $data['is_self_hosted'] = $request->is_self_hosted;
            $data['is_active'] = $request->is_active;
            if ($request->has('remove_coupon_image')){
                if($coupon->image){
                    Storage::delete($coupon->image);
                    $coupon->image = null;
                }
                $data['image'] = null;
            }
            if($request->hasFile('coupon_image')){
                if($coupon->image){
                    Storage::delete($coupon->image);
                }
                $data['image'] = $request->file('coupon_image')->store('coupon');
            }
            $coupon->update($data);
            $coupon->is_active = $request->is_active;

            if ($coupon->save()) {
                flash('Coupon details updated successfully!')->success();
            } else {
                flash('Unable to update coupon details. Try again later')->error();
            }
            return redirect(route('admin.coupons.index'));
        }
    }

    public function destroy(Request $request, $id)
    {
        if (!empty($request->action) && $request->action == 'delete_all') {
            $content = ['status' => 204, 'message' => "something went wrong"];
            $coupons = Coupon::whereIn('custom_id', explode(',', $request->ids))->get();
            foreach ($coupons as $coupon) {
                $coupon->delete();
            }
            $content['status'] = 200;
            $content['message'] = "Coupons deleted successfully.";
            $content['count'] = Coupon::all()->count();
            return response()->json($content);
        } else {
            $coupon = Coupon::where('custom_id', $id)->firstOrFail();
            $coupon->delete();
            if (request()->ajax()) {
                $content = array('status' => 200, 'message' => "Coupon deleted successfully.", 'count' => Coupon::all()->count());
                return response()->json($content);
            } else {
                flash('Coupon deleted successfully.')->success();
                return redirect()->route('admin.coupons.index');
            }
        }
    }
}
