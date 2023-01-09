@extends('admin.layouts.app')

@push('breadcrumb')
{!! Breadcrumbs::render('image_log_list') !!}
@endpush

@push('extra-css-styles')
<link rel="stylesheet" type="text/css" href="{{ asset('assets/plugins/custom/datatables/datatables.bundle.css') }}" />
@endpush

@section('content')
<div class="container">
    <div class="row">
        <div class="col-lg-3">
           <div class="card card-custom rounded-xl gutter-b bg-dark card-stretch">
              <div class="d-flex align-items-center mr-2">
                 <div class="symbol-label px-6 py-8 rounded-xl mr-7">
                   <span class="svg-icon svg-icon-4x svg-icon-white d-block my-2"><!--begin::Svg Icon | path:/var/www/preview.keenthemes.com/metronic/releases/2021-05-14-112058/theme/html/demo4/dist/../src/media/svg/icons/Design/Image.svg--><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
        <polygon points="0 0 24 0 24 24 0 24"/>
        <path d="M6,5 L18,5 C19.6568542,5 21,6.34314575 21,8 L21,17 C21,18.6568542 19.6568542,20 18,20 L6,20 C4.34314575,20 3,18.6568542 3,17 L3,8 C3,6.34314575 4.34314575,5 6,5 Z M5,17 L14,17 L9.5,11 L5,17 Z M16,14 C17.6568542,14 19,12.6568542 19,11 C19,9.34314575 17.6568542,8 16,8 C14.3431458,8 13,9.34314575 13,11 C13,12.6568542 14.3431458,14 16,14 Z" fill="#000000"/>
    </g>
</svg><!--end::Svg Icon--></span>
                    <span class="font-size-h6 text-muted font-weight-bold">Today's Approved</span>
                 </div>
                 <div>
                    <div class="font-size-h1 text-white font-weight-bolder" id="today_approve_total">Processing...</div>
                 </div>
              </div>
           </div>
        </div>

         <div class="col-lg-3">
           <div class="card card-custom rounded-xl gutter-b bg-dark card-stretch">
              <div class="d-flex align-items-center mr-2">
                 <div class="symbol-label px-6 py-8 rounded-xl mr-7">
                   <span class="svg-icon svg-icon-4x svg-icon-white d-block my-2"><!--begin::Svg Icon | path:/var/www/preview.keenthemes.com/metronic/releases/2021-05-14-112058/theme/html/demo4/dist/../src/media/svg/icons/Design/Image.svg--><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
        <polygon points="0 0 24 0 24 24 0 24"/>
        <path d="M6,5 L18,5 C19.6568542,5 21,6.34314575 21,8 L21,17 C21,18.6568542 19.6568542,20 18,20 L6,20 C4.34314575,20 3,18.6568542 3,17 L3,8 C3,6.34314575 4.34314575,5 6,5 Z M5,17 L14,17 L9.5,11 L5,17 Z M16,14 C17.6568542,14 19,12.6568542 19,11 C19,9.34314575 17.6568542,8 16,8 C14.3431458,8 13,9.34314575 13,11 C13,12.6568542 14.3431458,14 16,14 Z" fill="#000000"/>
    </g>
