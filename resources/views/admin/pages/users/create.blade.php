@extends('admin.layouts.app')

@push('breadcrumb')
    {!! Breadcrumbs::render('users_create') !!}
@endpush

@section('content')
<div class="container">
    <div class="card card-custom">
        <div class="card-header">
            <div class="card-title">
                <span class="card-icon">
                    <i class="fas fa-user-plus text-primary"></i>
                </span>
                <h3 class="card-label text-uppercase">ADD {{ $custom_title }}</h3>
            </div>
        </div>

        <!--begin::Form-->
        <form id="frmAddUser" method="POST" action="{{ route('admin.users.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="card-body">
                {{-- First Name --}}
                <div class="form-group">
                    <label for="first_name">{!!$mend_sign!!}First Name:</label>
                    <input type="text" class="form-control @error('first_name') is-invalid @enderror" id="first_name" name="first_name" value="{{ old('first_name') }}" placeholder="Enter first name" autocomplete="first_name" spellcheck="false" autocapitalize="sentences" tabindex="0" autofocus />
                    @if ($errors->has('first_name'))
                        <span class="help-block">
                            <strong class="form-text">{{ $errors->first('first_name') }}</strong>
                        </span>
                    @endif
                </div>

                {{-- Last Name --}}
                <div class="form-group">
                    <label for="last_name">{!!$mend_sign!!}Last Name:</label>
                    <input type="text" class="form-control @error('last_name') is-invalid @enderror" id="last_name" name="last_name" value="{{ old('last_name') }}" placeholder="Enter last name" autocomplete="last_name" spellcheck="false" autocapitalize="sentences" tabindex="0" autofocus />
                    @if ($errors->has('last_name'))
                        <span class="help-block">
                            <strong class="form-text">{{ $errors->first('last_name') }}</strong>
                        </span>
                    @endif
                </div>

                {{-- Country Code --}}
                <div class="form-group">
                    <label for="country_code">{!!$mend_sign!!}Country Code</label>
                    <input type="text" class="form-control @error('country_code') is-invalid @enderror" id="country_code" name="country_code" value="{{ old('country_code') }}" placeholder="Enter country code" autocomplete="country_code" spellcheck="false" tabindex="0" />
                    @if ($errors->has('country_code'))
                        <span class="text-danger">
                            <strong class="form-text">{{ $errors->first('country_code') }}</strong>
                        </span>
                    @endif
                </div>

                {{-- Contact Number --}}
                <div class="form-group">
                    <label for="contact_no">{!!$mend_sign!!}Contact Number</label>
                    <input type="text" class="form-control @error('contact_no') is-invalid @enderror" id="contact_no" name="contact_no" value="{{ old('contact_no') }}" placeholder="Enter contact number" autocomplete="contact_no" spellcheck="false" tabindex="0" />
                    @if ($errors->has('contact_no'))
                        <span class="text-danger">
                            <strong class="form-text">{{ $errors->first('contact_no') }}</strong>
                        </span>
                    @endif
                </div>

                {{-- Birth Date --}}
                <div class="form-group">
                    <label for="birth_date">{!!$mend_sign!!}Birth Date:</label>
                    <input type="date" class="form-control @error('birth_date') is-invalid @enderror" id="birth_date" name="birth_date" value="{{ old('birth_date') }}" max="{{ now()->subYears(config('utility.minimum_age'))->format('Y-m-d') }}" placeholder="Enter birth date" autocomplete="birth_date" spellcheck="false" tabindex="0" />
                    @if ($errors->has('birth_date'))
                        <span class="text-danger">
                            <strong class="form-text">{{ $errors->first('birth_date') }}</strong>
                        </span>
                    @endif
                </div>

                {{-- Gender --}}
                <div class="form-group">
                    <label for="gender">{!!$mend_sign!!}Gender</label>
                    <select type="text" class="form-control @error('gender') is-invalid @enderror" id="gender" name="gender" spellcheck="false" tabindex="0">
                        <option value="">Select Gender</option>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                    </select>
                    @if ($errors->has('gender'))
                        <span class="text-danger">
                            <strong class="form-text">{{ $errors->first('gender') }}</strong>
                        </span>
                    @endif
                </div>

                {{-- Email --}}
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" placeholder="Enter email" autocomplete="email" spellcheck="false" tabindex="0" />
                    @if ($errors->has('email'))
                        <span class="text-danger">
                            <strong class="form-text">{{ $errors->first('email') }}</strong>
                        </span>
                    @endif
                </div>

                {{-- Interest --}}
                <div class="form-group">
                    <label for="interest">Interest</label>
                    <select type="text" class="form-control @error('interest') is-invalid @enderror" id="interest" name="interest" spellcheck="false" tabindex="0" />
                        <option value="">Select Interest</option>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                        <option value="Both">Both</option>
                    </select>
                    @if ($errors->has('interest'))
                        <span class="text-danger">
                            <strong class="form-text">{{ $errors->first('interest') }}</strong>
                        </span>
                    @endif
                </div>

                {{-- Profile Photo --}}
                <div class="form-group">
                    <label for="profile_photo">Profile Photo</label>
                    <div class="custom-file">
                        <input type="file" class="custom-file-input" id="profile_photo" name="profile_photo" tabindex="0" />
                        <label class="custom-file-label @error('profile_photo') is-invalid @enderror" for="customFile">Choose file</label>
                        @if ($errors->has('profile_photo'))
                            <span class="text-danger">
                                <strong class="form-text">{{ $errors->first('profile_photo') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>

            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary mr-2 text-uppercase"> Add {{ $custom_title }}</button>
                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary text-uppercase">Cancel</a>
            </div>
        </form>
        <!--end::Form-->
    </div>
</div>
@endsection

@push('extra-js-scripts')
<script>
$(document).ready(function () {
    $("#frmAddUser").validate({
        rules: {
            first_name: {
                required: true,
                not_empty: true,
                minlength: 2,
            },
            last_name: {
                required: true,
                not_empty: true,
                minlength: 2,
            },
            email: {
                required: false,
                maxlength: 150,
                email: true,
                valid_email: true,
                remote: {
                    url: "{{ route('admin.check.email') }}",
                    type: "post",
                    data: {
                        _token: function() {
                            return "{{csrf_token()}}"
                        },
                        type: "user",
                    }
                },
            },
            country_code: {
                required: true,
                not_empty: true,
            },
            contact_no: {
                required: true,
                not_empty: true,
                maxlength: 16,
                minlength: 6,
                pattern: /^(\d+)(?: ?\d+)*$/,
                remote: {
                    url: "{{ route('admin.check.contact') }}",
                    type: "post",
                    data: {
                        _token: function() {
                            return "{{csrf_token()}}"
                        },
                        type: "user",
                    }
                },
            },
            birth_date: {
                required: true,
                not_empty: true,
                date: true,
            },
            gender: {
                required: true,
                not_empty: true,
            },
            interest: {
                required: false,
                not_empty: true,
            },
            profile_photo:{
                extension: "jpg|jpeg|png",
            },
        },
        messages: {
            first_name: {
                required: "@lang('validation.required',['attribute'=>'first name'])",
                not_empty: "@lang('validation.not_empty',['attribute'=>'first name'])",
                minlength:"@lang('validation.min.string',['attribute'=>'first name','min'=>2])",
            },
            last_name: {
                required: "@lang('validation.required',['attribute'=>'last name'])",
                not_empty: "@lang('validation.not_empty',['attribute'=>'last name'])",
                minlength:"@lang('validation.min.string',['attribute'=>'last name','min'=>2])",
            },
            email: {
                required: "@lang('validation.required',['attribute'=>'email address'])",
                maxlength:"@lang('validation.max.string',['attribute'=>'email address','max'=>150])",
                email:"@lang('validation.email',['attribute'=>'email address'])",
                valid_email:"@lang('validation.email',['attribute'=>'email address'])",
                remote:"@lang('validation.unique',['attribute'=>'email address'])",
            },
            contact_no: {
                required:"@lang('validation.required',['attribute'=>'contact number'])",
                not_empty:"@lang('validation.not_empty',['attribute'=>'contact number'])",
                maxlength:"@lang('validation.max.string',['attribute'=>'contact number','max'=>16])",
                minlength:"@lang('validation.min.string',['attribute'=>'contact number','min'=>6])",
                pattern:"@lang('validation.numeric',['attribute'=>'contact number'])",
                remote:"@lang('validation.unique',['attribute'=>'contact number'])",
            },
            birth_date: {
                required:"@lang('validation.required',['attribute'=>'birth date'])",
                not_empty:"@lang('validation.not_empty',['attribute'=>'birth date'])",
                date:"@lang('validation.date',['attribute'=>'birth date'])",
            },
            gender: {
                required: "@lang('validation.required',['attribute'=>'gender'])",
                not_empty: "@lang('validation.not_empty',['attribute'=>'gender'])",
            },
            interest: {
                required: "@lang('validation.required',['attribute'=>'interest'])",
                not_empty: "@lang('validation.not_empty',['attribute'=>'interest'])",
            },
            profile_photo: {
                extension:"@lang('validation.mimetypes',['attribute'=>'profile photo','value'=>'jpg|png|jpeg'])",
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
    $('#frmAddUser').submit(function () {
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
