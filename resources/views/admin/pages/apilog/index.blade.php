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
        </div>
        <div class="card-body">
            {{-- Datatable Start --}}
            <table class="table table-bordered table-hover table-checkable" id="apilog_table"
                style="margin-top: 13px !important"></table>
            {{-- Datatable End --}}
        </div>
    </div>
</div>

<!-- Modal-->
<div class="modal fade" id="usermatchsmodel" tabindex="-1" role="dialog" aria-labelledby="usermatchsmodelLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="usermatchsmodelLabel">User Match data</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <table class="table table-striped table-bordered nowrap" id="user_match_table">
                    <thead>
                        <tr>
                            <th title="Field #1">User name</th>
                            <th title="Field #2">Gender</th>
                            <th title="Field #2">Match date</th>
                        </tr>
                    </thead>
                    <tbody id="user_match_table_body">
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
            },
            columns: [
                { data: 'account_id' },
                { data: 'full_name' },
                { data: 'created_at' },
                { data: 'api_status' },
                { data: 'action'},
            ],
            columnDefs: [
                // Specify columns titles here...
                { targets: 0, title: 'Account Id', orderable: false },
                { targets: 1, title: 'Full Name', orderable: false },
                { targets: 2, title: 'Created Date', orderable: true },
                { targets: 3, title: 'Status Code', orderable: false },
                { targets: 4, title: 'Action',orderable: false },
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

    });
    
</script>


@endpush