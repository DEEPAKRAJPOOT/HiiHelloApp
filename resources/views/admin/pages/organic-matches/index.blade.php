@extends('admin.layouts.app')

@push('breadcrumb')
{!! Breadcrumbs::render('organic_matches') !!}
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
	.svg-icon.svg-icon-4x i{
		font-size: 35px;
		color: #ffffff;
	}
</style>
@endpush

@section('content')
<div class="container">
	<div class="mb-5">
		<a id="refresh-matches" href="javascript:void(0)" class="text-muted float-right"><i class="la la-refresh"></i> Refresh Data</a>
		<p class="mb-5">&nbsp;</p>
	</div>
	<div class="card card-custom mb-8">
		<div class="card-header">
			<div class="card-title">
				<span class="card-icon">
					<i class="fas fa-users text-primary"></i>
				</span>
				<h3 class="card-label">
					Organic Matches
				</h3>
			</div>
			<div class="card-toolbar">
				<a href="{{ route('admin.organic-matches.create') }}" class="btn btn-sm btn-primary font-weight-bolder text-uppercase ml-5">
					<i class="fas fa-plus"></i>
					Add New Like
				</a>
			</div>
		</div>
		<div class="card-body">
			<div class="row">
				<div class="col-lg-3">
					<div class="card card-custom rounded-xl gutter-b bg-dark card-stretch">
						<div class="d-flex align-items-center mr-2">
							<div class="symbol-label px-6 py-8 rounded-xl mr-7">
								<span class="svg-icon svg-icon-4x svg-icon-white d-block my-2">
									<i class="fa fa-users"></i>
								</span>
								<span class="font-size-h1 font-weight-bold d-block my-1 text-muted">Total</span>
								<span class="font-size-h6 text-muted"><small class="">Since {{ date('M Y',strtotime('2022-10-01')) }}</small></span>
							</div>
							<div>
								<div data-fillfrom="total_organic_matches" class="font-size-h1 text-white font-weight-bolder"></div>
							</div>
						</div>
					</div>
				</div>
				<div class="col-lg-3">
					<div class="card card-custom rounded-xl gutter-b bg-dark card-stretch">
						<div class="d-flex align-items-center mr-2">
							<div class="symbol-label px-6 py-8 rounded-xl mr-7">
								<span class="svg-icon svg-icon-4x svg-icon-white d-block my-2">
									<i class="fa fa-users"></i>
								</span>
								<span class="font-size-h1 font-weight-bold d-block my-1 text-muted">This Month</span>
								<span class="font-size-h6 text-muted"><small class="">{{ date('M Y') }}</small></span>
							</div>
							<div>
								<div data-fillfrom="organic_matches_this_month" class="font-size-h1 text-white font-weight-bolder"></div>
							</div>
						</div>
					</div>
				</div>
				<div class="col-lg-3">
					<div class="card card-custom rounded-xl gutter-b bg-dark card-stretch">
						<div class="d-flex align-items-center mr-2">
							<div class="symbol-label px-6 py-8 rounded-xl mr-7">
								<span class="svg-icon svg-icon-4x svg-icon-white d-block my-2">
									<i class="fa fa-users"></i>
								</span>
								<span class="font-size-h1 font-weight-bold d-block my-1 text-muted">This Week</span>
								<span class="font-size-h6 text-muted"><small class="">{{ date('jS M',strtotime('This week')) }} to {{ date('jS M') }}</small></span>
							</div>
							<div>
								<div data-fillfrom="organic_matches_this_week" class="font-size-h1 text-white font-weight-bolder"></div>
							</div>
						</div>
					</div>
				</div>
				<div class="col-lg-3">
					<div class="card card-custom rounded-xl gutter-b bg-dark card-stretch">
						<div class="d-flex align-items-center mr-2">
							<div class="symbol-label px-6 py-8 rounded-xl mr-7">
								<span class="svg-icon svg-icon-4x svg-icon-white d-block my-2">
									<i class="fa fa-users"></i>
								</span>
								<span class="font-size-h1 font-weight-bold d-block my-1 text-muted">Today</span>
								<span class="font-size-h6 text-muted"><small class="">{{ date('jS M') }}</small></span>
							</div>
							<div>
								<div data-fillfrom="organic_matches_today" class="font-size-h1 text-white font-weight-bolder"></div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="card card-custom mb-8">
		<div class="card-header">
			<div class="card-title">
				<span class="card-icon">
					<i class="fas fa-users text-primary"></i>
				</span>
				<h3 class="card-label">
					User Data
				</h3>
			</div>
		</div>
		<div class="card-body">
			<div class="row mb-4 pb-4">
				<div class="col-md-12">
					<div class="row">
						<div class="col-md-3 ml-auto">
							<label>Select Target Month</label>
							<select id="organic-matches-month" class="form-control">
								@php
								$target_date = now();
								@endphp
								@while($target_date->format('Ym') != '202209')
								<option value="{{ $target_date->format('Y-m-01') }}">{{ $target_date->format('F Y') }}</option>
								@php
								$target_date = $target_date->subMonth();
								@endphp
								@endwhile
							</select>
						</div>
					</div>
					<h4 id="organic-matches-target" class="mt-5 mb-0"></h4>
					<table class="table table-bordered mt-5">
						<tbody>
							<tr>
								<td>Likes Done</td>
								<td data-tablefill="monthly_likes_done"></td>
							</tr>
							<tr>
								<td>Dislikes Done</td>
								<td data-tablefill="monthly_dislikes_done"></td>
							</tr>
							<tr>
								<td>Organic Matches</td>
								<td data-tablefill="monthly_organic_matches"></td>
							</tr>
						</tbody>
					</table>
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
						<p class="mb-0">&nbsp;</p>
						<span class="card-icon"><i class="fa fa-filter text-primary"></i></span>
						<label class="mr-1">Filter:</label>
					</td>
					<td>
						<p class="mb-0">User Created After</p>
						<input id="user_created_after" type="date" class="form-control" value="">
					</td>
					<td>
						<p class="mb-0">User Created Before</p>
						<input id="user_created_before" type="date" class="form-control" value="">
					</td>
					<td>
						<p class="mb-0">Gender</p>
						<select id="gender_filter" name="gender_filter" class="form-control">
							<option value="">All</option>
							<option value="Male">Male</option>
							<option value="Female">Female</option>
						</select>
					</td>
					<td>
						<p class="mb-0">&nbsp;</p>
						<button id="btn_search_filter" type="button" class="btn btn-primary mr-1 ml-1">Filter</button>
					</td>
					<td>
						<p class="mb-0">&nbsp;</p>
						<button id="btn_reset_filter" type="button" class="btn btn-warning">Reset</button>
					</td>
				</tr>
			</table>
			<table id="organic_matches_table" class="table table-bordered table-hover table-checkable mt-5"></table>
		</div>
	</div>
