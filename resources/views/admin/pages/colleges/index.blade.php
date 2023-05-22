@extends('admin.layouts.app')

@push('breadcrumb')
{!! Breadcrumbs::render('colleges_list') !!}
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
					<i class="fas fa-users text-primary"></i>
				</span>
				<h3 class="card-label">
					@if(request()->get('college_filter') == 'unapproved')
					Unapproved
					@endif
					@if(empty(request()->get('college_filter')))
					All
					@endif
					{{ $custom_title }}
				</h3>
			</div>

			<div class="card-toolbar">

				@if(in_array('add',$permissions))
				<a href="{{ route('admin.colleges.create') }}" class="btn btn-sm btn-primary font-weight-bolder text-uppercase mx-1">
					<i class="fas fa-plus"></i>
					Add {{ $custom_title }}
				</a>
				@endif

				@if(in_array('delete',$permissions))
				<a href="{{ route('admin.colleges.destroy',0) }}" name="del_select" id="del_select" class="btn btn-sm btn-light-danger font-weight-bolder text-uppercase mx-1 delete_all_link">
					<i class="far fa-trash-alt"></i> Delete Selected
				</a>
				@endif

				@if(request()->get('college_filter') == 'unapproved')
				<a href="{{ route('admin.colleges.bulk-approve') }}" class="btn btn-sm btn-primary font-weight-bolder text-uppercase mx-1 approve_all_link">
					<i class="fa fa-check"></i> Approve
				</a>
				@endif

				@if(!empty(request()->get('college_filter')))
				<a href="{{ route('admin.colleges.index') }}" class="btn btn-sm btn-success font-weight-bolder text-uppercase mx-1">
					<i class="menu-icon icon-users"></i>
					All {{ $custom_title }}
				</a>
				@endif

				@if(request()->get('college_filter') != 'unapproved')
				<a href="{{ route('admin.colleges.index').'?college_filter=unapproved' }}" class="btn btn-sm btn-warning font-weight-bolder text-uppercase mx-1">
					<i class="menu-icon icon-users"></i>
					Unapproved
				</a>
				@endif

			</div>
		</div>

		<div class="card-body">
			@if(!empty($states))
			{{-- Filter Start --}}
			<table class="mb-5" align="center">
				<tr>
					<td>
						<span class="card-icon">
							<i class="fa fa-filter text-primary"></i>
						</span>
						<label class="mr-1">Filter:</label>
					</td>
					<td>
						<select id="state_filter" name="state_filter" class="form-control">
							<option value="">Select State</option>
							@foreach($states as $state)
							<option value="{{ $state }}">{{ $state }}</option> 
							@endforeach
						</select>
					</td>
					<td>
						<input id="btn_search_filter" type='button' class="btn btn-primary mr-1 ml-1" value="Search">
					</td>
					<td>
						<a id="btn_reset_filter" href="javascript:void(0)" class="btn btn-warning">Reset</a>
					</td>
				</tr>
			</table>
			{{-- Filter End --}}
			@endif
			{{-- Datatable Start --}}
			<table class="table table-bordered table-hover table-checkable" id="colleges_table" style="margin-top:13px!important"></table>
			{{-- Datatable End --}}
		</div>
	</div>
</div>
@endsection

@push('extra-js-scripts')
<script src="{{ asset('assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
<script>
	$(document).ready(function () {
		// datatable
		oTable = $('#colleges_table').DataTable({
			responsive: true,
			searchDelay: 500,
			processing: true,
			serverSide: true,
			ajax: {
				url: '{{ route("admin.colleges.listing") }}?{!! http_build_query(request()->query()) !!}',
				data: {
					columnsDef: ['checkbox', 'name', 'university', 'district', 'state', 'abbreviation', 'created_at', 'approval_status'],
				},
				data: function(data) {
					data.state_filter = $('#state_filter').val();
				}
			},
			columns: [
				{ data: 'checkbox' },
				{ data: 'name' },
				{ data: 'university' },
				{ data: 'district' },
				{ data: 'state' },
				{ data: 'abbreviation' },
				{ data: 'created_at' },
				{ data: 'approval_status' },
				{ data: 'action'},
				],
			columnDefs: [
				// Specify columns titles here...
				{ targets: 0, title: '<center><input type="checkbox" class="all_select"></center>',orderable:false},
				{ targets: 1, title: 'College Name', orderable: true },
				{ targets: 2, title: 'University', orderable: true },
				{ targets: 3, title: 'City', orderable: true },
				{ targets: 4, title: 'State', orderable: true },
				{ targets: 5, title: 'Abbr.', orderable: true },
				{ targets: 6, title: 'Created At', orderable: true },
				{ targets: 7, title: 'Approval Status', orderable: false },
				// Action buttons
				{ targets: -1, title: 'Action',orderable: false },
				],
			order: [
				[6, 'DESC']
				],
			lengthMenu: [
				[10, 20, 50, 100, 250, 500],
				[10, 20, 50, 100, 250, 500]
				],
			pageLength: 10
		});
	});

	$(document).on('click','#btn_search_filter',function(){
		oTable.draw();
	});
	$(document).on('click','#btn_reset_filter',function(){
		$('#state_filter').val('');
		oTable.draw();
	});

</script>
@endpush