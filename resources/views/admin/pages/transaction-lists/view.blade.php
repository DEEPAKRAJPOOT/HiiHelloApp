@extends('admin.layouts.app')

@push('breadcrumb')
    {!! Breadcrumbs::render('transaction_view', $tran->id) !!}
@endpush

@section('content')
    <div class="container">
        <div class="card card-custom">
            <div class="card-header">
                <div class="card-title">
                    <span class="card-icon">
                        <i class="{{ $icon }} text-primary"></i>
                    </span>
                    <h3 class="card-label text-uppercase">View {{ $custom_title }} Details</h3>
                </div>
            </div>

            <div class="profile-content">
                <div class="form-group col-md-12">
                    <div class="form-group col-md-12 row" style="margin:15px;">
                        <div class="form-group col-md-12">
                            <div class="mb-2">
                                @if ($tran->user && $tran->user->profile_photo)
                                    <div class="symbol symbol-120 mr-5">
                                        <a href="{{ generateURL($tran->user->profile_photo) }}" target="_blank"
                                            style="margin: 10px;">
                                            <div class="symbol-label"
                                                style="background-image:url({{ generateURL($tran->user->profile_photo) }})">
                                            </div>
                                        </a>
                                    </div>
                                @endif
                            </div>
                            <div class="mb-2">
                                <label class="control-label"><span class="mendatory" style="font-size: 20px;"></span>Full
                                    Name : <b>
                                        @if ($tran->user && $tran->user->userTransDefault)
                                            {{ $tran->user->userTransDefault ? $tran->user->userTransDefault->full_name : 'N/A' }}
                                        @else
                                            -
                                        @endif
                                    </b></label>
                            </div>
                            <div class="mb-2">
                                <label class="control-label"><span class="mendatory" style="font-size: 20px;"></span>Account
                                    Id : <b>
                                        @if ($tran->user &&  $tran->user->account_id)
                                            {{ $tran->user->account_id }}
                                        @else
                                            -
                                        @endif
                                    </b></label>
                            </div>
                            <div class="mb-2">
                                <label class="control-label"><span class="mendatory" style="font-size: 20px;"></span>Email Id:
                                    <b>
                                        @if ($tran->user &&  $tran->user->email)
                                            {{ $tran->user->email }}
                                        @else
                                            -
                                        @endif
                                    </b></label>
                            </div>
                            <div class="mb-2">
                                <label class="control-label"><span class="mendatory" style="font-size: 20px;"></span>Subscription Plan:
                                    <b>
                                        @if ($tran->subscriptionPlan && $tran->subscriptionPlan->subscriptionPlanTransDefault)
                                            {{ $tran->subscriptionPlan->subscriptionPlanTransDefault->name }}
                                        @else
                                            -
                                        @endif
                                    </b></label>
                            </div>
                            <div class="mb-2">
                                <label class="control-label"><span class="mendatory" style="font-size: 20px;"></span>Amount:
                                    <b>
                                        @if ($tran->amount)
                                            ₹{{ $tran->amount }}
                                        @else
                                            -
                                        @endif
                                    </b>
                                </label>
                            </div>
                            <div class="mb-2">
                                <label class="control-label"><span class="mendatory" style="font-size: 20px;"></span>Month
                                    : <b>
                                        @if ($tran->subscriptionPlan && $tran->subscriptionPlan->months)
                                            {{ $tran->subscriptionPlan->months }}
                                        @else
                                            -
                                        @endif
                                    </b></label>
                            </div>
                            <div class="mb-2">
                                <label class="control-label"><span class="mendatory" style="font-size: 20px;"></span>Order Id : <b>
                                        @if ($tran->razorpay_order_id)
                                            {{ $tran->razorpay_order_id }}
                                        @else
                                            -
                                        @endif
                                    </b></label>
                            </div>
                            <div class="mb-2">
                                <label class="control-label"><span class="mendatory" style="font-size: 20px;"></span>Razor
                                    Pay Payment Id : <b>
                                        @if ($tran->razorpay_payment_id)
                                            {{ $tran->razorpay_payment_id }}
                                        @else
                                            -
                                        @endif
                                    </b></label>
                            </div>
                            <div class="mb-4">
                                <label class="control-label"><span class="mendatory" style="font-size: 20px;"></span>Razor
                                    Pay Signature : <b style="word-break:break-all">
                                        @if ($tran->razorpay_signature)
                                            {{ $tran->razorpay_signature }}
                                        @else
                                            -
                                        @endif
                                    </b></label>
                            </div>
                            <div class="mb-2">
                                <label class="control-label"><span class="mendatory" style="font-size: 20px;"></span>Status
                                    : <b>
                                        @if ($tran->status)
                                            {{ ucfirst($tran->status) }}
                                        @else
                                            -
                                        @endif
                                    </b></label>
                            </div>
                            <div class="mb-2">
                                <label class="control-label"><span class="mendatory"
                                        style="font-size: 20px;"></span>Transaction Date : <b>
                                        @if ($tran->created_at)
                                            {{ date('Y-m-d', strtotime($tran->created_at)) }}
                                        @else
                                            -
                                        @endif
                                    </b></label>
                            </div>
                            <div class="mb-2">
                                <label class="control-label">
                                    <span class="mendatory" style="font-size: 20px;"></span>
                                    Transaction Mode :
                                    <b>
                                        @if ($tran->payment_type)
                                            {{ ucfirst(strtolower($tran->payment_type)) }}
                                            
                                        @else
                                            -
                                        @endif
                                    </b>
                                </label>
                            </div>
                            <div class="mb-2">
                                <label class="control-label"><span class="mendatory"
                                        style="font-size: 20px;"></span>End Date : <b>
                                        @if ($tran->subscription_end_date)
                                            {{ date('Y-m-d', strtotime($tran->subscription_end_date)) }}
                                        @else
                                            -
                                        @endif
                                </b></label>
                            </div>
                            @if(!empty($tran->payment_type) && $tran->payment_type == 'COUPON')
                                @if($tran->coupon_name)
                                    <div class="mb-2">
                                        <label class="control-label">Coupon Applied :
                                            <b>
                                                {{ $tran->coupon_name }}
                                            </b>
                                        </label>
                                    </div>
                                @endif
                                @if(!empty($tran->couponVendor) && !empty($tran->couponVendor->name))
                                    <div class="mb-2">
                                        <label class="control-label">Coupon Vendor :
                                            <b>
                                                {{ $tran->couponVendor->name }}
                                            </b>
                                        </label>
                                    </div>
                                @endif
                            @endif
                            @if(!empty($tran->refunded_amount))
                                <div class="mb-2">
                                    <label class="control-label">
                                        <span class="mendatory" style="font-size:20px"></span>
                                        Refund Given :
                                        <b>
                                            ₹{{ $tran->refunded_amount }}
                                        </b>
                                    </label>
                                </div>
                            @endif
                            @if($tran->payment_type && ($tran->payment_type == 'UPI') && (($tran->refunded_amount ?? 0) < $tran->amount))
                                <div class="mb-2">
                                    <a href="javascript:void(0)" class="btn btn-sm btn-primary font-weight-bolder" data-toggle="modal" data-target="#refundPaymentModal">Refund Payment</a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="refundPaymentModal" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Refund Payment</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <form id="refund-form" class="mb-0" method="post" action="{{ route('admin.transaction-lists.refund-transaction') }}">
                        @csrf
                        <input type="hidden" name="custom_transaction_id" value="{{ $tran->custom_id ?? '' }}">
                        <div class="form-group">
                            <label>Total Amount: <b>₹{{ floatval($tran->amount ?? 0) }}</b></label>
                        </div>
                        @if(!empty($tran->refunded_amount))
                            <div class="form-group">
                                <label>Already Refunded: <b>₹{{ $tran->refunded_amount }}</b></label>
                            </div>
                        @endif
                        <div class="form-group">
                            <label>Refund Amount</label>
                            <input type="number" class="form-control" name="amount_to_refund" step="1" placeholder="Amount to be refunded" spellcheck="false" value="{{ floatval(($tran->amount ?? 0) - ($tran->refunded_amount ?? 0)) }}" min="0" max="{{ floatval(($tran->amount ?? 0) - ($tran->refunded_amount ?? 0)) }}" required="required">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button id="refund-submit" type="button" class="btn btn-primary">Refund</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>      
        </div>
    </div>
@endsection
@push('extra-js-scripts')
<script type="text/javascript">
    $('#refund-submit').click(function(){
        if(!$('#refund-form').get(0).checkValidity()){
            $('#refund-form').get(0).reportValidity();
            return false;
        }
        if(!confirm('Are you sure you want to refund ₹'+$('[name="amount_to_refund"]').val()+' to the user?')){
            return false;
        }
        $('#refund-form').submit();
    });
</script>
@endpush