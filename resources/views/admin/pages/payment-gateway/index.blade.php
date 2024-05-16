
@extends('admin.layouts.app')

@push('breadcrumb')
    {!! Breadcrumbs::render('paymentgateway_list') !!}
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
        </div>
        <div class="row">
        <div class="col-sm-12" >
        {{-- Filter Start --}}
           
        {{-- Filter End --}}
</div>
</div>
        <div class="card-body">
            {{--  Datatable Start  --}}
            <table class="table table-bordered table-hover " id="users_table" style="margin-top: 13px !important"></table>
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
        oTable = $('#users_table').DataTable({
            responsive: true,
            searchDelay: 500,
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('admin.paymentgateway.listing') }}",
                data: {
                    columnsDef: ['name', 'active','action'],
                },
                dataSrc: function(response){
                    console.log(response.data);
                    // var locations;
                    
                    return response.data;
                },
            },
            columns: [
                { data: 'id' },
                { data: 'name' },
                { data: 'active' },
                // { data: 'action', responsivePriority: -1 },
            ],
            columnDefs: [
                // Specify columns titles here...
                { targets: 0, title: 'Id', orderable: true },
                { targets: 1, title: 'Name', orderable: false },
                { targets: 2, title: 'Active', orderable: false },
                // { targets: -1, title: 'Action',
                // orderable: false },
               
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
    });

    // $(document).on("click", "#btn_search_filter", function () {
    //     oTable.draw();
    // });
    // $(document).on("click", "#btn_reset_filter", function () {
    //     $("#location_filter").val('');
    //     oTable.draw();
    // });
</script>
@endpush
