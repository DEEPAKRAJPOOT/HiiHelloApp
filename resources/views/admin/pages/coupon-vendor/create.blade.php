@extends('admin.layouts.app')
@push('breadcrumb')
{!! Breadcrumbs::render('coupon_vendor_create') !!}
@endpush
@section('content')
<div class="container">
	<div class="card card-custom">
		<div class="card-header">
			<div class="card-title">
				<span class="card-icon">
					<i class="{{$icon}} text-primary"></i>
				</span>
				<h3 class="card-label text-uppercase">ADD {{ $custom_title }}</h3>
			</div>
		</div>
		<form id="formSaveCouponVendor" method="POST" action="{{ route('admin.coupon-vendors.store') }}">
			@csrf
			@include('admin.pages.coupon-vendor.partials.form')
			<div class="card-footer">
				<button type="submit" class="btn btn-primary mr-2"> Add {{ $custom_title }}</button>
				<a href="{{ route('admin.coupon-vendors.index') }}" class="btn btn-secondary">Cancel</a>
			</div>
		</form>
	</div>
</div>
@endsection