</div>
@endsection

@push('extra-js-scripts')
<script src="{{ asset('assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
<script>
	$(document).ready(function () {
		var ongoing_requests = 0;
		oTable = $('#organic_matches_table').DataTable({
			responsive: true,
			searchDelay: 500,
			processing: true,
			serverSide: true,
			ajax: {
				url: '{{ route("admin.organic-matches.listing") }}?{!! http_build_query(request()->query()) !!}',
				data: {
					user_created_after: function(){
						return $('#user_created_after').val();
					},
					user_created_before: function(){
						return $('#user_created_before').val();
					},
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
				{ data: 'organic_matches' },
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
				{ targets: 5, title: 'Organic Matches', orderable: true },
				{ targets: 6, title: 'User Since', orderable: true },
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
		$(document).on('click','#btn_search_filter',function(){
			oTable.draw();
		});
		$(document).on('click','#btn_reset_filter',function(){
			$('#gender_filter,#user_created_after,#user_created_before').val('');
			oTable.draw();
		});
		function getAllReporting(){
			$.ajax({
				'url':'{{ route("admin.organic-matches.reporting-data") }}',
				'type':'post',
				'dataType':'json',
				'data':{
					'_token':'{{ csrf_token() }}'
				},
				'beforeSend':function(){
					$('[data-fillfrom]').html('<i class="fa fa-circle-notch fa-spin"></i>');
				},
				'success':function(response){
					for(var datakey in response.data){
						$('[data-fillfrom="'+datakey+'"]').html(response.data[datakey]);
					}
				},
				'error':function(response){
					$('[data-fillfrom]').html('<i class="fa fa-exclamation text-danger"></i>');
				}
			});
		}
		$('#organic-matches-month').change(function(){
			$.ajax({
				'url':'{{ route("admin.organic-matches.table-data") }}',
				'type':'post',
				'dataType':'json',
				'data':{
					'_token':'{{ csrf_token() }}',
					'target_month_date':$('#organic-matches-month').val()
				},
				'beforeSend':function(){
					$('[data-tablefill]').html('Loading...');
					ongoing_requests++;
				},
				'success':function(response){
					$('#organic-matches-target').html('Showing Data for '+$('#organic-matches-month option:selected').html());
					for(var datakey in response.data){
						$('[data-tablefill="'+datakey+'"]').html(response.data[datakey]);
					}
				},
				'error':function(){
					$('[data-tablefill]').html('<span class="text-danger"><i class="fa fa-exclamation text-danger"></i> Error while loading</span>');
				},
				'complete':function(){
					ongoing_requests--;
				}
			});
		});
		$('#refresh-matches').on('click',function(){
			$.ajax({
				'url':'{{ route("admin.organic-matches.update",0) }}',
				'type':'put',
				'dataType':'json',
				'data':{
					'_token':'{{ csrf_token() }}',
				},
				'beforeSend':function(){
					ongoing_requests++;
					$('#refresh-matches').attr('disabled',true);
					$('#refresh-matches').html('Refreshing Data');
				},
				'success':function(response){
					$('#refresh-matches').remove();
					$('#organic-matches-month').trigger('change');
					getAllReporting();
				},
				'error':function(){
					$('#refresh-matches').attr('disabled',false);
					$('#refresh-matches').html('<i class="la la-refresh"></i> Refresh Data');
				},
				'complete':function(){
					ongoing_requests--;
				}
			});
		});
		$('#organic-matches-month').trigger('change');
		getAllReporting();
		window.onbeforeunload = function(){
			if(ongoing_requests > 0){
				return 'Data is still refreshing';
			}
		}
	});
</script>
@endpush