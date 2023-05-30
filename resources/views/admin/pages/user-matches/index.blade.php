@extends('admin.layouts.app')

@push('breadcrumb')
{!! Breadcrumbs::render('user_matches') !!}
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
		</div>

		<div class="card-body">
			{{-- Datatable Start --}}
			<table id="user_matches_table" class="table table-bordered table-hover table-checkable mt-5"></table>
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
		oTable = $('#user_matches_table').DataTable({
			responsive: true,
			searchDelay: 500,
			processing: true,
			serverSide: true,
			ajax: {
				url: '{{ route("admin.user-matches.listing") }}?{!! http_build_query(request()->query()) !!}'
			},
			columns: [
				{ data: 'name' },
				{ data: 'likes_done' },
				{ data: 'dislikes_done' },
				{ data: 'likes_received' },
				{ data: 'dislikes_received' },
				{ data: 'action'},
				],
			columnDefs: [
				// Specify columns titles here...
				{ targets: 0, title: 'Name', orderable: true },
				{ targets: 1, title: 'Likes Done', orderable: true },
				{ targets: 2, title: 'Dislikes Done', orderable: true },
				{ targets: 3, title: 'Likes Received', orderable: true },
				{ targets: 4, title: 'Dislikes Received', orderable: true },
				// Action buttons
				{ targets: -1, title: 'Action',orderable: false },
				],
			order: [
				[0, 'DESC']
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