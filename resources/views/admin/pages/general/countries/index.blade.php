@extends('admin.layouts.app')
@push('breadcrumb')
    {!! Breadcrumbs::render('countries_list') !!}
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
                @if (in_array('delete', $permissions))
                    <a href="{{ route('admin.countries.destroy', 0) }}" name="del_select" id="del_select" class="btn btn-sm btn-light-danger font-weight-bolder text-uppercase mr-2 delete_all_link" data-module="{{strtolower($custom_title)}}" data-route-name="{{ $routeName }}">
                        <i class="far fa-trash-alt"></i> Delete Selected
                    </a>
                @endif
                @if (in_array('add', $permissions))
                    <a href="{{ route('admin.countries.create') }}" class="btn btn-sm btn-primary font-weight-bolder text-uppercase">
                        <i class="fas fa-plus"></i>
                        Add {{ str_singular($custom_title) }}
                    </a>
                 @endif
            </div>

        </div>
        <div class="card-body">
            {{-- Datatable Start  --}}
            <table class="table table-bordered table-hover table-checkable" id="countries_table" style="margin-top: 13px !important"></table>
            {{-- Datatable End  --}}
        </div>
    </div>
</div>
@endsection

@push('extra-js-scripts')
<script src="{{ asset('assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
<script>
    $(document).ready(function() {
        // datatable
        oTable = $('#countries_table').DataTable({
            responsive: true,
            searchDelay: 500,
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('admin.countries.listing') }}",
                data: {
                    columnsDef: ['checkbox', 'name', 'code', 'phonecode', 'active', 'action'],
                },
            },
            columns: [{
                    data: 'checkbox'
                },
                {
                    data: 'name'
                },
                {
                    data: 'code'
                },
                {
                    data: 'phonecode'
                },
                {
                    data: 'active'
                },
                {
                    data: 'action',
                    responsivePriority: -1
                },
            ],
            columnDefs: [
                // Specify columns titles here...
                {
                    targets: 0,
                    title: "<center><input type='checkbox' class='all_select'></center>",
                    orderable: false
                },
                {
                    targets: 1,
                    title: 'Name',
                    orderable: true
                },
                {
                    targets: 2,
                    title: 'Code',
                    orderable: true
                },
                {
                    targets: 3,
                    title: 'Phonecode',
                    orderable: true
                },
                {
                    targets: 4,
                    title: 'Active',
                    orderable: false
                },
                // Action buttons
                {
                    targets: -1,
                    title: 'Action',
                    orderable: false
                },
            ],
            order: [
                [1, 'asc']
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