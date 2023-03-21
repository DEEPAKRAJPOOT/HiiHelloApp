@if(!empty($image))
	<img src="{{ generateURL($image) }}" height="50" width="50" class="my_profile_image" style="vertical-align:middle">
@else
	<div class="symbol symbol-32" style="padding:5px;vertical-align:middle">
		<div class="symbol-label" style="width:50px;height:50px"></div>
	</div>
@endif