<div class="card-body">
	<div class="form-group">
		<label for="name">Name:</label>
		<input type="text"class="form-control" id="name" name="name" value="@if(old('name')){{ old('name') }}@else{{ $coupon_vendor->name ?? '' }}@endif" placeholder="Enter Vendor name" autocomplete="name" spellcheck="false" autocapitalize="sentences" tabindex="0" autofocus />
		@if ($errors->has('name'))
		<span class="help-block">
			<strong class="form-text">{{ $errors->first('name') }}</strong>
		</span>
		@endif
	</div>
	<div class="form-group">
		<label for="is_active">{!!$mend_sign!!}Active:</label>
		<div class="custom-file">
			<div class="row">
				<div class="col-md-2">
					<input type="radio" id="vendor_active_y" name="is_active" value="y" @if(($coupon_vendor->is_active ?? 'y') == 'y') checked @endif data-error-container="#error-authorizable-coupon-vendor">
					<label for="vendor_active_y">Yes</label><br> 
				</div>
				<div class="col-md-2">
					<input type="radio" id="vendor_active_n" name="is_active" value="n" @if(($coupon_vendor->is_active ?? '') == 'n') checked @endif data-error-container="#error-authorizable-coupon-vendor">
					<label for="vendor_active_n">No</label><br>
				</div>
			</div>
			<span id="error-authorizable-coupon-vendor"></span>
			@if($errors->has('is_active'))
			<span class="help-block">
				<strong class="form-text">{{ $errors->first('is_active') }}</strong>
			</span>
			@endif
		</div>
	</div>
</div>
@push('extra-js-scripts')
<script>
	$(document).ready(function () {
		$("#formSaveCouponVendor").validate({
			'rules': {
				'name': {
					'required': true,
					'not_empty': true,
					'minlength': 3,
					'maxlength': 150,
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
					'required': "@lang('validation.required',['attribute'=>'Active'])",
					'not_empty': "@lang('validation.not_empty',['attribute'=>'Active'])",
				},
			},
			'errorClass': 'invalid-feedback',
			'errorElement': 'span',
			'highlight':function (element) {
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
		@if(empty($exclude_vendor_scripts))
		$('#formSaveCouponVendor').submit(function () {
			if ($(this).valid()) {
				addOverlay();
				$('#formSaveCouponVendor').find('input[type=submit],input[type=button],button[type=submit]').prop('disabled',true);
				return true;
			} else {
				return false;
			}
		});
		@endif
	});
</script>
@endpush