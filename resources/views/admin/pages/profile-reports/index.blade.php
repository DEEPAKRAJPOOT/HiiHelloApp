@extends('admin.layouts.app')

@push('breadcrumb')
    {!! Breadcrumbs::render('profile_reports_list') !!}
@endpush

@push('extra-css-styles')
<link rel="stylesheet" type="text/css" href="{{ asset('assets/plugins/custom/datatables/datatables.bundle.css') }}" />
@endpush

@section('content')
<div class="container">

    <div class="row">
        <div class="col-lg-3">
            <h3 class="card-label">Filter </h3>
        </div>
        <div class="col-lg-9" style="display: inline-flex; float: right;justify-content:end;">
                <select class="form-control" name="filter_type" id="filter_type" style="margin-bottom: 10px;width: 20%;">
                  <option value="">-- select filter type --</option>
                  <option value="1">Today</option>
                  <option value="2">This week</option>
                  <option value="3">This month</option>
                  <option value="4">This year</option>
                  <option value="5">Custom date range</option>
              </select>
              <input type='date' id='fromdate_search' class="form-control ml-2" placeholder='From date' style="width: 20%;">
              <input type='date' id='todate_search' class="form-control ml-2" placeholder='To date' style="width: 20%;">
              <input type='button' class="btn btn-primary mr-2 ml-2" id="filter_type_btn_search" value="Search" style="height: 38px !important;">
        </div>
        <input type="hidden" name="filter_type_url" id="filter_type_url" value="{{ route('admin.profile-reports.filters') }}">
    </div>
    <div class="row">
        <div class="col-lg-3">
           <div class="card card-custom rounded-xl gutter-b bg-dark card-stretch">
              <div class="d-flex align-items-center mr-2">
                 <div class="symbol-label px-6 py-8 rounded-xl mr-7">
                    <span class="svg-icon svg-icon-4x svg-icon-white d-block my-2">
                       <!--begin::Svg Icon | path:/metronic/theme/html/demo1/dist/assets/media/svg/icons/Communication/Group.svg-->
                       <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                          <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                             <polygon points="0 0 24 0 24 24 0 24"></polygon>
                             <path d="M18,14 C16.3431458,14 15,12.6568542 15,11 C15,9.34314575 16.3431458,8 18,8 C19.6568542,8 21,9.34314575 21,11 C21,12.6568542 19.6568542,14 18,14 Z M9,11 C6.790861,11 5,9.209139 5,7 C5,4.790861 6.790861,3 9,3 C11.209139,3 13,4.790861 13,7 C13,9.209139 11.209139,11 9,11 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"></path>
                             <path d="M17.6011961,15.0006174 C21.0077043,15.0378534 23.7891749,16.7601418 23.9984937,20.4 C24.0069246,20.5466056 23.9984937,21 23.4559499,21 L19.6,21 C19.6,18.7490654 18.8562935,16.6718327 17.6011961,15.0006174 Z M0.00065168429,20.1992055 C0.388258525,15.4265159 4.26191235,13 8.98334134,13 C13.7712164,13 17.7048837,15.2931929 17.9979143,20.2 C18.0095879,20.3954741 17.9979143,21 17.2466999,21 C13.541124,21 8.03472472,21 0.727502227,21 C0.476712155,21 -0.0204617505,20.45918 0.00065168429,20.1992055 Z" fill="#000000" fill-rule="nonzero"></path>
                          </g>
                       </svg>
                       <!--end::Svg Icon-->
                    </span>
                    <span class="font-size-h6 text-muted font-weight-bold">Total Block Users</span>
                 </div>
                 <div>
                    <div class="font-size-h1 text-white font-weight-bolder" id="total_block_user"></div>
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
                             <polygon points="0 0 24 0 24 24 0 24"></polygon>
                             <path d="M18,14 C16.3431458,14 15,12.6568542 15,11 C15,9.34314575 16.3431458,8 18,8 C19.6568542,8 21,9.34314575 21,11 C21,12.6568542 19.6568542,14 18,14 Z M9,11 C6.790861,11 5,9.209139 5,7 C5,4.790861 6.790861,3 9,3 C11.209139,3 13,4.790861 13,7 C13,9.209139 11.209139,11 9,11 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"></path>
                             <path d="M17.6011961,15.0006174 C21.0077043,15.0378534 23.7891749,16.7601418 23.9984937,20.4 C24.0069246,20.5466056 23.9984937,21 23.4559499,21 L19.6,21 C19.6,18.7490654 18.8562935,16.6718327 17.6011961,15.0006174 Z M0.00065168429,20.1992055 C0.388258525,15.4265159 4.26191235,13 8.98334134,13 C13.7712164,13 17.7048837,15.2931929 17.9979143,20.2 C18.0095879,20.3954741 17.9979143,21 17.2466999,21 C13.541124,21 8.03472472,21 0.727502227,21 C0.476712155,21 -0.0204617505,20.45918 0.00065168429,20.1992055 Z" fill="#000000" fill-rule="nonzero"></path>
                          </g>
                       </svg>
                       <!--end::Svg Icon-->
                    </span>
                    <span class="font-size-h6 text-muted font-weight-bold">Total Report Users</span>
                 </div>
                 <div>
                    <div class="font-size-h1 text-white font-weight-bolder"id="total_report_users"></div>
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
                @if (in_array('delete', $permissions))
                    <a href="{{ route('admin.profile-reports.destroy', 0) }}" name="del_select" id="del_select" class="btn btn-sm btn-light-danger font-weight-bolder text-uppercase mr-2 delete_all_link">
                        <i class="far fa-trash-alt"></i> Delete Selected
                    </a>
                @endif
                @if (in_array('add', $permissions))
                    <a href="{{ route('admin.profile-reports.create') }}" class="btn btn-sm btn-primary font-weight-bolder text-uppercase">
                        <i class="fas fa-plus"></i>
                        Add {{ $custom_title }}
                    </a>
                @endif
                <a href="{{ route('admin.profile-report.csv-download') }}"
                class="btn btn-sm btn-primary font-weight-bolder text-uppercase ml-2">
                <i class="fas fa-arrow-down"></i>
                Download CSV
            </a>
            </div>
        </div>
        <div class="card-body">
            <!-- Date Filter -->
            <table class="mb-5">
            <tr style="display: inline-flex;">
                <td>
                  <select class="form-control" name="type" id="type">
                      <option value="">-- select filter type --</option>
                      <option value="1">Today</option>
                      <option value="2">This week</option>
                      <option value="3">This month</option>
                      <option value="4">This year</option>
                      <option value="5">Custom date range</option>
                  </select>
                </td>
               <td id="search_fromdate_td">
                  <input type='date' id='search_fromdate' class="form-control" placeholder='From date'>
               </td>
               <td id="search_todate_td">
                  <input type='date' id='search_todate' class="form-control" placeholder='To date'>
               </td>
               <td>
                  <input type='button' class="btn btn-primary mr-1 ml-1" id="btn_search" value="Search">
               </td>
               <td>
                  <a href="javascript:;" class="btn btn-warning" id="btn_reset_filter">Reset</a>
               </td>
             </tr>
           </table>
            {{--  Datatable Start  --}}
            <table class="table table-bordered table-hover table-checkable" id="prfile_reports_table" style="margin-top: 13px !important"></table>
            {{--  Datatable End  --}}
        </div>
    </div>
