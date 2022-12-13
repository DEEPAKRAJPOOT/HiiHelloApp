@extends('admin.layouts.app')

@push('breadcrumb')
    {!! Breadcrumbs::render('profile_reports_list') !!}
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
            {{--  Datatable Start  --}}
            <table class="table table-bordered table-hover table-checkable" id="prfile_reports_table" style="margin-top: 13px !important"></table>
            {{--  Datatable End  --}}
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
            },
            columns: [
                { data: 'profile_photo' },
                { data: 'account_id' },
                { data: 'full_name' },
                { data: 'gender' },
                { data: 'contact_no' },
                { data: 'total_block' },
                { data: 'total_reports' },
            ],
            columnDefs: [
                // Specify columns titles here...
                { targets: 0, title: 'Photo 1', orderable: true },
                { targets: 1, title: 'Account Id', orderable: true },
                { targets: 2, title: 'Full name', orderable: true },
                { targets: 3, title: 'Gender', orderable: true },
                { targets: 4, title: 'Contact no', orderable: true },
                { targets: 5, title: 'Ban', orderable: true },
                { targets: 6, title: 'Report', orderable: false },
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