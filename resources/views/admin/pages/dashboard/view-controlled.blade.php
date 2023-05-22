@extends('admin.layouts.app')
@push('breadcrumb')
{!! Breadcrumbs::render('dashboard') !!}
@endpush
@push('extra-css-styles')
<link rel="stylesheet" type="text/css" href="{{ asset('assets/plugins/custom/datatables/datatables.bundle.css') }}">
<link href="{{ asset('admin/css/placeholder-loading.min.css') }}" rel="stylesheet">
<style type="text/css">
	.svg-icon.svg-icon-4x i{
		font-size: 35px;
		color: #ffffff;
	}
</style>
@endpush
@section('content')
<div class="container">
	@if(request()->is('*dashboard/preview*'))
	<div class="row">
		<div class="col-lg-12 text-right">
			<p>This is a Preview Version. <a href="{{ route('admin.dashboard.edit') }}">Click here</a> to edit this data.</p>
		</div>
	</div>
	@endif
	<div class="row">
		<div class="col-lg-3">
			<div class="card card-custom rounded-xl gutter-b bg-dark card-stretch">
				<div class="d-flex align-items-center mr-2">
					<div class="symbol-label px-6 py-8 rounded-xl mr-7">
						<span class="svg-icon svg-icon-4x svg-icon-white d-block my-2">
							<i class="fa fa-users"></i>
						</span>
						<span class="font-size-h6 text-muted font-weight-bold">Total Downloads</span>
					</div>
					<div>
						<div class="font-size-h1 text-white font-weight-bolder">{{ $total_downloads ?? 0 }}</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-lg-3">
			<div class="card card-custom rounded-xl gutter-b bg-dark card-stretch">
				<div class="d-flex align-items-center mr-2">
					<div class="symbol-label px-6 py-8 rounded-xl mr-7">
						<span class="svg-icon svg-icon-4x svg-icon-white d-block my-2">
							<i class="fa fa-user-plus"></i>
						</span>
						<span class="font-size-h6 text-muted font-weight-bold">Downloads in {{ date('M Y') }}</span>
					</div>
					<div>
						<div class="font-size-h1 text-white font-weight-bolder">{{ $downloads_this_month ?? 0 }}</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-lg-3">
			<div class="card card-custom rounded-xl gutter-b bg-dark card-stretch">
				<div class="d-flex align-items-center mr-2">
					<div class="symbol-label px-6 py-8 rounded-xl mr-7">
						<span class="svg-icon svg-icon-4x svg-icon-white d-block my-2">
							<i class="fa fa-user-minus"></i>
						</span>
						<span class="font-size-h6 text-muted font-weight-bold">Total Uninstalls</span>
					</div>
					<div>
						<div class="font-size-h1 text-white font-weight-bolder">{{ $total_uninstalls ?? 0 }}</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-lg-3">
			<div class="card card-custom rounded-xl gutter-b bg-dark card-stretch">
				<div class="d-flex align-items-center mr-2">
					<div class="symbol-label px-6 py-8 rounded-xl mr-7">
						<span class="svg-icon svg-icon-4x svg-icon-white d-block my-2">
							<i class="fa fa-user-minus"></i>
						</span>
						<span class="font-size-h6 text-muted font-weight-bold">Uninstalls in {{ date('M Y') }}</span>
					</div>
					<div>
						<div class="font-size-h1 text-white font-weight-bolder">{{ $uninstalls_this_month ?? 0 }}</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-lg-8">
			<div class="card card-custom gutter-b card-stretch">
				<div class="card-header h-auto border-0">
					<div class="card-title py-5">
						<div class="card-label">Monthly Downloads</div>
					</div>
				</div>
				<div class="card-body d-flex flex-column position-relative">
					<div id="monthly-dowloads-chart"></div>
				</div>
			</div>
		</div>
		<div class="col-lg-4">
			<div class="card card-custom gutter-b card-stretch">
				<div class="card-header h-auto border-0">
					<div class="card-title py-5">
						<div class="card-label">Downloads</div>
					</div>
				</div>
				<div class="card-body d-flex flex-column position-relative">
					<div id="male-female-downloads-chart"></div>
					<div class="mt-8 pt-8">
						<div class="d-flex align-items-center mb-5">
							<div class="symbol symbol-45 symbol-light-success mr-4 flex-shrink-0">
								<div class="symbol-label">
									<span class="svg-icon svg-icon-lg svg-icon-success">
										<i class="fa fa-users text-success"></i>
									</span>
								</div>
							</div>
							<div>
								<div class="font-size-h4 text-dark-75 font-weight-bolder">{{ $total_downloads_males ?? 0 }}</div>
								<div class="font-size-sm text-muted font-weight-bold mt-1">Male Users</div>
							</div>
						</div>
						<div class="d-flex align-items-center mb-5">
							<div class="symbol symbol-45 symbol-light-danger mr-4 flex-shrink-0">
								<div class="symbol-label">
									<span class="svg-icon svg-icon-lg svg-icon-danger">
										<i class="fa fa-users text-danger"></i>
									</span>
								</div>
							</div>
							<div>
								<div class="font-size-h4 text-dark-75 font-weight-bolder">{{ $total_downloads_females ?? 0 }}</div>
								<div class="font-size-sm text-muted font-weight-bold mt-1">Female Users</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-lg-12">
			<div class="card card-custom gutter-b">
				<div class="card-header flex-wrap border-0 py-5">
					<div class="card-title">
						<div class="card-label">Month Wise Data</div>
					</div>
				</div>
				<div class="card-body pt-0">
					<table class="table table-separate table-head-custom table-checkable dataTable no-footer dtr-inline">
						<thead>
							<tr>
								<th>Month / Year Name</th>
								<th>Total Downloads</th>
								<th>Male Users</th>
								<th>Female Users</th>
								<th>Organic Users</th>
								<th>Paid Users</th>
								<th>Referral Users</th>
								<th>Uninstalls</th>
								<th>Net App Installed Base</th>
							</tr>
						</thead>
						<tbody>
							@foreach(array_reverse($last_twelve_months_data) as $month_data_key => $month_data_value)
							<tr>
								<td>{{ $month_data_key }}</td>
								<td>{{ $month_data_value['total_downloads'] ?? 0 }}</td>
								<td>
									{{ $month_data_value['male_downloads'] ?? 0 }}
									@if(!empty($month_data_value['male_percentage']))
									({{ $month_data_value['male_percentage'] ?? 0 }}%)
									@endif
								</td>
								<td>
									{{ $month_data_value['female_downloads'] ?? 0 }}
									@if(!empty($month_data_value['female_percentage']))
									({{ $month_data_value['female_percentage'] ?? 0 }}%)
									@endif
								</td>
								<td>{{ $month_data_value['organic_downloads'] ?? 0 }}</td>
								<td>{{ $month_data_value['paid_downloads'] ?? 0 }}</td>
								<td>{{ $month_data_value['referral_downloads'] ?? 0 }}</td>
								<td>{{ $month_data_value['total_uninstalls'] ?? 0 }}</td>
								<td>{{ ($month_data_value['total_downloads'] ?? 0) - ($month_data_value['total_uninstalls'] ?? 0) }}</td>
							</tr>
							@endforeach
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-lg-12">
			<div class="card card-custom gutter-b card-stretch">
				<div class="card-header h-auto border-0">
					<div class="card-title py-5">
						<div class="card-label">Monthly Revenue</div>
					</div>
				</div>
				<div class="card-body d-flex flex-column position-relative">
					<div id="monthly-revenue-chart"></div>
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-lg-12">
			<div class="card card-custom gutter-b">
				<div class="card-header flex-wrap border-0 py-5">
					<div class="card-title">
						<div class="card-label">Daily Time Spent per active user</div>
					</div>
				</div>
				<div class="card-body pt-0">
					<table class="table table-separate table-head-custom table-checkable dataTable no-footer dtr-inline">
						<thead>
							<tr>
								<th>Month / Year Name</th>
								<th>Male Subscribers</th>
								<th>Male Non-Subscribers</th>
								<th>Female Users</th>
							</tr>
						</thead>
						<tbody>
							@foreach(array_reverse($last_twelve_months_data) as $month_data_key => $month_data_value)
							<tr>
								<td>{{ $month_data_key }}</td>
								<td>{{ $month_data_value['daily_time_male_subscribers'] ?? 'N/A' }}</td>
								<td>{{ $month_data_value['daily_time_male_non_subscribers'] ?? 'N/A' }}</td>
								<td>{{ $month_data_value['daily_time_female_users'] ?? 'N/A' }}</td>
							</tr>
							@endforeach
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-lg-12">
			<div class="card card-custom gutter-b">
				<div class="card-header flex-wrap border-0 py-5">
					<div class="card-title">
						<div class="card-label">Logins/App FireUps</div>
					</div>
				</div>
				<div class="card-body pt-0">
					<table class="table table-separate table-head-custom table-checkable dataTable no-footer dtr-inline">
						<thead>
							<tr>
								<th>Month / Year Name</th>
								<th>Male Subscribers</th>
								<th>Male Non-Subscribers</th>
								<th>Female Users</th>
							</tr>
						</thead>
						<tbody>
							@foreach(array_reverse($last_twelve_months_data) as $month_data_key => $month_data_value)
							<tr>
								<td>{{ $month_data_key }}</td>
								<td>{{ $month_data_value['logins_male_subscribers'] ?? 'N/A' }}</td>
								<td>{{ $month_data_value['logins_male_non_subscribers'] ?? 'N/A' }}</td>
								<td>{{ $month_data_value['logins_female_users'] ?? 'N/A' }}</td>
							</tr>
							@endforeach
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-lg-12">
			<div class="card card-custom gutter-b">
				<div class="card-header flex-wrap border-0 py-5">
					<div class="card-title">
						<div class="card-label">Active Users</div>
					</div>
				</div>
				<div class="card-body pt-0">
					<table class="table table-separate table-head-custom table-checkable dataTable no-footer dtr-inline">
						<thead>
							<tr>
								<th>Month / Year Name</th>
								<th>Daily Active Users</th>
								<th>Monthly Active Users</th>
								<th>DAU/MAU Ratio</th>
							</tr>
						</thead>
						<tbody>
							@foreach(array_reverse($last_twelve_months_data) as $month_data_key => $month_data_value)
							<tr>
								<td>{{ $month_data_key }}</td>
								<td>{{ $month_data_value['active_daily'] ?? 0 }}</td>
								<td>{{ $month_data_value['active_monthly'] ?? 0 }}</td>
								<td>{{ !empty($month_data_value['active_monthly']) ? round(($month_data_value['active_daily'] ?? 0) / $month_data_value['active_monthly'] * 100,2) : 100 }}%</td>
							</tr>
							@endforeach
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-lg-12">
			<div class="card card-custom gutter-b">
				<div class="card-header flex-wrap border-0 py-5">
					<div class="card-title">
						<div class="card-label">Notifications Sent</div>
					</div>
				</div>
				<div class="card-body pt-0">
					<table class="table table-separate table-head-custom table-checkable dataTable no-footer dtr-inline">
						<thead>
							<tr>
								<th>Month / Year Name</th>
								<th>Email (Remarketing/Bday)</th>
								<th>SMS (login)</th>
								<th>In-App (Remarketing/Nudge)</th>
							</tr>
						</thead>
						<tbody>
							@foreach(array_reverse($last_twelve_months_data) as $month_data_key => $month_data_value)
							<tr>
								<td>{{ $month_data_key }}</td>
								<td>
									{{ $month_data_value['notifications_sent_email'] ?? 0 }}
									({{ $month_data_value['notifications_clicked_percentages_email'] ?? 0 }}% clicked)
								</td>
								<td>
									{{ $month_data_value['notifications_sent_sms'] ?? 0 }}
									({{ $month_data_value['notifications_clicked_percentages_sms'] ?? 0 }}% clicked)
								</td>
								<td>
									{{ $month_data_value['notifications_sent_in_app'] ?? 0 }}
									({{ $month_data_value['notifications_clicked_percentages_in_app'] ?? 0 }}% clicked)
								</td>
							</tr>
							@endforeach
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-lg-12">
			<div class="card card-custom gutter-b">
				<div class="card-header flex-wrap border-0 py-5">
					<div class="card-title">
						<div class="card-label">App Retention</div>
					</div>
				</div>
				<div class="card-body pt-0">
					<table class="table table-separate table-head-custom table-checkable dataTable no-footer dtr-inline">
						<thead>
							<tr>
								<th>Month / Year Name</th>
								<th>Day 1</th>
								<th>Day 7</th>
								<th>Day 30</th>
							</tr>
						</thead>
						<tbody>
							@foreach(array_reverse($last_twelve_months_data) as $month_data_key => $month_data_value)
							<tr>
								<td>{{ $month_data_key }}</td>
								<td>{{ $month_data_value['users_retention_d1'] ? $month_data_value['users_retention_d1'].'%' : 'N/A' }}</td>
								<td>{{ $month_data_value['users_retention_d7'] ? $month_data_value['users_retention_d7'].'%' : 'N/A' }}</td>
								<td>{{ $month_data_value['users_retention_d30'] ? $month_data_value['users_retention_d30'].'%' : 'N/A' }}</td>
							</tr>
							@endforeach
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-lg-12">
			<div class="card card-custom gutter-b">
				<div class="card-header flex-wrap border-0 py-5">
					<div class="card-title">
						<div class="card-label">Paid Users</div>
					</div>
				</div>
				<div class="card-body pt-0">
					<table class="table table-separate table-head-custom table-checkable dataTable no-footer dtr-inline">
						<thead>
							<tr>
								<th>Month / Year Name</th>
								<th>Weekly Subscription</th>
								<th>Monthly Subscription</th>
								<th>Half Yearly Subscription</th>
								<th>Yearly Subscription</th>
							</tr>
						</thead>
						<tbody>
							@foreach(array_reverse($last_twelve_months_data) as $month_data_key => $month_data_value)
							<tr>
								<td>{{ $month_data_key }}</td>
								<td>{{ $month_data_value['paid_users_weekly'] ?? 0 }}</td>
								<td>{{ $month_data_value['paid_users_monthly'] ?? 0 }}</td>
								<td>{{ $month_data_value['paid_users_half_yearly'] ?? 0 }}</td>
								<td>{{ $month_data_value['paid_users_yearly'] ?? 0 }}</td>
							</tr>
							@endforeach
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection
@push('extra-js-scripts')
<script type="text/javascript">
	new ApexCharts($('#male-female-downloads-chart').get(0),{
		series:[{{ $total_downloads_males ?? 0 }},{{ $total_downloads_females ?? 0 }}],
		chart:{
			width:'100%',
			type:'pie',
		},
		legend:{
			position:'bottom',
		},
		labels:[],
		colors:['rgb(0,183,70)','rgb(239,64,60)'],
		labels:['Male','Female'],
		responsive:[{
			breakpoint: 480,
			options:{
				chart:{
					width: 200,
				},
				legend:{
					position:'bottom',
				},
			},
		}]
	}).render();
	new ApexCharts($('#monthly-dowloads-chart').get(0),{
		series:[{
			name:'Total Downloads',
			data:[{{ implode(',',array_column($last_twelve_months_data ?? [],'total_downloads')) }}]
		}],
		chart:{
			height:'400px',
			type:'bar'
		},
		xaxis:{
			categories:['{!! implode("','",array_keys($last_twelve_months_data ?? [])) !!}']
		},
		plotOptions:{
			bar:{
				dataLabels:{
					position:'bottom',
				}
			}
		},
		fill:{
			colors:['#1E1E2D']
		},
		yaxis:{
			showForNullSeries:false,
			tickAmount:10
		}
	}).render();
	new ApexCharts($('#monthly-revenue-chart').get(0),{
		series:[{
			name:'Weekly Subscriptions',
			data:[{{ implode(',',array_column($last_twelve_months_data ?? [],'revenue_by_weekly')) }}]
		},{
			name:'Monthly Subscriptions',
			data:[{{ implode(',',array_column($last_twelve_months_data ?? [],'revenue_by_monthly')) }}]
		},{
			name:'Half Yearly Subscriptions',
			data:[{{ implode(',',array_column($last_twelve_months_data ?? [],'revenue_by_half_yearly')) }}]
		},{
			name:'Yearly Subscriptions',
			data:[{{ implode(',',array_column($last_twelve_months_data ?? [],'revenue_by_yearly')) }}]
		}],
		dataLabels:{
			enabled:false
		},
		chart:{
			height:'400px',
			type:'bar',
			stacked:true,
		},
		xaxis:{
			categories:['{!! implode("','",array_keys($last_twelve_months_data ?? [])) !!}']
		},
		plotOptions:{
			bar:{
				dataLabels:{
					position:'top',
				}
			}
		},
		yaxis:{
			showForNullSeries:false,
			tickAmount:10,
			labels:{
				formatter:function(value,index){
					return '₹'+value;
				}
			}
		},
		tooltip:{
			y:{
				formatter:function(value, { series, seriesIndex, dataPointIndex, w }){
					return '₹'+value;
				}
			}
		}
	}).render();
</script>
@endpush