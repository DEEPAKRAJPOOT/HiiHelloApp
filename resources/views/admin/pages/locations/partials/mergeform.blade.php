<div class="card-body">
	{{-- College Name --}}
	<div class="form-group">
		<label for="college_name">Select Location From</label>
        <select class="form-control selectpicker" multiple aria-label="Default select example" data-live-search="true" name="from_location[]" id="from_location">
        @if(!$locations->isEmpty())
                @foreach($locations as $location)
                    @if($location->locationTransDefault)
                        <option value="{{ $location->id }}">{{ $location->locationTransDefault->name }} | @if($location->locationTransDefault->state){{$location->locationTransDefault->state}} @else No State @endif</option>
                    @endif
                @endforeach
            @endif
        </select>
        <span id="from_location[]-error"></span>
        
		@if($errors->has('from_location'))
		<span class="help-block">
			<strong class="form-text">{{ $errors->first('from_location') }}</strong>
		</span>
		@endif
	</div>
	
	{{-- University Name --}}
	<div class="form-group {{ $errors->has('location_id') ? 'has-error' : '' }}">
        <label for="location_id">Select To Location</label>
        <select class="form-control selectpicker" id="location_id" name="location_id"  data-live-search="true" aria-label="Default select example" data-error-container="#location-id-error">
            <option value="" selected>Select Location</option>
            @if(!$locations->isEmpty())
                @foreach($locations as $location)
                    @if($location->locationTransDefault)
                        <option value="{{ $location->id }}" selected>{{ $location->locationTransDefault->name }} | @if($location->locationTransDefault->state){{$location->locationTransDefault->state}} @else No State @endif</option>
                    @endif
                @endforeach
            @endif
        </select>
        <span id="location-id-error"></span>
        @if($errors->has('location_id'))
            <span class="help-block">
                <strong class="form-text"> {{ $errors->first('location_id') }}</strong>
            </span>
        @endif
   </div>
</div>

@push('extra-js-scripts')
<script src="{{ asset('admin/plugins/select2/js/select2.full.js') }}" type="text/javascript"></script>
<script>
	$(document).ready(function () {
		$("#formMergeCollege").validate({
			rules: {
				'from_location[]': {
					'required': true,
					'not_empty': true
				},
				'location_id': {
					'required': true,
					'not_empty': true
				}
			},
			'messages': {
				'from_location[]': {
					'required': "@lang('validation.required',['attribute'=>'From Location'])",
					'not_empty': "@lang('validation.not_empty',['attribute'=>'From Location'])",
				},
				'location_id': {
					'required': "@lang('validation.required',['attribute'=>'To Location'])",
					'not_empty': "@lang('validation.not_empty',['attribute'=>'To Location'])",
				},
				
			},
			'errorClass': 'invalid-feedback',
			'errorElement': 'span',
			'highlight':function (element){
				$(element).addClass('is-invalid');
				$(element).siblings('label').addClass('text-danger');
			},
			'unhighlight': function (element) {
				$(element).removeClass('is-invalid');
				$(element).siblings('label').removeClass('text-danger');
			},
			'errorPlacement': function (error, element) {
				if (element.attr("data-error-container")) {
					error.appendTo(element.attr("data-error-container"));
				} else {
					error.insertAfter(element);
				}
			}
		});
		$('#formMergeCollege').submit(function () {
			if($(this).valid()){
				addOverlay();
				$('#formMergeCollege').find('input[type=submit],input[type=button],button[type=submit]').prop('disabled',true);
				return true;
			}else{
				return false;
			}
		});
		$('#abbreviation').on('input',function(){
			$(this).val($(this).val().replace(/[^a-z]/gi,''));
		})
	});
</script>
@endpush
