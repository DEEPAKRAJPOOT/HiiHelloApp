@extends('admin.layouts.app')

@push('breadcrumb')
    {!! Breadcrumbs::render('coupon_view', $coupon->id) !!}
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
                                    Subscription Plan : <b>
                                        @if (!empty($coupon->subscriptionPlanTransDefault))
                                            {{ $coupon->subscriptionPlanTransDefault->name ?? '-' }}
                                        @else
                                            N/A
                                        @endif
                                    </b>
                                </label>
                            </div>
                            <div class="mb-2">
                                <label class="control-label"><span class="mendatory" style="font-size: 20px;"></span>
                                    Vendor : <b>
                                        @if (!empty($coupon->vendor))
                                            {{ $coupon->vendor->name ?? '-' }}
                                        @else
                                            N/A
                                        @endif
                                    </b>
                                </label>
                            </div>
                            <div class="mb-2">
                                <label class="control-label"><span class="mendatory" style="font-size: 20px;"></span>
                                    Title : <b>
                                        @if (!empty($coupon->title))
                                            {{ $coupon->title }}
                                        @else
                                            -
                                        @endif
                                    </b>
                                </label>
                            </div>
                            <div class="mb-2">
                                <label class="control-label"><span class="mendatory" style="font-size: 20px;"></span>
                                    Coupon Code : <b>
                                        @if (!empty($coupon->coupon))
                                            {{ $coupon->coupon }}
                                        @else
                                            -
                                        @endif
                                    </b>
                                </label>
                            </div>
                            <div class="mb-2">
                                <label class="control-label"><span class="mendatory" style="font-size: 20px;"></span>
                                    Description : <b>
                                        @if (!empty($coupon->description))
                                            {!! nl2br(e($coupon->description)) !!}
                                        @else
                                            -
                                        @endif
                                    </b>
                                </label>
                            </div>
                            <div class="mb-2">
                                <label class="control-label"><span class="mendatory" style="font-size: 20px;"></span>
                                    Coupon Type : <b>
                                        @if (!empty($coupon->type))
                                            {{ ucfirst($coupon->type) }}
                                            @if(!empty($coupon->value))
                                            - {{ $coupon->value }}
                                            @endif
                                        @else
                                            -
                                        @endif
                                    </b>
                                </label>
                            </div>
                            <div class="mb-2">
                                <label class="control-label"><span class="mendatory" style="font-size: 20px;"></span>
                                    Universal : <b>
                                        @if(($coupon->is_universal ?? '') == 'y')
                                            Yes
                                        @else
                                            No
                                        @endif
                                    </b>
                                </label>
                            </div>
                            <div class="mb-2">
                                <label class="control-label"><span class="mendatory" style="font-size: 20px;"></span>
                                    Re Usable : <b>
                                        @if(($coupon->is_reusable ?? '') == 'y')
                                            Yes
                                        @else
                                            No
                                        @endif
                                    </b>
                                </label>
                            </div>
                            <div class="mb-2">
                                <label class="control-label"><span class="mendatory" style="font-size: 20px;"></span>
                                    Self Hosted: <b>
                                        @if(($coupon->is_self_hosted ?? '') == 'y')
                                            Yes
                                        @else
                                            No
                                        @endif
                                    </b>
                                </label>
                            </div>
                            <div class="mb-2">
                                <label class="control-label"><span class="mendatory" style="font-size: 20px;"></span>
                                    Expires at: <b>
                                        @if(!empty($coupon->expired_at))
                                            {{ now()->create($coupon->expired_at)->format('Y-m-d H:i:s') }}
                                        @else
                                            N/A
                                        @endif
                                    </b>
                                </label>
                            </div>
                            <div class="mb-2">
                                <label class="control-label"><span class="mendatory" style="font-size: 20px;"></span>
                                    Status : <b>
                                        @if(($coupon->is_active ?? '') == 'y')
                                            Active
                                        @else
                                            Inactive
                                        @endif
                                    </b>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
