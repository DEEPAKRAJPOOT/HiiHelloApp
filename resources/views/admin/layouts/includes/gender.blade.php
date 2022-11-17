@if (in_array('edit', $permissions))
    @if ( Route::is($routeName.'.listing'))
        {{--  For Active Users  --}}
        <div class="d-flex align-item-center w-100px">
            <select class="table_gender form-control" data-id="{{ $params['user_id'] }}" data-url="{{ route('admin.users.genderupdate') }}">
                <option value="Male" {{ $params["male_user"] }}>Male</option>
                <option value="Female" {{ $params["female_user"] }}>Female</option>
                <option value="" {{ $params["na_user"] }}>N/A</option>
            </select>
        </div>
	@endif
@endif
