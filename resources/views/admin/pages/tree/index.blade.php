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
                  <a href="{{ route('admin.usertree')}}" class="btn btn-warning">Reset</a>
               </td>
             </tr>
           </table>
            {{-- Datatable Start --}}
            <table class="table table-bordered table-hover table-checkable" id="users_table"
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
                { targets: 0, title: 'Account Id', orderable: true },
                { targets: 1, title: 'Name', orderable: true },
                { targets: 2, title: 'Mode of Registration', orderable: true },
                { targets: 3, title: 'Registration Date', orderable: true },
                { targets: 4, title: 'Total like sent', orderable: false },
                { targets: 5, title: 'Total like received', orderable: false },
                { targets: 6, title: 'Total dislike sent', orderable: false },
                { targets: 7, title: 'Total dislike received', orderable: false },
                { targets: 8, title: 'Total matches', orderable: false },
                // Action buttons
                { targets: 9, title: 'Action',orderable: false },
            ],
            order: [
                [3, 'DESC']
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
    });

    // $(document).on("click", ".usermatchmodel", function (){
    //     // datatable
    //     oTable = $('#user_match_table').DataTable({
    //         responsive: true,
    //         searchDelay: 500,
    //         processing: true,
    //         serverSide: true,
    //         ajax: {
    //             url: "{{ route('admin.usertree.usermatchlisting') }}",
    //             data: {
    //                 columnsDef: ['checkbox', 'country_code', 'contact_no', 'gender','Created At', 'active', 'action'],                    
    //             },
    //             data: function(data) {
    //                 var user_id = $('.usermatchmodel').attr('data-id');
    //                 data.user_id = user_id;
    //                 data.flgPendingProfile = $(".getpendingprofile").is(':checked') ? 1 : 0;
    //            }                
    //         },
    //         columns: [
    //             { data: 'full_name' },
    //             { data: 'gender' },
    //             { data: 'created_at' },
    //         ],
    //         columnDefs: [
    //             // Specify columns titles here...
    //             { targets: 0, title: 'User name', orderable: true },
    //             { targets: 1, title: 'Gender', orderable: true },
    //             { targets: 2, title: 'Match date', orderable: true },
    //         ],
    //         order: [
    //             [2, 'DESC']
    //         ],
    //         lengthMenu: [
    //             [10, 20, 50, 100],
    //             [10, 20, 50, 100]
    //         ],
    //         pageLength: 10,
    //     });

    // });

    $(document).on("click", ".kt_search", function (){
        oTable.draw();
    });

    $(document).ready(function () {
        $("#search_fromdate_td").css("display","none");
        $("#search_todate_td").css("display","none");
        $("#btn_search").hide();
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
    });
</script>
@endpush