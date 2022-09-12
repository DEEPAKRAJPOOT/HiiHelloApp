@extends('admin.layouts.app')

@push('breadcrumb')
    {!! Breadcrumbs::render('call_log_view', $call->id) !!}
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
                            <b>Creator Detail</b>
                            <div class="mb-2">
                                @if ($call->room && $call->room->creator && $call->room->creator->profile_photo)
                                    <div class="symbol symbol-120 mr-5">
                                        <a href="{{ generateURL($call->room->creator->profile_photo) }}" target="_blank"
                                            style="margin: 10px;">
                                            <div class="symbol-label"
                                                style="background-image:url({{ generateURL($call->room->creator->profile_photo) }})">
                                            </div>
                                        </a>
                                    </div>
                                @endif
                            </div>

                            <div class="mb-2">
                                <label class="control-label"><span class="mendatory" style="font-size: 20px;"></span>Full
                                    Name : <b>
                                        @if ($call->room && $call->room->creator && $call->room->creator->userTransDefault)
                                            {{ $call->room->creator->userTransDefault ? $call->room->creator->userTransDefault->full_name : 'N/A' }}
                                        @else
                                            -
                                        @endif
                                    </b></label>
                            </div>

                            <div class="mb-2">
                                <label class="control-label"><span class="mendatory" style="font-size: 20px;"></span>Account
                                    Id : <b>
                                        @if ($call->room && $call->room->creator && $call->room->creator->account_id)
                                            {{ $call->room->creator->account_id }}
                                        @else
                                            -
                                        @endif
                                    </b></label>
                            </div>
                            <div class="mb-2">
                                <label class="control-label"><span class="mendatory" style="font-size: 20px;"></span>Email:
                                    <b>
                                        @if ($call->room && $call->room->creator && $call->room->creator->email)
                                            {{ $call->room->creator->email }}
                                        @else
                                            -
                                        @endif
                                    </b></label>
                            </div>
                            
                        </div>
                        
                        <div class="form-group col-md-6">
                            <b>Participator Detail</b>
                            <div class="mb-2">
                                @if ($call->room && $call->room->participator &&  $call->room->participator->userTransEn && $call->room->participator->profile_photo)
                                    <div class="symbol symbol-120 mr-5">
                                        <a href="{{ generateURL($call->room->participator->profile_photo) }}" target="_blank"
                                            style="margin: 10px;">
                                            <div class="symbol-label"
                                                style="background-image:url({{ generateURL($call->room->participator->profile_photo) }})">
                                            </div>
                                        </a>
                                    </div>
                                @endif
                            </div>

                            <div class="mb-2">
                                <label class="control-label"><span class="mendatory" style="font-size: 20px;"></span>Full
                                    Name : <b>
                                        @if ($call->room && $call->room->participator && $call->room->participator->userTransDefault)
                                            {{ $call->room->participator->userTransDefault ? $call->room->participator->userTransDefault->full_name : 'N/A' }}
                                        @else
                                            -
                                        @endif
                                    </b></label>
                            </div>

                            <div class="mb-2">
                                <label class="control-label"><span class="mendatory" style="font-size: 20px;"></span>Account
                                    Id : <b>
                                        @if ($call->room && $call->room->participator && $call->room->participator->account_id)
                                            {{ $call->room->participator->account_id }}
                                        @else
                                            -
                                        @endif
                                    </b></label>
                            </div>
                            <div class="mb-2">
                                <label class="control-label"><span class="mendatory" style="font-size: 20px;"></span>Email:
                                    <b>
                                        @if ($call->room && $call->room->participator && $call->room->participator->email)
                                            {{ $call->room->participator->email }}
                                        @else
                                            -
                                        @endif
                                    </b></label>
                            </div>
                           
                        </div>

                        <div class="form-group col-md-6">
                            
                            <div class="mb-2">
                                <label class="control-label"><span class="mendatory" style="font-size: 20px;"></span>
                                    Call date : <b>
                                        {{ $call->date }}
                                        
                                    </b></label>
                            </div>

                            <div class="mb-2">
                                <label class="control-label"><span class="mendatory" style="font-size: 20px;"></span>
                                    Start Time : <b>
                                        {{ $call->start_time }}
                                    </b></label>
                            </div>
                            <div class="mb-2">
                                <label class="control-label"><span class="mendatory" style="font-size: 20px;"></span>
                                    End Time : <b>
                                        {{ $call->end_time }}
                                    </b></label>
                            </div>
                            <div class="mb-2">
                                <label class="control-label"><span class="mendatory" style="font-size: 20px;"></span>
                                    Remaining Time : <b>
                                        {{ $call->remaining_time }}
                                    </b></label>
                            </div>
                        </div>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
@endsection
