@extends('admin.layouts.app')

@push('breadcrumb')
    {!! Breadcrumbs::render('coupons_list') !!}
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
                    <a href="{{ route('admin.coupons.destroy', 0) }}" name="del_select" id="del_select" class="btn btn-sm btn-light-danger font-weight-bolder text-uppercase mr-2 delete_all_link">
                        <i class="far fa-trash-alt"></i> Delete Selected
                    </a>
                @endif
                @if (in_array('add',$permissions))
                    <a href="{{ route('admin.coupons.create') }}" class="btn btn-sm btn-primary font-weight-bolder text-uppercase">
                        <i class="fas fa-plus"></i>
                        Add {{ $custom_title }}
                    </a>
                @endif
            </div>
        </div>
        <div class="card-body">

            {{-- Filter Start --}}
            <table class="mb-5" align="center">                
                <tr>
                    <td>
                        <p class="mb-0">&nbsp;</p>
                        <span class="card-icon">
                            <i class="fa fa-filter text-primary"></i>
                        </span>
                        <label class="pr-3">Filter:</label>
                    </td>

                    <td>
                        <p class="mb-0">Expiry After</p>
                        <input id="search_expiry_after" type="date" class="form-control" value="">
                    </td>
                    <td>
                        <p class="mb-0">Expiry Before</p>
                        <input id="search_expiry_before" type="date" class="form-control" value="">
                    </td>

                    <td>
                        <p class="mb-0">Plan</p>
                        <select id="search_plan" class="form-control" name="search_plan">
                           <option value="">-- Select Plan --</option>
                           @foreach ($subscription_plans as $subscription_plan)
                               <option value="{{ $subscription_plan->id }}">{{ $subscription_plan->name }}</option>
                           @endforeach
                       </select>
                    </td>

                    <td>
                        <p class="mb-0">Coupon Vendor</p>
                        <select id="search_vendor" class="form-control" name="search_vendor">
                           <option value="">-- Select Vendor --</option>
                           @foreach ($coupon_vendors as $coupon_vendor)
                               <option value="{{ $coupon_vendor->id }}">{{ $coupon_vendor->name }}</option>
                           @endforeach
                       </select>
                    </td>
                
                    <td>
                        <p class="mb-0">&nbsp;</p>
                        <input id="btn_search_filter" type="button" class="btn btn-primary mr-1 ml-1" value="Filter">
                    </td>
                    <td>
                        <p class="mb-0">&nbsp;</p>
                        <a id="btn_reset_filter" href="javascript:void(0);" class="btn btn-warning">Reset</a>
                    </td>
                </tr>
            </table>
            {{--  Datatable Start  --}}
            <table class="table table-bordered table-hover table-checkable" id="coupons_table" style="margin-top: 13px !important"></table>
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
        oTable = $('#coupons_table').DataTable({
            'responsive': true,
            'searchDelay': 500,
            'processing': true,
            'serverSide': true,
            'ajax': {
                'url': "{{ route('admin.coupons.listing') }}",
                'data': {
                    'columnsDef': ['checkbox','image', 'plan', 'vendor', 'title', 'coupon', 'type', 'expired_at', 'is_universal', 'is_reusable', 'is_self_hosted', 'is_active', 'action'],
                    'search_expiry_after':function(){
                        return $('#search_expiry_after').val();
                    },
                    'search_expiry_before':function(){
                        return $('#search_expiry_before').val();
                    },
                    'search_plan':function(){
                        return $('#search_plan').val();
                    },
                    'search_vendor':function(){
                        return $('#search_vendor').val();
                    },
                },
            },
            'columns': [
                { 'data': 'checkbox' },
                { 'data': 'image' },
                { 'data': 'plan' },
                { 'data': 'vendor' },
                { 'data': 'title' },
                { 'data': 'coupon' },
                { 'data': 'type' },
                { 'data': 'expired_at' },
                { 'data': 'is_universal' },
                { 'data': 'is_reusable' },
                { 'data': 'is_self_hosted' },
                { 'data': 'is_active' },
                { 'data': 'action', responsivePriority: -1 },
            ],
            'columnDefs': [
                // Specify columns titles here...
                { 'targets': 0, 'title': "<center><input type='checkbox' class='all_select'></center>", 'orderable': false },
                { 'targets': 1, 'title': 'Image', 'orderable': false },
                { 'targets': 2, 'title': 'Plan', 'orderable': false },
                { 'targets': 3, 'title': 'Vendor', 'orderable': false },
                { 'targets': 4, 'title': 'Title', 'orderable': true },
                { 'targets': 5, 'title': 'Coupon Code', 'orderable': true },
                { 'targets': 6, 'title': 'Coupon Type', 'orderable': true },
                { 'targets': 7, 'title': 'Expired At', 'orderable': true },
                { 'targets': 8, 'title': 'Universal', 'orderable': false },
                { 'targets': 9, 'title': 'Reusable', 'orderable': false },
                { 'targets': 10, 'title': 'Self Hosted', 'orderable': false },
                { 'targets': 11, 'title': 'Active', 'orderable': false },
                // Action buttons
                { 'targets': 12, 'title': 'Action', 'orderable': false },
            ],
            'order': [
                [4, 'asc']
            ],
            'lengthMenu': [
                [10, 20, 50, 100],
                [10, 20, 50, 100]
            ],
            'pageLength': 10,
        });
        $(document).on("click", "#btn_search_filter", function () {
            
            oTable.draw();
        });
        $('#search_expiry_after').change(function(){
            $('#search_expiry_before').attr('min',$(this).val());
            if(!$('#search_expiry_before').get(0).checkValidity()){
                $('#search_expiry_before').val('');
            }
        });

        $(document).on("click", "#btn_reset_filter", function () {
            $('#search_expiry_after,#search_expiry_before,#search_plan,#search_vendor').val('');
            oTable.draw();
        });
    });
</script>
@endpush
