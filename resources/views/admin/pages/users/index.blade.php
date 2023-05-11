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
                <h3 class="card-label">
                    @if(request()->get('user_filter') == 'deleted')
                    Deleted
                    @endif
                    @if(request()->get('user_filter') == 'test_users')
                    Test
                    @endif
                    @if(empty(request()->get('user_filter')))
                    All
                    @endif
                    {{ $custom_title }}
                    @if(request()->get('user_filter') == 'photo_under_review')
                    with Photo Under Review
                    @endif
                    @if(request()->get('user_filter') == 'email_under_review')
                    with Email Under Review
                    @endif
                </h3>
            </div>

            <div class="card-toolbar">

                @if(request()->get('user_filter') == 'photo_under_review')
                    <a href="javascript:void(0);" id="photo_verification" class="btn btn-sm btn-primary font-weight-bolder text-uppercase mr-2">
                        <i class="fa fa-check"></i> Verify Photo
                    </a>
                @endif
                @if(request()->get('user_filter') == 'email_under_review')
                    <a href="javascript:void(0);" id="email_verification" class="btn btn-sm btn-primary font-weight-bolder text-uppercase mr-2">
                        <i class="fa fa-check"></i> Verify Email
                    </a>
                @endif
                <a id="update_gender" href="javascript:void(0)" class="btn btn-sm btn-primary font-weight-bolder text-uppercase mr-2">
                    <i class="far fa-edit"></i> Update Gender
                </a>
                @if(request()->get('user_filter') != 'deleted')
                    @if (in_array('delete', $permissions))
                    <a href="{{ route('admin.users.destroy', 0) }}" name="del_select" id="del_select"
                        class="btn btn-sm btn-light-danger font-weight-bolder text-uppercase mr-2 delete_all_link">
                        <i class="far fa-trash-alt"></i> Delete Selected
                    </a>
                    @endif
                @endif

                @if(!empty(request()->get('user_filter')))
                    <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-success font-weight-bolder text-uppercase ml-2">
                        <i class="menu-icon icon-users"></i>
                        All Users
                    </a>
                @endif

                @if(request()->get('user_filter') != 'photo_under_review')
                    <a href="{{ route('admin.users.index').'?user_filter=photo_under_review' }}" class="btn btn-sm btn-warning font-weight-bolder text-uppercase ml-2">
                        <i class="menu-icon icon-users"></i>
                        Photo Under Review
                    </a>
                @endif
                @if(request()->get('user_filter') != 'email_under_review')
                    <a href="{{ route('admin.users.index').'?user_filter=email_under_review' }}" class="btn btn-sm btn-warning font-weight-bolder text-uppercase ml-2">
                        <i class="menu-icon icon-users"></i>
                        Email Under Review
                    </a>
                @endif

                @if(request()->get('user_filter') != 'deleted')
                    <a href="{{ route('admin.users.index').'?user_filter=deleted' }}" class="btn btn-sm btn-danger font-weight-bolder text-uppercase ml-2">
                        <i class="menu-icon icon-users"></i>
                        Deleted Users
                    </a>
                @endif

                @if(request()->get('user_filter') != 'test_users')
                    <a href="{{ route('admin.users.index').'?user_filter=test_users' }}" class="btn btn-sm btn-primary font-weight-bolder text-uppercase ml-2">
                        <i class="menu-icon icon-users"></i>
                        Test Users
                    </a>
                @endif

                {{-- @if (in_array('add', $permissions))
                <a href="{{ route('admin.users.create') }}" class="btn btn-sm btn-primary font-weight-bolder text-uppercase">
                    <i class="fas fa-plus"></i>
                    Add {{ $custom_title }}
                </a>
                @endif --}}

                {{-- <a href="{{ route('admin.users.csv-download') }}"
                    class="btn btn-sm btn-primary font-weight-bolder text-uppercase ml-2">
                    <i class="fas fa-arrow-down"></i>
                    Download CSV
                </a> --}}
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
                        <input type='text' id='profile_percentage' class="form-control" placeholder='profile percentage'>
                    </td>
                    <td>
                        <select name="city_filter" id="city_filter" class="form-control">
                            <option value="">Select City</option>
                            @foreach($locations as $location)
                                <option value="{{ $location['loc_ids'] }}">{{ $location['name'].' (' .$location['user_count'].')' }}</option> 
                            @endforeach
                        </select>
                    </td>
                    <td>
                        <select name="state_filter" id="state_filter" class="form-control">
                            <option value="">Select State</option>
                            @foreach($states as $state)
                                <option value="{{ $state['loc_ids'] }}">{{ $state['state'].' (' .$state['user_count'].')' }}</option> 
                            @endforeach
                        </select>
                    </td>
                    <td>
                        <select name="status_filter" id="status_filter" class="form-control">
                            <option value="">Select Status</option>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
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
                url: "{{ route('admin.users.listing') }}?{!! http_build_query(request()->query()) !!}",
                data: {
                    columnsDef: ['checkbox', 'country_code', 'contact_no', 'gender','Created At', 'active', 'action'],                    
                },
                data: function(data) {                    

                    // ST - Filter Params
                    var from_date       = $("#search_fromdate").val();
                    var to_date         = $("#search_todate").val();
                    var gender_filter   = $("select[name=gender_filter] :selected").val();
                    var profile_percentage = $("#profile_percentage").val();
                    var city_filter = $("#city_filter").val();
                    var status_filter = $("#status_filter").val();
                    // EN - Filter Params
                    data.from_date         = from_date;
                    data.to_date           = to_date;
                    data.gender_filter     = gender_filter;
                    data.profile_percentage= profile_percentage;
                    data.city_filter       = city_filter;
                    data.status_filter     = status_filter;
                    data.state_filter = $('#state_filter').val();
               }                
            },
            columns: [
                { data: 'checkbox' },
                { data: 'profile_photo' },
                { data: 'verify_photo' },
                { data: 'account_id' },
                { data: 'full_name' },
                { data: 'gender' },
                { data: 'created_at' },
                { data: 'profile_percentage' },
                { data: 'contact_no' },
                { data: 'email' },                
                { data: 'city' },
                { data: 'device_app_version' },
                { data: 'lat_long' },
                { data: 'active' },
                { data: 'user_status' },
                { data: 'action'},
            ],
            columnDefs: [
                // Specify columns titles here...
                { targets: 0, title: "<center><input type='checkbox' class='all_select'></center>", orderable: false },
                { targets: 1, title: 'Photo 1', orderable: false },
                { targets: 2, title: 'Photo 2', orderable: false },
                { targets: 3, title: 'Account Id', orderable: true },
                { targets: 4, title: 'Name', orderable: false },
                { targets: 5, title: 'Gender', orderable: true },
                { targets: 6, title: 'Created At', orderable: true },
                { targets: 7, title: 'Percentage', orderable: true },
                { targets: 8, title: 'Number', orderable: true },
                { targets: 9, title: 'E-mail', orderable: true },                
                { targets: 10, title: 'City', orderable: false },                
                { targets: 11, title: 'Device/version', orderable: true },                
                { targets: 12, title: 'Lat/Long', orderable: false },
                { targets: 13, title: 'Ban', orderable: false },
                { targets: 14, title: 'User Status', orderable: false },
                // Action buttons
                { targets: 15, title: 'Action',orderable: false },
            ],
            order: [
                [6, 'DESC']
            ],
            lengthMenu: [
                [10, 20, 50, 100,250,500],
                [10, 20, 50, 100,250,500]
            ],
            pageLength: 10
        });
    });

    $(document).on("click", "#btn_search_filter", function () {
        oTable.draw();
    });

    $(document).on("click", "#btn_reset_filter", function () {
        $("#search_fromdate,#search_todate,#gender_filter,#profile_percentage").val('');
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


<!-- Modal For Photo Verification -->
<div class="modal fade" id="myModalPhotoVerification" role="dialog" style="display: none;">
    <div class="modal-dialog">
    
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Photo Verification</h4>
            </div>
            <div class="modal-body">
                <form method="POST" name="frm_photo_verification" id="frm_photo_verification" action="{{ route('admin.users.bulk_photo_verification') }}">
                    <input type="hidden" name="multi_user_id" id="multi_user_id">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <div class="form-group">
                        <label for="exampleFormControlSelect2">Photo Verification Status</label>
                        <select name="verify_photo_status" id="verify_photo_status" class="form-control">
                            <option value="under_review">Under Review</option>
                            <option value="verified">Verified</option>
                            <option value="unverified" selected>UnVerified</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <span class="processing" style="display: none;">Processing...</span>
                <button type="button" class="btn btn-primary save_frm_photo_verification">Submit</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>      
    </div>
</div>

<!-- Modal For Email Verification -->
<div class="modal fade" id="myModalEmailVerification" role="dialog" style="display:none">
    <div class="modal-dialog">
    
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Email Verification</h4>
            </div>
            <div class="modal-body">
                <form method="POST" name="frm_email_verification" id="frm_email_verification" action="{{ route('admin.users.bulk_email_verification') }}">
                    <input type="hidden" name="multi_user_email_id" id="multi_user_email_id">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <div class="form-group">
                        <label>Email Verification Status</label>
                        <select name="verify_email_status" id="verify_email_status" class="form-control">
                            <option value="verified">Verified</option>
                            <option value="unverified" selected="selected">UnVerified</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <span class="processing" style="display: none;">Processing...</span>
                <button type="button" class="btn btn-primary save_frm_email_verification">Submit</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>      
    </div>
</div>


<div id="myimageModal" class="modal">

    <!-- The Close Button -->
    <span class="close">&times;</span>

    <!-- Modal Content (The Image) -->
    <div class="row">
        <div class="col-md-6">
            <img class="modal-content" id="img01">
        </div>
        <div class="col-md-6">
            <img class="modal-content" id="img02">
        </div>
    </div>

    <!-- Modal Caption (Image Text) -->
    <div id="caption"></div>

</div>

<style type="text/css">
    #myImg {
  border-radius: 5px;
  cursor: pointer;
  transition: 0.3s;
}

#myImg:hover {opacity: 0.7;}

/* The Modal (background) */
#myimageModal {
  display: none; /* Hidden by default */
  position: fixed; /* Stay in place */
  z-index: 99999; /* Sit on top */
  padding-top: 100px; /* Location of the box */
  left: 0;
  top: 0;
  width: 100%; /* Full width */
  height: 100%; /* Full height */
  overflow: auto; /* Enable scroll if needed */
  background-color: rgb(0,0,0); /* Fallback color */
  background-color: rgba(0,0,0,0.9); /* Black w/ opacity */
}

