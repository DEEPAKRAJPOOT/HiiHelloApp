@extends('admin.layouts.app')

@push('breadcrumb')
    {!! Breadcrumbs::render('coupon_vendor_view', $coupon_vendor->id) !!}
@endpush

@section('content')
    <div class="container">
        <div class="card card-custom">
            <div class="card-header">
                <div class="card-title">
                    <span class="card-icon">
                        <i class="{{ $icon }} text-primary"></i>
                    </span>
                    <h3 class="card-label text-uppercase">{{ $custom_title }} Details</h3>
                </div>
            </div>

            <div class="profile-content">
                <div class="form-group col-md-12">
                    <div class="form-group col-md-12 row" style="margin:15px;">
                        <div class="form-group col-md-6">
                            <div class="mb-2">
                                <label class="control-label"><span class="mendatory" style="font-size: 20px;"></span>
                                    Name : <b>
                                        @if (!empty($coupon_vendor->name))
                                            {{ $coupon_vendor->name }}
                                        @else
                                            -
                                        @endif
                                    </b></label>
                            </div>
                            <div class="mb-2">
                                <label class="control-label"><span class="mendatory" style="font-size: 20px;"></span>Status
                                    : <b>
                                        @if ($coupon_vendor->is_active)
                                            Active
                                        @else
                                            Inactive
                                        @endif
                                    </b></label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
