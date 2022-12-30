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
                    <select type="text" class="form-control @error('country_code') is-invalid @enderror" id="country_code" name="country_code" spellcheck="false" tabindex="0" />
                        <option value="">Select Country Code</option>
                        @foreach($countries as $country)
                            <option value="{{ $country->phonecode }}">{{ $country->phonecode }}</option> 
                        @endforeach
                    </select>
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
                    <label for="interest">{!!$mend_sign!!}Interest</label>
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
                <div class="row">
                    {{-- Location --}}
                    <div class="form-group col-md-4">
                        <label for="location">Location</label>
                        <select type="text" class="form-control @error('location') is-invalid @enderror" id="location" name="location" spellcheck="false" tabindex="0" disabled />
                            <option value="">Select Location</option>
                            @foreach($locations as $location)
                                @if($location->locationTransDefault)
                                    <option value="{{ $location->id }}">{{ $location->locationTransDefault->name }}</option> 
                                @endif
                            @endforeach
                        </select>
                        @if ($errors->has('location'))
                            <span class="text-danger">
                                <strong class="form-text">{{ $errors->first('location') }}</strong>
                            </span>
                        @endif
                    </div>

                    {{-- Latitude --}}
                    <div class="form-group col-md-4">
                        <label for="location">Latitude</label>
                        <input type="text" class="form-control @error('latitude') is-invalid @enderror" id="latitude" name="latitude" value="{{ old('latitude') }}" placeholder="Enter latitude" autocomplete="latitude" autocapitalize="sentences" tabindex="0" autofocus />
                    </div>
                    {{-- longitude --}}
                    <div class="form-group col-md-4">
                        <label for="location">Longitude</label>
                        <input type="text" class="form-control @error('longitude') is-invalid @enderror" id="longitude" name="longitude" value="{{ old('longitude') }}" placeholder="Enter longitude" autocomplete="longitude" spellcheck="false" autocapitalize="sentences" tabindex="0" autofocus />
                    </div>
                </div>

                {{-- Language --}}
                <div class="form-group">
                    <label for="language">{!!$mend_sign!!}Language</label>
                    <select type="text" class="form-control @error('language') is-invalid @enderror" id="language" name="language" spellcheck="false" tabindex="0" />
                        <option value="">Select Language</option>
                        @foreach($languages as $language)
                            <option value="{{ $language->lang_code }}">{{ $language->lang_code }} ({{ $language->hint }})</option> 
                        @endforeach
                    </select>
                    @if ($errors->has('language'))
                        <span class="text-danger">
                            <strong class="form-text">{{ $errors->first('language') }}</strong>
                        </span>
                    @endif
                </div>

                {{-- Profile Photo --}}
                <div class="form-group">
                    <label for="profile_photo">{!!$mend_sign!!}Profile Photo</label>
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

                <label>Verification Details ::</label>
                <div class="form-group col-md-6">
                    <div class="row">
                        {{-- Checkbox Selection For photos --}}
                        <div class="col-md-3 custom-checkbox">
                            <input type="checkbox" class="form-control @error('photo_verified_at') is-invalid @enderror" name="verify_photo" id="categpry" value="y">
                            <label for="verify_photo">Photos</label>
                        </div>

                        {{-- Checkbox Selection For Videos --}}
                        <div class="col-md-3 custom-checkbox">
                            <input type="checkbox" class="form-control @error('video_verified_at') is-invalid @enderror" name="verify_video" id="categpry" value="y">
                            <label for="verify_video">Videos</label>
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

            </div>
            
        <!--end::Form-->
    </div>

    <br><br>
    <div class="card card-custom">
        <div class="card-header">
            <div class="card-title">
                <span class="card-icon">
                    <i class="fas fa-user-edit text-primary"></i>
                </span>
                <h3 class="card-label text-uppercase">Long Profile</h3>
            </div>
        </div>
        <div class="card-body">

            {{-- Fav movie --}}
            <div class="form-group">
                <label for="fav_movie">Favourite Movie:</label>
                <textarea type="text" class="form-control @error('fav_movie') is-invalid @enderror" id="fav_movie" name="fav_movie" placeholder="Enter favourite movie" autocomplete="fav_movie" spellcheck="false" autocapitalize="sentences" tabindex="0" autofocus>{{ old('fav_movie') }}</textarea>
                @if ($errors->has('fav_movie'))
                    <span class="help-block">
                        <strong class="form-text">{{ $errors->first('fav_movie') }}</strong>
                    </span>
                @endif
            </div>   

            {{-- About Me --}}
            <div class="form-group">
                <label for="about_me">About Me:</label>
                <textarea type="text" class="form-control @error('about_me') is-invalid @enderror" id="about_me" name="about_me" placeholder="Enter favourite movie" autocomplete="about_me" spellcheck="false" autocapitalize="sentences" tabindex="0" autofocus>{{ old('about_me') }}</textarea>
                @if ($errors->has('about_me'))
                    <span class="help-block">
                        <strong class="form-text">{{ $errors->first('about_me') }}</strong>
                    </span>
                @endif
            </div>  

            {{-- Personality --}}
            <div class="form-group">
                <label for="personalities[]">Personality Type</label>
                <select type="text" class="form-control @error('personalities[]') is-invalid @enderror" id="personality_id" name="personalities[]" spellcheck="false" tabindex="0" multiple="multiple" />
                    <option value="">Select Personality</option>
                    @foreach($personalities as $personality)
                        @if($personality->personalityTransDefault)
                            <option value="{{ $personality->id }}"> {{ $personality->personalityTransDefault->title }}</option>
                        @endif
                    @endforeach
                </select>
                @if ($errors->has('personalities[]'))
                    <span class="text-danger">
                        <strong class="form-text">{{ $errors->first('personalities[]') }}</strong>
                    </span>
                @endif
            </div>

            {{-- Education --}}
            <div class="form-group">
                <label for="education_id">Education</label>
                <select type="text" class="form-control @error('education_id') is-invalid @enderror" id="education_id" name="education_id" spellcheck="false" tabindex="0" />
                    <option value="">Select Education</option>
                    @foreach($attributes as $education)
                        @if($education->attribute == 'education' && $education->profileDetailTransDefault)
                            <option value="{{ $education->id }}"> {{ $education->profileDetailTransDefault->value }}</option>
                        @endif
                    @endforeach
                </select>
                @if ($errors->has('education_id'))
                    <span class="text-danger">
                        <strong class="form-text">{{ $errors->first('education_id') }}</strong>
                    </span>
                @endif
            </div>

            {{-- University --}}
            <div class="form-group">
                <label for="university_id">University</label>
                <select type="text" class="form-control @error('university_id') is-invalid @enderror" id="university_id" name="university_id" spellcheck="false" tabindex="0" />
                    <option value="">Select University</option>
                    @foreach($attributes as $collage)
                        @if($collage->attribute == 'university_college' && $collage->profileDetailTransDefault)
                            <option value="{{ $collage->id }}"> {{ $collage->profileDetailTransDefault->value }}</option> 
                        @endif  
                    @endforeach
                </select>
                @if ($errors->has('university_id'))
                    <span class="text-danger">
                        <strong class="form-text">{{ $errors->first('university_id') }}</strong>
                    </span>
                @endif
            </div>

            {{-- Profession --}}
            <div class="form-group">
                <label for="profession_id">Profession</label>
                <select type="text" class="form-control @error('profession_id') is-invalid @enderror" id="profession_id" name="profession_id" spellcheck="false" tabindex="0" />
                    <option value="">Select Profession</option>
                    @foreach($attributes as $profession)
                        @if($profession->attribute == 'profession' && $profession->profileDetailTransDefault)
                            <option value="{{ $profession->id }}"> {{ $profession->profileDetailTransDefault->value }}</option>
                        @endif
                    @endforeach
                </select>
                @if ($errors->has('profession_id'))
                    <span class="text-danger">
                        <strong class="form-text">{{ $errors->first('profession_id') }}</strong>
                    </span>
                @endif
            </div>

            {{-- Religion --}}
            <div class="form-group">
                <label for="religion_id">Religion</label>
                <select type="text" class="form-control @error('religion_id') is-invalid @enderror" id="religion_id" name="religion_id" spellcheck="false" tabindex="0" />
                    <option value="">Select Religion</option>
                    @foreach($attributes as $religion)
                        @if($religion->attribute == 'religion' && $religion->profileDetailTransDefault)
                            <option value="{{ $religion->id }}"> {{ $religion->profileDetailTransDefault->value }}</option>
                        @endif
                    @endforeach
                </select>
                @if ($errors->has('religion_id'))
                    <span class="text-danger">
                        <strong class="form-text">{{ $errors->first('religion_id') }}</strong>
                    </span>
                @endif
            </div>

            {{-- You are here --}}
            <div class="form-group">
                <label for="you_are_here_id">You Are Here</label>
                <select type="text" class="form-control @error('you_are_here_id') is-invalid @enderror" id="you_are_here_id" name="you_are_here_id" spellcheck="false" tabindex="0" />
                    <option value="">Select You Are Here</option>
                    @foreach($attributes as $here)
                        @if($here->attribute == 'i_am_here' && $here->profileDetailTransDefault)
                            <option value="{{ $here->id }}"> {{ $here->profileDetailTransDefault->value }}</option>
                        @endif
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
                    @foreach($attributes as $food)
                        @if($food->attribute == 'food_preference' && $food->profileDetailTransDefault)
                            <option value="{{ $food->id }}"> {{ $food->profileDetailTransDefault->value }}</option>
                        @endif
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
                    @foreach($attributes as $drink)
                        @if($drink->attribute == 'drinking' && $drink->profileDetailTransDefault)
                            <option value="{{ $drink->id }}"> {{ $drink->profileDetailTransDefault->value }}</option>
                        @endif
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
                    @foreach($attributes as $smoke)
                        @if($smoke->attribute == 'smoking' && $smoke->profileDetailTransDefault)
                            <option value="{{ $smoke->id }}"> {{ $smoke->profileDetailTransDefault->value }}</option>
                        @endif
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
                    @foreach($attributes as $pet)
                        @if($pet->attribute == 'pet' && $pet->profileDetailTransDefault)
                            <option value="{{ $pet->id }}"> {{ $pet->profileDetailTransDefault->value }}</option>
                        @endif
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
                    @foreach($attributes as $star_sign)
                        @if($star_sign->attribute == 'star_sign' && $star_sign->profileDetailTransDefault)
                            <option value="{{ $star_sign->id }}"> {{ $star_sign->profileDetailTransDefault->value }}</option>
                        @endif
                    @endforeach
                </select>
                @if ($errors->has('star_sign_id'))
                    <span class="text-danger">
                        <strong class="form-text">{{ $errors->first('star_sign_id') }}</strong>
                    </span>
                @endif
            </div>

            {{-- Relationship status --}}
            <div class="form-group">
                <label for="relationship_status_id">Relationship Status</label>
                <select type="text" class="form-control @error('relationship_status_id') is-invalid @enderror" id="relationship_status_id" name="relationship_status_id" spellcheck="false" tabindex="0" />
                    <option value="">Select Relationship Status</option>
                    @foreach($attributes as $relation)
                        @if($relation->attribute == 'relationship_status' && $relation->profileDetailTransDefault)
                            <option value="{{ $relation->id }}"> {{ $relation->profileDetailTransDefault->value }}</option>
                        @endif
                    @endforeach
                </select>
                @if ($errors->has('relationship_status_id'))
                    <span class="text-danger">
                        <strong class="form-text">{{ $errors->first('relationship_status_id') }}</strong>
                    </span>
                @endif
            </div>

            {{-- Community --}}
            <div class="form-group">
                <label for="community_id">Community</label>
                <select type="text" class="form-control @error('community_id') is-invalid @enderror" id="community_id" name="community_id" spellcheck="false" tabindex="0" />
                    <option value="">Select Community</option>
                    @foreach($attributes as $com)
                        @if($com->attribute == 'community' && $com->profileDetailTransDefault)
                            <option value="{{ $com->id }}"> {{ $com->profileDetailTransDefault->value }}</option>
                        @endif
                    @endforeach
                </select>
                @if ($errors->has('community_id'))
                    <span class="text-danger">
                        <strong class="form-text">{{ $errors->first('community_id') }}</strong>
                    </span>
                @endif
            </div>
        </div>
    </div>

    <br><br>
    <div class="card card-custom">
        <div class="card-header">
            <div class="card-title">
                <span class="card-icon">
                    <i class="fas fa-user-edit text-primary"></i>
                </span>
                <h3 class="card-label text-uppercase">Profile Interests</h3>
            </div>
        </div>
        <div class="card-body">

            {{-- Idea Of Travelling --}}
            <div class="form-group">
                <label for="traveling_id[]">Idea Of Travelling</label>
                <select type="text" class="form-control @error('traveling_id[]') is-invalid @enderror" id="traveling_id" name="traveling_id[]" spellcheck="false" tabindex="0"  multiple="multiple" />
                    <option value="">Select Idea of Travelling</option>
                    @foreach($interests as $travel)
                        @if($travel->slug == 'traveling')
                            @foreach($travel->subInterests as $sub)
                                @if($sub->interestTransDefault)
                                    <option value="{{ $sub->id }}"> {{ $sub->interestTransDefault->title }}</option>
                                @endif
                            @endforeach
                        @endif
                    @endforeach
                </select>
                @if ($errors->has('traveling_id[]'))
                    <span class="text-danger">
                        <strong class="form-text">{{ $errors->first('traveling_id[]') }}</strong>
                    </span>
                @endif
            </div>

            {{-- My Kind Of Music --}}
            <div class="form-group">
                <label for="music_id[]">My Kind Of Music</label>
                <select type="text" class="form-control @error('music_id[]') is-invalid @enderror" id="music_id" name="music_id[]" spellcheck="false" tabindex="0"  multiple="multiple" />
                    <option value="">Select My Kind Of Music</option>
                    @foreach($interests as $music)
                        @if($music->slug == 'music')
                            @foreach($music->subInterests as $sub_music)
                                @if($sub_music->interestTransDefault)
                                    <option value="{{ $sub_music->id }}"> {{ $sub_music->interestTransDefault->title }}</option>
                                @endif
                            @endforeach
                        @endif
                    @endforeach
                </select>
                @if ($errors->has('music_id[]'))
                    <span class="text-danger">
                        <strong class="form-text">{{ $errors->first('music_id[]') }}</strong>
                    </span>
                @endif
            </div>

            {{-- Hobbies & Pass Time --}}
            <div class="form-group">
                <label for="hobbie_id[]">Hobbies & Pass Time</label>
                <select type="text" class="form-control @error('hobbie_id[]') is-invalid @enderror" id="hobbie_id" name="hobbie_id[]" spellcheck="false" tabindex="0"  multiple="multiple" />
                    <option value="">Select Hobbies & Pass Time</option>
                    @foreach($interests as $hobbie)
                        @if($hobbie->slug == 'hobbies')
                            @foreach($hobbie->subInterests as $sub_hobbie)
                                @if($sub_hobbie->interestTransDefault)
                                    <option value="{{ $sub_hobbie->id }}"> {{ $sub_hobbie->interestTransDefault->title }}</option>
                                @endif
                            @endforeach
                        @endif
                    @endforeach
                </select>
                @if ($errors->has('hobbie_id[]'))
                    <span class="text-danger">
                        <strong class="form-text">{{ $errors->first('hobbie_id[]') }}</strong>
                    </span>
                @endif
            </div>

            {{-- Favourite Childhood Game --}}
            <div class="form-group">
                <label for="game_id[]">Favourite Childhood Game</label>
                <select type="text" class="form-control @error('game_id[]') is-invalid @enderror" id="game_id" name="game_id[]" spellcheck="false" tabindex="0"  multiple="multiple" />
                    <option value="">Select Favourite Childhood Game</option>
                    @foreach($interests as $game)
                        @if($game->slug == 'childhood-game')
                            @foreach($game->subInterests as $sub_game)
                                @if($sub_game->interestTransDefault)
                                    <option value="{{ $sub_game->id }}"> {{ $sub_game->interestTransDefault->title }}</option>
                                @endif
                            @endforeach
                        @endif
                    @endforeach
                </select>
                @if ($errors->has('game_id[]'))
                    <span class="text-danger">
                        <strong class="form-text">{{ $errors->first('game_id[]') }}</strong>
                    </span>
                @endif
            </div>

            {{-- Favourite Sports --}}
            <div class="form-group">
                <label for="sport_id[]">Favourite Sports</label>
                <select type="text" class="form-control @error('sport_id[]') is-invalid @enderror" id="sport_id" name="sport_id[]" spellcheck="false" tabindex="0"  multiple="multiple" />
                    <option value="">Select Favourite Sports</option>
                    @foreach($interests as $sport)
                        @if($sport->slug == 'sports')
                            @foreach($sport->subInterests as $sub_sport)
                                @if($sub_sport->interestTransDefault)
                                    <option value="{{ $sub_sport->id }}"> {{ $sub_sport->interestTransDefault->title }}</option>
                                @endif
                            @endforeach
                        @endif
                    @endforeach
                </select>
                @if ($errors->has('sport_id[]'))
                    <span class="text-danger">
                        <strong class="form-text">{{ $errors->first('sport_id[]') }}</strong>
                    </span>
                @endif
            </div>

            {{-- Food I Love To Eat --}}
            <div class="form-group">
                <label for="food_id[]">Food I Love To Eat</label>
                <select type="text" class="form-control @error('food_id[]') is-invalid @enderror" id="food_id" name="food_id[]" spellcheck="false" tabindex="0"  multiple="multiple" />
                    <option value="">Select Food I Love To Eat</option>
                    @foreach($interests as $food)
                        @if($food->slug == 'food')
                            @foreach($food->subInterests as $sub_food)
                                @if($sub_food->interestTransDefault)
                                    <option value="{{ $sub_food->id }}"> {{ $sub_food->interestTransDefault->title }}</option>
                                @endif
                            @endforeach
                        @endif
                    @endforeach
                </select>
                @if ($errors->has('food_id[]'))
                    <span class="text-danger">
                        <strong class="form-text">{{ $errors->first('food_id[]') }}</strong>
                    </span>
                @endif
            </div>

            {{-- Fav Actors --}}
            <div class="form-group">
                <label for="actor_id[]">Favourite Actors/Actress</label>
                <select type="text" class="form-control @error('actor_id[]') is-invalid @enderror" id="actor_id" name="actor_id[]" spellcheck="false" tabindex="0" multiple="multiple" />
                    <option value="">Select Favourite Actors/Actress</option>
                    @foreach($interests as $actor)
                        @if($actor->slug == 'actors')
                            @foreach($actor->subInterests as $sub_actor)
                                @foreach($sub_actor->subInterests as $sub_gender)
                                    @if($sub_gender->interestTransDefault)
                                        <option value="{{ $sub_gender->id }}"> {{ $sub_gender->interestTransDefault->title }}</option>
                                    @endif
                                @endforeach
                            @endforeach
                        @endif
                    @endforeach
                </select>
                @if ($errors->has('actor_id[]'))
                    <span class="text-danger">
                        <strong class="form-text">{{ $errors->first('actor_id[]') }}</strong>
                    </span>
                @endif
            </div>
            
            {{-- Select Singer (Male/Female) --}}
            <div class="form-group">
                <label for="singer_id[]">Select Singer (Male/Female)</label>
                <select type="text" class="form-control @error('singer_id[]') is-invalid @enderror" id="singer_id" name="singer_id[]" spellcheck="false" tabindex="0" multiple="multiple" />
                    <option value="">Select Singer (Male/Female)</option>
                    @foreach($interests as $singer)
                        @if($singer->slug == 'singers')
                            @foreach($singer->subInterests as $sub_singer)
                                @foreach($sub_singer->subInterests as $sub_gender)
                                    @if($sub_gender->interestTransDefault)
                                        <option value="{{ $sub_gender->id }}"> {{ $sub_gender->interestTransDefault->title }}</option>
                                    @endif
                                @endforeach
                            @endforeach
                        @endif
                    @endforeach
                </select>
                @if ($errors->has('singer_id[]'))
                    <span class="text-danger">
                        <strong class="form-text">{{ $errors->first('singer_id[]') }}</strong>
                    </span>
                @endif
            </div>
        </div>

        <div class="card-footer">
            <button type="submit" class="btn btn-primary mr-2 text-uppercase"> Add {{ $custom_title }}</button>
            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary text-uppercase">Cancel</a>
        </div>
        </form>
    </div>
