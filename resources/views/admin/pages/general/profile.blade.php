@extends('admin.layouts.app')

@push('breadcrumb')
    {!! Breadcrumbs::render('my_profile') !!}
@endpush

@push('extra-css-styles')
    <link rel="stylesheet" type="text/css" href="{{ asset('admin/css/custom.css') }}">
@endpush

@section('content')
<div class="container">
    <!--begin::Profile 2-->
    <div class="d-flex flex-row">
        <!--begin::Aside-->
        <div class="flex-row-auto offcanvas-mobile w-300px" id="kt_profile_aside">
            <!--begin::Card-->
            <div class="card card-custom">
                <!--begin::Body-->
                <div class="card-body pt-15">
                    <!--begin::User-->
                    <div class="text-center mb-10">
                        <div class="symbol symbol-60 symbol-circle symbol-xl-90">
                        <div class="symbol-label" style="background-image:url({{ $user->asset_profile }})"></div>
                            <i class="symbol-badge symbol-badge-bottom bg-success"></i>
                        </div>
                    <h4 class="font-weight-bold my-2">{{ $user->full_name}}</h4>
                        {{-- <div class="text-muted mb-2">Application Developer</div> --}}
                        <span class="label label-light-warning label-inline font-weight-bold label-lg">Active</span>
                    </div>
                    <!--end::User-->
                    <!--begin::Tab-->
                    <ul class="nav nav-tabs flex-column">
                        <li ><a href="#personal_info" data-toggle="tab" class="btn btn-hover-light-primary font-weight-bold py-3 px-6 mb-2 text-center btn-block active">Personal info</a></li>
                        <li ><a href="#change_password" data-toggle="tab" class="btn btn-hover-light-primary font-weight-bold py-3 px-6 mb-2 text-center btn-block">Change Password</a></li>
                    </ul>
                    <!--end::Tab-->
                </div>
                <!--end::Body-->
            </div>
            <!--end::Card-->
        </div>
        <!--end::Aside-->
        <!--begin::Content-->

        <div class="flex-row-fluid ml-lg-8">

            <div class="card card-custom gutter-b">

                <!--begin::Body-->
                    <div class="tab-content">
                        <div class="tab-pane active" id="personal_info">


                            <div class="card card-custom card-stretch">

                                <!--begin::Form-->
                                <form class="form" id="profile_update_form" action="{{ route('admin.profile.update')}}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <!--begin::Header-->
                                <div class="card-header py-3">
                                    <div class="align-items-start flex-column">
                                        <h3 class="card-label font-weight-bolder text-dark">Personal Information</h3>
                                        <span class="text-muted font-weight-bold font-size-sm mt-1">Update your personal informaiton</span>
                                    </div>
                                    <div class="card-toolbar">
                                        <button type="submit" class="btn btn-success mr-2 text-uppercase">Save Changes</button>
                                        <a href="{{ route('admin.dashboard.index')}}" class="btn btn-secondary text-uppercase">Cancel</a>
                                    </div>
                                </div>
                                <!--end::Header-->
                                    <!--begin::Body-->
                                    <div class="card-body">
                                        <div class="row">
                                            <label class="col-xl-3"></label>
                                            <div class="col-lg-9 col-xl-6">
                                                <h5 class="font-weight-bold mb-6">Customer Info</h5>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-xl-3 col-lg-3 col-form-label text-right">Avatar {{ $user->first_name }}</label>

                                            <div class="col-lg-9 col-xl-6">

                                                <div class="image-input image-input-outline" id="kt_profile_avatar" style="background-image: url({{ $user->asset_profile}})">
                                                    <div class="image-input-wrapper" style="background-image:url({{ $user->asset_profile}})"></div>
                                                    <label class="btn btn-xs btn-icon btn-circle btn-white btn-hover-text-primary btn-shadow" data-action="change" data-toggle="tooltip" title="" data-original-title="Change avatar">
                                                        <i class="fa fa-pen icon-sm text-muted"></i>
                                                        <input type="file" name="profile_avatar" accept=".png, .jpg, .jpeg" />
                                                        {{-- <input type="hidden" name="profile_avatar_remove" /> --}}
                                                    </label>
                                                    <span class="btn btn-xs btn-icon btn-circle btn-white btn-hover-text-primary btn-shadow" data-action="cancel" data-toggle="tooltip" title="Cancel avatar">
                                                        <i class="ki ki-bold-close icon-xs text-muted"></i>
                                                    </span>
                                                    {{-- <span class="btn btn-xs btn-icon btn-circle btn-white btn-hover-text-primary btn-shadow" data-action="remove" data-toggle="tooltip" title="Remove avatar">
                                                        <i class="ki ki-bold-close icon-xs text-muted"></i>
                                                    </span> --}}
                                                </div>
                                                <span class="form-text text-muted">Allowed file types: png, jpg, jpeg.</span>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-xl-3 col-lg-3 col-form-label text-lg-right">Name</label>
                                            <div class="col-lg-9 col-xl-6">
                                                <input class="form-control form-control-lg" name="full_name" id="full_name" type="text" value="{{ $user->full_name }}" placeholder="Enter name" />
                                                @if ($errors->has('full_name'))
                                                    <span class="text-danger">
                                                        <strong class="form-text">{{ $errors->first('full_name') }}</strong>
                                                    </span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-xl-3 col-lg-3 col-form-label text-lg-right">Contact Number</label>
                                            <div class="col-lg-9 col-xl-6">
                                                <input type="text" class="form-control form-control-lg" name="contact_no" id="contact_no" value="{{ $user->contact_no}}" placeholder="Contact Number" />
                                                @if ($errors->has('contact_no'))
                                                    <span class="text-danger">
                                                        <strong class="form-text">{{ $errors->first('contact_no') }}</strong>
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-xl-3 col-lg-3 col-form-label text-lg-right">Email Address</label>
                                            <div class="col-lg-9 col-xl-6">
                                                <input type="text" class="form-control form-control-lg" name="email" id="email" value="{{ $user->email }}" placeholder="Email" />
                                                @if ($errors->has('email'))
                                                    <span class="text-danger">
                                                        <strong class="form-text">{{ $errors->first('email') }}</strong>
                                                    </span>
                                                @endif
                                            </div>
                                        </div>

                                    </div>
                                    <!--end::Body-->
                                </form>
                                <!--end::Form-->
                            </div>

                        </div>
                        <div class="tab-pane" id="change_password"><div class="card card-custom">

                            <!--begin::Form-->
                            <form class="form" id="change_password_form" action="{{ route('admin.update-password')}}" method="POST">
                                @csrf
                                @method('PUT')

                                <!--begin::Header-->
                            <div class="card-header py-3">
                                <div class="align-items-start flex-column">
                                    <h3 class="card-label font-weight-bolder text-dark">Change Password</h3>
                                    <span class="text-muted font-weight-bold font-size-sm mt-1">Change your account password</span>
                                </div>
                                <div class="card-toolbar">
                                    <button type="submit" class="btn btn-success mr-2">Save Changes</button>
                                <a href="{{ route('admin.dashboard.index')}}" class="btn btn-secondary">Cancel</a>
                                </div>
                            </div>
                            <!--end::Header-->
                                <div class="card-body">

                                    <div class="form-group row">
                                        <label class="col-xl-3 col-lg-3 col-form-label text-alert">Current Password</label>
                                        <div class="col-lg-9 col-xl-6">
                                            <input type="password" class="form-control form-control-lg form-control-solid mb-2" value="" name="current_password" id="current_password" placeholder="Current password" />
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-xl-3 col-lg-3 col-form-label text-alert">New Password</label>
                                        <div class="col-lg-9 col-xl-6">
                                            <input type="password" class="form-control form-control-lg" value="" name="password" id="password" placeholder="New password" />
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-xl-3 col-lg-3 col-form-label text-alert">Verify Password</label>
                                        <div class="col-lg-9 col-xl-6">
                                            <input type="password" class="form-control form-control-lg" value="" name="confirm_password" id="confirm_password" placeholder="Verify password" />
                                        </div>
                                    </div>
                                </div>
                            </form>
                            <!--end::Form-->
                        </div></div>
                    </div>
                <!--end::Body-->
            </div>



        </div>
        <!--end::Content-->
    </div>
    <!--end::Profile 2-->
