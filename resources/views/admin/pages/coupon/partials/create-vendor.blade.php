<div id="addCouponVendorModal" class="modal fade">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Add Vendor</h4>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<div class="modal-body p-0">
				<form id="formSaveCouponVendor" method="POST" action="javascript:void(0)">
					@csrf
					@include('admin.pages.coupon-vendor.partials.form',['exclude_vendor_scripts'=>true])
					<div class="card-footer">
						<button type="submit" class="btn btn-primary mr-2"> Add {{ $custom_title }}</button>
						<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
@push('extra-js-scripts')
<script>
$('#formSaveCouponVendor').submit(function () {
	if(!$(this).valid()){
		return false;
	}
	$.ajax({
		'url':'{{ route("admin.coupon-vendors.store") }}',
		'type':'post',
		'dataType':'json',
		'data':$('#formSaveCouponVendor').serialize(),
		'success':function(response){
			$('#addCouponVendorModal').modal('hide');
			$('#vendor_id').html('<option value="">-- Select Coupon Vendor --</option>');
			response.vendors.forEach(function(vendor){
				$('#vendor_id').append('<option value="'+vendor.id+'">'+vendor.name+'</option>');
			});
			$('#vendor_id option[value="'+response.vendor_id+'"]').prop('selected',true);
			$('#formSaveCouponVendor [name="name"]').val('');
			$('#formSaveCouponVendor [name="is_active"][value="y"]').prop('checked',true);
		}
	});
});
</script>
@endpush