@extends('admin.layouts.app')

@push('breadcrumb')
{!! Breadcrumbs::render('users_list') !!}
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
                <a href="{{ route('admin.users.csv-download-unde-review') }}"
                    class="btn btn-sm btn-primary font-weight-bolder text-uppercase ml-2">
                    <i class="fas fa-arrow-down"></i>
                    Download CSV
                </a>
            </div>
        </div>
        <div class="card-body">
            {{-- Datatable Start --}}
            <table class="table table-bordered table-hover table-checkable" id="users_table"
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
        oTable = $('#users_table').DataTable({
            responsive: true,
            searchDelay: 500,
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('admin.users.listing') }}",
                data: {
                    columnsDef: ['checkbox', 'country_code', 'contact_no', 'gender','Created At', 'active', 'action'],                    
                },
                data: function(data) {
                    data.flgPendingProfile = 1;
               }                
            },
            columns: [
                { data: 'account_id' },
                { data: 'full_name' },
                { data: 'profile_percentage' },
                { data: 'contact_no' },
                { data: 'email' },
                { data: 'gender' },
                { data: 'created_at' },
                { data: 'active' },
                { data: 'action', responsivePriority: -1 },
            ],
            columnDefs: [
                // Specify columns titles here...
                { targets: 0, title: 'Account Id', orderable: true },
                { targets: 1, title: 'Name', orderable: false },
                { targets: 2, title: 'Profile Percentage', orderable: true },
                { targets: 3, title: 'Contact Number', orderable: true },
                { targets: 4, title: 'E-mail', orderable: true },
                { targets: 5, title: 'Gender', orderable: true },
                { targets: 6, title: 'Created At', orderable: true },
                { targets: 7, title: 'Ban', orderable: false },
                // Action buttons
                { targets: -1, title: 'Action',
                orderable: false },
            ],
            order: [
                [6, 'DESC']
            ],
            lengthMenu: [
                [10, 20, 50, 100],
                [10, 20, 50, 100]
            ],
            pageLength: 10,
        });
    });

    $(document).on("click", ".getpendingprofile", function (){
        oTable.draw();
    });
</script>
@endpush