/* Modal Content (image) */
#myimageModal .modal-content {
  margin: auto;
  display: block;
  width: 80%;
  max-width: 700px;
}

/* Caption of Modal Image */
#myimageModal #caption {
  margin: auto;
  display: block;
  width: 80%;
  max-width: 700px;
  text-align: center;
  color: #ccc;
  padding: 10px 0;
  height: 150px;
}

/* Add Animation */
#myimageModal .modal-content, #caption {  
  -webkit-animation-name: zoom;
  -webkit-animation-duration: 0.6s;
  animation-name: zoom;
  animation-duration: 0.6s;
}

@-webkit-keyframes zoom {
  from {-webkit-transform:scale(0)} 
  to {-webkit-transform:scale(1)}
}

@keyframes zoom {
  from {transform:scale(0)} 
  to {transform:scale(1)}
}

/* The Close Button */
#myimageModal .close {
  position: absolute;
  top: 15px;
  right: 35px;
  color: #f1f1f1;
  font-size: 40px;
  font-weight: bold;
  transition: 0.3s;
}

#myimageModal .close:hover,
#myimageModal .close:focus {
  color: #bbb;
  text-decoration: none;
  cursor: pointer;
}

/* 100% Image Width on Smaller Screens */
@media only screen and (max-width: 700px){
  #myimageModal .modal-content {
    width: 100%;
  }

</style>