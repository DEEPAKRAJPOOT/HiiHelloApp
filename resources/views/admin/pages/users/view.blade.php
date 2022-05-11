@extends('admin.layouts.app')

@push('breadcrumb')
    {!! Breadcrumbs::render('users_view', $user->id) !!}
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

        <div class="profile-content">
            <div class="form-group col-md-12">
                <div class="form-group col-md-12 row" style="margin:15px;">
                    <div class="form-group col-md-6">
                        <br>
                        <label class="control-label"><span class="mendatory" style="font-size: 20px;"></span>
                            <h4>Basic Information</h4>
                        </label>

                        <div class="mb-2">
                            <label class="control-label"><span class="mendatory" style="font-size: 20px;"></span>Full Name : <b>@if($user->full_name) {{ $user->full_name }} @else - @endif </b></label>
                        </div>
                        <div class="mb-2">
                            <label class="control-label"><span class="mendatory" style="font-size: 20px;"></span>Email : <b>@if($user->email) {{ $user->email }} @else - @endif </b></label>
                        </div>
                        <div class="mb-2">
                            <label class="control-label"><span class="mendatory" style="font-size: 20px;"></span>Contact Number : <b>@if($user->contact_no) {{ $user->country_code }} {{ $user->contact_no }} @else - @endif </b></label>
                        </div>
                        <div class="mb-2">
                            <label class="control-label"><span class="mendatory" style="font-size: 20px;"></span>Birth Date : <b>@if($user->birth_date) {{ $user->birth_date }} @else - @endif </b></label>
                        </div>
                        <div class="mb-2">
                            <label class="control-label"><span class="mendatory" style="font-size: 20px;"></span>Gender : <b>@if($user->gender) {{ $user->gender }} @else - @endif </b></label>
                        </div>
                        <div class="mb-2">
                            <label class="control-label"><span class="mendatory" style="font-size: 20px;"></span>Location : <b>@if($user->location && $user->location->locationTransDefault) 
                                {{ $user->location->locationTransDefault->name }}
                            @else
                                - 
                            @endif </b></label>
                        </div>
                        <div class="mb-2">
                            <label class="control-label"><span class="mendatory" style="font-size: 20px;"></span>Country : <b>@if($user->country && $user->country->countryTransDefault) 
                                {{ $user->country->countryTransDefault->name }}
                            @else
                                - 
                            @endif </b></label>
                        </div>
                        <div class="mb-2">
                            <label class="control-label"><span class="mendatory" style="font-size: 20px;"></span>Language : <b>@if($user->language) 
                                {{ $user->language->hint }}
                            @else
                                - 
                            @endif </b></label>
                        </div>
                        <div class="mb-2">
                            <label class="control-label"><span class="mendatory" style="font-size: 20px;"></span>Social Login : <b>@if($user->is_social_user) 
                                {{ $user->is_social_user }}
                            @else
                                - 
                            @endif </b></label>
                        </div>
                        <div class="mb-2">
                            <label class="control-label"><span class="mendatory" style="font-size: 20px;"></span>Photos Verified : <b>@if($user->photo_verified_at) 
                                {{ $user->photo_verified_at }}
                            @else
                                - 
                            @endif </b></label>
                        </div>
                        <div class="mb-2">
                            <label class="control-label"><span class="mendatory" style="font-size: 20px;"></span>Videos Verified : <b>@if($user->video_verified_at) 
                                {{ $user->video_verified_at }}
                            @else
                                - 
                            @endif </b></label>
                        </div>
                        <div class="mb-2">
                            <label class="control-label"><span class="mendatory" style="font-size: 20px;"></span>Account Verified : <b>@if($user->verify_status) 
                                {{ $user->verify_status }}
                            @else
                                - 
                            @endif </b></label>
                        </div>
                    </div>

                    <div class="form-group col-md-6">
                        <br>
                        <label class="control-label"><span class="mendatory" style="font-size: 20px;"></span>
                            <h4>Profile Information</h4>
                        </label>
                        <div class="mb-2">
                            <label class="control-label">
                                <span class="mendatory" style="font-size: 20px;"></span>RelationShip Status : 
                                <b>
                                    @if($user->relationshipStatus && $user->relationshipStatus->profileDetailTransDefault)
                                        {{ $user->relationshipStatus->profileDetailTransDefault->value }} 
                                    @else
                                        - 
                                    @endif 
                                </b>
                            </label>
                        </div>
                        <div class="mb-2">
                            <label class="control-label">
                                <span class="mendatory" style="font-size: 20px;"></span>You Are Here : 
                                <b>
                                    @if($user->youAreHere && $user->youAreHere->profileDetailTransDefault)
                                        {{ $user->youAreHere->profileDetailTransDefault->value }} 
                                    @else
                                        - 
                                    @endif 
                                </b>
                            </label>
                        </div>
                        <div class="mb-2">
                            <label class="control-label">
                                <span class="mendatory" style="font-size: 20px;"></span>Food Preference : 
                                <b>
                                    @if($user->foodPreference && $user->foodPreference->profileDetailTransDefault)
                                        {{ $user->foodPreference->profileDetailTransDefault->value }} 
                                    @else
                                        - 
                                    @endif 
                                </b>
                            </label>
                        </div>
                        <div class="mb-2">
                            <label class="control-label">
                                <span class="mendatory" style="font-size: 20px;"></span>Drinking : 
                                <b>
                                    @if($user->drinking && $user->drinking->profileDetailTransDefault)
                                        {{ $user->drinking->profileDetailTransDefault->value }} 
                                    @else
                                        - 
                                    @endif 
                                </b>
                            </label>
                        </div>
                        <div class="mb-2">
                            <label class="control-label">
                                <span class="mendatory" style="font-size: 20px;"></span>Smoking : 
                                <b>
                                    @if($user->smoking && $user->smoking->profileDetailTransDefault)
                                        {{ $user->smoking->profileDetailTransDefault->value }} 
                                    @else
                                        - 
                                    @endif 
                                </b>
                            </label>
                        </div>
                        <div class="mb-2">
                            <label class="control-label">
                                <span class="mendatory" style="font-size: 20px;"></span>Star Sign : 
                                <b>
                                    @if($user->starSign && $user->starSign->profileDetailTransDefault)
                                        {{ $user->starSign->profileDetailTransDefault->value }} 
                                    @else
                                        - 
                                    @endif 
                                </b>
                            </label>
                        </div>
                        <div class="mb-2">
                            <label class="control-label">
                                <span class="mendatory" style="font-size: 20px;"></span>Religion : 
                                <b>
                                    @if($user->religion && $user->religion->profileDetailTransDefault)
                                        {{ $user->religion->profileDetailTransDefault->value }} 
                                    @else
                                        - 
                                    @endif 
                                </b>
                            </label>
                        </div>
                        <div class="mb-2">
                            <label class="control-label">
                                <span class="mendatory" style="font-size: 20px;"></span>Community : 
                                <b>
                                    @if($user->community && $user->community->profileDetailTransDefault)
                                        {{ $user->community->profileDetailTransDefault->value }} 
                                    @else
                                        - 
                                    @endif 
                                </b>
                            </label>
                        </div>
                    </div>

                    <div class="form-group col-md-6">
                        <br>
                        <label class="control-label"><span class="mendatory" style="font-size: 20px;"></span>
                            <h4>Advance Profile Information</h4>
                        </label>
                        <div class="mb-2">
                            <label class="control-label">
                                <span class="mendatory" style="font-size: 20px;"></span>Education : 
                                <b>
                                    @if($user->education && $user->education->profileDetailTransDefault)
                                        {{ $user->education->profileDetailTransDefault->value }} 
                                    @else
                                        - 
                                    @endif 
                                </b>
                            </label>
                        </div>
                    </div>

                    <div class="form-group col-md-6">
                        <br>
                        <label class="control-label"><span class="mendatory" style="font-size: 20px;"></span>
                            <h4>Multiple Choises</h4>
                        </label>
                        <div class="mb-2">
                            <label class="control-label">
                                <span class="mendatory" style="font-size: 20px;"></span>Interests : 
                                <b>
                                    @if($user->interests->isNotEmpty())
                                        @foreach($user->interests as $interest)
                                            @if($interest->interest && $interest->interest->interestTransDefault)
                                                {{ $interest->interest->interestTransDefault->title }}, 
                                            @endif
                                        @endforeach
                                    @else
                                        - 
                                    @endif 
                                </b>
                            </label>
                        </div>
                        <div class="mb-2">
                            <label class="control-label">
                                <span class="mendatory" style="font-size: 20px;"></span>Pets : 
                                <b>
                                    @if($user->pets->isNotEmpty())
                                        @foreach($user->pets as $pets)
                                            @if($pets->pet && $pets->pet->profileDetailTransDefault)
                                                {{ $pets->pet->profileDetailTransDefault->value }}, 
                                            @endif
                                        @endforeach
                                    @else
                                        - 
                                    @endif 
                                </b>
                            </label>
                        </div>
                    </div>

                    @if($user->userDetails->isNotEmpty())
                    <div class="form-group col-md-12">
                        <label class="control-label"><span class="mendatory" style="font-size: 20px;"></span>
                            <h4>Images</h4>
                        </label>
                        <div class="mb-2">
                            @foreach($user->userDetails as $userDetail)
                                @if($userDetail->image && generateURL($userDetail->image))
                                    <a href="{{ generateURL($userDetail->image) }}" target="_blank" style="margin: 10px;">
                                        <img style="height:200px; width:200px" src="{{ generateURL($userDetail->image) }}">
                                    </a>
                                @endif
                            @endforeach
                        </div>
                    </div>
                    @endif

                    @if($user->userDetails->isNotEmpty())
                    <div class="form-group col-md-6">
                        <div class="row">
                            <div class="col-md-12">
                                <label class="control-label"><span class="mendatory" style="font-size: 20px;"></span>
                                    <h4>Videos</h4>
                                </label>
                                <div class="mb-2">
                                    @foreach($user->userDetails as $userDetail)
                                        @if($userDetail->video && generateURL($userDetail->video))
                                            <video width="420" height="340" controls>
                                                <source src="{{ generateURL($userDetail->video) }}" type="video/mp4">
                                                <source src="{{ generateURL($userDetail->video) }}" type="video/ogg">
                                                Your browser does not support the video tag.
                                            </video>
                                            <br><br>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <div class="form-group col-md-12">
                        <br><br>
                        <div class="form-group col-md-12">
                            <label class="control-label"><span class="mendatory" style="font-size: 20px;"></span>
                                <h4>Report Status : {{ $user->status }}</h4>
                            </label>
                        </div>
                    </div>

                </div>
                <br><br>
            </div>
        </div>
    </div>
</div>
@endsection
