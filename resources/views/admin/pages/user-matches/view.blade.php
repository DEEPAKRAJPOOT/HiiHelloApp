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
                <div class="col-md-12">
                    <label class="control-label"><span class="mendatory" style="font-size: 20px;"></span>
                        <h1>Profile Information</h1>
                    </label>
                    <div class="row">
                        @if(!empty($user->userTransDefault))
                            <div class="col-md-6 mb-2">
                                <label class="control-label">Full Name : <b>@if($user->userTransDefault) {{ $user->userTransDefault->full_name }} @else - @endif </b></label>
                            </div>
                        @endif
                        @if(!empty($user->account_id))
                            <div class="col-md-6 mb-2">
                                <label class="control-label">Account Id : <b>{{ $user->account_id }}</b></label>
                            </div>
                        @endif
                        @if(!empty($user->email))
                            <div class="col-md-6 mb-2">
                                <label class="control-label">Email : <b>{{ $user->email }}</b></label>
                            </div>
                        @endif
                        <div class="col-md-6 mb-2">
                            <label class="control-label">Profile Percentage : <b>{{ $user->profile_percentage }}%</b></label>
                        </div>
                        @if(!empty($user->contact_no))
                            <div class="col-md-6 mb-2">
                                <label class="control-label">Contact Number : <b>{{ !empty($user->country_code) ? '+'.$user->country_code.'-' : '' }}{{ $user->contact_no }}</b></label>
                            </div>
                        @endif
                        @if(!empty($user->birth_date))
                            <div class="col-md-6 mb-2">
                                <label class="control-label">Birth Date : <b>{{ $user->birth_date }}</b></label>
                            </div>
                        @endif
                        @if(!empty($user->gender))
                            <div class="col-md-6 mb-2">
                                <label class="control-label">Gender : <b>{{ $user->gender }}</b></label>
                            </div>
                        @endif
                        @if(!empty($user->location) && !empty($user->location->locationTransDefault))
                            <div class="col-md-6 mb-2">
                                <label class="control-label">Location : <b>{{ $user->location->locationTransDefault->name }}</b></label>
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="control-label">State : <b>{{ $user->location->locationTransDefault->state }}</b></label>
                            </div>
                        @endif
                        @if(!empty($user->country) && !empty($user->country->countryTransDefault))
                            <div class="col-md-6 mb-2">
                                <label class="control-label">Country : <b>{{ $user->country->countryTransDefault->name }}</b></label>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="col-md-12 mt-4 pt-4">
                    <label class="control-label"><span class="mendatory" style="font-size: 20px;"></span>
                        <h1>Match Data</h1>
                    </label>
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <label class="control-label">
                                Likes Done : <b>{{ $user->likes_done_count ?? 0 }}</b>
                            </label>
                        </div>
                        <div class="col-md-6 mb-2">
                            <label class="control-label">
                                Likes Received : <b>{{ $user->likes_count ?? 0 }}</b>
                            </label>
                        </div>
                        <div class="col-md-6 mb-2">
                            <label class="control-label">
                                Dislikes Done : <b>{{ $user->dislikes_done_count ?? 0 }}</b>
                            </label>
                        </div>
                        <div class="col-md-6 mb-2">
                            <label class="control-label">
                                Dislikes Received : <b>{{ $user->dislikes_count ?? 0 }}</b>
                            </label>
                        </div>
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
                    </div>
                </div>
                @if(!$user->system_matches_for->isEmpty() || !$user->system_matches_to->isEmpty())
                    <div class="col-md-12 mt-4 pt-4">
                        <label class="control-label"><span class="mendatory" style="font-size: 20px;"></span>
                            <h1>System Matches</h1>
                        </label>
                        <table id="view_user_matches_table" class="table table-bordered table-hover mt-5">
                            <thead>
                                <tr>
                                    <th>Match Shown To</th>
                                    <th>Match User</th>
                                    <th>Match Date</th>
                                    <th>Match Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($user->system_matches_for as $user_match)
                                    @if(!empty($user_match->to_user))
                                        <tr>
                                            <td>
                                                {{ !empty($user_match->for_user->userTransDefault) ? $user_match->for_user->userTransDefault->full_name : '-' }}
                                                @if($user->custom_id != $user_match->for_user->custom_id)
                                                    <a href="{{ route('admin.user-matches.show',$user_match->for_user->custom_id) }}" class="ml-2"><i class="fa fa-eye"></i></a>
                                                @endif
                                            </td>
                                            <td>
                                                {{ !empty($user_match->to_user->userTransDefault) ? $user_match->to_user->userTransDefault->full_name : '-' }}
                                                @if($user->custom_id != $user_match->to_user->custom_id)
                                                    <a href="{{ route('admin.user-matches.show',$user_match->to_user->custom_id) }}" class="ml-2"><i class="fa fa-eye"></i></a>
                                                @endif
                                            </td>
                                            <td>{{ now()->create($user_match->match_date)->format('jS M Y') }}</td>
                                            <td>
                                                @switch($user_match->is_connected ?? '')
                                                    @case('0')
                                                        Pending
                                                    @break
                                                    @case('1')
                                                        Connected
                                                    @break
                                                    @case('2')
                                                        Expired
                                                    @break
                                                @endswitch
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                                @foreach($user->system_matches_to as $user_match)
                                    @if(!empty($user_match->for_user))
                                        <tr>
                                            <td>
                                                {{ !empty($user_match->for_user->userTransDefault) ? $user_match->for_user->userTransDefault->full_name : '-' }}
                                                @if($user->custom_id != $user_match->for_user->custom_id)
                                                    <a href="{{ route('admin.user-matches.show',$user_match->for_user->custom_id) }}" class="ml-2"><i class="fa fa-eye"></i></a>
                                                @endif
                                            </td>
                                            <td>
                                                {{ !empty($user_match->to_user->userTransDefault) ? $user_match->to_user->userTransDefault->full_name : '-' }}
                                                @if($user->custom_id != $user_match->to_user->custom_id)
                                                    <a href="{{ route('admin.user-matches.show',$user_match->to_user->custom_id) }}" class="ml-2"><i class="fa fa-eye"></i></a>
                                                @endif
                                            </td>
                                            <td>{{ now()->create($user_match->match_date)->format('jS M Y') }}</td>
                                            <td>
                                                @switch($user_match->is_connected ?? '')
                                                    @case('0')
                                                        Pending
                                                    @break
                                                    @case('1')
                                                        Connected
                                                    @break
                                                    @case('2')
                                                        Expired
                                                    @break
                                                @endswitch
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
@push('extra-js-scripts')
<script type="text/javascript">
    if($('#view_user_matches_table tbody tr').length == 0){
        $('#view_user_matches_table tbody').append('<tr><td colspan="3" class="text-center">No Data<td></tr>');
    }
</script>
@endpush