<div class="card-body">

	{{-- Vendor --}}
	<div class="form-group">
		<label for="vendor_id">Coupon Vendor:</label>
		<div class="row">
			<div class="col-md-6">
				<select id="vendor_id" name="vendor_id" class="form-control" tabindex="0" autofocus>
					<option value="">-- Select Coupon Vendor --</option>
					@forelse($coupon_vendors as $coupon_vendor)
					<option value="{{ $coupon_vendor->id }}" {!! (old('vendor_id', $coupon->vendor_id ?? '') == $coupon_vendor->id) ? 'selected="selected"' : '' !!}>{{ $coupon_vendor->name }}</option>
					@endforeach
				</select>
			</div>
			<div class="col-md-2">
				<a href="javascript:void(0)" class="btn btn-sm btn-primary font-weight-bolder text-uppercase" data-toggle="modal" data-target="#addCouponVendorModal">
					<i class="fas fa-plus"></i>
					Add Coupon Vendor
				</a>
			</div>
		</div>
		@if($errors->has('vendor_id'))
		<span class="help-block">
			<strong class="form-text">{{ $errors->first('vendor_id') }}</strong>
		</span>
		@endif
	</div>

	{{-- Plan --}}
	<div class="form-group">
		<label for="plan_id">Subscription Plan:</label>
		<select id="plan_id" name="plan_id" class="form-control" tabindex="0" autofocus>
			<option value="">-- Select Subscription Plan --</option>
			@forelse($subscription_plans as $subscription_plan)
			<option value="{{ $subscription_plan->id }}" {!! (old('plan_id', $coupon->plan_id ?? '') == $subscription_plan->id) ? 'selected="selected"' : '' !!}>{{ $subscription_plan->name }}</option>
			@endforeach
		</select>
		@if($errors->has('plan_id'))
		<span class="help-block">
			<strong class="form-text">{{ $errors->first('plan_id') }}</strong>
		</span>
		@endif
	</div>
	
	{{-- Title --}}
	<div class="form-group">
		<label for="title">Coupon Title:</label>
		<input type="text" class="form-control" id="title" name="title" value="{{ old('title', $coupon->title ?? '') }}" placeholder="Enter coupon title" autocomplete="title" spellcheck="false" autocapitalize="sentences" tabindex="0" />
		@if ($errors->has('title'))
		<span class="help-block">
			<strong class="form-text">{{ $errors->first('title') }}</strong>
		</span>
		@endif
	</div>
	
	{{-- Coupon Code --}}
	<div class="form-group">
		<label for="name">Coupon Code:</label>
		<input type="text" class="form-control" id="coupon" name="coupon" value="{{ old('coupon', $coupon->coupon ?? '') }}" placeholder="Enter coupon code" autocomplete="coupon" spellcheck="false" autocapitalize="sentences" tabindex="0" />
		@if ($errors->has('coupon'))
		<span class="help-block">
			<strong class="form-text">{{ $errors->first('coupon') }}</strong>
		</span>
		@endif
	</div>
	
	{{-- Expires At --}}
	<div class="form-group">
		<label for="name">Coupon Expires At:</label>
		<input type="date" class="form-control @error('expired_at') is-invalid @enderror" id="expired_at" name="expired_at" value="{{ !empty(old('expired_at',$coupon->expired_at ?? '')) ? now()->create(old('expired_at', $coupon->expired_at))->format('Y-m-d') : '' }}" min="{{ now()->format('Y-m-d') }}" placeholder="Select expiry date" autocomplete="expired_at" spellcheck="false" tabindex="0" />
		@if ($errors->has('expired_at'))
		<span class="help-block">
			<strong class="form-text">{{ $errors->first('expired_at') }}</strong>
		</span>
		@endif
	</div>

	{{-- Coupon Image --}}
	<div class="form-group">
		<label for="coupon_image">Coupon Image</label>
		<div class="custom-file">
			<input type="file" class="custom-file-input" id="coupon_image" name="coupon_image" tabindex="0">
			<label class="custom-file-label @error('coupon_image') is-invalid @enderror" for="customFile">Choose File</label>
			@if($errors->has('coupon_image'))
			<span class="text-danger">
				<strong class="form-text">{{ $errors->first('coupon_image') }}</strong>
			</span>
			@endif
		</div>
	</div>
	@if(!empty($coupon->image))
	<div class="symbol symbol-120 mr-5">
		<div class="symbol-label" style="background-image:url('{{ generateURL($coupon->image) }}')">
			<a href="javascript:void(0)" class="btn btn-icon btn-light btn-hover-danger remove-img" style="width:18px;height:18px">
				<i class="ki ki-close icon-xs text-muted"></i>
			</a>
		</div>
	</div>
	@endif
	
	{{-- Description --}}
	<div class="form-group">
		<label for="name">Coupon Description:</label>
		<textarea id="description" rows="3" class="form-control" name="description" placeholder="Enter description" autocomplete="description" spellcheck="false" autocapitalize="sentences" tabindex="0" maxlength="191">{{ old('description', $coupon->description ?? '') }}</textarea>
		@if ($errors->has('description'))
		<span class="help-block">
			<strong class="form-text">{{ $errors->first('description') }}</strong>
		</span>
		@endif
	</div>

	{{-- Type --}}
	<div class="form-group" {{ $errors->has('type') ? 'has-error' : '' }}>
		<label for="type">{!!$mend_sign!!}Coupon Type:</label>
		<div class="row">
			<div class="col-auto">
				<input type="radio" id="percentage" name="type" value="percentage" data-error-container="#error-authorizable" {!! (old('type', $coupon->type ?? '') == 'percentage') ? 'checked="checked"' : '' !!}>
				<label for="percentage">Percentage</label><br> 
			</div>
			<div class="col-auto pl-5">
				<input type="radio" id="full" name="type" value="full" data-error-container="#error-authorizable" {!! (old('type', $coupon->type ?? 'full') == 'full') ? 'checked="checked"' : '' !!}>
				<label for="full">Full</label><br>
			</div>
		</div>
		<span id="error-authorizable"></span>
		@if ($errors->has('type'))
		<span class="help-block">
			<strong class="form-text">{{ $errors->first('type') }}</strong>
		</span>
		@endif
	</div>
	
	{{-- Coupon Value --}}
	<div id="coupon_value_section" @class([
		'form-group'=>true,
		'd-none'=>old('type', $coupon->type ?? '') != 'percentage'
	])>
	<label for="name">Coupon Value:</label>
	<input type="text" class="form-control" id="value" name="value" value="{{ old('value',$coupon->value ?? '') }}" placeholder="Enter coupon value" autocomplete="value" spellcheck="false" autocapitalize="sentences" tabindex="0" />
	@if ($errors->has('value'))
	<span class="help-block">
		<strong class="form-text">{{ $errors->first('value') }}</strong>
	</span>
	@endif
