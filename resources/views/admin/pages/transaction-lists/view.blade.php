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
                        <div class="form-group col-md-6">
                            <label class="control-label"><span class="mendatory" style="font-size: 20px;"></span>
                                <h1>User Transaction Information</h1>
                            </label>

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
                                <label class="control-label"><span class="mendatory" style="font-size: 20px;"></span>Razor
                                    Pay Order Id : <b>
                                        @if ($tran->razorpay_order_id)
                                            {{ $tran->razorpay_order_id }}
                                        @else
                                            -
                                        @endif
                                    </b></label>
                            </div>
                            <div class="mb-2">
                                <label class="control-label"><span class="mendatory" style="font-size: 20px;"></span>Full
                                    Name : <b>
                                        @if ($tran->user_id)
                                            {{ $tran->user->userTransDefault ? $tran->user->userTransDefault->full_name : 'N/A' }}
                                        @else
                                            -
                                        @endif
                                    </b></label>
                            </div>
                            <div class="mb-2">
                                <label class="control-label"><span class="mendatory" style="font-size: 20px;"></span>Account
                                    Id : <b>
                                        @if ($tran->user->account_id)
                                            {{ $tran->user->account_id }}
                                        @else
                                            -
                                        @endif
                                    </b></label>
                            </div>
                            <div class="mb-2">
                                <label class="control-label"><span class="mendatory" style="font-size: 20px;"></span>Email:
                                    <b>
                                        @if ($tran->user->email)
                                            {{ $tran->user->email }}
                                        @else
                                            -
                                        @endif
                                    </b></label>
                            </div>
                            <div class="mb-2">
                                <label class="control-label"><span class="mendatory" style="font-size: 20px;"></span>Amount
                                    : <b>
                                        @if ($tran->amount)
                                            {{ $tran->amount }}
                                        @else
                                            -
                                        @endif
                                    </b></label>
                            </div>


                            <div class="mb-2">
                                <label class="control-label"><span class="mendatory" style="font-size: 20px;"></span>Status
                                    : <b>
                                        @if ($tran->status)
                                            {{ $tran->status }}
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
                        </div>

                    </div>
                    <br><br>
                </div>
            </div>
        </div>
    </div>
@endsection
