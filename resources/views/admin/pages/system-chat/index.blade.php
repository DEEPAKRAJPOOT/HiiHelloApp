@extends('admin.layouts.app')

@push('breadcrumb')
{!! Breadcrumbs::render('system_chats_list') !!}
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
					{{ $custom_title }}
				</h3>
			</div>

			<div class="card-toolbar">

				@if(in_array('add',$permissions))
				<a href="{{ route('admin.system-chat.create') }}" class="btn btn-sm btn-primary font-weight-bolder text-uppercase mx-1">
					<i class="fas fa-plus"></i>
					Send New Message
				</a>
				@endif

				@if(in_array('delete',$permissions))
				<a href="{{ route('admin.colleges.destroy',0) }}" name="del_select" id="del_select" class="btn btn-sm btn-light-danger font-weight-bolder text-uppercase mx-1 delete_all_link">
					<i class="far fa-trash-alt"></i> Delete Selected
				</a>
				@endif

			</div>
		</div>

		<div class="card-body">
			{{-- Datatable Start --}}
			<table class="table table-bordered table-hover table-checkable" id="system_chats_table" style="margin-top:13px!important"></table>
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
		oTable = $('#system_chats_table').DataTable({
			responsive: true,
			searchDelay: 500,
			processing: true,
			serverSide: true,
			ajax: {
				url: '{{ route("admin.system-chat.listing") }}?{!! http_build_query(request()->query()) !!}',
				data: {
					columnsDef: ['full_name', 'latest_message', 'created_at'],
				}
			},
			columns: [
				{ data: 'full_name' },
				{ data: 'latest_message' },
				{ data: 'created_at' },
				{ data: 'action'},
				],
			columnDefs: [
				// Specify columns titles here...
				{ targets: 0, title: 'User Name', orderable: true },
				{ targets: 1, title: 'Message', orderable: true },
				{ targets: 2, title: 'Created At', orderable: true },
				// Action buttons
				{ targets: -1, title: 'Action',orderable: false },
				],
			order: [
				[2, 'DESC']
				],
			lengthMenu: [
				[10, 20, 50, 100, 250, 500],
				[10, 20, 50, 100, 250, 500]
				],
			pageLength: 10
		});
	});

</script>
@endpush