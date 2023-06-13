@extends('admin.layouts.app')

@push('breadcrumb')
{!! Breadcrumbs::render('user_matches') !!}
@endpush

@push('extra-css-styles')
<link rel="stylesheet" type="text/css" href="{{ asset('assets/plugins/custom/datatables/datatables.bundle.css') }}" />
<style type="text/css">
    .full-loader-div{
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        text-align: center;
        background-color: rgb(255,255,255,0.5);
    }
    .full-loader-div i {
        font-size: 30px;
        position: absolute;
        left: 50%;
        top: 50%;
        transform: translate(-50%,-50%);
        color: #808080;
    }
</style>
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
				<div class="col-md-12">
					<div class="row">
						<div class="col-md-3 ml-auto">
							<label>Select Target Month</label>
							<select id="system-matches-month" class="form-control">
								<option value="">All Data</option>
	                            @php
	                                $target_date = now();
	                            @endphp
	                            @while($target_date->format('Ym') != '202209')
	                                <option value="{{ $target_date->format('Y-m-d') }}">{{ $target_date->format('F Y') }}</option>
	                                @php
	                                    $target_date = $target_date->subMonth();
	                                @endphp
	                            @endwhile
							</select>
						</div>
					</div>
					<div class="text-center">
						<div id="system-matches-chart"></div>
						<h4 id="system-matches-target" class="mt-5 mb-0"></h4>
					</div>
				</div>
			</div>
            <div class="full-loader-div d-none">
                <i class="fa fa-circle-notch fa-spin"></i>
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
			<table class="mb-5" align="center">
				<tr>
					<td>
						<span class="card-icon"><i class="fa fa-filter text-primary"></i></span>
						<label class="mr-1">Filter:</label>
					</td>
					<td>
						<select id="gender_filter" name="gender_filter" class="form-control">
							<option value="">Select Gender</option>
							<option value="Male">Male</option>
							<option value="Female">Female</option>
						</select>
					</td>
					<td>
						<button id="btn_search_filter" type="button" class="btn btn-primary mr-1 ml-1">Search</button>
					</td>
					<td>
						<button id="btn_reset_filter" type="button" class="btn btn-warning">Reset</button>
					</td>
				</tr>
			</table>
			<table id="user_matches_table" class="table table-bordered table-hover table-checkable mt-5"></table>
		</div>
	</div>
</div>
@endsection

@push('extra-js-scripts')
<script src="{{ asset('assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
<script>
	$(document).ready(function () {
		oTable = $('#user_matches_table').DataTable({
			responsive: true,
			searchDelay: 500,
			processing: true,
			serverSide: true,
			ajax: {
				url: '{{ route("admin.user-matches.listing") }}?{!! http_build_query(request()->query()) !!}',
				data: {
					gender_filter: function(){
						return $('#gender_filter').val();
					}
				},
			},
			columns: [
				{ data: 'full_name' },
				{ data: 'likes_done' },
				{ data: 'dislikes_done' },
				{ data: 'likes_received' },
				{ data: 'dislikes_received' },
				{ data: 'system_matches' },
				{ data: 'organic_matches' },
				{ data: 'chat_initiations' },
				{ data: 'created_at' },
				{ data: 'action'},
			],
			columnDefs: [
				// Specify columns titles here...
				{ targets: 0, title: 'Name', orderable: true },
				{ targets: 1, title: 'Likes Done', orderable: false },
				{ targets: 2, title: 'Dislikes Done', orderable: false },
				{ targets: 3, title: 'Likes Received', orderable: false },
				{ targets: 4, title: 'Dislikes Received', orderable: false },
				{ targets: 5, title: 'System Matches', orderable: false },
				{ targets: 6, title: 'Organic Matches', orderable: true },
				{ targets: 7, title: 'Chat Initiations', orderable: false },
				{ targets: 8, title: 'Created At', orderable: true },
				// Action buttons
				{ targets: -1, title: 'Action',orderable: false },
			],
			order: [
				[8, 'DESC']
			],
			lengthMenu: [
				[10, 20, 50, 100, 250, 500],
				[10, 20, 50, 100, 250, 500]
			],
			pageLength: 10
		});
		$(document).on('click','#btn_search_filter',function(){
			oTable.draw();
		});
		$(document).on('click','#btn_reset_filter',function(){
			$('#gender_filter').val('');
			oTable.draw();
		});
		var system_matches_chart = new ApexCharts($('#system-matches-chart').get(0),{
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
	        stroke:{
	            show:false
	        },
			labels:[' Active',' Connected',' Expired']
		});
		system_matches_chart.render();
		$('#system-matches-month').change(function(){
			$.ajax({
				'url':'{{ route("admin.user-matches.chart-data") }}',
				'type':'post',
				'dataType':'json',
				'data':{
                	'_token':'{{ csrf_token() }}',
                	'target_month_date':$('#system-matches-month').val()
				},
				'beforeSend':function(){
					$('.full-loader-div').removeClass('d-none');
				},
				'success':function(response){
					system_matches_chart.updateOptions(response.options);
					if($('#system-matches-month').val() != ''){
						$('#system-matches-target').html('Showing Data for '+$('#system-matches-month option:selected').html());
					}else{
						$('#system-matches-target').html('');
					}
				},
				'complete':function(){
					$('.full-loader-div').addClass('d-none');
				},
			});
		});
	});
</script>
@endpush