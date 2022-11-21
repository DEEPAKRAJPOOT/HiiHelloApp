@if (in_array('delete', $permissions))
	<center><input type="checkbox" class="small-chk" data-id="{{ $params['user_id'] }}" value="{{$id}}"></center>
@endif