@extends('admin.layouts.app')

@push('breadcrumb')
{!! Breadcrumbs::render('usertree') !!}
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
                <h3 class="card-label">{{ $custom_title }}</h3>
            </div>
            <div class="card-toolbar">
                <a href="{{ route('admin.apilog.csv-download') }}"
                class="btn btn-sm btn-primary font-weight-bolder text-uppercase ml-2">
                    <i class="fas fa-arrow-down"></i>
                    Download
                </a>
            </div>
        </div>
        <div class="card-body">
            <table class="mb-5" align="center">
            <tr>
                <td>
                    <span class="card-icon">
                        <i class="fa fa-filter text-primary"></i>
                    </span>
                    <label>Filter:&nbsp;&nbsp;</label>
                </td>
               <td id="search_fromdate_td">
                  <input type='date' id='search_fromdate' class="form-control" placeholder='From date'>
               </td>
               <td id="search_todate_td">
                  <input type='date' id='search_todate' class="form-control" placeholder='To date'>
               </td>
               <td id="search_todate_td">
                  <select class="form-control" id="api_status">
                      <option value="">-- Select status --</option>
                      <option value="200">200</option>
                      <option value="412">412</option>
                  </select>
               </td>
               <td>
                  <input type='button' class="btn btn-primary mr-1 ml-1" id="btn_search" value="Search">
               </td>
               <td>
                  <a href="javascript:;" class="btn btn-warning" id="btn_reset_filter">Reset</a>
               </td>
             </tr>
           </table>
            {{-- Datatable Start --}}
            <table class="table table-bordered table-hover table-checkable" id="apilog_table"
                style="margin-top: 13px !important"></table>
            {{-- Datatable End --}}
        </div>
    </div>
</div>

<!-- Modal-->
<div class="modal fade" id="apilogmodel" tabindex="-1" role="dialog" aria-labelledby="usermatchsmodelLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="usermatchsmodelLabel">User Api Log</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <table class="table table-striped table-bordered nowrap" id="user_match_table">
                    <thead>
                        <tr>
                            <th title="Field #1">Account Id</th>
                            <th title="Field #2">Full name</th>
                            <th title="Field #2">Created date</th>
                        </tr>
                    </thead>
                    <tbody id="user_api_log">
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('extra-js-scripts')
<script src="{{ asset('assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
<script>
    $(document).ready(function () {
        // datatable
        oTable = $('#apilog_table').DataTable({
            responsive: true,
            searchDelay: 500,
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('admin.apilog.listing') }}",
                data: {
                    columnsDef: ['Account Id', 'Full Name', 'Created At', 'Status Code', 'Action'],                    
                },
                data: function(data) {
                    // Read values
                    var from_date = $('#search_fromdate').val();
                    var to_date = $('#search_todate').val();
                    var api_status = $('#api_status').val();

                    // Append to data
                    data.from_date = from_date;
                    data.to_date = to_date;
                    data.api_status = api_status;
                }
            },
            columns: [
                { data: 'account_id' },
                { data: 'full_name' },
                { data: 'created_at' },
                { data: 'api_status' },
                // { data: 'action'},
            ],
            columnDefs: [
                // Specify columns titles here...
                { targets: 0, title: 'Account Id', orderable: false },
                { targets: 1, title: 'Full Name', orderable: false },
                { targets: 2, title: 'Created Date', orderable: true },
                { targets: 3, title: 'Status Code', orderable: true },
                // { targets: 4, title: 'Action',orderable: false },
            ],
            order: [
                [2, 'DESC']
            ],
            lengthMenu: [
                [10, 20, 50, 100],
                [10, 20, 50, 100]
            ],
            pageLength: 10,
        });
        // Search button
        $('#btn_search').click(function(){
            $('#apilog_table').DataTable().draw();
        });
         $(document).on("click", "#btn_reset_filter", function () {
            $("#search_fromdate,#search_todate,#api_status").val('');
            oTable.draw();
        });

    });
    
</script>


@endpush