</div>
@endsection

@push('extra-js-scripts')
<script src="{{ asset('assets/js/pages/custom/profile/profile.js?v=7.0.5')}}"></script>
<script type="text/javascript">

    $(function(){
        $('#profile_update_form').validate({
            errorElement: 'span',
            errorClass: 'help-block',
            focusInvalid: false,
            rules: {
                full_name:{
                    required:true,
                    not_empty:true,
                    maxlength:50
                },
                email:{
                    required:true,
                    valid_email:true,
                    maxlength:80,
                },
                contact_no:{
                    required:true,
                    digits:true,
                    minlength:6,
                    maxlength:15,
                },
                profile_avatar:{
                    extension:'jpg|png|jpeg'
                },
                password: {
                    required: true,
                    no_space:true,
                    minlength:8,
                    maxlength:15,
                },
            },
            messages: {
                full_name:{
                    required:"@lang('validation.required',['attribute'=>'name'])",
                    not_empty:"@lang('validation.not_empty',['attribute'=>'name'])",
                    maxlength:"@lang('validation.max.string',['attribute'=>'name','max'=>50])"
                },
                email:{
                    required:"@lang('validation.required',['attribute'=>'email address'])",
                    remote:"@lang('validation.unique',['attribute'=>'email address'])",
                    maxlength:"@lang('validation.max.string',['attribute'=>'email address','max'=>80])",
                    valid_email:"@lang('validation.email',['attribute'=>'email address'])",
                    pattern:"@lang('validation.email',['attribute'=>'email address'])",
                    email:"@lang('validation.email',['attribute'=>'email address'])",
                },
                contact_no:{
                    required:"@lang('validation.required',['attribute'=>'contact number'])",
                    minlength:"@lang('validation.min.numeric',['attribute'=>'contact number','min'=>6])",
                    maxlength:"@lang('validation.max.numeric',['attribute'=>'contact number','max'=>15])"
                },
                profile_avatar:{
                    extension:"@lang('validation.mimetypes',['attribute'=>'profile photo','value'=>'jpg|png|jpeg'])"
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
                $(element).siblings('label').removeClass('text-danger'); // For Label
            },
            errorPlacement: function (error, element) {
                if (element.attr("data-error-container")) {
                    error.appendTo(element.attr("data-error-container"));
                } else {
                    error.insertAfter(element);
                }
            }
        });
        $(document).on('submit','#frmProfile',function(){
            if($("#frmProfile").valid()){
                $(this).submit(function() {
                    return false;
                });
                addOverlay();
                $("input[type=submit], input[type=button], button[type=submit]").prop("disabled", "disabled");
                return true;
            }else{
                return false;
            }
        });
    });

    $(function(){
        $('#change_password_form').validate({
            errorElement: 'span',
            errorClass: 'help-block',
            focusInvalid: false,
            rules: {
                current_password: {
                    required: true,
                    no_space:true,
                    minlength:8,
                    maxlength:15,
                    remote: {
                        url: '{{ route("admin.profile.check-password") }}',
                        type: "post",
                        data: {
                            _token: '{{ csrf_token() }}',
                            current_password: function() {
                                return $( "#current_password" ).val();
                            },
                        }
                    }
                },
                password: {
                    required: true,
                    no_space:true,
                    minlength:8,
                    maxlength:15,
                },
                confirm_password: {
                    required: true,
                    no_space:true,
                    minlength:8,
                    maxlength:15,
                    equalTo: '#password'
                },
            },
            messages: {

                current_password: {
                    required: "The Current Password field is required.",
                    no_space: "The Current Password must not have space.",
                    minlength:"The Current Password must be at least 8 characters.",
                    maxlength:"The Current Password may not be greater than 15 characters.",
                    remote:"The Current Password doesn't match."
                },
                password: {
                    required: "The Password field is required.",
                    no_space: "The Password must not have space.",
                    minlength:"The Password must be at least 8 characters.",
                    maxlength:"The Password may not be greater than 15 characters."
                },
                confirm_password: {
                    required: "The Confirm Password field is required.",
                    no_space: "The Confirm Password must not have space.",
                    minlength:"The Confirm Password must be at least 8 characters.",
                    maxlength:"The Confirm Password may not be greater than 15 characters.",
                    equalTo: "The Confirm Password does not match Password.",
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
                $(element).siblings('label').removeClass('text-danger'); // For Label
            },
            errorPlacement: function (error, element) {
                if (element.attr("type") == "radio") {
                      error.appendTo('.a');
                }else{
                    if (element.attr("data-error-container")) {
                        error.appendTo(element.attr("data-error-container"));
                    } else {
                        error.insertAfter(element);
                    }
                }
            }
        });
        $(document).on('submit','#change_password_form',function(){
            if($("#change_password_form").valid()){
                $(this).submit(function() {
                    return false;
                });
                addOverlay();
                $("input[type=submit], input[type=button], button[type=submit]").prop("disabled", "disabled");
                return true;
            }else{
                return false;
            }
        });
    });
</script>
@endpush
