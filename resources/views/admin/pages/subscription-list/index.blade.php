@extends('admin.layouts.app')

@push('breadcrumb')
{!! Breadcrumbs::render('subscription_list') !!}
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
                {{-- @if (in_array('delete', $permissions))
                <a href="{{ route('admin.subscription-plans.destroy', 0) }}" name="del_select" id="del_select"
                    class="btn btn-sm btn-light-danger font-weight-bolder text-uppercase mr-2 delete_all_link">
                    <i class="far fa-trash-alt"></i> Delete Selected
                </a>
                @endif
                @if (in_array('add', $permissions))
                <a href="{{ route('admin.subscription-plans.create') }}"
                    class="btn btn-sm btn-primary font-weight-bolder text-uppercase">
                    <i class="fas fa-plus"></i>
                    Add {{ $custom_title }}
                </a>
                @endif --}}
                <a href="{{ route('admin.subscriptions.csv-download') }}"
                    class="btn btn-sm btn-primary font-weight-bolder text-uppercase ml-2">
                    <i class="fas fa-arrow-down"></i>
                    Download CSV
                </a>
            </div>
        </div>
        <div class="card-body">
        <div class="row">
        <div class="col-sm-12" >
            {{-- Filter Start --}}
            <table class="mb-5" align="left" style="margin-left:25px;margin: top 15px !important;">
                <tr>
                    <td>
                        <span class="card-icon">
                            <i class="fa fa-filter text-primary"></i>
                        </span>
                        <label>Filter:&nbsp;&nbsp;</label>
                    </td>
                    <td>                        
                        <input type='date' id='search_fromdate' class="form-control" placeholder='From date'>
                    </td>
                    <td>
                        <input type='date' id='search_todate' class="form-control" placeholder='To date'>
                    </td>
                    <td>
                        <select name="status_filter" id="status_filter" class="form-control">
                            <option value="">Select status</option>
                            <option value="incomplete">Incomplete</option>
                            <option value="incomplete_expired">Incomplete expired</option>
                            <option value="trialing">Trialing</option>
                            <option value="active">Active</option>
                            <option value="past_due">Past due</option>
                            <option value="canceled">Canceled</option>
                            <option value="unpaid">Unpaid</option>
                        </select>
                    </td>
                    <td>
                        <input type='button' class="btn btn-primary mr-1 ml-1" id="btn_search_filter" value="Search">
                    </td>
                    <td>
                        <a href="javascript:;" class="btn btn-warning" id="btn_reset_filter">Reset</a>
                    </td>
                </tr>
            </table>
            </div>
            </div>
            {{-- Filter End --}}
            {{-- Datatable Start --}}
            <table class="table table-bordered table-hover table-checkable" id="subscription_pan_table"
                style="margin-top: 13px !important"></table>
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
        oTable = $('#subscription_pan_table').DataTable({
            responsive: true,
            searchDelay: 500,
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('admin.subscription-lists.listing') }}",
                data: {
                    columnsDef: ['user_id', 'plan_id', 'account_id','months','amount', 'status','action'],
                },
                data: function(data) {
                    var from_date = $('#search_fromdate').val();
                    var to_date = $('#search_todate').val();
                    data.from_date = from_date;
                    data.to_date = to_date;
                  // Read values
                  var status_filter = $('#status_filter').val();

                  // Append to data
                  data.status_filter = status_filter;
               }            
            },
            columns: [
                { data: 'account_id' },
                { data: 'user_id' },
                { data: 'plan_id' },
                { data: 'months' },
                { data: 'day' },
                { data: 'amount' },
                { data: 'start_date' },
                { data: 'end_date' },
                { data: 'payment_type' },
                { data: 'status' },
                { data: 'action', responsivePriority: -1 },
            ],
            columnDefs: [
                // Specify columns titles here...
                { targets: 0, title: "Account Id", orderable: false },
                { targets: 1, title: "User Name", orderable: false },
                { targets: 2, title: 'Plan Name', orderable: true },
                { targets: 3, title: 'Month', orderable: false },
                { targets: 4, title: 'Day', orderable: false },
                { targets: 5, title: 'Amount', orderable: true },
                { targets: 6, title: 'Start date', orderable: true },
                { targets: 7, title: 'End date', orderable: true },
                { targets: 8, title: 'Payment Type', orderable: true },
                { targets: 9, title: 'Status', orderable: false },
                // Action buttons
                { targets: -1, title: 'Action',
                orderable: false },
            ],
            order: [
                [1, 'asc']
            ],
            lengthMenu: [
                [10, 50, 100, 250, 500],
                [10, 50, 100, 250, 500]
            ],
            pageLength: 10,
        });

        // Search button
        $('#btn_search_filter').click(function(){
            $('#subscription_pan_table').DataTable().draw();
        });

        $(document).on("click", "#btn_reset_filter", function () {
            $("#status_filter").val('');
            oTable.draw();
        });
    });
</script>
@endpush