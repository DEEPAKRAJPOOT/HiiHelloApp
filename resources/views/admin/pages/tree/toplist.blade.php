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
                <h3 class="card-label">Top 5 Male Tree</h3>
            </div>
        </div>
        <div class="card-body">
            {{-- Datatable Start --}}
            <table class="table table-bordered table-hover table-checkable"
                style="margin-top: 13px !important">
               <thead>
                  <tr>
                     <th>Account Id</th>
                     <th>Name</th>
                     <th>Contact Number</th>
                     <th>Total Like Recived</th>
                     <th>Total Match</th>
                  </tr>
               </thead>
               <tbody>
                  @if(count($male_users_records) > 0)
                     @foreach($male_users_records  as $val)
                     <tr>
                        <td>{{ $val['account_id'] }}</td>
                        <td>{{ $val['full_name'] }}</td>
                        <td>{{ $val['contact_no'] }}</td>
                        <td>{{ $val['total_likes'] }}</td>
                        <td>{{ $val['total_match'] }}</td>
                     <tr>
                     @endforeach
                  @endif
               </tbody>
             </table>
            {{-- Datatable End --}}
        </div>
    </div>

    <div class="card card-custom mt-10">
        <div class="card-header">
            <div class="card-title">
                <span class="card-icon">
                    <i class="fas fa-users text-primary"></i>
                </span>
                <h3 class="card-label">Top 5 Female Tree</h3>
            </div>
        </div>
        <div class="card-body">
            {{-- Datatable Start --}}
            <table class="table table-bordered table-hover table-checkable"
                style="margin-top: 13px !important">
               <thead>
                  <tr>
                     <th>Account Id</th>
                     <th>Name</th>
                     <th>Contact Number</th>
                     <th>Total Like Recived</th>
                     <th>Total Match</th>
                  </tr>
               </thead>
               <tbody>
                  @if(count($female_users_records) > 0)
                     @foreach($female_users_records  as $val)
                     <tr>
                        <td>{{ $val['account_id'] }}</td>
                        <td>{{ $val['full_name'] }}</td>
                        <td>{{ $val['contact_no'] }}</td>
                        <td>{{ $val['total_likes'] }}</td>
                        <td>{{ $val['total_match'] }}</td>
                     <tr>
                     @endforeach
                  @endif
               </tbody>
             </table>
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
                url: "{{ route('admin.usertree.listing') }}",
                data: {
                    columnsDef: ['checkbox', 'country_code', 'contact_no', 'gender','Created At', 'active', 'action'],                    
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
                  data.flgPendingProfile = $(".getpendingprofile").is(':checked') ? 1 : 0;
               }                
            },
            columns: [
                { data: 'profile_photo' },
                { data: 'verify_photo' },
                { data: 'account_id' },
                { data: 'full_name' },
                { data: 'mode_of_registration' },
                { data: 'created_at' },
                { data: 'send_total_like' },
                { data: 'received_total_like' },
                { data: 'send_total_dislikes' },
                { data: 'received_total_dislikes' },
                { data: 'total_matches' },
                { data: 'action'},
            ],
            columnDefs: [
                // Specify columns titles here...
                { targets: 0, title: 'Photo 1', orderable: false },
                { targets: 1, title: 'Photo 2', orderable: false },
                { targets: 2, title: 'Account Id', orderable: true },
                { targets: 3, title: 'Name', orderable: true },
                { targets: 4, title: 'Mode of Registration', orderable: false },
                { targets: 5, title: 'Registration Date', orderable: true },
                { targets: 6, title: 'Total like sent', orderable: false },
                { targets: 7, title: 'Total like received', orderable: false },
                { targets: 8, title: 'Total dislike sent', orderable: false },
                { targets: 9, title: 'Total dislike received', orderable: false },
                { targets: 10, title: 'Total matches', orderable: false },
                // Action buttons
                { targets: 11, title: 'Action',orderable: false },
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

        // Search button
        $('#btn_search').click(function(){
            $('#users_table').DataTable().draw();
        });
        $(document).on("click", "#btn_reset_filter", function () {
            $("#type,#search_fromdate,#search_todate").val('');
            oTable.draw();
        });
    });
    $(document).on("click", ".kt_search", function (){
        oTable.draw();
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
                        $("#total_like_male").html("<p style='font-size: 18px;'>processing..</p>");
                        $("#total_female_like").html("<p style='font-size: 18px;'>processing..</p>");
                        $("#total_male_dislikes").html("<p style='font-size: 18px;'>processing..</p>");
                        $("#total_female_dislikes").html("<p style='font-size: 18px;'>processing..</p>");
                        $("#total_male_system_match").html("<p style='font-size: 18px;'>processing..</p>");
                        $("#total_female_system_match").html("<p style='font-size: 18px;'>processing..</p>");
                        $("#total_male_org_match").html("<p style='font-size: 18px;'>processing..</p>");
                        $("#total_female_org_match").html("<p style='font-size: 18px;'>processing..</p>");
                    },
                    complete: function(){
                        $("#total_like_male").html();
                        $("#total_female_like").html();
                        $("#total_male_dislikes").html();
                        $("#total_female_dislikes").html();
                        $("#total_male_system_match").html();
                        $("#total_female_system_match").html();
                        $("#total_male_org_match").html();
                        $("#total_female_org_match").html();
                    },
                    success: function (result) {
                        if (result != '') {
                            $("#total_like_male").html(result.total_like_male);
                            $("#total_female_like").html(result.total_female_like);
                            $("#total_male_dislikes").html(result.total_male_dislikes);
                            $("#total_female_dislikes").html(result.total_female_dislikes);
                            $("#total_male_system_match").html(result.total_male_system_match);
                            $("#total_female_system_match").html(result.total_female_system_match);
                            $("#total_male_org_match").html(result.total_male_org_match);
                            $("#total_female_org_match").html(result.total_female_org_match);
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
                        $("#total_like_male").html("<p style='font-size: 18px;'>processing..</p>");
                        $("#total_female_like").html("<p style='font-size: 18px;'>processing..</p>");
                        $("#total_male_dislikes").html("<p style='font-size: 18px;'>processing..</p>");
                        $("#total_female_dislikes").html("<p style='font-size: 18px;'>processing..</p>");
                        $("#total_male_system_match").html("<p style='font-size: 18px;'>processing..</p>");
                        $("#total_female_system_match").html("<p style='font-size: 18px;'>processing..</p>");
                        $("#total_male_org_match").html("<p style='font-size: 18px;'>processing..</p>");
                        $("#total_female_org_match").html("<p style='font-size: 18px;'>processing..</p>");
                    },
                    complete: function(){
                        $("#total_like_male").html();
                        $("#total_female_like").html();
                        $("#total_male_dislikes").html();
                        $("#total_female_dislikes").html();
                        $("#total_male_system_match").html();
                        $("#total_female_system_match").html();
                        $("#total_male_org_match").html();
                        $("#total_female_org_match").html();
                    },
                    success: function (result) {
                        if (result != '') {
                            $("#total_like_male").html(result.total_like_male);
                            $("#total_female_like").html(result.total_female_like);
                            $("#total_male_dislikes").html(result.total_male_dislikes);
                            $("#total_female_dislikes").html(result.total_female_dislikes);
                            $("#total_male_system_match").html(result.total_male_system_match);
                            $("#total_female_system_match").html(result.total_female_system_match);
                            $("#total_male_org_match").html(result.total_male_org_match);
                            $("#total_female_org_match").html(result.total_female_org_match);
                        }
                    },
                });
            }
            
        });
    });
</script>

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

@endpush