</svg><!--end::Svg Icon--></span>
                    <span class="font-size-h6 text-muted font-weight-bold">Today's Decline</span>
                 </div>
                 <div>
                    <div class="font-size-h1 text-white font-weight-bolder" id="today_decline_total">Processing...</div>
                 </div>
              </div>
           </div>
        </div>

         <div class="col-lg-3">
           <div class="card card-custom rounded-xl gutter-b bg-dark card-stretch">
              <div class="d-flex align-items-center mr-2">
                 <div class="symbol-label px-6 py-8 rounded-xl mr-7">
                   <span class="svg-icon svg-icon-4x svg-icon-white d-block my-2"><!--begin::Svg Icon | path:/var/www/preview.keenthemes.com/metronic/releases/2021-05-14-112058/theme/html/demo4/dist/../src/media/svg/icons/Design/Image.svg--><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
        <polygon points="0 0 24 0 24 24 0 24"/>
        <path d="M6,5 L18,5 C19.6568542,5 21,6.34314575 21,8 L21,17 C21,18.6568542 19.6568542,20 18,20 L6,20 C4.34314575,20 3,18.6568542 3,17 L3,8 C3,6.34314575 4.34314575,5 6,5 Z M5,17 L14,17 L9.5,11 L5,17 Z M16,14 C17.6568542,14 19,12.6568542 19,11 C19,9.34314575 17.6568542,8 16,8 C14.3431458,8 13,9.34314575 13,11 C13,12.6568542 14.3431458,14 16,14 Z" fill="#000000"/>
    </g>
</svg><!--end::Svg Icon--></span>
                    <span class="font-size-h6 text-muted font-weight-bold">Weekly Approved</span>
                 </div>
                 <div>
                    <div class="font-size-h1 text-white font-weight-bolder" id="weekly_approve_total">Processing...</div>
                 </div>
              </div>
           </div>
        </div>

         <div class="col-lg-3">
           <div class="card card-custom rounded-xl gutter-b bg-dark card-stretch">
              <div class="d-flex align-items-center mr-2">
                 <div class="symbol-label px-6 py-8 rounded-xl mr-7">
                   <span class="svg-icon svg-icon-4x svg-icon-white d-block my-2"><!--begin::Svg Icon | path:/var/www/preview.keenthemes.com/metronic/releases/2021-05-14-112058/theme/html/demo4/dist/../src/media/svg/icons/Design/Image.svg--><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
        <polygon points="0 0 24 0 24 24 0 24"/>
        <path d="M6,5 L18,5 C19.6568542,5 21,6.34314575 21,8 L21,17 C21,18.6568542 19.6568542,20 18,20 L6,20 C4.34314575,20 3,18.6568542 3,17 L3,8 C3,6.34314575 4.34314575,5 6,5 Z M5,17 L14,17 L9.5,11 L5,17 Z M16,14 C17.6568542,14 19,12.6568542 19,11 C19,9.34314575 17.6568542,8 16,8 C14.3431458,8 13,9.34314575 13,11 C13,12.6568542 14.3431458,14 16,14 Z" fill="#000000"/>
    </g>