</div>

{{-- Universal --}}
<div class="form-group" {{ $errors->has('is_universal') ? 'has-error' : '' }}>
	<label for="is_universal">{!!$mend_sign!!}Universal:</label>
	<div class="row">
		<div class="col-md-1">
			<input type="radio" id="u_yes" name="is_universal" value="y" data-error-container="#error-authorizable" {!! (old('is_universal', $coupon->is_universal ?? '') == 'y') ? 'checked="checked"' : '' !!}>
			<label for="u_yes">Yes</label><br> 
		</div>
		<div class="col-md-1">
			<input type="radio" id="u_no" name="is_universal" value="n" data-error-container="#error-authorizable" {!! (old('is_universal', $coupon->is_universal ?? 'n') == 'n') ? 'checked="checked"' : '' !!}>
			<label for="u_no">No</label><br>
		</div>
	</div>
	<span id="error-authorizable"></span>
	@if ($errors->has('is_universal'))
	<span class="help-block">
		<strong class="form-text">{{ $errors->first('is_universal') }}</strong>
	</span>
	@endif
</div>

{{-- Re-Usable --}}
<div class="form-group" {{ $errors->has('is_reusable') ? 'has-error' : '' }}>
	<label for="is_reusable">{!!$mend_sign!!}Reusable:</label>
	<div class="row">
		<div class="col-md-1">
			<input type="radio" id="r_yes" name="is_reusable" value="y" data-error-container="#error-authorizable" {!! (old('is_reusable', $coupon->is_reusable ?? '') == 'y') ? 'checked="checked"' : '' !!}>
			<label for="r_yes">Yes</label><br> 
		</div>
		<div class="col-md-1">
			<input type="radio" id="r_no" name="is_reusable" value="n" data-error-container="#error-authorizable" {!! (old('is_reusable', $coupon->is_reusable ?? 'n') == 'n') ? 'checked="checked"' : '' !!}>
			<label for="r_no">No</label><br>
		</div>
	</div>
	<span id="error-authorizable"></span>
	@if ($errors->has('is_reusable'))
	<span class="help-block">
		<strong class="form-text">{{ $errors->first('is_reusable') }}</strong>
	</span>
	@endif
</div>