</div>

<!-- Modal-->
<div class="modal fade" id="userreportmodel" tabindex="-1" role="dialog" aria-labelledby="usermatchsmodelLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="usermatchsmodelLabel">User report data</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <table class="table table-striped table-bordered nowrap" id="user_profile_report_table">
                    <thead>
                        <tr>
                            <th title="Field #1">User name</th>
                            <th title="Field #2">Gender</th>
                            <th title="Field #3">Message</th>
                            <th title="Field #4">Date</th>
                        </tr>
                    </thead>
                    <tbody id="user_profile_report_table_body">
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div id="myimageModal" class="modal">

    <!-- The Close Button -->
    <span class="close">&times;</span>

    <!-- Modal Content (The Image) -->
    <div class="row">
        <div class="col-md-12">
            <img class="modal-content" id="img01">
        </div>
    </div>

    <!-- Modal Caption (Image Text) -->
    <div id="caption"></div>

</div>
@endsection

@push('extra-js-scripts')
<script src="{{ asset('assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
<script>
    $(document).ready(function () {
        // datatable
        oTable = $('#prfile_reports_table').DataTable({
            responsive: true,
            searchDelay: 500,
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('admin.profile-reports.listing') }}",
                data: {
                    columnsDef: ['user_id', 'reported_user_id', 'message', 'action'],
                },
                data: function(data) {
                  // Read values
                  var filter_types = $('#type').val();
                  var from_date = $('#search_fromdate').val();
                  var to_date = $('#search_todate').val();

                  // Append to data
                  data.from_date = from_date;
                  data.to_date = to_date;
                  data.filter_types = filter_types;
               }      
            },
            columns: [
                { data: 'profile_photo' },
                { data: 'account_id' },
                { data: 'full_name' },
                { data: 'gender' },
                { data: 'contact_no' },
                { data: 'active' },
                { data: 'total_block' },
                { data: 'total_reports' },
                { data: 'action'},
            ],
            columnDefs: [
                // Specify columns titles here...
                { targets: 0, title: 'Photo 1', orderable: true },
                { targets: 1, title: 'Account Id', orderable: true },
                { targets: 2, title: 'Full name', orderable: true },
                { targets: 3, title: 'Gender', orderable: true },
                { targets: 4, title: 'Contact no', orderable: true },
                { targets: 5, title: 'Ban', orderable: false },
                { targets: 6, title: 'Block', orderable: false },
                { targets: 7, title: 'Report', orderable: false },
                { targets: 8, title: 'Action',orderable: false },
            ],
            order: [
                [3, 'DESC']
            ],
            lengthMenu: [
                [10, 50, 100, 250, 500],
                [10, 50, 100, 250, 500]
            ],
            pageLength: 10,
        });

        // Search button
        $('#btn_search').click(function(){
            $('#prfile_reports_table').DataTable().draw();
        });
        $(document).on("click", "#btn_reset_filter", function () {
            $("#type,#search_fromdate,#search_todate").val('');
            $("#search_fromdate_td").css("display","none");
            $("#search_todate_td").css("display","none");
            $("#btn_search").hide();
            oTable.draw();
        });
    });

    $(document).ready(function () {
        $("#search_fromdate_td").css("display","none");
        $("#search_todate_td").css("display","none");
        $("#btn_search").hide();

        $("#filter_type_btn_search").hide();
        $("#fromdate_search").css("display","none");
        $("#todate_search").css("display","none");
        $(document).on("change", "#type", function (e) {
            $("#btn_search").show();
            var type = $("#type").val();
            if (type == 1 || type == 2 || type == 3 || type == 4) {
                $("#search_fromdate_td").css("display","none");
                $("#search_todate_td").css("display","none");
            }
            if (type == 5) {
                $("#search_fromdate_td").css("display","block");
                $("#search_todate_td").css("display","block");
            }
        });

        $(document).on("change", "#filter_type", function (e) {
            $("#filter_type_btn_search").show();
            var filter_type = $("#filter_type").val();
            if (filter_type == 1 || filter_type == 2 || filter_type == 3 || filter_type == 4) {
                $("#fromdate_search").css("display","none");
                $("#todate_search").css("display","none");
            }
            if (filter_type == 5) {
                $("#fromdate_search").css("display","block");
                $("#todate_search").css("display","block");
            }
        });
    });
