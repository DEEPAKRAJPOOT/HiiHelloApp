@if (in_array('delete', $permissions))
	<center><input type="checkbox" class="small-chk" data-id="@if(!empty($params['user_id'])){{ $params['user_id'] }}@endif" value="{{$params['id']}}"></center>
@endif