@extends('admin.layouts.app')
@push('breadcrumb')
{!! Breadcrumbs::render('coupon_users_list') !!}
@endpush
@push('extra-css-styles')
<link rel="stylesheet" type="text/css" href="{{ asset('assets/plugins/custom/datatables/datatables.bundle.css') }}" />
@endpush
@section('content')
<div class="container">
	<div class="card card-custom">
		<div class="card-header">
			<div class="card-title">
				<span class="card-icon">
					<i class="{{$icon}} text-primary"></i>
				</span>
				<h3 class="card-label">{{ $custom_title }}</h3>
			</div>
			<div class="card-toolbar">
				<a id="coupon-users-csv-button" href="javascript:void(0)" class="btn btn-sm btn-primary font-weight-bolder text-uppercase ml-2">
					<i class="fas fa-arrow-down"></i>
					Download CSV
				</a>
			</div>
		</div>
		<div class="card-body">
			{{-- Filter Start --}}
			<table class="mb-5" align="center">                
				<tr>
					<td>
						<span class="card-icon">
							<i class="fa fa-filter text-primary"></i>
						</span>
						<label>Filter:&nbsp;&nbsp;</label>
					</td>
					<td>                        
						<input type="date" id="search_fromdate" class="form-control" placeholder="From date" value="">
					</td>
					<td>
						<input type="date" id="search_todate" class="form-control" placeholder="To date" value="">
					</td>
					<td>
						<select class="form-control" name="search_vendor" id="search_vendor">
							<option value="">-- Select Vendor --</option>
							@foreach ($coupon_vendors as $coupon_vendor)
							<option value="{{ $coupon_vendor->id }}">{{ $coupon_vendor->name }}</option>
							@endforeach
						</select>
					</td>
					<td>
						<select class="form-control" name="search_plan" id="search_plan">
							<option value="">-- Select plan --</option>
							<?php foreach($subscription_plans as $subscription_plan){ ?>
								<option value="{{ $subscription_plan->id }}">{{ $subscription_plan->name }}</option>
							<?php } ?>
						</select>
					</td>
					<td>
						<input type='button' class="btn btn-primary mr-1 ml-1" id="btn_search_filter" value="Search">
					</td>
					<td>
						<a href="javascript:void(0);" class="btn btn-warning" id="btn_reset_filter">Reset</a>
					</td>
				</tr>
			</table>
			{{-- Filter End --}}
			{{--  Datatable Start  --}}
			<table class="table table-bordered table-hover table-checkable" id="coupon_users_table" style="margin-top: 13px !important"></table>
			{{--  Datatable End  --}}
		</div>
	</div>
</div>
@endsection

@push('extra-js-scripts')
<script src="{{ asset('assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
<script>
	$(document).ready(function(){
		// datatable
		oTable = $('#coupon_users_table').DataTable({
			'responsive': true,
			'searchDelay': 500,
			'processing': true,
			'serverSide': true,
			'ajax': {
				'url':'{{ route("admin.coupon-users.listing") }}',
				'data': {
					'columnsDef':['account_id','user_id','plan_id','razorpay_order_id','amount','purchase_date','subscription_end_date','created_at','coupon_name','status','revoked_at','state','city','email','phone','action'],
				},
				'data':function(data){
					// ST - Filter Params
					var from_date       = $("#search_fromdate").val();
					var to_date         = $("#search_todate").val();
					var search_plan     = $("#search_plan").val();
					var search_vendor   = $("#search_vendor").val();
					// EN - Filter Params
					data.from_date      = from_date;
					data.to_date        = to_date;
					data.search_plan    = search_plan;
					data.search_vendor  = search_vendor;
				},           
			},
			'columns': [
				{'data':'account_id'},
				{'data':'user_id'},
				{'data':'plan_id'},
				{'data':'razorpay_order_id'},
				{'data':'amount'},
				{'data':'purchase_date'},
				{'data':'subscription_end_date'},
				{'data':'created_at'},
				{'data':'coupon_name'},
				{'data':'status'},
				{'data':'revoked_at'},
				{'data':'state'},
				{'data':'city'},
				{'data':'email'},
				{'data':'phone'},
				{'data':'action','responsivePriority':-1},
				],
			'columnDefs': [
				// Specify columns titles here...
				{'targets':0,'title':'Account Id','orderable':false},
				{'targets':1,'title':'User Name','orderable':false},
				{'targets':2,'title':'Plan Name','orderable':true},
				{'targets':3,'title':'Order Id','orderable':false},
				{'targets':4,'title':'Amount','orderable':true},
				{'targets':5,'title':'Start Date','orderable':true},
				{'targets':6,'title':'End Date','orderable':true},
				{'targets':7,'title':'Created At','orderable':true},
				{'targets':8,'title':'Coupon','orderable':true},
				{'targets':9,'title':'Status','orderable':true},
				{'targets':10,'title':'Revoked At','orderable':true},
				{'targets':11,'title':'State','orderable':false},
				{'targets':12,'title':'City','orderable':false},
				{'targets':13,'title':'E-mail','orderable':false},
				{'targets':14,'title':'Phone','orderable':false},
				// Action buttons
				{'targets':-1,'title':'Action','orderable':false},
				],
			'order':[
				[5,'DESC']
				],
			'lengthMenu':[
                [10, 50, 100, 250, 500],
                [10, 50, 100, 250, 500]
				],
			'pageLength':10,
		});
	});
	$(document).on('click','#btn_search_filter',function(){
		oTable.draw();
	});
	$(document).on('click','#btn_reset_filter',function(){
		$('#search_fromdate,#search_todate,#search_plan,#search_vendor,#coupon_users_table_filter input').val('');
		oTable.draw();
	});
	var base_coupon_users_csv_url = '{{ route("admin.coupon-users.csv-download") }}';
	$('#coupon-users-csv-button').attr('href',base_coupon_users_csv_url);
	$('#search_fromdate,#search_todate,#search_plan,#search_vendor,#coupon_users_table_filter input').change(function(){
		var coupon_users_csv_url = base_coupon_users_csv_url;
		var values = [];
		if($('#search_fromdate').val() && $('#search_fromdate').val() != ''){
			values.push('from_date='+$('#search_fromdate').val());
		}
		if($('#search_todate').val() && $('#search_todate').val() != ''){
			values.push('to_date='+$('#search_todate').val());
		}
		if($('#search_plan').val() && $('#search_plan').val() != ''){
			values.push('search_plan='+$('#search_plan').val());
		}
		if($('#search_vendor').val() && $('#search_vendor').val() != ''){
			values.push('search_vendor='+$('#search_vendor').val());
		}
		if($('#coupon_users_table_filter input').val() && $('#coupon_users_table_filter input').val() != ''){
			values.push('search_keyword='+$('#coupon_users_table_filter input').val());
		}
		if(values.length > 0){
			coupon_users_csv_url += '?'+values.join('&');
		}
		$('#coupon-users-csv-button').attr('href',coupon_users_csv_url);
	});
</script>
@endpush