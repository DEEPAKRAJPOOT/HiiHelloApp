@extends('admin.layouts.app')

@push('breadcrumb')
    {!! Breadcrumbs::render('users_update', $user->id) !!}
@endpush

@section('content')
<div class="container">
    <div class="card card-custom">
        <div class="card-header">
            <div class="card-title">
                <span class="card-icon">
                    <i class="fas fa-user-edit text-primary"></i>
                </span>
                <h3 class="card-label text-uppercase">Edit {{ $custom_title }}</h3>
            </div>
        </div>

        <!--begin::Form-->
        <form id="frmEditUser" method="POST" action="{{ route('admin.users.update', $user->custom_id) }}" enctype="multipart/form-data">
            @csrf
            @method('put')
            <div class="card-body">

                {{-- Full Name --}}
                <div class="form-group">
                    <label for="full_name">{!!$mend_sign!!}Full Name:</label>
                    <input type="text" class="form-control @error('full_name') is-invalid @enderror" id="full_name" name="full_name" value="{{ old('full_name') != null ? old('full_name') : $user->full_name }}" placeholder="Enter full name" autocomplete="full_name" spellcheck="false" autocapitalize="sentences" tabindex="0" autofocus />
                    @if ($errors->has('full_name'))
                        <span class="help-block">
                            <strong class="form-text">{{ $errors->first('full_name') }}</strong>
                        </span>
                    @endif
                </div>

                {{-- Country Code --}}
                <div class="form-group">
                    <label for="country_code">{!!$mend_sign!!}Country Code</label>
                    <input type="text" class="form-control @error('country_code') is-invalid @enderror" id="country_code" name="country_code" value="{{ old('country_code') != null ? old('country_code') : $user->country_code }}" placeholder="Enter country code" autocomplete="country_code" spellcheck="false" tabindex="0" />
                    @if ($errors->has('country_code'))
                        <span class="text-danger">
                            <strong class="form-text">{{ $errors->first('country_code') }}</strong>
                        </span>
                    @endif
                </div>
                
                {{-- Contact Number --}}
                <div class="form-group">
                    <label for="contact_no">{!!$mend_sign!!}Contact Number</label>
                    <input type="contact_no" class="form-control @error('contact_no') is-invalid @enderror" id="contact_no" name="contact_no" value="{{ old('contact_no') != null ? old('contact_no') : $user->contact_no }}" placeholder="Enter contact number" autocomplete="contact_no" spellcheck="false" tabindex="0" />
                    @if ($errors->has('contact_no'))
                        <span class="text-danger">
                            <strong class="form-text">{{ $errors->first('contact_no') }}</strong>
                        </span>
                    @endif
                </div>

                {{-- Birth Date --}}
                <div class="form-group">
                    <label for="birth_date">Birth Date:</label>
                    <input type="date" class="form-control @error('birth_date') is-invalid @enderror" id="birth_date" name="birth_date" value="{{ old('birth_date') != null ? old('birth_date') : $user->birth_date }}" max="{{ now()->subYears(config('utility.minimum_age'))->format('Y-m-d') }}"  placeholder="Enter birth date" autocomplete="birth_date" spellcheck="false" tabindex="0" />
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
                        @if($user->gender == 'Male')
                            <option value="">Select Gender</option>
                            <option value="Male" selected>Male</option>
                            <option value="Female">Female</option>
                        @elseif($user->gender == 'Female')
                            <option value="">Select Gender</option>
                            <option value="Male">Male</option>
                            <option value="Female" selected>Female</option>
                        @else
                            <option value="">Select Gender</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                        @endif
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
                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') != null ? old('email') : $user->email }}" placeholder="Enter email" autocomplete="email" spellcheck="false" tabindex="0" />
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
                        @if($user->interest == 'Male')
                            <option value="">Select Interest</option>
                            <option value="Male" selected>Male</option>
                            <option value="Female">Female</option>
                            <option value="Both">Both</option>
                        @elseif($user->interest == 'Female')
                            <option value="">Select Interest</option>
                            <option value="Male">Male</option>
                            <option value="Female" selected>Female</option>
                            <option value="Both">Both</option>
                        @elseif($user->interest == 'Both')
                            <option value="">Select Interest</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                            <option value="Both" selected>Both</option>
                        @else
                            <option value="">Select Interest</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                            <option value="Both">Both</option>
                        @endif
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
                @if ($user->profile_photo)
                <div class="symbol symbol-120 mr-5">
                        <div class="symbol-label" style="background-image:url({{ generateURL($user->profile_photo)}})">
                        {{-- Custom css added .symbol div a --}}
                            <a href="#" class="btn btn-icon btn-light btn-hover-danger remove-img" id="kt_quick_user_close" style="width: 18px; height: 18px;">
                                <i class="ki ki-close icon-xs text-muted"></i>
                            </a>
                        </div>
                 </div>
                 @endif
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
            {{-- Personality --}}
                <div class="form-group">
                    <label for="personality_id">Personality Type</label>
                    <select type="text" class="form-control @error('personality_id') is-invalid @enderror" id="personality_id" name="personality_id"/>
                        <option value="">Select Personality</option>
                        @foreach($personalities as $personality)
                        <option {{ $personality->id == $user->personality_id ? 'selected=selected' : '' }} value="{{ $personality->id }}"> {{ $personality->title }}</option>
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
                        <option {{ $collage->id == $user->university_id ? 'selected=selected' : '' }} value="{{ $collage->id }}"> {{ $collage->profileDetailTransDefault->value }}</option>
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
                        <option {{ $education->id == $user->education_id ? 'selected=selected' : '' }} value="{{ $education->id }}"> {{ $education->profileDetailTransDefault->value }}</option>
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
                        <option {{ $profession->id == $user->profession_id ? 'selected=selected' : '' }} value="{{ $profession->id }}"> {{ $profession->profileDetailTransDefault->value }}</option>
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
                        <option {{ $religion->id == $user->religion_id ? 'selected=selected' : '' }} value="{{ $religion->id }}"> {{ $religion->profileDetailTransDefault->value }}</option>
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
                        <option {{ $relation->id == $user->relationship_status_id ? 'selected=selected' : '' }} value="{{ $relation->id }}"> {{ $relation->profileDetailTransDefault->value }}</option>
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
                        <option {{ $here->id == $user->you_are_here_id ? 'selected=selected' : '' }} value="{{ $here->id }}"> {{ $here->profileDetailTransDefault->value }}</option>
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
                        <option {{ $food->id == $user->food_preference_id ? 'selected=selected' : '' }} value="{{ $food->id }}"> {{ $food->profileDetailTransDefault->value }}</option>
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
                        <option {{ $drink->id == $user->drinking_id ? 'selected=selected' : '' }} value="{{ $drink->id }}"> {{ $drink->profileDetailTransDefault->value }}</option>
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
                        <option {{ $smoke->id == $user->smoking_id ? 'selected=selected' : '' }} value="{{ $smoke->id }}"> {{ $smoke->profileDetailTransDefault->value }}</option>
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
                        <option {{ $pet->id == $user->pet_id ? 'selected=selected' : '' }} value="{{ $pet->id }}"> {{ $pet->profileDetailTransDefault->title }}</option>
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
                        <option {{ $star_sign->id == $user->star_sign_id ? 'selected=selected' : '' }} value="{{ $star_sign->id }}"> {{ $star_sign->profileDetailTransDefault->value }}</option>
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
                        <option {{ $com->id == $user->community_id ? 'selected=selected' : '' }} value="{{ $com->id }}"> {{ $com->profileDetailTransDefault->value }}</option>
                        @endforeach
                    </select>
                    @if ($errors->has('community_id'))
                        <span class="text-danger">
                            <strong class="form-text">{{ $errors->first('community_id') }}</strong>
                        </span>
                    @endif
                </div>

                {{-- Fav movie --}}
                <div class="form-group">
                    <label for="fav_movie">Favourite Movie:</label>
                    <input type="text" class="form-control @error('fav_movie') is-invalid @enderror" id="fav_movie" name="fav_movie" value="{{ old('fav_movie') != null ? old('fav_movie') : $user->fav_movie }}" placeholder="Enter favourite movie" autocomplete="fav_movie" spellcheck="false" autocapitalize="sentences" tabindex="0" autofocus />
                    @if ($errors->has('fav_movie'))
                        <span class="help-block">
                            <strong class="form-text">{{ $errors->first('fav_movie') }}</strong>
                        </span>
                    @endif
                </div>

                {{-- idea of travelling --}}
                <div class="form-group">
                    <label for="traveling_id">Idea of Travelling</label>
                    <select type="text" class="form-control @error('traveling_id') is-invalid @enderror" id="traveling_id" name="traveling_id" spellcheck="false" tabindex="0" />
                        <option value="">Select Idea of Travelling</option>
                        @foreach($travelling as $travel)
                            @foreach($travel->subInterests as $sub)
                                @if(in_array($sub->id, $user_interest))
                                    <option value="{{ $sub->id }}" selected> {{ $sub->interestTransDefault->title }}</option>
                                @else
                                    <option value="{{ $sub->id }}"> {{ $sub->interestTransDefault->title }}</option>
                                @endif
                            @endforeach
                        @endforeach
                    </select>
                    @if ($errors->has('traveling_id'))
                        <span class="text-danger">
                            <strong class="form-text">{{ $errors->first('traveling_id') }}</strong>
                        </span>
                    @endif
                </div>

                {{-- Music --}}
                <div class="form-group">
                    <label for="music_id">Music</label>
                    <select type="text" class="form-control @error('music_id') is-invalid @enderror" id="music_id" name="music_id" spellcheck="false" tabindex="0" />
                        <option value="">Select Music</option>
                        @foreach($musics as $music)
                            @foreach($music->subInterests as $sub_music)
                                @if(in_array($sub_music->id, $user_interest))
                                    <option value="{{ $sub_music->id }}" selected> {{ $sub_music->interestTransDefault->title }}</option>
                                @else
                                    <option value="{{ $sub_music->id }}"> {{ $sub_music->interestTransDefault->title }}</option>
                                @endif
                            @endforeach
                        @endforeach
                    </select>
                    @if ($errors->has('music_id'))
                        <span class="text-danger">
                            <strong class="form-text">{{ $errors->first('interest_id') }}</strong>
                        </span>
                    @endif
                </div>

                {{-- Hobbies --}}
                <div class="form-group">
                    <label for="hobbie_id">Hobbies</label>
                    <select type="text" class="form-control @error('hobbie_id') is-invalid @enderror" id="hobbie_id" name="hobbie_id" spellcheck="false" tabindex="0" />
                        <option value="">Select Hobbies</option>
                        @foreach($hobbies as $hobbie)
                            @foreach($hobbie->subInterests as $sub_hobbie)
                                @if(in_array($sub_hobbie->id, $user_interest))
                                    <option value="{{ $sub_hobbie->id }}" selected> {{ $sub_hobbie->interestTransDefault->title }}</option>
                                @else
                                    <option value="{{ $sub_hobbie->id }}"> {{ $sub_hobbie->interestTransDefault->title }}</option>
                                @endif
                            @endforeach
                        @endforeach
                    </select>
                    @if ($errors->has('hobbie_id'))
                        <span class="text-danger">
                            <strong class="form-text">{{ $errors->first('hobbie_id') }}</strong>
                        </span>
                    @endif
                </div>

                {{-- Childhood Game --}}
                <div class="form-group">
                    <label for="game_id">Childhood Game</label>
                    <select type="text" class="form-control @error('game_id') is-invalid @enderror" id="game_id" name="game_id" spellcheck="false" tabindex="0" />
                        <option value="">Select Childhood Game</option>
                        @foreach($childhood as $game)
                            @foreach($game->subInterests as $sub_game)
                                @if(in_array($sub_game->id, $user_interest))
                                    <option value="{{ $sub_game->id }}" selected> {{ $sub_game->interestTransDefault->title }}</option>
                                @else
                                    <option value="{{ $sub_game->id }}"> {{ $sub_game->interestTransDefault->title }}</option>
                                @endif
                            @endforeach
                        @endforeach
                    </select>
                    @if ($errors->has('game_id'))
                        <span class="text-danger">
                            <strong class="form-text">{{ $errors->first('game_id') }}</strong>
                        </span>
                    @endif
                </div>

                {{-- Sports --}}
                <div class="form-group">
                    <label for="sport_id">Sports</label>
                    <select type="text" class="form-control @error('sport_id') is-invalid @enderror" id="sport_id" name="sport_id" spellcheck="false" tabindex="0" />
                        <option value="">Select Sports</option>
                        @foreach($sports as $sport)
                            @foreach($sport->subInterests as $sub_sport)
                                @if(in_array($sub_sport->id, $user_interest))
                                    <option value="{{ $sub_sport->id }}" selected> {{ $sub_sport->interestTransDefault->title }}</option>
                                @else
                                    <option value="{{ $sub_sport->id }}"> {{ $sub_sport->interestTransDefault->title }}</option>
                                @endif
                            @endforeach
                        @endforeach
                    </select>
                    @if ($errors->has('sport_id'))
                        <span class="text-danger">
                            <strong class="form-text">{{ $errors->first('sport_id') }}</strong>
                        </span>
                    @endif
                </div>

                {{-- film --}}
                <div class="form-group">
                    <label for="film_id">Film</label>
                    <select type="text" class="form-control @error('film_id') is-invalid @enderror" id="film_id" name="film_id" spellcheck="false" tabindex="0" />
                        <option value="">Select Films</option>
                        @foreach($films as $film)
                            @foreach($film->subInterests as $sub_film)
                                @if(in_array($sub_film->id, $user_interest))
                                    <option value="{{ $sub_film->id }}" selected> {{ $sub_film->interestTransDefault->title }}</option>
                                @else
                                    <option value="{{ $sub_film->id }}"> {{ $sub_film->interestTransDefault->title }}</option>
                                @endif
                            @endforeach
                        @endforeach
                    </select>
                    @if ($errors->has('film_id'))
                        <span class="text-danger">
                            <strong class="form-text">{{ $errors->first('film_id') }}</strong>
                        </span>
                    @endif
                </div>

                {{-- Fav Actors --}}
                <div class="form-group">
                    <label for="actor_id">Select Gender</label>
                    <select type="text" class="form-control @error('actor_id') is-invalid @enderror" id="actor_id" name="actor_id" spellcheck="false" tabindex="0" />
                        <option value="">Select Gender</option>
                        @foreach($actors as $actor)
                            @foreach($actor->subInterests as $sub_actor)
                                @if(in_array($sub_actor->id,$user_interest))
                                    <option value="{{ $sub_actor->id }}" selected> {{ $sub_actor->interestTransDefault->title }}</option>
                                @else
                                    <option value="{{ $sub_actor->id }}"> {{ $sub_actor->interestTransDefault->title }}</option>
                                @endif
                            @endforeach
                        @endforeach
                    </select>
                    @if ($errors->has('actor_id'))
                        <span class="text-danger">
                            <strong class="form-text">{{ $errors->first('actor_id') }}</strong>
                        </span>
                    @endif
                </div>

                {{-- Depend data --}}
                <div class="form-group">
                    <label for="depend_id">Favourite Actors/Actress</label>
                    <select type="text" class="form-control @error('depend_id') is-invalid @enderror" id="depend_id" name="depend_id" />
                    <option value="">Select Films</option>
                    @foreach($actors as $actor)
                        @foreach($actor->subInterests as $sub_actor)
                            @foreach($sub_actor->subInterests as $sub_gender)
                                @if(in_array($sub_gender->id,$user_interest))
                                    <option value="{{ $sub_gender->id }}" selected> {{ $sub_gender->interestTransDefault->title }}</option>
                                @else
                                    <option value="{{ $sub_gender->id }}"> {{ $sub_gender->interestTransDefault->title }}</option>
                                @endif
                            @endforeach
                        @endforeach
                    @endforeach
                    </select>
                    @if ($errors->has('depend_id'))
                        <span class="text-danger">
                            <strong class="form-text">{{ $errors->first('depend_id') }}</strong>
                        </span>
                    @endif
                </div>

                {{-- select Singer gender --}}
                <div class="form-group">
                    <label for="singer_male_id">Select Gender</label>
                    <select type="text" class="form-control @error('singer_male_id') is-invalid @enderror" id="singer_male_id" name="singer_male_id" spellcheck="false" tabindex="0" />
                        <option value="">Select Gender</option>
                        @foreach($singers as $singer)
                            @foreach($singer->subInterests as $sub_singer)
                                @if(in_array($sub_singer->id,$user_interest))
                                    <option {{ $sub_singer->id == $user_interest->interest->parent_id ? 'selected=selected' : '' }} value="{{ $sub_singer->id }}" selected> {{ $sub_singer->interestTransDefault->title }}</option>
                                @else
                                    <option value="{{ $sub_singer->id }}"> {{ $sub_singer->interestTransDefault->title }}</option>
                                @endif
                            @endforeach
                        @endforeach
                    </select>
                    @if ($errors->has('singer_male_id'))
                        <span class="text-danger">
                            <strong class="form-text">{{ $errors->first('singer_male_id') }}</strong>
                        </span>
                    @endif
                </div>

                {{-- Fav Singer (Male/Female) --}}
                <div class="form-group">
                    <label for="singer_id">Favourite Singer (Male/Female)</label>
                    <select type="text" class="form-control @error('singer_id') is-invalid @enderror" id="singer_id" name="singer_id" />
                    <option value="">Select Films</option>
                    @foreach($singers as $singer)
                        @foreach($singer->subInterests as $sub_singer)
                            @foreach($sub_singer->subInterests as $sub_gender)
                                @if(in_array($sub_gender->id,$user_interest))
                                    <option value="{{ $sub_gender->id }}" selected> {{ $sub_gender->interestTransDefault->title }}</option>
                                @else
                                    <option value="{{ $sub_gender->id }}"> {{ $sub_gender->interestTransDefault->title }}</option>
                                @endif
                            @endforeach
                        @endforeach
                    @endforeach
                    </select>
                    @if ($errors->has('singer_id'))
                        <span class="text-danger">
                            <strong class="form-text">{{ $errors->first('singer_id') }}</strong>
                        </span>
                    @endif
                </div>

                {{-- Fav Food --}}
                <div class="form-group">
                    <label for="food_id">Favourite Food</label>
                    <select type="text" class="form-control @error('food_id') is-invalid @enderror" id="food_id" name="food_id" spellcheck="false" tabindex="0" />
                        <option value="">Select Food</option>
                        @foreach($foods as $food)
                            @foreach($food->subInterests as $sub_food)
                                @if(in_array($sub_food->id,$user_interest))
                                    <option value="{{ $sub_food->id }}" selected> {{ $sub_food->interestTransDefault->title }}</option>
                                @else
                                    <option value="{{ $sub_food->id }}"> {{ $sub_food->interestTransDefault->title }}</option>
                                @endif
                            @endforeach
                        @endforeach
                    </select>
                    @if ($errors->has('food_id'))
                        <span class="text-danger">
                            <strong class="form-text">{{ $errors->first('food_id') }}</strong>
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
                <h3 class="card-label text-uppercase">Verification Details</h3>
            </div>
        </div>
            <div class="card-body">
                {{-- Verification Photo --}}
                <div class="form-group row col-md-12">
                    <div class="form-group col-md-6">
                        <div class="row">
                            <div class="col-md-6">
                                <label class="control-label"><span class="mendatory" style="font-size: 20px;"></span>
                                    <h4>Verification Photo</h4>
                                </label>
                                <div class="symbol symbol-120 mr-5">
                                    @if (generateURL($user->verify_photo))
                                        <a href="{{ generateURL($user->verify_photo) }}" target="_blank">
                                            <div class="symbol-label" style="width: 500px; height: 350px; background-image:url({{ generateURL($user->verify_photo)}})">
                                            </div>
                                        </a>
                                    @else
                                        <label>Photo Not Available.</label>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group col-md-6">
                        <div class="row">
                            <div class="col-md-12">
                                <label class="control-label"><span class="mendatory" style="font-size: 20px;"></span>
                                    <h4>Verification Video</h4>
                                </label>
                                <div class="mb-2">

                                    @if (generateURL($user->verify_video))
                                        <video width="420" height="340" controls>
                                            <source src="{{ generateURL($user->verify_video) }}" type="video/mp4">
                                            <source src="{{ generateURL($user->verify_video) }}" type="video/ogg">
                                            Your browser does not support the video tag.
                                        </video>
                                        <br><br>
                                    @else
                                        <label>Video Not Available.</label>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group col-md-6">
                    <div class="row">
                        {{-- Checkbox Selection For photos --}}
                        <div class="col-md-3 custom-checkbox">
                            @if($user->photo_verified_at)
                                <input type="checkbox" class="form-control @error('photo_verified_at') is-invalid @enderror" name="photo_verified_at" id="categpry" value="y" checked>
                            @else
                                <input type="checkbox" class="form-control @error('photo_verified_at') is-invalid @enderror" name="photo_verified_at" id="categpry" value="y">
                            @endif
                            <label for="photo_verified_at">Photos</label>
                        </div>

                        {{-- Checkbox Selection For Videos --}}
                        <div class="col-md-3 custom-checkbox">
                            @if($user->video_verified_at)
                                <input type="checkbox" class="form-control @error('video_verified_at') is-invalid @enderror" name="video_verified_at" id="categpry" value="y" checked>
                            @else
                                <input type="checkbox" class="form-control @error('video_verified_at') is-invalid @enderror" name="video_verified_at" id="categpry" value="y">
                            @endif
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

                {{-- Photo Suggestion --}}
                <div class="form-group">
                    <label for="photo_suggestion">Photo Suggestion:</label>
                    <textarea type="text" class="form-control @error('photo_suggestion') is-invalid @enderror" id="photo_suggestion" name="photo_suggestion" placeholder="Enter Photo Suggestion" autocomplete="photo_suggestion" spellcheck="false" autocapitalize="sentences" tabindex="0" autofocus>{{ old('photo_suggestion') != null ? old('photo_suggestion') : $user->photo_suggestion }}</textarea>
                    @if ($errors->has('photo_suggestion'))
                        <span class="help-block">
                            <strong class="form-text">{{ $errors->first('photo_suggestion') }}</strong>
                        </span>
                    @endif
                </div>

                {{-- Video Suggestion --}}
                <div class="form-group">
                    <label for="video_suggestion">Video Suggestion:</label>
                    <textarea type="text" class="form-control @error('video_suggestion') is-invalid @enderror" id="video_suggestion" name="video_suggestion" placeholder="Enter Video Suggestion" autocomplete="video_suggestion" spellcheck="false" autocapitalize="sentences" tabindex="0" autofocus>{{ old('video_suggestion') != null ? old('video_suggestion') : $user->video_suggestion }}</textarea>
                    @if ($errors->has('video_suggestion'))
                        <span class="help-block">
                            <strong class="form-text">{{ $errors->first('video_suggestion') }}</strong>
                        </span>
                    @endif
                </div>

                {{-- Photo Verification Status --}}
                <div class="form-group">
                    <label for="verify_photo_status">{!!$mend_sign!!} Photo Verification Status:</label>
                    <select type="text"class="form-control" 
                    id="verify_photo_status" name="verify_photo_status" value="@if(old('verify_photo_status')){{ old('verify_photo_status') }}@else{{ $user->verify_photo_status }}@endif"
                    placeholder="Select Verification Status" spellcheck="false" autocapitalize="sentences" tabindex="0" autofocus />
                        @if($user->verify_photo_status == 'under_review')
                            <option value="under_review" selected>Under Review</option>
                            <option value="verified">Verified</option>
                            <option value="unverified">UnVerified</option>
                        @elseif($user->verify_photo_status == 'verified')
                            <option value="under_review">Under Review</option>
                            <option value="verified" selected>Verified</option>
                            <option value="unverified">UnVerified</option>
                        @elseif($user->verify_photo_status == 'unverified')
                            <option value="under_review">Under Review</option>
                            <option value="verified">Verified</option>
                            <option value="unverified" selected>UnVerified</option>
                        @else
                            <option value="" selected>Select Verification Status</option>
                            <option value="under_review" selected>Under Review</option>
                            <option value="verified">Verified</option>
                            <option value="unverified">UnVerified</option>
                        @endif
                    </select>
                    @if ($errors->has('verify_photo_status'))
                        <span class="help-block">
                            <strong class="form-text">{{ $errors->first('verify_photo_status') }}</strong>
                        </span>
                    @endif
                </div>

                {{-- Video Verification Status --}}
                <div class="form-group">
                    <label for="verify_video_status">{!!$mend_sign!!} Video Verification Status:</label>
                    <select type="text"class="form-control" 
                    id="verify_video_status" name="verify_video_status" value="@if(old('verify_video_status')){{ old('verify_video_status') }}@else{{ $user->verify_video_status }}@endif"
                    placeholder="Select Verification Status" spellcheck="false" autocapitalize="sentences" tabindex="0" autofocus />
                        @if($user->verify_video_status == 'under_review')
                            <option value="under_review" selected>Under Review</option>
                            <option value="verified">Verified</option>
                            <option value="unverified">UnVerified</option>
                        @elseif($user->verify_video_status == 'verified')
                            <option value="under_review">Under Review</option>
                            <option value="verified" selected>Verified</option>
                            <option value="unverified">UnVerified</option>
                        @elseif($user->verify_video_status == 'unverified')
                            <option value="under_review">Under Review</option>
                            <option value="verified">Verified</option>
                            <option value="unverified" selected>UnVerified</option>
                        @else
                            <option value="" selected>Select Verification Status</option>
                            <option value="under_review" selected>Under Review</option>
                            <option value="verified">Verified</option>
                            <option value="unverified">UnVerified</option>
                        @endif
                    </select>
                    @if ($errors->has('verify_video_status'))
                        <span class="help-block">
                            <strong class="form-text">{{ $errors->first('verify_video_status') }}</strong>
                        </span>
                    @endif
                </div>

                {{-- Profile Verification Status --}}
                <div class="form-group">
                    <label for="verify_status">{!!$mend_sign!!} Profile Verification Status:</label>
                    <select type="text"class="form-control" 
                    id="verify_status" name="verify_status" value="@if(old('verify_status')){{ old('verify_status') }}@else{{ $user->verify_status }}@endif"
                    placeholder="Select Verification Status" spellcheck="false" autocapitalize="sentences" tabindex="0" autofocus />
                        @if($user->verify_status == 'under_review')
                            <option value="under_review" selected>Under Review</option>
                            <option value="verified">Verified</option>
                            <option value="unverified">UnVerified</option>
                        @elseif($user->verify_status == 'verified')
                            <option value="under_review">Under Review</option>
                            <option value="verified" selected>Verified</option>
                            <option value="unverified">UnVerified</option>
                        @elseif($user->verify_status == 'unverified')
                            <option value="under_review">Under Review</option>
                            <option value="verified">Verified</option>
                            <option value="unverified" selected>UnVerified</option>
                        @else
                            <option value="" selected>Select Verification Status</option>
                            <option value="under_review" selected>Under Review</option>
                            <option value="verified">Verified</option>
                            <option value="unverified">UnVerified</option>
                        @endif
                    </select>
                    @if ($errors->has('verify_status'))
                        <span class="help-block">
                            <strong class="form-text">{{ $errors->first('verify_status') }}</strong>
                        </span>
                    @endif
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary mr-2">Update {{ $custom_title }}</button>
                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
        <!--end::Form-->
    </div>
