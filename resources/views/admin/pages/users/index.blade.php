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
                <div class="form-check form-switch">
                  <input class="form-check-input getpendingprofile"  type="checkbox" role="switch" id="is_profile_under_review" >
                  <label class="form-check-label" for="is_profile_under_review">Profile Under Review&nbsp;&nbsp;</label>
                </div>
                {{-- 
                <a href="javascript:;" id="photo_verification"
                    class="btn btn-sm btn-primary font-weight-bolder text-uppercase mr-2">
                    <i class="fa fa-check"></i> Photo Verification
                </a>
                --}}
                <a href="javascript:;" id="update_gender"
                    class="btn btn-sm btn-primary font-weight-bolder text-uppercase mr-2">
                    <i class="far fa-edit"></i> Update Gender
                </a>
                @if (in_array('delete', $permissions))
                <a href="{{ route('admin.users.destroy', 0) }}" name="del_select" id="del_select"
                    class="btn btn-sm btn-light-danger font-weight-bolder text-uppercase mr-2 delete_all_link">
                    <i class="far fa-trash-alt"></i> Delete Selected
                </a>
                @endif
                @if (in_array('add', $permissions))
                <a href="{{ route('admin.users.create') }}"
                    class="btn btn-sm btn-primary font-weight-bolder text-uppercase">
                    <i class="fas fa-plus"></i>
                    Add {{ $custom_title }}
                </a>
                @endif

                {{-- 
                <a href="{{ route('admin.users.csv-download') }}"
                    class="btn btn-sm btn-primary font-weight-bolder text-uppercase ml-2">
                    <i class="fas fa-arrow-down"></i>
                    Download CSV
                </a>
                --}}

                <a href="{{ route('admin.users.unde-review') }}"
                    class="btn btn-sm btn-warning font-weight-bolder text-uppercase ml-2">
                    <i class="menu-icon icon-users"></i>
                    Profile Under Review
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
                        <input type='date' id='search_fromdate' class="form-control" placeholder='From date'>
                    </td>
                    <td>
                        <input type='date' id='search_todate' class="form-control" placeholder='To date'>
                    </td>
                    <td>
                        <select name="gender_filter" id="gender_filter" class="form-control">
                            <option value="">Select Gender</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
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

                    // ST - Filter Params
                    var from_date       = $("#search_fromdate").val();
                    var to_date         = $("#search_todate").val();
                    var gender_filter   = $("select[name=gender_filter] :selected").val();
                    // EN - Filter Params

                    data.from_date         = from_date;
                    data.to_date           = to_date;
                    data.gender_filter     = gender_filter;
                    data.flgPendingProfile = $(".getpendingprofile").is(':checked') ? 1 : 0;
               }                
            },
            columns: [
                { data: 'checkbox' },
                { data: 'account_id' },
                { data: 'full_name' },
                { data: 'gender' },
                { data: 'created_at' },
                { data: 'profile_percentage' },
                { data: 'contact_no' },
                { data: 'email' },                
                { data: 'city' },
                { data: 'active' },
                { data: 'action'},
            ],
            columnDefs: [
                // Specify columns titles here...
                { targets: 0, title: "<center><input type='checkbox' class='all_select'></center>", orderable: false },
                { targets: 1, title: 'Account Id', orderable: true },
                { targets: 2, title: 'Name', orderable: false },
                { targets: 3, title: 'Gender', orderable: true },
                { targets: 4, title: 'Created At', orderable: true },
                { targets: 5, title: 'Profile Percentage', orderable: true },
                { targets: 6, title: 'Contact Number', orderable: true },
                { targets: 7, title: 'E-mail', orderable: true },                
                { targets: 8, title: 'City', orderable: false },                
                { targets: 9, title: 'Ban', orderable: false },
                // Action buttons
                { targets: 10, title: 'Action',
                orderable: false },
            ],
            order: [
                [3, 'DESC']
            ],
            lengthMenu: [
                [10, 20, 50, 100],
                [10, 20, 50, 100]
            ],
            pageLength: 10
        });
    });

    $(document).on("click", ".getpendingprofile", function () {
        oTable.draw();
    });

    $(document).on("click", "#btn_search_filter", function () {
        oTable.draw();
    });

    $(document).on("click", "#btn_reset_filter", function () {
        $("#search_fromdate,#search_todate,#gender_filter").val('');
        oTable.draw();
    });

</script>
@endpush

<!-- Modal For Update Gender -->
<div class="modal fade" id="myModal" role="dialog" style="display: none;">
    <div class="modal-dialog">
    
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Update Gender</h4>
            </div>
            <div class="modal-body">
                <form method="POST" name="frm_gender" id="frm_gender" action="{{ route('admin.users.bulk_gender_update') }}">
                    <input type="hidden" name="multi_user_id" id="multi_user_id">
                    <input type="hidden" name="multi_auto_user_id" id="multi_auto_user_id">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <div class="form-group">
                        <label for="exampleFormControlSelect2">Gender</label>
                        <select name="target_gender" class="form-control">
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <span class="processing" style="display: none;">Processing...</span>
                <button type="button" class="btn btn-primary save_frm_gender">Submit</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>      
    </div>
</div>
