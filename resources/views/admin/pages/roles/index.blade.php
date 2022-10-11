@extends('admin.layouts.app')
@push('extra-css-styles')
<link rel="stylesheet" type="text/css" href="{{ asset('assets/plugins/custom/datatables/datatables.bundle.css') }}" />
@endpush

@push('breadcrumb')
{!! Breadcrumbs::render('roles_list') !!}
@endpush

@section('content')
<div class="container">
    <div class="card card-custom">
        <div class="card-header">
            <div class="card-title">
                <span class="card-icon">
                    <i class="fab fa-black-tie text-primary"></i>
                </span>
                <h3 class="card-label">{{ $custom_title }}</h3>
            </div>

            <div class="card-toolbar">
                @if (in_array('delete', $permissions))
                <a href="{{ route('admin.roles.destroy',0) }}" name="del_select" id="del_select" class="btn btn-sm btn-light-danger font-weight-bolder text-uppercase mr-2 delete_all_link">
                    <i class="far fa-trash-alt"></i> {{ __('Delete Selected') }}
                </a>
              @endif
              @if (in_array('add', $permissions))
                <a href="{{ route('admin.roles.create') }}" class="btn btn-sm btn-primary font-weight-bolder text-uppercase">
                    <i class="fas fa-plus"></i>New Role
                </a>
              @endif
            </div>
        </div>

        <div class="card-body">
            <!--begin: Datatable-->
            <table class="table table-bordered table-hover table-checkable" id="RoleUser" style="margin-top: 13px !important">
            </table>
            <!--end: Datatable-->
        </div>
    </div>
</div>
@endsection

@push('extra-js-scripts')
<script src="{{ asset('assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
<script type="text/javascript">
  $(document).ready(function () {
    var table = $("#RoleUser");
        // datatable
    oTable = table.DataTable({
        responsive: true,
        searchDelay: 500,
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('admin.roles.listing') }}",
            data: {
                columnsDef: ['','full_name', 'email','contact_no','status','action'],
            },
        },
        columns: [
        @if (in_array('delete', $permissions))
          { data: 'checkbox'},
        @endif
            { data: 'full_name' },
            { data: 'email' },
            { data: 'contact_no'},
        @if (in_array('edit', $permissions))
            { data: 'active' },
        @endif
        @if (in_array('view', $permissions) || in_array('edit', $permissions) || in_array('delete', $permissions))
            { data: 'action', responsivePriority: -1 },
        @endif
        ],
        columnDefs: [
            // columns titles here
            { targets: 0, title: "<input type='checkbox' class='all_select'>", orderable:false },
            { targets: 1, title: 'Name', orderable: true },
            { targets: 2, title: 'Email', orderable: true },
            { targets: 3, title: 'Contact', orderable: true},
            // Action buttons
            { targets: -2, title: 'Status', orderable:false },
            { targets: -1, title: 'Action', orderable: false },
        ],
        "order": [
          @if (in_array('delete', $permissions))
              [1, 'asc']
          @else
              [0, 'asc']
          @endif
        ],
        // drawCallback: function( oSettings ) {
        //   $('.status-switch').bootstrapSwitch();
        //   $('.status-switch').bootstrapSwitch('onColor', 'primary');
        //   $('.status-switch').bootstrapSwitch('offColor', 'danger');
        //   removeOverlay();
        // },
        lengthMenu: [
            [10, 20, 50, 100],
            [10, 20, 50, 100]
        ],
        pageLength: 10,
    });
});

</script>
@endpush

