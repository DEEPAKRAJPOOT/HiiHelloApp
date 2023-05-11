<div class="card-body">
	{{-- College Name --}}
	<div class="form-group">
		<label for="college_name">College Name</label>
		<input type="text" class="form-control" id="college_name" name="college_name" value="{{ old('college_name',$college->name ?? '') }}" placeholder="Enter College Name" autocomplete="college" spellcheck="false" autocapitalize="sentences" tabindex="0" />
		@if($errors->has('college_name'))
		<span class="help-block">
			<strong class="form-text">{{ $errors->first('college_name') }}</strong>
		</span>
		@endif
	</div>
	
	{{-- University Name --}}
	<div class="form-group">
		<label for="university_name">University Name</label>
		<input type="text" class="form-control" id="university_name" name="university_name" value="{{ old('university_name', $college->university ?? '') }}" placeholder="Enter University Name" autocomplete="university" spellcheck="false" autocapitalize="sentences" tabindex="0" />
		@if($errors->has('university_name'))
		<span class="help-block">
			<strong class="form-text">{{ $errors->first('university_name') }}</strong>
		</span>
		@endif
	</div>
	
	{{-- College Abbreviation --}}
	<div class="form-group">
		<label for="abbreviation">College Abbreviation</label>
		<input type="text" class="form-control" id="abbreviation" name="abbreviation" value="{{ old('abbreviation', $college->abbreviation ?? '') }}" placeholder="Enter College Abbreviation (example - IIT)" autocomplete="abbreviation" spellcheck="false" autocapitalize="sentences" tabindex="0">
		@if($errors->has('abbreviation'))
		<span class="help-block">
			<strong class="form-text">{{ $errors->first('abbreviation') }}</strong>
		</span>
		@endif
	</div>
	
	{{-- District --}}
	<div class="form-group">
		<label for="district_name">City Name</label>
		<input type="text" class="form-control" id="district_name" name="district_name" value="{{ old('district_name',$college->district ?? '') }}" placeholder="Enter District Name" autocomplete="district" spellcheck="false" autocapitalize="sentences" tabindex="0" />
		@if($errors->has('district_name'))
		<span class="help-block">
			<strong class="form-text">{{ $errors->first('district_name') }}</strong>
		</span>
		@endif
	</div>
	
	{{-- State --}}
	<div class="form-group">
		<label for="state_name">State Name</label>
		<input type="text" class="form-control" id="state_name" name="state_name" value="{{ old('state_name',$college->state ?? '') }}" placeholder="Enter State Name" autocomplete="state" spellcheck="false" autocapitalize="sentences" list="states" tabindex="0" />
		@if($errors->has('state_name'))
		<span class="help-block">
			<strong class="form-text">{{ $errors->first('state_name') }}</strong>
		</span>
		@endif
	</div>
</div>
<datalist id="states">
@foreach(($states ?? []) as $state)
<option value="{{ $state }}" />
@endforeach
</datalist>
@push('extra-js-scripts')
<script>
	$(document).ready(function () {
		$("#formSaveCollege").validate({
			rules: {
				'college_name': {
					'required': true,
					'not_empty': true
				},
				'university_name': {
					'required': true,
					'not_empty': true
				},
				'district_name': {
					'required': true,
					'not_empty': true
				},
				'state_name': {
					'required': true,
					'not_empty': true
				},
			},
			'messages': {
				'college_name': {
					'required': "@lang('validation.required',['attribute'=>'College name'])",
					'not_empty': "@lang('validation.not_empty',['attribute'=>'College name'])",
					'minlength':"@lang('validation.min.string',['attribute'=>'College name','min'=>3])",
					'maxlength':"@lang('validation.max.string',['attribute'=>'College name','max'=>150])",
				},
				'university_name': {
					'required': "@lang('validation.required',['attribute'=>'University name'])",
					'not_empty': "@lang('validation.not_empty',['attribute'=>'University name'])",
					'minlength':"@lang('validation.min.string',['attribute'=>'University name','min'=>3])",
					'maxlength':"@lang('validation.max.string',['attribute'=>'University name','max'=>150])",
				},
				'district_name': {
					'required': "@lang('validation.required',['attribute'=>'District'])",
					'not_empty': "@lang('validation.not_empty',['attribute'=>'District'])",
					'minlength':"@lang('validation.min.string',['attribute'=>'District','min'=>3])",
					'maxlength':"@lang('validation.max.string',['attribute'=>'District','max'=>150])",
				},
				'state_name': {
					'required': "@lang('validation.required',['attribute'=>'State'])",
					'not_empty': "@lang('validation.not_empty',['attribute'=>'State'])",
					'minlength':"@lang('validation.min.string',['attribute'=>'State','min'=>3])",
					'maxlength':"@lang('validation.max.string',['attribute'=>'State','max'=>150])",
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
		$('#formSaveCollege').submit(function () {
			if($(this).valid()){
				addOverlay();
				$('#formSaveCollege').find('input[type=submit],input[type=button],button[type=submit]').prop('disabled',true);
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
