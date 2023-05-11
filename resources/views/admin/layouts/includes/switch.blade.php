@if (in_array('edit', $permissions))
    @if ( Route::is($routeName.'.listing'))
        {{--  For Active Users  --}}
        <div class="d-flex align-item-center w-60px">
            <span class="switch switch-outline switch-icon switch-success switch-sm">
                <label>
                    @if(!empty($params['id']))
                    <input type="checkbox" class="toggleSwitch" {{ $params["checked"] }} data-id="{{ $params['id'] }}" data-url="{{ route($routeName.'.update', $params['id']) }}"
                    @if(!empty($params['custom_action'])) data-getaction="{{ $params['custom_action'] }}" @endif/>
                    <span></span>
                    @endif
                </label>
            </span>
        </div>
	@endif
@endif
