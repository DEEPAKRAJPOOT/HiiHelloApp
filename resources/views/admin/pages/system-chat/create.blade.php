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
                <h3 class="card-label text-uppercase">Send New System Message</h3>
            </div>
        </div>
        <form id="formSendSystemMessage" name="formSendSystemMessage" method="POST" action="{{ route('admin.system-chat.send-bulk') }}">
            @csrf
            <div class="row">
                <div class="col-md-12">
                    <div class="card-body ">
                        <div class="profile-content">
                            <div class="col-md-12">
                                <div class="form-group {{ $errors->has('message') ? 'has-error' : '' }}">
                                    <label for="message">{!! $mend_sign !!}Message</label>
                                    <textarea id="message" type="text" placeholder="Enter message" class="form-control" name="message"></textarea>
                                    @if($errors->has('message'))
                                        <span class="help-block">
                                            {{ $errors->first('message') }}
                                        </span>
                                    @endif
                                </div>
                                <div class="form-group" {{ $errors->has('user_type') ? 'has-error' : '' }}>
                                    <label for="user_type">{!!$mend_sign!!}User Type:</label>
                                    <div class="custom-file">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <select class="form-control" name="user_type">
                                                    <option value="">-- Select Type --</option>
                                                    <option value="send_all">All</option>
                                                    <option value="send_male">Male</option>
                                                    <option value="send_female">Female</option>
                                                    <option value="send_empty_profile_image">Empty Profile Image</option>
                                                    <option value="send_empty_location">Empty Location</option>
                                                    <option value="send_empty_college">Empty College</option>
                                                    <option value="send_less_then_20_pr">Less Then 20% Profile Percentage</option>
                                                    <option value="send_unverified_photo">Unverified Photo</option>
                                                    <option value="send_unverified_email">Unverified Email</option>
                                                    <option value="send_unverified_phone">Unverified Phone</option>
                                                    <option value="send_paid_male_subscription_not_expired">Paid Male subscription still not expired</option>
                                                    <option value="send_paid_male_subscription_expired">Paid Male subscription expired</option>
                                                    <option value="send_selected_users">Selected Users Only</option>
                                                    <option value="send_test_users">Only Test Users</option>
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

                                {{-- Select Users --}}
                                <div id="select_users_section" class="form-group d-none" {{ $errors->has('users') ? 'has-error' : '' }}>
                                    <label for="users">{!!$mend_sign!!}Selected Users :</label>
                                    <div class="custom-file">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <select id="users" class="form-control" name="users[]" multiple="multiple">
                                                    <option></option>
                                                </select>
                                            </div>
                                        </div>
                                        @if ($errors->has('users'))
                                            <span class="help-block">
                                                <strong class="form-text">{{ $errors->first('users') }}</strong>
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
                <button type="submit" class="btn btn-primary mr-2 text-uppercase">Send <i class="fa fa-paper-plane"></i></button>
                <a href="{{ route('admin.system-chat.index') }}" class="btn btn-secondary text-uppercase">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('extra-js-scripts')
<script type="text/javascript">
    $(document).ready(function () {
        $("#formSendSystemMessage").validate({
            rules: {
                user_type: {
                    required: true,
                    not_empty: true,
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
        $('#formSendSystemMessage').submit(function () {
            if($(this).valid()){
                addOverlay();
                $('input[type=submit],input[type=button],button[type=submit]').prop("disabled", "disabled");
                return true;
            } else {
                return false;
            }
        });
        $('[name="user_type"]').change(function(){
            $('#select_users_section').toggleClass('d-none',$(this).val() != 'send_selected_users');
        });
        $('#users').select2({
            'tags':false,
            'width':'100%',
            'placeholder':'Select Users',
            'ajax':{
                'url':'{{ route("admin.users.selection-listing") }}',
                'type':'get',
                'dataType':'json',
                'data':function(params){
                    return {
                        'search':params.term,
                        'page':params.page || 1
                    };
                }
            },
            'escapeMarkup':function(markup){
                return markup;
            },
            'templateResult':function(a){
                return a.text;
            },
            'templateSelection':function(a){
                return a.selection;
            }
        });
    });

</script>
@endpush
