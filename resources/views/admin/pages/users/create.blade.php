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
                {{-- Full Name --}}
                <div class="form-group">
                    <label for="full_name">{!!$mend_sign!!}Full Name:</label>
                    <input type="text" class="form-control @error('full_name') is-invalid @enderror" id="full_name" name="full_name" value="{{ old('full_name') }}" placeholder="Enter full name" autocomplete="full_name" spellcheck="false" autocapitalize="sentences" tabindex="0" autofocus />
                    @if ($errors->has('full_name'))
                        <span class="help-block">
                            <strong class="form-text">{{ $errors->first('full_name') }}</strong>
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

                <label>Verification Details ::</label>
                <div class="form-group col-md-6">
                    <div class="row">
                        {{-- Checkbox Selection For photos --}}
                        <div class="col-md-3 custom-checkbox">
                            <input type="checkbox" class="form-control @error('photo_verified_at') is-invalid @enderror" name="photo_verified_at" id="categpry" value="y">
                            <label for="photo_verified_at">Photos</label>
                        </div>

                        {{-- Checkbox Selection For Videos --}}
                        <div class="col-md-3 custom-checkbox">
                            <input type="checkbox" class="form-control @error('video_verified_at') is-invalid @enderror" name="video_verified_at" id="categpry" value="y">
                            <label for="video_verified_at">Videos</label>
                        </div>
                    </div>
                    @if ($errors->has('photo_verified_at'))
                        <span class="help-block">
                            <strong class="form-text">{{ $errors->first('photo_verified_at') }}</strong>
                        </span>
                    @endif
                    @if ($errors->has('video_verified_at'))
                        <span class="help-block">
                            <strong class="form-text">{{ $errors->first('video_verified_at') }}</strong>
                        </span>
                    @endif
                </div>

                {{-- Personality --}}
                <div class="form-group">
                    <label for="personality_id">Personality Type</label>
                    <select type="text" class="form-control @error('personality_id') is-invalid @enderror" id="personality_id" name="personality_id" spellcheck="false" tabindex="0" />
                        <option value="">Select Personality</option>
                        @foreach($personalities as $personality)
                        <option value="{{ $personality->id }}"> {{ $personality->title }}</option>
                        @endforeach
                    </select>
                    @if ($errors->has('personality_id'))
                        <span class="text-danger">
                            <strong class="form-text">{{ $errors->first('personality_id') }}</strong>
                        </span>
                    @endif
                </div>

                {{-- University --}}
                <div class="form-group">
                    <label for="university_id">University</label>
                    <select type="text" class="form-control @error('university_id') is-invalid @enderror" id="university_id" name="university_id" spellcheck="false" tabindex="0" />
                        <option value="">Select University</option>
                        @foreach($university as $collage)
                        <option value="{{ $collage->id }}"> {{ $collage->slug }}</option>
                        @endforeach
                    </select>
                    @if ($errors->has('university_id'))
                        <span class="text-danger">
                            <strong class="form-text">{{ $errors->first('university_id') }}</strong>
                        </span>
                    @endif
                </div>

                {{-- Education --}}
                <div class="form-group">
                    <label for="education_id">Education</label>
                    <select type="text" class="form-control @error('education_id') is-invalid @enderror" id="education_id" name="education_id" spellcheck="false" tabindex="0" />
                        <option value="">Select Education</option>
                        @foreach($educations as $education)
                        <option value="{{ $education->id }}"> {{ $education->slug }}</option>
                        @endforeach
                    </select>
                    @if ($errors->has('education_id'))
                        <span class="text-danger">
                            <strong class="form-text">{{ $errors->first('education_id') }}</strong>
                        </span>
                    @endif
                </div>

                {{-- Profession --}}
                <div class="form-group">
                    <label for="profession_id">Profession</label>
                    <select type="text" class="form-control @error('profession_id') is-invalid @enderror" id="profession_id" name="profession_id" spellcheck="false" tabindex="0" />
                        <option value="">Select Profession</option>
                        @foreach($professions as $profession)
                        <option value="{{ $profession->id }}"> {{ $profession->slug }}</option>
                        @endforeach
                    </select>
                    @if ($errors->has('education_id'))
                        <span class="text-danger">
                            <strong class="form-text">{{ $errors->first('education_id') }}</strong>
                        </span>
                    @endif
                </div>

                {{-- Religion --}}
                <div class="form-group">
                    <label for="religion_id">Religion</label>
                    <select type="text" class="form-control @error('religion_id') is-invalid @enderror" id="religion_id" name="religion_id" spellcheck="false" tabindex="0" />
                        <option value="">Select Religion</option>
                        @foreach($religions as $religion)
                        <option value="{{ $religion->id }}"> {{ $religion->slug }}</option>
                        @endforeach
                    </select>
                    @if ($errors->has('religion_id'))
                        <span class="text-danger">
                            <strong class="form-text">{{ $errors->first('religion_id') }}</strong>
                        </span>
                    @endif
                </div>

                {{-- Relationship status --}}
                <div class="form-group">
                    <label for="relationship_status_id">Relationship Status</label>
                    <select type="text" class="form-control @error('relationship_status_id') is-invalid @enderror" id="relationship_status_id" name="relationship_status_id" spellcheck="false" tabindex="0" />
                        <option value="">Select Relationship Status</option>
                        @foreach($relationship_status as $relation)
                        <option value="{{ $relation->id }}"> {{ $relation->slug }}</option>
                        @endforeach
                    </select>
                    @if ($errors->has('relationship_status_id'))
                        <span class="text-danger">
                            <strong class="form-text">{{ $errors->first('relationship_status_id') }}</strong>
                        </span>
                    @endif
                </div>

                {{-- You are here --}}
                <div class="form-group">
                    <label for="you_are_here_id">You Are Here</label>
                    <select type="text" class="form-control @error('you_are_here_id') is-invalid @enderror" id="you_are_here_id" name="you_are_here_id" spellcheck="false" tabindex="0" />
                        <option value="">Select You Are Here</option>
                        @foreach($you_are_here as $here)
                        <option value="{{ $here->id }}"> {{ $here->slug }}</option>
                        @endforeach
                    </select>
                    @if ($errors->has('you_are_here_id'))
                        <span class="text-danger">
                            <strong class="form-text">{{ $errors->first('you_are_here_id') }}</strong>
                        </span>
                    @endif
                </div>

                {{-- Food Preference --}}
                <div class="form-group">
                    <label for="food_preference_id">Food Preference</label>
                    <select type="text" class="form-control @error('food_preference_id') is-invalid @enderror" id="food_preference_id" name="food_preference_id" spellcheck="false" tabindex="0" />
                        <option value="">Select Food Preference</option>
                        @foreach($food_preferences as $food)
                        <option value="{{ $food->id }}"> {{ $food->slug }}</option>
                        @endforeach
                    </select>
                    @if ($errors->has('food_preference_id'))
                        <span class="text-danger">
                            <strong class="form-text">{{ $errors->first('food_preference_id') }}</strong>
                        </span>
                    @endif
                </div>

                {{-- Drinking --}}
                <div class="form-group">
                    <label for="drinking_id">Drinking</label>
                    <select type="text" class="form-control @error('drinking_id') is-invalid @enderror" id="drinking_id" name="drinking_id" spellcheck="false" tabindex="0" />
                        <option value="">Select Drinking</option>
                        @foreach($drinking as $drink)
                        <option value="{{ $drink->id }}"> {{ $drink->slug }}</option>
                        @endforeach
                    </select>
                    @if ($errors->has('drinking_id'))
                        <span class="text-danger">
                            <strong class="form-text">{{ $errors->first('drinking_id') }}</strong>
                        </span>
                    @endif
                </div>

                {{-- Smoking --}}
                <div class="form-group">
                    <label for="smoking_id">Smoking</label>
                    <select type="text" class="form-control @error('smoking_id') is-invalid @enderror" id="smoking_id" name="smoking_id" spellcheck="false" tabindex="0" />
                        <option value="">Select Smoking</option>
                        @foreach($smoking as $smoke)
                        <option value="{{ $smoke->id }}"> {{ $smoke->slug }}</option>
                        @endforeach
                    </select>
                    @if ($errors->has('smoking_id'))
                        <span class="text-danger">
                            <strong class="form-text">{{ $errors->first('smoking_id') }}</strong>
                        </span>
                    @endif
                </div>

                {{-- Pet --}}
                <div class="form-group">
                    <label for="pet_id">Pet</label>
                    <select type="text" class="form-control @error('pet_id') is-invalid @enderror" id="pet_id" name="pet_id" spellcheck="false" tabindex="0" />
                        <option value="">Select Pet</option>
                        @foreach($pets as $pet)
                        <option value="{{ $pet->id }}"> {{ $pet->slug }}</option>
                        @endforeach
                    </select>
                    @if ($errors->has('pet_id'))
                        <span class="text-danger">
                            <strong class="form-text">{{ $errors->first('pet_id') }}</strong>
                        </span>
                    @endif
                </div>

                {{-- Star_sign --}}
                <div class="form-group">
                    <label for="star_sign_id">Star Sign</label>
                    <select type="text" class="form-control @error('star_sign_id') is-invalid @enderror" id="star_sign_id" name="star_sign_id" spellcheck="false" tabindex="0" />
                        <option value="">Select Star Sign</option>
                        @foreach($star_signs as $star_sign)
                        <option value="{{ $star_sign->id }}"> {{ $star_sign->slug }}</option>
                        @endforeach
                    </select>
                    @if ($errors->has('star_sign_id'))
                        <span class="text-danger">
                            <strong class="form-text">{{ $errors->first('star_sign_id') }}</strong>
                        </span>
                    @endif
                </div>

                {{-- Community --}}
                <div class="form-group">
                    <label for="community_id">Community</label>
                    <select type="text" class="form-control @error('community_id') is-invalid @enderror" id="community_id" name="community_id" spellcheck="false" tabindex="0" />
                        <option value="">Select Community</option>
                        @foreach($community as $com)
                        <option value="{{ $com->id }}"> {{ $com->slug }}</option>
                        @endforeach
                    </select>
                    @if ($errors->has('community_id'))
                        <span class="text-danger">
                            <strong class="form-text">{{ $errors->first('community_id') }}</strong>
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
            full_name: {
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
            personality_id: {
                required: true,
                not_empty: true,
            },
            education_id: {
                required:true,
                not_empty: true,
            },
            university_id: {
                required:true,
                not_empty: true,
            },
            profession_id: {
                required: true,
                not_empty: true,
            },
            religion_id: {
                required: true,
                not_empty: true,
            },
            relationship_status_id: {
                required: true,
                not_empty: true,
            },
            you_are_here_id: {
                required: true,
                not_empty: true,
            },
            food_preference_id: {
                required: true,
                not_empty: true,
            },
            drinking_id: {
                required: true,
                not_empty: true,
            },
            smoking_id: {
                required: true,
                not_empty: true,
            },
            pet_id: {
                required: true,
                not_empty: true,
            },
            star_sign_id: {
                required: true,
                not_empty: true,
            },
            community_id: {
                required: true,
                not_empty: true,
            },
            profile_photo:{
                extension: "jpg|jpeg|png",
            },
        },
        messages: {
            full_name: {
                required: "@lang('validation.required',['attribute'=>'full name'])",
                not_empty: "@lang('validation.not_empty',['attribute'=>'full name'])",
                minlength:"@lang('validation.min.string',['attribute'=>'full name','min'=>2])",
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
            personality_id: {
                required: "@lang('validation.required',['attribute'=>'personality'])",
                not_empty: "@lang('validation.not_empty',['attribute'=>'personality'])",
            },
            education_id: {
                required: "@lang('validation.required',['attribute'=>'education'])",
                not_empty: "@lang('validation.not_empty',['attribute'=>'education'])",
            },
            university_id: {
                required: "@lang('validation.required',['attribute'=>'university'])",
                not_empty: "@lang('validation.not_empty',['attribute'=>'university'])",
            },
            religion_id: {
                required: "@lang('validation.required',['attribute'=>'religion'])",
                not_empty: "@lang('validation.not_empty',['attribute'=>'religion'])",
            },
            relationship_status_id: {
                required: "@lang('validation.required',['attribute'=>'relationship_status'])",
                not_empty: "@lang('validation.not_empty',['attribute'=>'relationship_status'])",
            },
            you_are_here_id: {
                required: "@lang('validation.required',['attribute'=>'you are here'])",
                not_empty: "@lang('validation.not_empty',['attribute'=>'you are here'])",
            },
            food_preference_id: {
                required: "@lang('validation.required',['attribute'=>'food preference'])",
                not_empty: "@lang('validation.not_empty',['attribute'=>'food preference'])",
            },
            drinking_id: {
                required: "@lang('validation.required',['attribute'=>'drinking'])",
                not_empty: "@lang('validation.not_empty',['attribute'=>'drinking'])",
            },
            smoking_id: {
                required: "@lang('validation.required',['attribute'=>'smoking'])",
                not_empty: "@lang('validation.not_empty',['attribute'=>'smoking'])",
            },
            pet_id:{
                required: "@lang('validation.required',['attribute'=>'pet'])",
                not_empty: "@lang('validation.not_empty',['attribute'=>'pet'])",
            },
            star_sign_id: {
                required: "@lang('validation.required',['attribute'=>'star sign'])",
                not_empty: "@lang('validation.not_empty',['attribute'=>'star sign'])",
            },
            community_id: {
                required: "@lang('validation.required',['attribute'=>'community'])",
                not_empty: "@lang('validation.not_empty',['attribute'=>'community'])",
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
