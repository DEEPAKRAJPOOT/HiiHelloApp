@extends('admin.layouts.app')

@push('breadcrumb')
    {!! Breadcrumbs::render('user_matches_view', $user->id) !!}
@endpush

@section('content')
<div class="container">
    <div class="card card-custom">
        <div class="card-header">
            <div class="card-title">
                <span class="card-icon">
                    <i class="{{$icon}} text-primary"></i>
                </span>
                <h3 class="card-label text-uppercase">View {{ $custom_title }}</h3>
            </div>
        </div>
        <div class="card-body">
            <div class="row mb-4 pb-4">
                <div class="col-auto">
                    @if($user->profile_photo)
                    <div class="symbol symbol-120 mr-5">
                        <a href="{{ generateURL($user->profile_photo) }}" target="_blank">
                            <div class="symbol-label" style="background-image:url('{{ generateURL($user->profile_photo)}}')"></div>
                        </a>
                    </div>
                    @endif
                    <h5 class="mb-4">
                        @if(($user->is_test_user ?? '') == 'y')
                            <span class="badge bg-primary text-white">Test User</span>
                        @endif
                    </h5>
                </div>
                <div class="col-auto d-flex flex-wrap align-content-center">
                    <h3>{{ $user->userTransDefault ? $user->userTransDefault->full_name : '' }}</h3>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 mb-4 pb-4">
                    <label class="control-label"><span class="mendatory" style="font-size: 20px;"></span>
                        <h1>User Matches</h1>
                    </label>
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <label class="control-label">
                                Unmatches Done : <b>{{ $user->unmatches_done_count ?? 0 }}</b>
                            </label>
                        </div>
                        <div class="col-md-6 mb-2">
                            <label class="control-label">
                                Unmatches Received : <b>{{ $user->unmatches_count ?? 0 }}</b>
                            </label>
                        </div>
                        <div class="col-md-6 mb-2">
                            <label class="control-label">
                                Likes Done : <b>{{ $user->likes_done_count ?? 0 }}</b>
                            </label>
                        </div>
                        <div class="col-md-6 mb-2">
                            <label class="control-label">
                                Dislikes Done : <b>{{ $user->dislikes_done_count ?? 0 }}</b>
                            </label>
                        </div>
                        <div class="col-md-6 mb-2">
                            <label class="control-label">
                                Likes Received : <b>{{ $user->likes_count ?? 0 }}</b>
                            </label>
                        </div>
                        <div class="col-md-6 mb-2">
                            <label class="control-label">
                                Dislikes Received : <b>{{ $user->dislikes_count ?? 0 }}</b>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <label class="control-label"><span class="mendatory" style="font-size: 20px;"></span>
                        <h1>Profile Information</h1>
                    </label>
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <label class="control-label"><span class="mendatory" style="font-size: 20px;"></span>Full Name : <b>@if($user->userTransDefault) {{ $user->userTransDefault->full_name }} @else - @endif </b></label>
                        </div>
                        <div class="col-md-6 mb-2">
                            <label class="control-label"><span class="mendatory" style="font-size: 20px;"></span>Account Id : <b>@if($user->account_id) {{ $user->account_id }} @else - @endif </b></label>
                        </div>
                        <div class="col-md-6 mb-2">
                            <label class="control-label"><span class="mendatory" style="font-size: 20px;"></span>Email : <b>@if($user->email) {{ $user->email }} @else - @endif </b></label>
                        </div>
                        <div class="col-md-6 mb-2">
                            <label class="control-label"><span class="mendatory" style="font-size: 20px;"></span>Profile Percentage : <b>{{ $user->profile_percentage }}%</b></label>
                        </div>
                        <div class="col-md-6 mb-2">
                            <label class="control-label"><span class="mendatory" style="font-size: 20px;"></span>Contact Number : <b>@if($user->contact_no) {{ $user->country_code }} {{ $user->contact_no }} @else - @endif </b></label>
                        </div>
                        <div class="col-md-6 mb-2">
                            <label class="control-label"><span class="mendatory" style="font-size: 20px;"></span>Birth Date : <b>@if($user->birth_date) {{ $user->birth_date }} @else - @endif </b></label>
                        </div>
                        <div class="col-md-6 mb-2">
                            <label class="control-label"><span class="mendatory" style="font-size: 20px;"></span>Gender : <b>@if($user->gender) {{ $user->gender }} @else - @endif </b></label>
                        </div>
                        <div class="col-md-6 mb-2">
                            <label class="control-label"><span class="mendatory" style="font-size: 20px;"></span>Location : <b>@if($user->location && $user->location->locationTransDefault) 
                                {{ $user->location->locationTransDefault->name }}
                            @else
                                - 
                            @endif </b>
                            </label>
                        </div> 
                        <div class="col-md-6 mb-2">
                            <label class="control-label"><span class="mendatory" style="font-size: 20px;"></span>State : <b>@if($user->location && $user->location->locationTransDefault) 
                                {{ $user->location->locationTransDefault->state }}
                            @else
                                - 
                            @endif </b>
                            </label>
                        </div>
                        <div class="col-md-6 mb-2">
                            <label class="control-label"><span class="mendatory" style="font-size: 20px;"></span>Country : <b>@if($user->country && $user->country->countryTransDefault) 
                                {{ $user->country->countryTransDefault->name }}
                            @else
                                - 
                            @endif </b></label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