</div>
@endsection

@push('extra-js-scripts')
<script>
$(document).ready(function () {
    $('#gender').select2({ placeholder: 'Select gender'});
    $('#country_code').select2({ placeholder: 'Select county code'});
    $('#interest').select2({ placeholder: 'Select interest'});
    $('#location').select2({ placeholder: 'Select location'});
    $('#language').select2({ placeholder: 'Select language'});
    $('#personality_id').select2({ placeholder: 'Select personality'});
    $('#education_id').select2({ placeholder: 'Select education'});
    $('#university_id').select2({ placeholder: 'Select university'});
    $('#profession_id').select2({ placeholder: 'Select profession'});
    $('#religion_id').select2({ placeholder: 'Select religion'});
    $('#you_are_here_id').select2({ placeholder: 'Select you are here'});
    $('#food_preference_id').select2({ placeholder: 'Select food preference'});
    $('#drinking_id').select2({ placeholder: 'Select drinking'});
    $('#smoking_id').select2({ placeholder: 'Select smoking'});
    $('#pet_id').select2({ placeholder: 'Select pet'});
    $('#star_sign_id').select2({ placeholder: 'Select star sign'});
    $('#relationship_status_id').select2({ placeholder: 'Select relationship status'});
    $('#community_id').select2({ placeholder: 'Select community'});
    $('#traveling_id').select2({ placeholder: 'Select traveling'});
    $('#music_id').select2({ placeholder: 'Select music'});
    $('#hobbie_id').select2({ placeholder: 'Select hobbie'});
    $('#game_id').select2({ placeholder: 'Select game'});
    $('#sport_id').select2({ placeholder: 'Select sport'});
    $('#food_id').select2({ placeholder: 'Select food'});
    $('#actor_id').select2({ placeholder: 'Select actor gender'});
    $('#singer_id').select2({ placeholder: 'Select singer'});

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
                required: true,
                not_empty: true,
            },
            location: {
                required: true,
                not_empty: true,
            },
            language: {
                required: true,
                not_empty: true,
            },
            'personalities[]': {
                required: false,
                not_empty: true,
            },
            education_id: {
                required:false,
                not_empty: true,
            },
            university_id: {
                required:false,
                not_empty: true,
            },
            profession_id: {
                required: false,
                not_empty: true,
            },
            religion_id: {
                required: false,
                not_empty: true,
            },
            you_are_here_id: {
                required: false,
                not_empty: true,
            },
            food_preference_id: {
                required: false,
                not_empty: true,
            },
            drinking_id: {
                required: false,
                not_empty: true,
            },
            smoking_id: {
                required: false,
                not_empty: true,
            },
            pet_id: {
                required: false,
                not_empty: true,
            },
            star_sign_id: {
                required: false,
                not_empty: true,
            },
            relationship_status_id: {
                required: false,
                not_empty: true,
            },
            community_id: {
                required: false,
                not_empty: true,
            },
            fav_movie: {
                required: false,
                not_empty: true,
                minlength: 1,
                maxlength: 250,
            },
            about_me: {
                required: false,
                not_empty: true,
                minlength: 3,
                maxlength: 1000,
            },
            traveling_id: {
                required: false,
                not_empty: true,
            },
            music_id: {
                required: false,
                not_empty: true,
            },
            hobbie_id: {
                required: false,
                not_empty: true,
            },
            game_id: {
                required: false,
                not_empty: true,
            },
            sport_id: {
                required: false,
                not_empty: true,
            },
            food_id: {
                required: false,
                not_empty: true,
            },
            profile_photo:{
                required: true,
                not_empty: true,
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
            country_code: {
                required: "@lang('validation.required',['attribute'=>'country code'])",
                not_empty: "@lang('validation.not_empty',['attribute'=>'country code'])",
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
            location: {
                required: "@lang('validation.required',['attribute'=>'location'])",
                not_empty: "@lang('validation.not_empty',['attribute'=>'location'])",
            },
            language: {
                required: "@lang('validation.required',['attribute'=>'language'])",
                not_empty: "@lang('validation.not_empty',['attribute'=>'language'])",
            },
            fav_movie: {
                required: "@lang('validation.required',['attribute'=>'favourite movie'])",
                not_empty: "@lang('validation.not_empty',['attribute'=>'favourite movie'])",
                minlength:"@lang('validation.min.string',['attribute'=>'favourite movie','min'=>1])",
                maxlength:"@lang('validation.max.string',['attribute'=>'favourite movie','max'=>250])",
            },
            about_me: {
                required: "@lang('validation.required',['attribute'=>'about us'])",
                not_empty: "@lang('validation.not_empty',['attribute'=>'about us'])",
                minlength:"@lang('validation.min.string',['attribute'=>'about us','min'=>3])",
                maxlength:"@lang('validation.max.string',['attribute'=>'about us','max'=>1000])",
            },
            'personalities[]': {
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
            relationship_status_id: {
                required: "@lang('validation.required',['attribute'=>'relationship_status'])",
                not_empty: "@lang('validation.not_empty',['attribute'=>'relationship_status'])",
            },
            community_id: {
                required: "@lang('validation.required',['attribute'=>'community'])",
                not_empty: "@lang('validation.not_empty',['attribute'=>'community'])",
            },
            traveling_id: {
                required: "@lang('validation.required',['attribute'=>'traveling'])",
                not_empty: "@lang('validation.not_empty',['attribute'=>'traveling'])",
            },
            music_id: {
                required: "@lang('validation.required',['attribute'=>'music'])",
                not_empty: "@lang('validation.not_empty',['attribute'=>'music'])",
            },
            hobbie_id: {
                required: "@lang('validation.required',['attribute'=>'hobbie'])",
                not_empty: "@lang('validation.not_empty',['attribute'=>'hobbie'])",
            },
            game_id: {
                required: "@lang('validation.required',['attribute'=>'game'])",
                not_empty: "@lang('validation.not_empty',['attribute'=>'game'])",   
            },
            sport_id: {
                required: "@lang('validation.required',['attribute'=>'sport'])",
                not_empty: "@lang('validation.not_empty',['attribute'=>'sport'])",
            },
            food_id: {
                required: "@lang('validation.required',['attribute'=>'food'])",
                not_empty: "@lang('validation.not_empty',['attribute'=>'food'])",
            },
            profile_photo: {
                required: "@lang('validation.required',['attribute'=>'profile photo'])",
                not_empty: "@lang('validation.not_empty',['attribute'=>'profile photo'])",
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
