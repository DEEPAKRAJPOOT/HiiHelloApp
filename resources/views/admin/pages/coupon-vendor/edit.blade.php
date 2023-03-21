@extends('admin.layouts.app')
@push('breadcrumb')
{!! Breadcrumbs::render('coupon_vendor_update', $coupon_vendor->custom_id) !!}
@endpush
@section('content')
<div class="container">
	<div class="card card-custom">
		<div class="card-header">
			<div class="card-title">
				<span class="card-icon">
					<i class="{{$icon}} text-primary"></i>
				</span>
				<h3 class="card-label text-uppercase">Edit {{ $custom_title }}</h3>
			</div>
		</div>
		<form id="formSaveCouponVendor" method="POST" action="{{ route('admin.coupon-vendors.update', $coupon_vendor->custom_id) }}">
			@csrf
			@method('put')
			@include('admin.pages.coupon-vendor.partials.form')
			<div class="card-footer">
				<button type="submit" class="btn btn-primary mr-2">Update {{ $custom_title }}</button>
				<a href="{{ route('admin.coupon-vendors.index') }}" class="btn btn-secondary">Cancel</a>
			</div>
		</form>
	</div>
</div>
@endsection