@extends('admin.layouts.app')

@push('breadcrumb')
{!! Breadcrumbs::render('user_matches') !!}
@endpush

@push('extra-css-styles')
<link rel="stylesheet" type="text/css" href="{{ asset('assets/plugins/custom/datatables/datatables.bundle.css') }}" />
@endpush

@section('content')
<div class="container">
	<div class="card card-custom mb-8">
		<div class="card-header">
			<div class="card-title">
				<span class="card-icon">
					<i class="fas fa-users text-primary"></i>
				</span>
				<h3 class="card-label">
					System Matches
				</h3>
			</div>
		</div>
		<div class="card-body">
			<div class="row mb-4 pb-4">
				<div class="col-md-12 text-center">
					<div id="system-matches-chart"></div>
				</div>
			</div>
		</div>
	</div>
	<div class="card card-custom">
		<div class="card-header">
			<div class="card-title">
				<span class="card-icon">
					<i class="fas fa-users text-primary"></i>
				</span>
				<h3 class="card-label">
					Users
				</h3>
			</div>
		</div>
		<div class="card-body">
			<table id="user_matches_table" class="table table-bordered table-hover table-checkable mt-5"></table>
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
				{ data: 'system_matches' },
				{ data: 'system_matches_connected' },
				{ data: 'action'},
				],
			columnDefs: [
				// Specify columns titles here...
				{ targets: 0, title: 'Name', orderable: false },
				{ targets: 1, title: 'Likes Done', orderable: false },
				{ targets: 2, title: 'Dislikes Done', orderable: false },
				{ targets: 3, title: 'Likes Received', orderable: false },
				{ targets: 4, title: 'Dislikes Received', orderable: false },
				{ targets: 5, title: 'Total Matches', orderable: false },
				{ targets: 6, title: 'Matches Connected', orderable: false },
				// Action buttons
				{ targets: -1, title: 'Action',orderable: false },
				],
			lengthMenu: [
				[10, 20, 50, 100, 250, 500],
				[10, 20, 50, 100, 250, 500]
				],
			pageLength: 10
		});
		new ApexCharts($('#system-matches-chart').get(0),{
			series:[{{ $active_system_matches ?? 0 }},{{ $connected_system_matches ?? 0 }},{{ $expired_system_matches ?? 0 }}],
			chart:{
				height:'300px',
				type:'pie',
			},
			legend:{
				position:'bottom',
				formatter:function(seriesName, opts){
					return [seriesName, " - ", opts.w.globals.series[opts.seriesIndex]]
				}
			},
			labels:[' Active',' Connected',' Expired']
		}).render();
	});
</script>
@endpush