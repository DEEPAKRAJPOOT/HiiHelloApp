@if($is_profile_photo == 1)
	@if($profile_photo == "N/A")
		<div class="symbol symbol-32" style="padding: 5; vertical-align: middle;"><div class="symbol-label" style="width: 50px; height: 50px;"></div></div>
	@else
		<img src="{{ generateURL($profile_photo)}}" id="1photo_{{$user_id}}" data-id ={{$user_id}} height="50" width="50" class="my_profile_image" style=" vertical-align: middle; cursor: pointer;" />
	@endif
@endif	


@if($is_verify_photo == 1)

	@if($verify_photo == "N/A")
		<div class="symbol symbol-32" style="padding: 5; vertical-align: middle;"><div class="symbol-label" style="width: 50px; height: 50px;"></div></div>
	@else
		<img src="{{ generateURL($verify_photo)}}" id="2photo_{{$user_id}}" data-id ={{$user_id}} height="50" width="50" class="my_profile_image" style=" vertical-align: middle; cursor: pointer;" />
	@endif
@endif
