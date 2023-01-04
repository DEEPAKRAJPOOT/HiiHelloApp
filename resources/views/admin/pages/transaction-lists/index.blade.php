@extends('admin.layouts.app')

@push('breadcrumb')
    {!! Breadcrumbs::render('transaction_list') !!}
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
                    <a href="{{ route('admin.subscription-plans.destroy', 0) }}" name="del_select" id="del_select" class="btn btn-sm btn-light-danger font-weight-bolder text-uppercase mr-2 delete_all_link">
                        <i class="far fa-trash-alt"></i> Delete Selected
                    </a>
                @endif
                @if (in_array('add', $permissions))
                    <a href="{{ route('admin.subscription-plans.create') }}" class="btn btn-sm btn-primary font-weight-bolder text-uppercase">
                        <i class="fas fa-plus"></i>
                        Add {{ $custom_title }}
                    </a>
                @endif --}}
                <a href="{{ route('admin.transactions.csv-download') }}"
                class="btn btn-sm btn-primary font-weight-bolder text-uppercase ml-2">
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
                        <input type='date' id='search_fromdate' class="form-control" placeholder='From date' value="">
                    </td>
                    <td>
                        <input type='date' id='search_todate' class="form-control" placeholder='To date' value="">
                    </td>

                    <td>
                       <select class="form-control" name="search_status" id="search_status">
                           <option value="">-- Select mode --</option>
                           <option value="Google Play">Google Play</option>
                           <option value="UPI">UPI</option>
                           <option value="android">Android</option>
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
            {{-- Filter End --}}
            {{--  Datatable Start  --}}
            <table class="table table-bordered table-hover table-checkable" id="subscription_pan_table" style="margin-top: 13px !important"></table>
            {{--  Datatable End  --}}
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
                url: "{{ route('admin.transaction-lists.listing') }}",
                data: {
                    columnsDef: ['account_id','user_id', 'plan_id', 'razorpay_order_id','amount', 'status','action'],
                },
                data: function(data) {                    

                    // ST - Filter Params
                    var from_date       = $("#search_fromdate").val();
                    var to_date         = $("#search_todate").val();  
                    var search_status   = $("#search_status").val();  
                        
                    // EN - Filter Params

                    data.from_date         = from_date;
                    data.to_date           = to_date;                    
                    data.search_status     = search_status;                    
                    //data.flgPendingProfile = $(".getpendingprofile").is(':checked') ? 1 : 0;
                },           
            },
            columns: [
                { data: 'account_id' },
                { data: 'user_id' },
                { data: 'plan_id' },
                { data: 'razorpay_order_id' },
                { data: 'amount' },
                { data: 'purchase_date' },
                { data: 'original_purchase_date' },
                { data: 'payment_type' },
                { data: 'action', responsivePriority: -1 },
            ],
            columnDefs: [
                // Specify columns titles here...
                { targets: 0, title: "Account Id", orderable: false },
                { targets: 1, title: "User Name", orderable: false },
                { targets: 2, title: 'Plan Name', orderable: true },
                { targets: 3, title: 'Order Id', orderable: false },
                { targets: 4, title: 'Amount', orderable: true },
                { targets: 5, title: 'Start Date', orderable: true },
                { targets: 6, title: 'End Date', orderable: true },
                { targets: 7, title: 'Mode', orderable: true },
                // Action buttons
                { targets: -1, title: 'Action',
                orderable: false },
            ],
            order: [
                [5, 'DESC']
            ],
            lengthMenu: [
                [10, 20, 50, 100],
                [10, 20, 50, 100]
            ],
            pageLength: 10,
        });
    });

    $(document).on("click", "#btn_search_filter", function () {
        
        oTable.draw();
    });

    $(document).on("click", "#btn_reset_filter", function () {
        $("#search_fromdate,#search_todate,#search_status").val('');
        oTable.draw();
    });
</script>
@endpush
