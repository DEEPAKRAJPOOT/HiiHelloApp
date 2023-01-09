@if (in_array('edit', $permissions))
    @if ( Route::is($routeName.'.listing'))
        {{--  For Active Users  --}}
        <div class="d-flex align-item-center w-65px">
            <select class="table_gender form-control dynamic_gender_{{ $params['user_id'] }}" data-id="{{ $params['user_id'] }}" data-url="{{ route('admin.users.genderupdate') }}">
                <option value="" >Select</option>}
                <option value="Male" {{ $params["male_user"] }}>M</option>
                <option value="Female" {{ $params["female_user"] }}>F</option>                
            </select>
        </div>
	@endif
@endif
