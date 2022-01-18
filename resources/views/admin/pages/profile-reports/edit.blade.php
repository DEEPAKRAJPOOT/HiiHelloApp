@extends('admin.layouts.app')

@push('breadcrumb')
    {!! Breadcrumbs::render('profile_reports_update', $profile_report->id) !!}
@endpush

@section('content')
<div class="container">
    <div class="card card-custom">
        <div class="card-header">
            <div class="card-title">
                <span class="card-icon">
                    <i class="{{$icon}} text-primary"></i>
                </span>
                <h3 class="card-label text-uppercase">Edit {{ $custom_title }}</h3>
            </div>
        </div>

        <!--begin::Form-->
        <form id="frmEditProfileReport" method="POST" action="{{ route('admin.profile-reports.update', $profile_report->custom_id) }}" enctype="multipart/form-data">
            @csrf
            @method('put')
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
                                <h4>Reporter User Information</label></h4>

                            <div class="mb-2">
                                <label class="control-label"><span class="mendatory" style="font-size: 20px;"></span>First Name : <b>@if($profile_report->user) {{ $profile_report->user->first_name }} @else - @endif </b></label>
                            </div>
                            <div class="mb-2">
                                <label class="control-label"><span class="mendatory" style="font-size: 20px;"></span>last Name : <b>@if($profile_report->user) {{ $profile_report->user->last_name }} @else - @endif </b></label>
                            </div>
                            <div class="mb-2">
                                <label class="control-label"><span class="mendatory" style="font-size: 20px;"></span>Contact Number : <b>@if($profile_report->user) {{ $profile_report->user->country_code }} {{ $profile_report->user->contact_no }} @else - @endif </b></label>
                            </div>
                        </div>

                        <div class="form-group col-md-6">
                            <br>
                            <label class="control-label"><span class="mendatory" style="font-size: 20px;"></span>
                                <h4>Reported User Information</label></h4>

                            <div class="mb-2">
                                <label class="control-label"><span class="mendatory" style="font-size: 20px;"></span>First Name : <b>@if($profile_report->reportedUser) {{ $profile_report->reportedUser->first_name }} @else - @endif </b></label>
                            </div>
                            <div class="mb-2">
                                <label class="control-label"><span class="mendatory" style="font-size: 20px;"></span>last Name : <b>@if($profile_report->reportedUser) {{ $profile_report->reportedUser->last_name }} @else - @endif </b></label>
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
                </label>
            </div>

            <div class="card-body">
                {{-- Title --}}
                <div class="form-group">
                    <label for="status">{!!$mend_sign!!} status:</label>
                    <select type="text"class="form-control" 
                    id="status" name="status" value="@if(old('status')){{ old('status') }}@else{{ $profile_report->status }}@endif"
                    placeholder="Select Status" spellcheck="false" autocapitalize="sentences" tabindex="0" autofocus />
                        @if($profile_report->status == 'Pending')
                            <option value="Pending" selected>Pending</option>
                            <option value="Accepted">Accepted</option>
                            <option value="Rejected">Rejected</option>
                        @elseif($profile_report->status == 'Accepted')
                            <option value="Accepted" selected>Accepted</option>
                            <option value="Rejected">Rejected</option>
                        @elseif($profile_report->status == 'Rejected')
                            <option value="Accepted">Accepted</option>
                            <option value="Rejected" selected>Rejected</option>
                        @else
                            <option value="" selected>Select Status</option>
                            <option value="Pending">Pending</option>
                            <option value="Accepted">Accepted</option>
                            <option value="Rejected">Rejected</option>
                        @endif
                    </select>
                    @if ($errors->has('status'))
                        <span class="help-block">
                            <strong class="form-text">{{ $errors->first('status') }}</strong>
                        </span>
                    @endif
                </div>

            </div>

            <div class="card-footer">
                <button type="submit" class="btn btn-primary mr-2">Update {{ $custom_title }}</button>
                <a href="{{ route('admin.profile-reports.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
        <!--end::Form-->
    </div>
</div>
@endsection

@push('extra-js-scripts')
<script>
$(document).ready(function () {
    $("#frmEditProfileReport").validate({
        rules: {
            status: {
                required: true,
                not_empty: true,
            },
        },
        messages: {
            status: {
                required: "@lang('validation.required',['attribute'=>'status'])",
                not_empty: "@lang('validation.not_empty',['attribute'=>'status'])",
            },
        },
        errorClass: 'invalid-feedback',
        errorElement: 'span',
        highlight: function (element) {
            $(element).addClass('is-invalid');
            $(element).siblings('label').addClass('text-danger'); // For Label
        },
        unhighlight: function (element) {
            $(element).removeClass('is-invalid');
            $(element).siblings('label').removeClass('text-danger');
        },
        errorPlacement: function (error, element) {
            if (element.attr("data-error-container")) {
                error.appendTo(element.attr("data-error-container"));
            } else {
                error.insertAfter(element);
            }
        }
    });
    $('#frmEditProfileReport').submit(function () {
        if ($(this).valid()) {
            addOverlay();
            $("input[type=submit], input[type=button], button[type=submit]").prop("disabled", "disabled");
            return true;
        } else {
            return false;
        }
    });
});
</script>
@endpush
