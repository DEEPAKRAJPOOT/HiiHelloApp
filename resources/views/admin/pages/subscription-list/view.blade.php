@extends('admin.layouts.app')

@push('breadcrumb')
    {!! Breadcrumbs::render('subscription_view', $sub->id) !!}
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
                                <h1>Subscription Information</h1>
                            </label>
                            <div class="mb-2">

                                @if ($sub->user && $sub->user->profile_photo)
                                    <div class="symbol symbol-120 mr-5">
                                        <a href="{{ generateURL($sub->user->profile_photo) }}" target="_blank"
                                            style="margin: 10px;">
                                            <div class="symbol-label"
                                                style="background-image:url({{ generateURL($sub->user->profile_photo) }})">
                                            </div>
                                        </a>
                                    </div>
                                @endif
                            </div>

                            <div class="mb-2">
                                <label class="control-label"><span class="mendatory" style="font-size: 20px;"></span>Full
                                    Name : <b>
                                        @if ($sub->user && $sub->user->userTransDefault)
                                            {{ $sub->user->userTransDefault ? $sub->user->userTransDefault->full_name : 'N/A' }}
                                        @else
                                            -
                                        @endif
                                    </b></label>
                            </div>

                            <div class="mb-2">
                                <label class="control-label"><span class="mendatory" style="font-size: 20px;"></span>Account
                                    Id : <b>
                                        @if ($sub->user && $sub->user->account_id)
                                            {{ $sub->user->account_id }}
                                        @else
                                            -
                                        @endif
                                    </b></label>
                            </div>
                            <div class="mb-2">
                                <label class="control-label"><span class="mendatory" style="font-size: 20px;"></span>Email:
                                    <b>
                                        @if ($sub->user && $sub->user->email)
                                            {{ $sub->user->email }}
                                        @else
                                            -
                                        @endif
                                    </b></label>
                            </div>
                            <div class="mb-2">
                                <label class="control-label"><span class="mendatory" style="font-size: 20px;"></span>Amount
                                    : <b>
                                        @if ($sub->amount)
                                            {{ $sub->amount }}
                                        @else
                                            -
                                        @endif
                                    </b></label>
                            </div>
                            <div class="mb-2">
                                <label class="control-label"><span class="mendatory" style="font-size: 20px;"></span>Start
                                    Date: <b>
                                        @if ($sub->start_date)
                                            {{ $sub->start_date }}
                                        @else
                                            -
                                        @endif
                                    </b></label>
                            </div>
                            <div class="mb-2">
                                <label class="control-label"><span class="mendatory" style="font-size: 20px;"></span>End
                                    Date : <b>
                                        @if ($sub->end_date)
                                            {{ $sub->end_date }}
                                        @else
                                            -
                                        @endif
                                    </b></label>
                            </div>
                            <div class="mb-2">
                                <label class="control-label"><span class="mendatory" style="font-size: 20px;"></span>Payment
                                    Date: <b>
                                        @if ($sub->payment_date)
                                            {{ $sub->payment_date }}
                                        @else
                                            -
                                        @endif
                                    </b></label>
                            </div>
                            <div class="mb-2">
                                <label class="control-label"><span class="mendatory" style="font-size: 20px;"></span>Mohnth
                                    : <b>
                                        @if ($sub->months)
                                            {{ $sub->months }}
                                        @else
                                            -
                                        @endif
                                    </b></label>
                            </div>
                            <div class="mb-2">
                                <label class="control-label"><span class="mendatory" style="font-size: 20px;"></span>Status
                                    : <b>
                                        @if ($sub->status)
                                            {{ $sub->status }}
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
