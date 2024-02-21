@extends('admin.layouts.app')

@push('breadcrumb')
    {!! Breadcrumbs::render('locations_list') !!}
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
                    <a href="{{ route('admin.locations.destroy', 0) }}" name="del_select" id="del_select" class="btn btn-sm btn-light-danger font-weight-bolder text-uppercase mr-2 delete_all_link">
                        <i class="far fa-trash-alt"></i> Delete Selected
                    </a>
                @endif
                @if (in_array('add', $permissions))
                    <a href="{{ route('admin.locations.create') }}" class="btn btn-sm btn-primary font-weight-bolder text-uppercase">
                        <i class="fas fa-plus"></i>
                        Add {{ $custom_title }}
                    </a>
                @endif
                <a href="{{ route('admin.locations.duplicate-location') }}"
                class="btn btn-sm btn-primary font-weight-bolder text-uppercase ml-2">
                    <i class="fas fa-clone"></i>
                    Duplicate Locations
                </a>
                {{--<a href="{{ route('admin.locations.merge-location') }}"
                class="btn btn-sm btn-primary font-weight-bolder text-uppercase ml-2">
                    <i class="fas fa-location-arrow"></i>
                    Merge Locations
                </a>--}}

                <a href="{{ route('admin.location.csv-download') }}"
                class="btn btn-sm btn-primary font-weight-bolder text-uppercase ml-2">
                    <i class="fas fa-arrow-down"></i>
                    Download CSV
                </a>

                <a href="{{ route('admin.location.user-location-csv-download') }}"
                class="btn btn-sm btn-primary font-weight-bolder text-uppercase ml-2">
                    <i class="fas fa-arrow-down"></i>
                    Download User location CSV
                </a>
                <a href="{{ route('admin.location.user-not-location-csv-download') }}"
                class="btn btn-sm btn-primary font-weight-bolder text-uppercase ml-2">
                    <i class="fas fa-arrow-down"></i>
                    Download Not location Id Assign
                </a>
            </div>
        </div>
        <div class="row">
        <div class="col-sm-6" >
        {{-- Filter Start --}}
            <table class="mb-5" align="left" style="margin-left:14px;margin: top 15px;">
                <tr>
                    <td>
                        <span class="card-icon">
                            <i class="fa fa-filter text-primary"></i>
                        </span>
                        <label>Filter:&nbsp;&nbsp;</label>
                    </td>
                    <td>
                        <select name="location_filter" id="location_filter" class="form-control">
                            <option value="">Filter Location</option>
                            <option value=1>All</option>
                            <option value=2>Dublicates</option>
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
</div>
</div>
        <div class="card-body">
            {{--  Datatable Start  --}}
            <table class="table table-bordered table-hover table-checkable" id="users_table" style="margin-top: 13px !important"></table>
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
                url: "{{ route('admin.locations.listing') }}",
                data: {
                    columnsDef: ['checkbox','name', 'active','Created At','action'],
                },
                data: function(data) {
                    
                    var location_filter = $("#location_filter").val();
                    data.location_filter       = location_filter;
                },
                dataSrc: function(response){
                    console.log(response.data);
                    var locations;
                    for ( var i=0, ien=response.data.length ; i<ien ; i++ ) {
                        response.data[i];
                        var locations = '<table cellpadding="5" cellspacing="0" border="0" style="padding-left:50px;">';
                        locations += '<tr><th></th><th>Location ID</th><th>Location Type</th><th>Name</th><th>Total Users</th><th>Click To Merge</th></tr>';
                        for(var j=0, jen=response.data[i].locationByUser.length;j<jen;j++){
                            var locType = '';
                            if(typeof response.data[i].locationByUser[j].location_type !== 'undefined'){
                                locType = response.data[i].locationByUser[j].location_type;
                            }
                            locations += '<tr>' +
                            '<td></td><td>'+ response.data[i].locationByUser[j].location_id+'</td><td>'+locType+'</td><td>'+response.data[i].locationByUser[j].name+'</td><td>'+response.data[i].locationByUser[j].total+'</td><td><a href="#" class="btn btn-sm btn-primary font-weight-bolder text-uppercase ml-2"><i class="fas fa-location-arrow"></i>Merge Locations</a></td><td></td>';
                            // Nested table content
                            // if(typeof response.data[i].locationByUser[j].location_type !== 'undefined'){
                            //     locations +=
                            // }

                            locations += '</tr>';
                        }
            
                        locations += '</table>';
                        response.data[i].hello = locations;
                        console.log(response.data[i].hello);

                    }
                    return response.data;
                },
            },
            columns: [
                { data: 'checkbox' },
                { data: 'id' },
                { data: 'name' },
                { data: 'state' },
                { data: 'active' },
                {data: 'created_at'},
                {data: 'duplicates'},
                {data: 'mergeToId'},
                {data: 'hello'},
                { data: 'action', responsivePriority: -1 },
            ],
            columnDefs: [
                // Specify columns titles here...
                { targets: 0, title: "<center><input type='checkbox' class='all_select'></center>", orderable: false },
                { targets: 1, title: 'Id', orderable: true },
                { targets: 2, title: 'Name', orderable: false },
                { targets: 3, title: 'State Name', orderable: false },
                { targets: 4, title: 'Active', orderable: false },
                { targets: 5, title: 'Created At', orderable: true },
                { targets: 6, title: 'Total Dublicates', orderable: false, searchable: false },
                { targets: 7, title: 'Merge with location id', orderable: false, searchable: false },
                { targets: 8, title: 'Dublicates Locations Details', orderable: false, searchable: false },
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
    });

    $(document).on("click", "#btn_search_filter", function () {
        oTable.draw();
    });
    $(document).on("click", "#btn_reset_filter", function () {
        $("#location_filter").val('');
        oTable.draw();
    });
</script>
@endpush
