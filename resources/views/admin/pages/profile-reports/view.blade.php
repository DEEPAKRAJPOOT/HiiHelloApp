@extends('admin.layouts.app')

@push('breadcrumb')
    {!! Breadcrumbs::render('profile_reports_view', $profile_report->id) !!}
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
                <div class="form-group col-md-12">
                    <br><br>
                    <div class="form-group col-md-12">
                        <label class="control-label"><span class="mendatory" style="font-size: 20px;"></span>
                            <h4>Report Status : {{ $profile_report->status }}</h4>
                        </label>
                    </div>
                </div>
                <div class="form-group col-md-12">
                    
                    <div class="form-group col-md-6">
                        <br>
                        <label class="control-label"><span class="mendatory" style="font-size: 20px;"></span>
                            <h4>Reporter User Information</h4>
                        </label>

                        <div class="mb-2">
                            <label class="control-label"><span class="mendatory" style="font-size: 20px;"></span>Full Name : <b>@if($profile_report->user) {{ $profile_report->user->full_name }} @else - @endif </b></label>
                        </div>
                        <div class="mb-2">
                            <label class="control-label"><span class="mendatory" style="font-size: 20px;"></span>Contact Number : <b>@if($profile_report->user) {{ $profile_report->user->country_code }} {{ $profile_report->user->contact_no }} @else - @endif </b></label>
                        </div>
                    </div>

                    <div class="form-group col-md-6">
                        <br>
                        <label class="control-label"><span class="mendatory" style="font-size: 20px;"></span>
                            <h4>Reported User Information</h4>
                        </label>
                        <div class="mb-2">
                            <label class="control-label"><span class="mendatory" style="font-size: 20px;"></span>Full Name : <b>@if($profile_report->reportedUser) {{ $profile_report->reportedUser->full_name }} @else - @endif </b></label>
                        </div>
                        <div class="mb-2">
                            <label class="control-label"><span class="mendatory" style="font-size: 20px;"></span>Contact Number : <b>@if($profile_report->reportedUser) {{ $profile_report->reportedUser->country_code }} {{ $profile_report->reportedUser->contact_no }} @else - @endif </b></label>
                        </div>
                    </div>

                    <div class="form-group col-md-6">
                        <br>
                        <label class="control-label"><span class="mendatory" style="font-size: 20px;"></span>
                            <h4>Report Date</h4>
                            <label>{{ $profile_report->created_at }}</label>
                        </label>
                    </div>

                    @if(generateURL($profile_report->image))
                    <div class="form-group col-md-6">
                        <label class="control-label"><span class="mendatory" style="font-size: 20px;"></span>
                            <h4>Report Image</h4>
                            <a href="{{ generateURL($profile_report->image) }}" target="_blank"><img style="height:100px; width:100px" src="{{ generateURL($profile_report->image) }}"></a>
                        </label>
                    </div>
                    @endif

                    <div class="form-group col-md-6">
                        <br>
                        <label class="control-label"><span class="mendatory" style="font-size: 20px;"></span>
                            <h4>Report Message</h4>
                            <label>{{ $profile_report->message }}</label>
                        </label>
                    </div>

                </div>
                <br><br>
            </div>
        </div>
    </div>
</div>
@endsection
