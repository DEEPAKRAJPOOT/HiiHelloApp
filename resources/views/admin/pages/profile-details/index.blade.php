@extends('admin.layouts.app')

@push('breadcrumb')
    {!! Breadcrumbs::render('profile_details_list') !!}
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
                    <a href="{{ route('admin.profile-details.destroy', 0) }}" name="del_select" id="del_select" class="btn btn-sm btn-light-danger font-weight-bolder text-uppercase mr-2 delete_all_link">
                        <i class="far fa-trash-alt"></i> Delete Selected
                    </a>
                @endif
                @if (in_array('add', $permissions))
                    <a href="{{ route('admin.profile-details.create') }}" class="btn btn-sm btn-primary font-weight-bolder text-uppercase">
                        <i class="fas fa-plus"></i>
                        Add {{ $custom_title }}
                    </a>
                @endif
                <a href="javascript:;"  data-toggle="modal" data-target="#csvupload" style="margin: 0px 0px 0px 5px;" class="btn btn-sm btn-info font-weight-bolder text-uppercase">
                    <i class="fas fa-upload"></i>
                    Upload Csv
                </a>
            </div>
        </div>
        <div class="card-body">
            {{--  Datatable Start  --}}
            <table class="table table-bordered table-hover table-checkable" id="profile_details_table" style="margin-top: 13px !important"></table>
            {{--  Datatable End  --}}
        </div>
    </div>
</div>

{{-- Pop Up --}}
<div class="modal fade" id="csvupload" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
              <h4 class="modal-title">Upload Multiple Details</h4>
              <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <form id="frmUploadCsv" class="form-horizontal" role="form" method="POST" action="{{ route('admin.profile-details.csv-upload') }}" enctype="multipart/form-data">
                    @csrf 
                    <div class="row">
                        <div class="col-md-10">
                            {{-- Select File --}}
                            <div class="form-group margin-0">
                                <label class="control-label">{!! $mend_sign !!}Select CSV File</label>
                                <input type="file" placeholder="Select CSV File" class="form-control" id="csvFile" name="csvFile" data-error-container="#error-csv-file"/>
                                <span id="error-csv-file"></span>
                            </div>

                            {{-- Sample CSV Download --}}
                            <a href="{{ route('admin.profile-details.sample-csv-download') }}">Download sample CSV</a>

                            {{-- Submit and Cancel Button --}}
                            <div class="form-group">
                                <div class="col-md-offset-4 col-md-8">
                                    <button type="submit" style="margin: 10px 0px 0px -10px;" class="btn btn-sm btn-info font-weight-bolder text-uppercase">Submit</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-sm btn-danger font-weight-bolder text-uppercase" data-dismiss="modal">Close</button>
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
        oTable = $('#profile_details_table').DataTable({
            responsive: true,
            searchDelay: 500,
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('admin.profile-details.listing') }}",
                data: {
                    columnsDef: ['checkbox','title', 'attribute', 'active', 'action'],
                },
            },
            columns: [
                { data: 'checkbox' },
                { data: 'id' },
                { data: 'value' },
                { data: 'attribute' },
                { data: 'active' },
                { data: 'action', responsivePriority: -1 },
            ],
            columnDefs: [
                // Specify columns titles here...
                { targets: 0, title: "<center><input type='checkbox' class='all_select'></center>", orderable: false },
                { targets: 1, title: 'Id', orderable: true },
                { targets: 2, title: 'Value', orderable: false },
                { targets: 3, title: 'Attribute', orderable: false },
                { targets: 4, title: 'Active', orderable: false },
                // Action buttons
                { targets: -1, title: 'Action',
                orderable: false },
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

         $("#frmUploadCsv").validate({
            rules: {
                csvFile: {
                    required: true,
                    not_empty: true,
                    extension: "csv",
                },
            },
            messages: {
                csvFile:{
                    required:"@lang('validation.required',['attribute'=>'CSV file'])",
                    not_empty:"@lang('validation.not_empty',['attribute'=>'CSV file'])",
                    maxlength:"@lang('validation.max.string',['attribute'=>'CSV file','max'=>50])",
                    extension:"@lang('validation.mimetypes',['attribute'=>'image','value'=>'csv'])"
                },
            },
            errorClass: 'invalid-feedback',
            errorElement: 'span',
            highlight: function (element) {
                $(element).addClass('is-invalid');
                $(element).siblings('label').addClass('text-danger'); // For Label
            },
            unhighlight: function (element) {
                $(element).removeClass('is-invalid');
                $(element).siblings('label').removeClass('text-danger'); // For Label
            },
            errorPlacement: function (error, element) {
                if (element.attr("data-error-container")) {
                    error.appendTo(element.attr("data-error-container"));
                } else {
                    error.insertAfter(element);
                }
            }
        });

        $('#frmUploadCsv').submit(function(){
            if( $(this).valid() ){
                addOverlay();
                $("input[type=submit], input[type=button], button[type=submit]").prop("disabled", "disabled");
                return true;
            }
            else{
                return false;
            }
        });
    });
</script>
@endpush