</script>
<script type="text/javascript">
    $(document).ready(function () {
            var filter_type_url     = $("#filter_type_url").val();
            if (filter_type_url != '') {
                $.ajax({
                    url: filter_type_url,
                    type: "POST",
                    dataType: "json",
                    data: {
                        _token: $("meta[name='csrf-token']").attr("content"),
                        filter_type: 6,
                    },
                    cache: false,
                    beforeSend: function(){
                        $("#total_block_user").html("<p style='font-size: 18px;'>processing..</p>");
                        $("#total_report_users").html("<p style='font-size: 18px;'>processing..</p>");
                    },
                    complete: function(){
                        $("#total_block_user").html();
                        $("#total_report_users").html();
                    },
                    success: function (result) {
                        if (result != '') {
                            $("#total_block_user").html(result.total_block_user);
                            $("#total_report_users").html(result.total_report_users);
                        }
                    },
                });
            }


        $(document).on("click", "#filter_type_btn_search", function (e) {
            var filter_type         = $("#filter_type").val();
            var filter_type_url     = $("#filter_type_url").val();
            var fromdate_search     = $("#fromdate_search").val();
            var todate_search       = $("#todate_search").val();

            if (filter_type != '' && filter_type_url != '') {
                $.ajax({
                    url: filter_type_url,
                    type: "POST",
                    dataType: "json",
                    data: {
                        _token: $("meta[name='csrf-token']").attr("content"),
                        filter_type: filter_type,
                        fromdate_search: fromdate_search,
                        todate_search: todate_search,
                    },
                    cache: false,
                    beforeSend: function(){
                        $("#total_block_user").html("<p style='font-size: 18px;'>processing..</p>");
                        $("#total_report_users").html("<p style='font-size: 18px;'>processing..</p>");
                    },
                    complete: function(){
                        $("#total_block_user").html();
                        $("#total_report_users").html();
                    },
                    success: function (result) {
                        if (result != '') {
                            $("#total_block_user").html(result.total_block_user);
                            $("#total_report_users").html(result.total_report_users);
                        }
                    },
                });
            }
            
        });
    });
</script>
@endpush

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