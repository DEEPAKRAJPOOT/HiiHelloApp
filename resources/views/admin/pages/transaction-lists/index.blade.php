@extends('admin.layouts.app')

@push('breadcrumb')
    {!! Breadcrumbs::render('transaction_list') !!}
@endpush

@push('extra-css-styles')
<link rel="stylesheet" type="text/css" href="{{ asset('assets/plugins/custom/datatables/datatables.bundle.css') }}" />
@endpush

@section('content')
<div class="container">
    <div class="row">
        <input type="hidden" name="filter_type_url" id="filter_type_url" value="{{ route('admin.transaction.filters') }}">
        <div class="col-lg-3">
           <div class="card card-custom rounded-xl gutter-b bg-dark card-stretch">
              <div class="d-flex align-items-center mr-2">
                 <div class="symbol-label px-6 py-8 rounded-xl mr-7">
                    <span class="svg-icon svg-icon-4x svg-icon-white d-block my-2">
                       <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                <rect x="0" y="0" width="24" height="24"/>
                                <path d="M2,6 L21,6 C21.5522847,6 22,6.44771525 22,7 L22,17 C22,17.5522847 21.5522847,18 21,18 L2,18 C1.44771525,18 1,17.5522847 1,17 L1,7 C1,6.44771525 1.44771525,6 2,6 Z M11.5,16 C13.709139,16 15.5,14.209139 15.5,12 C15.5,9.790861 13.709139,8 11.5,8 C9.290861,8 7.5,9.790861 7.5,12 C7.5,14.209139 9.290861,16 11.5,16 Z" fill="#000000" opacity="0.3" transform="translate(11.500000, 12.000000) rotate(-345.000000) translate(-11.500000, -12.000000) "/>
                                <path d="M2,6 L21,6 C21.5522847,6 22,6.44771525 22,7 L22,17 C22,17.5522847 21.5522847,18 21,18 L2,18 C1.44771525,18 1,17.5522847 1,17 L1,7 C1,6.44771525 1.44771525,6 2,6 Z M11.5,16 C13.709139,16 15.5,14.209139 15.5,12 C15.5,9.790861 13.709139,8 11.5,8 C9.290861,8 7.5,9.790861 7.5,12 C7.5,14.209139 9.290861,16 11.5,16 Z M11.5,14 C12.6045695,14 13.5,13.1045695 13.5,12 C13.5,10.8954305 12.6045695,10 11.5,10 C10.3954305,10 9.5,10.8954305 9.5,12 C9.5,13.1045695 10.3954305,14 11.5,14 Z" fill="#000000"/>
                            </g>
                        </svg>
                    </span>
                    <span class="font-size-h6 text-muted font-weight-bold">Google Play</span>
                 </div>
                 <div>
                    <div class="font-size-h1 text-white font-weight-bolder" id="total_google_play"></div>
                 </div>
              </div>
           </div>
        </div>

         <div class="col-lg-3">
           <div class="card card-custom rounded-xl gutter-b bg-dark card-stretch">
              <div class="d-flex align-items-center mr-2">
                 <div class="symbol-label px-6 py-8 rounded-xl mr-7">
                    <span class="svg-icon svg-icon-4x svg-icon-white d-block my-2">
                       <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                <rect x="0" y="0" width="24" height="24"/>
                                <path d="M2,6 L21,6 C21.5522847,6 22,6.44771525 22,7 L22,17 C22,17.5522847 21.5522847,18 21,18 L2,18 C1.44771525,18 1,17.5522847 1,17 L1,7 C1,6.44771525 1.44771525,6 2,6 Z M11.5,16 C13.709139,16 15.5,14.209139 15.5,12 C15.5,9.790861 13.709139,8 11.5,8 C9.290861,8 7.5,9.790861 7.5,12 C7.5,14.209139 9.290861,16 11.5,16 Z" fill="#000000" opacity="0.3" transform="translate(11.500000, 12.000000) rotate(-345.000000) translate(-11.500000, -12.000000) "/>
                                <path d="M2,6 L21,6 C21.5522847,6 22,6.44771525 22,7 L22,17 C22,17.5522847 21.5522847,18 21,18 L2,18 C1.44771525,18 1,17.5522847 1,17 L1,7 C1,6.44771525 1.44771525,6 2,6 Z M11.5,16 C13.709139,16 15.5,14.209139 15.5,12 C15.5,9.790861 13.709139,8 11.5,8 C9.290861,8 7.5,9.790861 7.5,12 C7.5,14.209139 9.290861,16 11.5,16 Z M11.5,14 C12.6045695,14 13.5,13.1045695 13.5,12 C13.5,10.8954305 12.6045695,10 11.5,10 C10.3954305,10 9.5,10.8954305 9.5,12 C9.5,13.1045695 10.3954305,14 11.5,14 Z" fill="#000000"/>
                            </g>
                        </svg>
                    </span>
                    <span class="font-size-h6 text-muted font-weight-bold">UPI</span>
                 </div>
                 <div>
                    <div class="font-size-h1 text-white font-weight-bolder"id="total_upi"></div>
                 </div>
              </div>
           </div>
        </div>

         <div class="col-lg-3">
           <div class="card card-custom rounded-xl gutter-b bg-dark card-stretch">
              <div class="d-flex align-items-center mr-2">
                 <div class="symbol-label px-6 py-8 rounded-xl mr-7">
                    <span class="svg-icon svg-icon-4x svg-icon-white d-block my-2">
                       <!--begin::Svg Icon | path:/metronic/theme/html/demo1/dist/assets/media/svg/icons/Communication/Group.svg-->
                       <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                <rect x="0" y="0" width="24" height="24"/>
                                <path d="M2,6 L21,6 C21.5522847,6 22,6.44771525 22,7 L22,17 C22,17.5522847 21.5522847,18 21,18 L2,18 C1.44771525,18 1,17.5522847 1,17 L1,7 C1,6.44771525 1.44771525,6 2,6 Z M11.5,16 C13.709139,16 15.5,14.209139 15.5,12 C15.5,9.790861 13.709139,8 11.5,8 C9.290861,8 7.5,9.790861 7.5,12 C7.5,14.209139 9.290861,16 11.5,16 Z" fill="#000000" opacity="0.3" transform="translate(11.500000, 12.000000) rotate(-345.000000) translate(-11.500000, -12.000000) "/>
                                <path d="M2,6 L21,6 C21.5522847,6 22,6.44771525 22,7 L22,17 C22,17.5522847 21.5522847,18 21,18 L2,18 C1.44771525,18 1,17.5522847 1,17 L1,7 C1,6.44771525 1.44771525,6 2,6 Z M11.5,16 C13.709139,16 15.5,14.209139 15.5,12 C15.5,9.790861 13.709139,8 11.5,8 C9.290861,8 7.5,9.790861 7.5,12 C7.5,14.209139 9.290861,16 11.5,16 Z M11.5,14 C12.6045695,14 13.5,13.1045695 13.5,12 C13.5,10.8954305 12.6045695,10 11.5,10 C10.3954305,10 9.5,10.8954305 9.5,12 C9.5,13.1045695 10.3954305,14 11.5,14 Z" fill="#000000"/>
                            </g>
                        </svg>
                       <!--end::Svg Icon-->
                    </span>
                    <span class="font-size-h6 text-muted font-weight-bold">IOS</span>
                 </div>
                 <div>
                    <div class="font-size-h1 text-white font-weight-bolder" id="total_ios"></div>
                 </div>
              </div>
           </div>
        </div>
    </div>
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
                           <option value="IOS">IOS</option>
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

                { data: 'subscription_end_date' },

                

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

<script type="text/javascript">
    $(document).ready(function () {
        var filter_type_url     = $("#filter_type_url").val();
        if (filter_type_url != '') {
            $.ajax({
                url: filter_type_url,
                type: "GET",
                dataType: "json",
                data: {
                    _token: $("meta[name='csrf-token']").attr("content"),
                },
                cache: false,
                beforeSend: function(){
                    $("#total_google_play").html("<p style='font-size: 18px;'>processing..</p>");
                    $("#total_upi").html("<p style='font-size: 18px;'>processing..</p>");
                    $("#total_android").html("<p style='font-size: 18px;'>processing..</p>");
                    $("#total_ios").html("<p style='font-size: 18px;'>processing..</p>");
                },
                complete: function(){
                    $("#total_google_play").html();
                    $("#total_upi").html();
                    $("#total_android").html();
                    $("#total_ios").html();
                },
                success: function (result) {
                    if (result != '') {
                        $("#total_google_play").html(result.total_google_play);
                        $("#total_upi").html(result.total_upi);
                        $("#total_android").html(result.total_android);
                        $("#total_ios").html(result.total_ios);
                    }
                },
            });
        }
    });
</script>
@endpush