</div>
@endsection

@push('extra-js-scripts')
<script>
$(document).ready(function () {
    $("#frmEditUser").validate({
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
            },
            country_code: {
                required: true,
                not_empty: true,
            },
            contact_no: {
                required: false,
                not_empty: true,
                maxlength: 16,
                minlength: 6,
                pattern: /^(\d+)(?: ?\d+)*$/,
            },
            birth_date: {
                required: false,
                not_empty: false,
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
                required: false,
                not_empty: true,
            },
            profession_id:{
                required: false,
                not_empty: true,
            },
            education_id: {
                required:false,
                not_empty: true,
            },
            university_id: {
                required: false,
                not_empty: true,
            },
            pet_id: {
                required: false,
                not_empty: true,
            },
            religion_id: {
                required: false,
                not_empty: true,
            },
            community_id: {
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
            drinking_id: {
                required: false,
                not_empty: true,
            },
            smoking_id: {
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
            photo_suggestion: {
                required: false,
                not_empty: false,
                minlength: 3,
                maxlength: 150,
            },
            video_suggestion: {
                required: false,
                not_empty: false,
                minlength: 3,
                maxlength: 150,
            },
            verify_photo_status: {
                required: true,
                not_empty: true,
            },
            verify_video_status: {
                required: true,
                not_empty: true,
            },
            verify_status: {
                required: true,
                not_empty: true,
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
            film_id: {
                required: false,
                not_empty: true,
            },
            food_id: {
                required: false,
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
            photo_suggestion: {
                required: "@lang('validation.required',['attribute'=>'photo suggestion'])",
                not_empty: "@lang('validation.not_empty',['attribute'=>'photo suggestion'])",
                minlength:"@lang('validation.min.string',['attribute'=>'photo suggestion','min'=>3])",
                maxlength:"@lang('validation.max.string',['attribute'=>'photo suggestion','min'=>150])",
            },
            video_suggestion: {
                required: "@lang('validation.required',['attribute'=>'video suggestion'])",
                not_empty: "@lang('validation.not_empty',['attribute'=>'video suggestion'])",
                minlength:"@lang('validation.min.string',['attribute'=>'video suggestion','min'=>3])",
                maxlength:"@lang('validation.max.string',['attribute'=>'video suggestion','min'=>150])",
            },
            verify_photo_status: {
                required: "@lang('validation.required',['attribute'=>'photo verification status'])",
                not_empty: "@lang('validation.not_empty',['attribute'=>'photo verification status'])",
            },
            verify_video_status: {
                required: "@lang('validation.required',['attribute'=>'video verification status'])",
                not_empty: "@lang('validation.not_empty',['attribute'=>'video verification status'])",
            },
            verify_status: {
                required: "@lang('validation.required',['attribute'=>'profile verification status'])",
                not_empty: "@lang('validation.not_empty',['attribute'=>'profile verification status'])",
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
            film_id: {
                required: "@lang('validation.required',['attribute'=>'film'])",
                not_empty: "@lang('validation.not_empty',['attribute'=>'film'])",
            },
            food_id: {
                required: "@lang('validation.required',['attribute'=>'food'])",
                not_empty: "@lang('validation.not_empty',['attribute'=>'food'])",
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
    $('#frmEditUser').submit(function () {
        if ($(this).valid()) {
            addOverlay();
            $("input[type=submit], input[type=button], button[type=submit]").prop("disabled", "disabled");
            return true;
        } else {
            return false;
        }
    });

    //remove the imaegs
    $(".remove-img").on('click',function(e){
        e.preventDefault();
        $(this).parents(".symbol").remove();
        $('#frmEditUser').append('<input type="hidden" name="remove_profie_photo" id="remove_image" value="removed">');
    });
});
</script>
@endpush
