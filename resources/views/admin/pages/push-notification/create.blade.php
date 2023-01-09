@extends('admin.layouts.app')

@push('breadcrumb')
    {!! Breadcrumbs::render('push_notification_create') !!}
@endpush

@section('content')
<div class="container">
    <div class="card card-custom">
        <div class="card-header">
            <div class="card-title">
                <span class="card-icon">
                    <i class="{{$icon}} text-primary"></i>
                </span>
                <h3 class="card-label text-uppercase">Send {{ $custom_title }}</h3>
            </div>
        </div>

        <form id="frmSendPushNotification" name="frmSendPushNotification" method="POST" action="{{ route('admin.push-notification.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="row">
                <div class="col-md-12">
                    <div class="card-body ">
                        <div class="profile-content">
                            <div class="col-md-12">

                                {{-- Subject --}}
                                <div class="form-group {{ $errors->has('subject') ? 'has-error' : '' }}">
                                    <label for="subject">{!! $mend_sign !!}Subject</label>
                                    <input type="text" placeholder="Enter Subject" class="form-control" id="subject" name="subject" maxlength="500" autocomplete="off" value="{{ old('subject') }}" />
                                    @if($errors->has('subject'))
                                        <span class="help-block">
                                            {{ $errors->first('subject') }}
                                        </span>
                                    @endif
                                </div>

                                {{-- Message --}}
                                <div class="form-group {{ $errors->has('message') ? 'has-error' : '' }}">
                                    <label for="message">{!! $mend_sign !!}Message</label>
                                    <textarea type="text" placeholder="Enter message" class="form-control" id="message" name="message"></textarea>
                                    @if($errors->has('message'))
                                        <span class="help-block">
                                            {{ $errors->first('message') }}
                                        </span>
                                    @endif
                                </div>

                                {{-- User Type --}}
                                <div class="form-group" {{ $errors->has('parent_id') ? 'has-error' : '' }}>
                                    <label for="user_type">{!!$mend_sign!!}Notifiaction Type:</label>
                                    <div class="custom-file">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <select class="form-control" name="user_type">
                                                    <option value="">-- Select Type --</option>
                                                    <option value="send_all">All</option>
                                                    <option value="send_male">Male</option>
                                                    <option value="send_female">Female</option>
                                                    <option value="send_empty_profile_image">Empty Profile Image</option>
                                                    <option value="send_less_then_15_pr">Less Then 15% Profile Percentage</option>
                                                    <option value="send_unverified_photo">Unverified Photo</option>
                                                    <option value="send_unverified_email">Unverified Email</option>
                                                    <option value="send_unverified_phone">Unverified Phone</option>
                                                </select>
                                            </div>
                                        </div>
                                        <span id="error-authorizable"></span>
                                        @if ($errors->has('user_type'))
                                            <span class="help-block">
                                                <strong class="form-text">{{ $errors->first('user_type') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                
                            </div>
                        </div>
                    </div>
                </div>
            </div>  
            <div class="card-footer">
                <button type="submit" class="btn btn-primary mr-2 text-uppercase"> Send {{ $custom_title }}</button>
                <a href="{{ route('admin.push-notification.index') }}" class="btn btn-secondary text-uppercase">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('extra-js-scripts')
<script type="text/javascript">
    $(document).ready(function () {
        $("#frmSendPushNotification").validate({
            rules: {
                user_type: {
                    required: true,
                    not_empty: true,
                },
                subject: {
                    required: true,
                    not_empty: true,
                    minlength: 3,
                    maxlength: 500,
                },
                message: {
                    required: true,
                    not_empty: true,
                    minlength: 3,
                },
            },
            messages: {
                user_type: {
                    required: "@lang('validation.required',['attribute'=>'user type'])",
                    not_empty: "@lang('validation.not_empty',['attribute'=>'user type'])",
                },
                subject:{
                    required:"@lang('validation.required',['attribute'=>'subject'])",
                    not_empty:"@lang('validation.not_empty',['attribute'=>'subject'])",
                    minlength:"@lang('validation.min.string',['attribute'=>'subject','min'=>3])",
                    maxlength:"@lang('validation.max.string',['attribute'=>'subject','max'=>500])",
                },
                message:{
                    required:"@lang('validation.required',['attribute'=>'message'])",
                    not_empty:"@lang('validation.not_empty',['attribute'=>'message'])",
                    minlength:"@lang('validation.min.string',['attribute'=>'message','min'=>3])",
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
        $('#frmSendPushNotification').submit(function () {
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