{{-- Self Hosted --}}
<div class="form-group" {{ $errors->has('is_self_hosted') ? 'has-error' : '' }}>
	<label for="is_self_hosted">{!!$mend_sign!!}Self Hosted:</label>
	<div class="row">
		<div class="col-md-1">
			<input type="radio" id="sh_yes" name="is_self_hosted" value="y" data-error-container="#error-authorizable" {!! (old('is_self_hosted', $coupon->is_self_hosted ?? 'y') == 'y') ? 'checked="checked"' : '' !!}>
			<label for="sh_yes">Yes</label><br> 
		</div>
		<div class="col-md-1">
			<input type="radio" id="sh_no" name="is_self_hosted" value="n" data-error-container="#error-authorizable" {!! (old('is_self_hosted', $coupon->is_self_hosted ?? '') == 'n') ? 'checked="checked"' : '' !!}>
			<label for="sh_no">No</label><br>
		</div>
	</div>
	<span id="error-authorizable"></span>
	@if ($errors->has('is_self_hosted'))
	<span class="help-block">
		<strong class="form-text">{{ $errors->first('is_self_hosted') }}</strong>
	</span>
	@endif
</div>

{{-- Active --}}
<div class="form-group" {{ $errors->has('is_active') ? 'has-error' : '' }}>
	<label for="is_active">{!!$mend_sign!!}Active:</label>
	<div class="row">
		<div class="col-md-1">
			<input type="radio" id="yes" name="is_active" value="y" data-error-container="#error-authorizable" {!! (old('is_active', $coupon->is_active ?? 'y') == 'y') ? 'checked="checked"' : '' !!}>
			<label for="Yes">Yes</label><br> 
		</div>
		<div class="col-md-1">
			<input type="radio" id="no" name="is_active" value="n" data-error-container="#error-authorizable" {!! (old('is_active', $coupon->is_active ?? '') == 'n') ? 'checked="checked"' : '' !!}>
			<label for="No">No</label><br>
		</div>
	</div>
	<span id="error-authorizable"></span>
	@if ($errors->has('is_active'))
	<span class="help-block">
		<strong class="form-text">{{ $errors->first('is_active') }}</strong>
	</span>
	@endif
</div>

</div>
@push('extra-js-scripts')
<script>
	$(document).ready(function () {
		$("#formSaveCoupon").validate({
			rules: {
				'plan_id': {
					'required': false,
					'not_empty': false
				},
				'vendor_id': {
					'required': false,
					'not_empty': false
				},
				'title': {
					'required': true,
					'not_empty': true,
					'minlength': 3,
					'maxlength': 191,
				},
				'coupon': {
					'required': true,
					'not_empty': true,
					'minlength': 3,
					'maxlength': 191,
				},
				'description': {
					'required': false,
					'not_empty': false,
					'maxlength': 191,
				},
				'type': {
					'required': true,
					'not_empty': true,
				},
				'value': {
					'required': false,
					'not_empty': false
				},
				'expired_at': {
					'required': true,
					'not_empty': true,
				},
				'is_universal': {
					'required': true,
					'not_empty': true,
				},
				'is_reusable': {
					'required': true,
					'not_empty': true,
				},
				'is_self_hosted': {
					'required': true,
					'not_empty': true,
				},
				'is_active': {
					'required': true,
					'not_empty': true,
				},
			},
			'messages': {
				'name': {
					'required': "@lang('validation.required',['attribute'=>'name'])",
					'not_empty': "@lang('validation.not_empty',['attribute'=>'name'])",
					'minlength':"@lang('validation.min.string',['attribute'=>'name','min'=>3])",
					'maxlength':"@lang('validation.max.string',['attribute'=>'name','max'=>150])",
				},
				'is_active': {
					'required': "@lang('validation.required',['attribute'=>'active'])",
					'not_empty': "@lang('validation.not_empty',['attribute'=>'active'])",
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
		$('#formSaveCoupon').submit(function () {
			if ($(this).valid()) {
				addOverlay();
				$('#formSaveCoupon').find('input[type=submit],input[type=button],button[type=submit]').prop('disabled',true);
				return true;
			} else {
				return false;
			}
		});
		$('[name="type"]').change(function(){
			$('#coupon_value_section').toggleClass('d-none',$('[name="type"]:checked').val() != 'percentage');
		});
		$('#formSaveCouponVendor').on('submit',function(e){
			e.preventDefault();
		});
		$('.remove-img').on('click',function(e){
			e.preventDefault();
			$(this).parents('.symbol').remove();
			$('#formSaveCoupon').append('<input type="hidden" name="remove_coupon_image" id="remove_image" value="removed">');
		});
	});
</script>
@endpush
