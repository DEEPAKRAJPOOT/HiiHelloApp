<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Coupon,CouponVendor};
use Illuminate\Http\{Request,Response};
use App\Http\Requests\Admin\CouponVendorRequest;

class CouponVendorController extends Controller
{
    public function index()
    {
        return view('admin.pages.coupon-vendor.index')->with(['custom_title' => 'Vendors']);
    }
    public function show($custom_id)
    {
        $coupon_vendor = CouponVendor::whereCustomId($custom_id)->firstOrFail();
        return view('admin.pages.coupon-vendor.view', ["coupon_vendor" => $coupon_vendor])->with(['custom_title' => 'Vendor']);
    }
    public function create()
    {
        return view('admin.pages.coupon-vendor.create')->with(['custom_title' => 'Vendor']);
    }

    public function edit(CouponVendor $coupon_vendor)
    {
        return view('admin.pages.coupon-vendor.edit', compact('coupon_vendor'))->with(['custom_title' => 'Vendor']);
    }

    public function store(CouponVendorRequest $request)
    {
        $data['custom_id'] = getUniqueString('coupon_vendors');
        $data['name'] = $request->name;
        $data['is_active'] = $request->is_active;

        $coupon_vendor = CouponVendor::create($data);
        $coupon_vendor->is_active = $request->is_active;

        if ($coupon_vendor->save()) {
            if($request->expectsJson()){
                $vendors = CouponVendor::select('id','name')->whereIsActive('y')->orderBy('name','asc')->get();
                $vendor_id = $coupon_vendor->id;
                return response()->json(compact('vendors','vendor_id'));
            }
            flash('Vendor created successfully!')->success();
        } else {
            if($request->expectsJson()){
                return response()->json([],Response::HTTP_INTERNAL_SERVER_ERROR);
            }
            flash('Unable to save Vendor. Please try again later.')->error();
        }
        return redirect(route('admin.coupon-vendors.index'));
    }


    public function listing(Request $request)
    {
        extract($this->DTFilters($request->all()));
        $records = [];
        $coupon_vendors = CouponVendor::orderBy($sort_column, $sort_order);

        if ($search != '') {
            $coupon_vendors->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('custom_id', 'like', "%{$search}%");
            });
        }

        $count = $coupon_vendors->count();

        $records['recordsTotal'] = $count;
        $records['recordsFiltered'] = $count;
        $records['data'] = [];

        $coupon_vendors = $coupon_vendors->offset($offset)->limit($limit)->orderBy($sort_column, $sort_order);

        $coupon_vendors = $coupon_vendors->get();

        foreach ($coupon_vendors as $coupon_vendor) {

            $params = [
                'id' => $coupon_vendor->custom_id,
                'checked' => ($coupon_vendor->is_active == 'y' ? 'checked' : ''),
                'getaction' => $coupon_vendor->is_active,
                'class' => '',
            ];

            $records['data'][] = [
                'id' => $coupon_vendor->id,
                'name' => $coupon_vendor->name ? $coupon_vendor->name : "",
                'is_active' => view('admin.layouts.includes.switch', compact('params'))->render(),
                'action' => view('admin.layouts.includes.actions')->with(['custom_title' => 'Vendors', 'id' => $coupon_vendor->custom_id], $coupon_vendor)->render(),
                'checkbox' => view('admin.layouts.includes.checkbox')->with('id', $coupon_vendor->custom_id)->render(),
            ];
        }
        return $records;
    }

    public function update(CouponVendorRequest $request, CouponVendor $coupon_vendor)
    {
        if (!empty($request->action) && $request->action == 'change_status') {
            $content = ['status' => 204, 'message' => "Something went wrong"];
            if ($coupon_vendor) {
                $coupon_vendor->is_active = $request->value;
                if ($coupon_vendor->save()) {
                    $content['status'] = 200;
                    $content['message'] = "Status updated successfully.";
                }
            }
            return response()->json($content);
        } else {
            $data['name'] = $request->name;
            $data['is_active'] = $request->is_active;

            $coupon_vendor->update($data);
            $coupon_vendor->is_active = $request->is_active;

            if ($coupon_vendor->save()) {
                flash('Vendor details updated successfully!')->success();
            } else {
                flash('Unable to update vendor details. Try again later')->error();
            }
            return redirect(route('admin.coupon-vendors.index'));
        }
    }

    public function destroy(Request $request, $id)
    {
        if (!empty($request->action) && $request->action == 'delete_all') {
            $content = ['status' => 204, 'message' => "something went wrong"];
            $coupon_vendors = CouponVendor::whereIn('custom_id', explode(',', $request->ids))->get();
            foreach ($coupon_vendors as $coupon_vendor) {
                $coupon_vendor->coupons()->delete();
                $coupon_vendor->delete();
            }
            $content['status'] = 200;
            $content['message'] = "Vendors deleted successfully.";
            $content['count'] = CouponVendor::all()->count();
            return response()->json($content);
        } else {
            $coupon_vendor = CouponVendor::where('custom_id', $id)->firstOrFail();
            $coupon_vendor->coupons()->delete();
            $coupon_vendor->delete();
            if (request()->ajax()) {
                $content = array('status' => 200, 'message' => "Vendor deleted successfully.", 'count' => CouponVendor::all()->count());
                return response()->json($content);
            } else {
                flash('Vendor deleted successfully.')->success();
                return redirect()->route('admin.coupon-vendors.index');
            }
        }
    }
}