</svg><!--end::Svg Icon--></span>
                    <span class="font-size-h6 text-muted font-weight-bold">Weekly Decline</span>
                 </div>
                 <div>
                    <div class="font-size-h1 text-white font-weight-bolder" id="weekly_decline_total">Processing...</div>
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
                {{-- @if (in_array('delete', $permissions))
                <a href="{{ route('admin.subscription-plans.destroy', 0) }}" name="del_select" id="del_select"
                    class="btn btn-sm btn-light-danger font-weight-bolder text-uppercase mr-2 delete_all_link">
                    <i class="far fa-trash-alt"></i> Delete Selected
                </a>
                @endif
                @if (in_array('add', $permissions))
                <a href="{{ route('admin.subscription-plans.create') }}"
                    class="btn btn-sm btn-primary font-weight-bolder text-uppercase">
                    <i class="fas fa-plus"></i>
                    Add {{ $custom_title }}
                </a>
                @endif --}}

                <a href="javascript:void(0)"
                    class="btn btn-sm btn-primary font-weight-bolder text-uppercase ml-2 clsDownloadCsv">
                    <i class="fas fa-arrow-down"></i>
                    Download CSV
                </a>
            </div>
        </div>
        <div class="card-body">
            <form id="frmcsv"  name="frmcsv" action="{{ route('admin.image-log.csv-download') }}" method="get" enctype="multipart">
                <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                <input type="hidden" name="from_date_hidden" id="from_date_hidden" value="">
                <input type="hidden" name="to_date_hidden" id="to_date_hidden" value="">
                <input type="hidden" name="search_data_hidden" id="search_data_hidden" value="">
            </form>
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
                        <input type='date' id='search_fromdate' class="form-control" placeholder='From date' value="">
                    </td>
                    <td>
                        <input type='date' id='search_todate' class="form-control" placeholder='To date' value="">
                    </td>

                    <td>
                       <select class="form-control" name="search_status" id="search_status">
                           <option value="">-- Select status --</option>
                           <option value="0">Decline</option>
                           <option value="1">Approve</option>
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
            <table class="table table-bordered table-hover table-checkable" id="image_logs_table"
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
        oTable = $('#image_logs_table').DataTable({
            responsive: true,
            searchDelay: 500,
            processing: true,
            serverSide: true,
            cache: false,
            ajax: {
                url: "{{ route('admin.image-logs.listing') }}",
                data: {
                    columnsDef: ['account_id','full_name', 'message', 'created_at','is_approved','total_face_detected','endpoint_url'],
                },                
                data: function(data) {                    

                    // ST - Filter Params
                    var from_date       = $("#search_fromdate").val();
                    var to_date         = $("#search_todate").val();  
                    var search_status   = $("#search_status").val();  
                        
                    // EN - Filter Params

                    data.from_date         = from_date;
                    data.to_date           = to_date;                    
                    data.search_status     = search_status;                    
                    //data.flgPendingProfile = $(".getpendingprofile").is(':checked') ? 1 : 0;
               },               
               complete: function(response) {
                    //console.log(response);
                    if (response != '') {

                        var summury_data  = JSON.parse(response.responseText).summury;

                        //console.log(summury_data);
                        /*
                        console.log("Result " + summury_data.today_approved);
                        console.log("Result " + summury_data.today_decline);
                        console.log("Result " + summury_data.weekly_approved);
                        console.log("Result " + summury_data.weekly_decline);
                        */

                        $("#today_approve_total").html(summury_data.today_approved);
                        $("#today_decline_total").html(summury_data.today_decline);
                        $("#weekly_approve_total").html(summury_data.weekly_approved);
                        $("#weekly_decline_total").html(summury_data.weekly_decline); 
                    }  
              },
            },
            columns: [
                { data: 'account_id' },
                { data: 'full_name' },
                { data: 'message' },
                { data: 'created_at' },
                { data: 'is_approved' },
                { data: 'total_face_detected' },
                { data: 'endpoint_url', responsivePriority: -1 },
            ],
            columnDefs: [
                // Specify columns titles here...
                { targets: 0, title: "Account Id", orderable: true },
                { targets: 1, title: "User Name", orderable: true },
                { targets: 2, title: "Message", orderable: false },
                { targets: 3, title: "Date", orderable: true },
                { targets: 4, title: 'Status', orderable: true },
                { targets: 5, title: 'Total Face Detected', orderable: false },
                // Action buttons
                { targets: -1, title: 'End Point',
                orderable: false },
            ],
            order: [
                [3, 'desc']
            ],
            lengthMenu: [
                [10, 20, 50, 100],
                [10, 20, 50, 100]
            ],
            pageLength: 10,
        });
    });

    $(document).on("click", "#btn_search_filter", function () {
        
        oTable.draw();
    });

    $(document).on("click", "#btn_reset_filter", function () {
        $("#search_fromdate,#search_todate,#gender_filter,#search_status").val('');
        oTable.draw();
    });

    $(document).on("click", ".clsDownloadCsv", function () {             
         var from_date       = $("#search_fromdate").val();
         var to_date         = $("#search_todate").val();  
         var input           = $('.dataTables_filter input')[0];  

         var search_data     = input.value;
         console.log("Data" + search_data);
         
         $('#from_date_hidden').val(from_date);
         $('#to_date_hidden').val(to_date);
         $('#search_data_hidden').val(search_data);
         
         $("#frmcsv").submit();

         //window.location = "{{ route('admin.image-log.csv-download') }}";
         

    });


</script>
@endpush