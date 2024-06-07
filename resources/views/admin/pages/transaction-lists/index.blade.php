@extends('admin.layouts.app')

@push('breadcrumb')
    {!! Breadcrumbs::render('transaction_list') !!}
@endpush

@push('extra-css-styles')
<link rel="stylesheet" type="text/css" href="{{ asset('assets/plugins/custom/datatables/datatables.bundle.css') }}" />
@endpush

@section('content')
<div class="container">
    <div class="row">
        <div class="col-lg-12">
            <h3 class="card-label">Mode of payment</h3>
        </div>
        <input type="hidden" name="filter_type_url" id="filter_type_url" value="{{ route('admin.transaction.filters') }}">
        <div class="col-lg-3">
           <div class="card card-custom rounded-xl gutter-b bg-dark card-stretch">
              <div class="d-flex align-items-center mr-2">
                 <div class="symbol-label px-6 py-8 rounded-xl mr-7">
                    <span class="svg-icon svg-icon-4x svg-icon-white d-block my-2">
                       <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                <rect x="0" y="0" width="24" height="24"/>
                                <path d="M2,6 L21,6 C21.5522847,6 22,6.44771525 22,7 L22,17 C22,17.5522847 21.5522847,18 21,18 L2,18 C1.44771525,18 1,17.5522847 1,17 L1,7 C1,6.44771525 1.44771525,6 2,6 Z M11.5,16 C13.709139,16 15.5,14.209139 15.5,12 C15.5,9.790861 13.709139,8 11.5,8 C9.290861,8 7.5,9.790861 7.5,12 C7.5,14.209139 9.290861,16 11.5,16 Z" fill="#000000" opacity="0.3" transform="translate(11.500000, 12.000000) rotate(-345.000000) translate(-11.500000, -12.000000) "/>
                                <path d="M2,6 L21,6 C21.5522847,6 22,6.44771525 22,7 L22,17 C22,17.5522847 21.5522847,18 21,18 L2,18 C1.44771525,18 1,17.5522847 1,17 L1,7 C1,6.44771525 1.44771525,6 2,6 Z M11.5,16 C13.709139,16 15.5,14.209139 15.5,12 C15.5,9.790861 13.709139,8 11.5,8 C9.290861,8 7.5,9.790861 7.5,12 C7.5,14.209139 9.290861,16 11.5,16 Z M11.5,14 C12.6045695,14 13.5,13.1045695 13.5,12 C13.5,10.8954305 12.6045695,10 11.5,10 C10.3954305,10 9.5,10.8954305 9.5,12 C9.5,13.1045695 10.3954305,14 11.5,14 Z" fill="#000000"/>
                            </g>
                        </svg>
                    </span>
                    <span class="font-size-h6 text-muted font-weight-bold">Google Play</span>
                 </div>
                 <div>
                    <div class="font-size-h1 text-white font-weight-bolder" id="total_google_play"></div>
                 </div>
              </div>
           </div>
        </div>

         <div class="col-lg-3">
           <div class="card card-custom rounded-xl gutter-b bg-dark card-stretch">
              <div class="d-flex align-items-center mr-2">
                 <div class="symbol-label px-6 py-8 rounded-xl mr-7">
                    <span class="svg-icon svg-icon-4x svg-icon-white d-block my-2">
                       <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                <rect x="0" y="0" width="24" height="24"/>
                                <path d="M2,6 L21,6 C21.5522847,6 22,6.44771525 22,7 L22,17 C22,17.5522847 21.5522847,18 21,18 L2,18 C1.44771525,18 1,17.5522847 1,17 L1,7 C1,6.44771525 1.44771525,6 2,6 Z M11.5,16 C13.709139,16 15.5,14.209139 15.5,12 C15.5,9.790861 13.709139,8 11.5,8 C9.290861,8 7.5,9.790861 7.5,12 C7.5,14.209139 9.290861,16 11.5,16 Z" fill="#000000" opacity="0.3" transform="translate(11.500000, 12.000000) rotate(-345.000000) translate(-11.500000, -12.000000) "/>
                                <path d="M2,6 L21,6 C21.5522847,6 22,6.44771525 22,7 L22,17 C22,17.5522847 21.5522847,18 21,18 L2,18 C1.44771525,18 1,17.5522847 1,17 L1,7 C1,6.44771525 1.44771525,6 2,6 Z M11.5,16 C13.709139,16 15.5,14.209139 15.5,12 C15.5,9.790861 13.709139,8 11.5,8 C9.290861,8 7.5,9.790861 7.5,12 C7.5,14.209139 9.290861,16 11.5,16 Z M11.5,14 C12.6045695,14 13.5,13.1045695 13.5,12 C13.5,10.8954305 12.6045695,10 11.5,10 C10.3954305,10 9.5,10.8954305 9.5,12 C9.5,13.1045695 10.3954305,14 11.5,14 Z" fill="#000000"/>
                            </g>
                        </svg>
                    </span>
                    <span class="font-size-h6 text-muted font-weight-bold">UPI</span>
                 </div>
                 <div>
                    <div class="font-size-h1 text-white font-weight-bolder"id="total_upi"></div>
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
                                <rect x="0" y="0" width="24" height="24"/>
                                <path d="M2,6 L21,6 C21.5522847,6 22,6.44771525 22,7 L22,17 C22,17.5522847 21.5522847,18 21,18 L2,18 C1.44771525,18 1,17.5522847 1,17 L1,7 C1,6.44771525 1.44771525,6 2,6 Z M11.5,16 C13.709139,16 15.5,14.209139 15.5,12 C15.5,9.790861 13.709139,8 11.5,8 C9.290861,8 7.5,9.790861 7.5,12 C7.5,14.209139 9.290861,16 11.5,16 Z" fill="#000000" opacity="0.3" transform="translate(11.500000, 12.000000) rotate(-345.000000) translate(-11.500000, -12.000000) "/>
                                <path d="M2,6 L21,6 C21.5522847,6 22,6.44771525 22,7 L22,17 C22,17.5522847 21.5522847,18 21,18 L2,18 C1.44771525,18 1,17.5522847 1,17 L1,7 C1,6.44771525 1.44771525,6 2,6 Z M11.5,16 C13.709139,16 15.5,14.209139 15.5,12 C15.5,9.790861 13.709139,8 11.5,8 C9.290861,8 7.5,9.790861 7.5,12 C7.5,14.209139 9.290861,16 11.5,16 Z M11.5,14 C12.6045695,14 13.5,13.1045695 13.5,12 C13.5,10.8954305 12.6045695,10 11.5,10 C10.3954305,10 9.5,10.8954305 9.5,12 C9.5,13.1045695 10.3954305,14 11.5,14 Z" fill="#000000"/>
                            </g>
                        </svg>
                       <!--end::Svg Icon-->
                    </span>
                    <span class="font-size-h6 text-muted font-weight-bold">IOS</span>
                 </div>
                 <div>
                    <div class="font-size-h1 text-white font-weight-bolder" id="total_ios"></div>
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
                                 <rect x="0" y="0" width="24" height="24"/>
                                 <path d="M2,6 L21,6 C21.5522847,6 22,6.44771525 22,7 L22,17 C22,17.5522847 21.5522847,18 21,18 L2,18 C1.44771525,18 1,17.5522847 1,17 L1,7 C1,6.44771525 1.44771525,6 2,6 Z M11.5,16 C13.709139,16 15.5,14.209139 15.5,12 C15.5,9.790861 13.709139,8 11.5,8 C9.290861,8 7.5,9.790861 7.5,12 C7.5,14.209139 9.290861,16 11.5,16 Z" fill="#000000" opacity="0.3" transform="translate(11.500000, 12.000000) rotate(-345.000000) translate(-11.500000, -12.000000) "/>
                                 <path d="M2,6 L21,6 C21.5522847,6 22,6.44771525 22,7 L22,17 C22,17.5522847 21.5522847,18 21,18 L2,18 C1.44771525,18 1,17.5522847 1,17 L1,7 C1,6.44771525 1.44771525,6 2,6 Z M11.5,16 C13.709139,16 15.5,14.209139 15.5,12 C15.5,9.790861 13.709139,8 11.5,8 C9.290861,8 7.5,9.790861 7.5,12 C7.5,14.209139 9.290861,16 11.5,16 Z M11.5,14 C12.6045695,14 13.5,13.1045695 13.5,12 C13.5,10.8954305 12.6045695,10 11.5,10 C10.3954305,10 9.5,10.8954305 9.5,12 C9.5,13.1045695 10.3954305,14 11.5,14 Z" fill="#000000"/>
                             </g>
                         </svg>
                        <!--end::Svg Icon-->
                     </span>
                     <span class="font-size-h6 text-muted font-weight-bold">Coupon</span>
                  </div>
                  <div>
                     <div class="font-size-h1 text-white font-weight-bolder" id="total_coupon"></div>
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
                                 <rect x="0" y="0" width="24" height="24"/>
                                 <path d="M2,6 L21,6 C21.5522847,6 22,6.44771525 22,7 L22,17 C22,17.5522847 21.5522847,18 21,18 L2,18 C1.44771525,18 1,17.5522847 1,17 L1,7 C1,6.44771525 1.44771525,6 2,6 Z M11.5,16 C13.709139,16 15.5,14.209139 15.5,12 C15.5,9.790861 13.709139,8 11.5,8 C9.290861,8 7.5,9.790861 7.5,12 C7.5,14.209139 9.290861,16 11.5,16 Z" fill="#000000" opacity="0.3" transform="translate(11.500000, 12.000000) rotate(-345.000000) translate(-11.500000, -12.000000) "/>
                                 <path d="M2,6 L21,6 C21.5522847,6 22,6.44771525 22,7 L22,17 C22,17.5522847 21.5522847,18 21,18 L2,18 C1.44771525,18 1,17.5522847 1,17 L1,7 C1,6.44771525 1.44771525,6 2,6 Z M11.5,16 C13.709139,16 15.5,14.209139 15.5,12 C15.5,9.790861 13.709139,8 11.5,8 C9.290861,8 7.5,9.790861 7.5,12 C7.5,14.209139 9.290861,16 11.5,16 Z M11.5,14 C12.6045695,14 13.5,13.1045695 13.5,12 C13.5,10.8954305 12.6045695,10 11.5,10 C10.3954305,10 9.5,10.8954305 9.5,12 C9.5,13.1045695 10.3954305,14 11.5,14 Z" fill="#000000"/>
                             </g>
                         </svg>
                        <!--end::Svg Icon-->
                     </span>
                     <span class="font-size-h6 text-muted font-weight-bold">RazorPay</span>
                  </div>
                  <div>
                     <div class="font-size-h1 text-white font-weight-bolder" id="total_razorpay"></div>
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
                                 <rect x="0" y="0" width="24" height="24"/>
                                 <path d="M2,6 L21,6 C21.5522847,6 22,6.44771525 22,7 L22,17 C22,17.5522847 21.5522847,18 21,18 L2,18 C1.44771525,18 1,17.5522847 1,17 L1,7 C1,6.44771525 1.44771525,6 2,6 Z M11.5,16 C13.709139,16 15.5,14.209139 15.5,12 C15.5,9.790861 13.709139,8 11.5,8 C9.290861,8 7.5,9.790861 7.5,12 C7.5,14.209139 9.290861,16 11.5,16 Z" fill="#000000" opacity="0.3" transform="translate(11.500000, 12.000000) rotate(-345.000000) translate(-11.500000, -12.000000) "/>
                                 <path d="M2,6 L21,6 C21.5522847,6 22,6.44771525 22,7 L22,17 C22,17.5522847 21.5522847,18 21,18 L2,18 C1.44771525,18 1,17.5522847 1,17 L1,7 C1,6.44771525 1.44771525,6 2,6 Z M11.5,16 C13.709139,16 15.5,14.209139 15.5,12 C15.5,9.790861 13.709139,8 11.5,8 C9.290861,8 7.5,9.790861 7.5,12 C7.5,14.209139 9.290861,16 11.5,16 Z M11.5,14 C12.6045695,14 13.5,13.1045695 13.5,12 C13.5,10.8954305 12.6045695,10 11.5,10 C10.3954305,10 9.5,10.8954305 9.5,12 C9.5,13.1045695 10.3954305,14 11.5,14 Z" fill="#000000"/>
                             </g>
                         </svg>
                        <!--end::Svg Icon-->
                     </span>
                     <span class="font-size-h6 text-muted font-weight-bold">Cashfree</span>
                  </div>
                  <div>
                     <div class="font-size-h1 text-white font-weight-bolder" id="total_cashfree"></div>
                  </div>
               </div>
            </div>
         </div>
        <div class="col-lg-3"></div>

    </div>


    <div class="row">
        <div class="col-lg-12">
            <h3 class="card-label">Subscription plans</h3>
        </div>
        <?php foreach ($subscription_plans as $key => $value) { ?>
        <div class="col-lg-3"> 
           <div class="card card-custom rounded-xl gutter-b bg-dark card-stretch">
              <div class="d-flex align-items-center mr-2">
                 <div class="symbol-label px-6 py-8 rounded-xl mr-7">

                    <span class="svg-icon svg-icon-4x svg-icon-white d-block my-2"><!--begin::Svg Icon | path:/var/www/preview.keenthemes.com/metronic/releases/2021-05-14-112058/theme/html/demo1/dist/../src/media/svg/icons/Text/Align-justify.svg--><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
        <rect x="0" y="0" width="24" height="24"/>
        <path d="M5,5 L19,5 C19.5522847,5 20,5.44771525 20,6 C20,6.55228475 19.5522847,7 19,7 L5,7 C4.44771525,7 4,6.55228475 4,6 C4,5.44771525 4.44771525,5 5,5 Z M5,13 L19,13 C19.5522847,13 20,13.4477153 20,14 C20,14.5522847 19.5522847,15 19,15 L5,15 C4.44771525,15 4,14.5522847 4,14 C4,13.4477153 4.44771525,13 5,13 Z" fill="#000000" opacity="0.3"/>
        <path d="M5,9 L19,9 C19.5522847,9 20,9.44771525 20,10 C20,10.5522847 19.5522847,11 19,11 L5,11 C4.44771525,11 4,10.5522847 4,10 C4,9.44771525 4.44771525,9 5,9 Z M5,17 L19,17 C19.5522847,17 20,17.4477153 20,18 C20,18.5522847 19.5522847,19 19,19 L5,19 C4.44771525,19 4,18.5522847 4,18 C4,17.4477153 4.44771525,17 5,17 Z" fill="#000000"/>
    </g>
</svg><!--end::Svg Icon--></span>

                    <span class="font-size-h6 text-muted font-weight-bold"><?=$value->name?></span>
                 </div>
                 <div>
                    <div class="font-size-h1 text-white font-weight-bolder planCountTotal" id="planCountTotal_<?=$value->id?>" data-planID="<?=$value->id?>"></div>
                 </div>
              </div>
           </div>
        </div>
        <?php } ?>
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
                    <a href="{{ route('admin.subscription-plans.destroy', 0) }}" name="del_select" id="del_select" class="btn btn-sm btn-light-danger font-weight-bolder text-uppercase mr-2 delete_all_link">
                        <i class="far fa-trash-alt"></i> Delete Selected
                    </a>
                @endif
                @if (in_array('add', $permissions))
                    <a href="{{ route('admin.subscription-plans.create') }}" class="btn btn-sm btn-primary font-weight-bolder text-uppercase">
                        <i class="fas fa-plus"></i>
                        Add {{ $custom_title }}
                    </a>
                @endif --}}
                <a id="transactions-csv-button" href="javascript:void(0)" class="btn btn-sm btn-primary font-weight-bolder text-uppercase ml-2">
                <i class="fas fa-arrow-down"></i>
                Download CSV
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
                        <input type='date' id='search_fromdate' class="form-control" placeholder='From date' value="">
                    </td>
                    <td>
                        <input type='date' id='search_todate' class="form-control" placeholder='To date' value="">
                    </td>

                    <td>
                       <select class="form-control" name="search_mode" id="search_mode">
                           <option value="">-- Select mode --</option>
                           <option value="Google Play">Google Play</option>
                           <option value="UPI">UPI</option>
                           <option value="IOS">IOS</option>
                           <option value="COUPON">Coupon</option>
                           <option value="Razorpay">Razorpay</option>
                           <option value="Cashfree">Cashfree</option>
                       </select>
                    </td>

                    <td id="search_vendor_container" class="d-none">
                       <select class="form-control" name="search_vendor" id="search_vendor">
                           <option value="">-- Select Vendor --</option>
                           @foreach ($coupon_vendors as $coupon_vendor)
                               <option value="{{ $coupon_vendor->id }}">{{ $coupon_vendor->name }}</option>
                           @endforeach
                       </select>
                    </td>

                    <td>
                       <select class="form-control" name="search_status" id="search_status">
                           <option value="">-- Select Status --</option>
                           <option value="success">Successful</option>
                           <option value="pending">Pending</option>
                           <option value="fail">Failed</option>
                           <option value="refund">Refund</option>
                       </select>
                    </td>

                    <td>
                        <select class="form-control" name="search_plan" id="search_plan">
                           <option value="">-- Select plan --</option>
                           <?php foreach ($subscription_plans as $key => $value) { ?>
                               <option value="<?=$value->id?>"><?=$value->name?></option>
                           <?php } ?>
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
            {{--  Datatable Start  --}}
            <table class="table table-bordered table-hover table-checkable" id="subscription_pan_table" style="margin-top: 13px !important"></table>
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
        oTable = $('#subscription_pan_table').DataTable({
            responsive: true,
            searchDelay: 500,
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('admin.transaction-lists.listing') }}",
                data: {
                    columnsDef: ['account_id','user_id','plan_id','transaction_id','razorpay_vpa','amount','purchase_date','subscription_end_date','created_at','razorpay_contact','razorpay_email','coupon_name','payment_type','status','state','city','email','phone','refund_id','refunded_amount','refunded_at','action'],
                },
                data: function(data) {                    

                    // ST - Filter Params
                    var from_date       = $("#search_fromdate").val();
                    var to_date         = $("#search_todate").val();  
                    var search_mode     = $("#search_mode").val();
                    var search_status   = $("#search_status").val();
                    var search_plan     = $("#search_plan").val();
                    var search_vendor   = $("#search_vendor").val();
                        
                    // EN - Filter Params

                    data.from_date      = from_date;
                    data.to_date        = to_date;
                    data.search_mode    = search_mode;
                    data.search_status  = search_status;
                    data.search_plan    = search_plan;
                    data.search_vendor  = search_vendor;
                    //data.flgPendingProfile = $(".getpendingprofile").is(':checked') ? 1 : 0;
                },           
            },
            columns: [
                { data: 'account_id' },
                { data: 'user_id' },
                { data: 'plan_id' },
                { data: 'transaction_id' },
                { data: 'razorpay_vpa' },
                { data: 'amount' },
                { data: 'purchase_date' },
                { data: 'subscription_end_date' },
                { data: 'created_at' },
                { data: 'razorpay_contact' },
                { data: 'razorpay_email' },
                { data: 'coupon_name' },
                { data: 'payment_type' },
                { data: 'status' },
                { data: 'state' },
                { data: 'city' },
                { data: 'email' },
                { data: 'phone' },
                { data: 'refund_id' },
                { data: 'refunded_amount' },
                { data: 'refunded_at' },
                { data: 'action', responsivePriority: -1 },
            ],
            columnDefs: [
                // Specify columns titles here...
                { targets: 0, title: 'Account Id', orderable: false },
                { targets: 1, title: 'User Name', orderable: false },
                { targets: 2, title: 'Plan Name', orderable: true },
                { targets: 3, title: 'Transaction Id', orderable: false },
                { targets: 4, title: 'VPA Id', orderable: false },
                { targets: 5, title: 'Amount', orderable: true },
                { targets: 6, title: 'Start Date', orderable: true },
                { targets: 7, title: 'End Date', orderable: true },
                { targets: 8, title: 'Created At', orderable: true },
                { targets: 9, title: 'Contact No', orderable: false },
                { targets: 10, title: 'Email Id', orderable: false },
                { targets: 11, title: 'Coupon', orderable: true },
                { targets: 12, title: 'Mode', orderable: true },
                { targets: 13,title: 'Status', orderable: true },
                { targets: 14,title: 'State', orderable: false },
                { targets: 15,title: 'City', orderable: false },
                { targets: 16,title: 'E-mail', orderable: false },
                { targets: 17,title: 'Phone', orderable: false },
                { targets: 18,title: 'Refund Id', orderable: false },
                { targets: 19,title: 'Refund Amount', orderable: false },
                { targets: 20,title: 'Refund date', orderable: false },
                // Action buttons
                { targets: -1, title: 'Action',
                orderable: false },
            ],
            order: [
                [8, 'DESC']
            ],
            lengthMenu: [
                [10, 50, 100, 250, 500],
                [10, 50, 100, 250, 500]
            ],
            pageLength: 10,
        });
    });

    $(document).on("click", "#btn_search_filter", function () {
        
        oTable.draw();
    });

    $(document).on("click", "#btn_reset_filter", function () {
        $("#search_fromdate,#search_todate,#search_mode,#search_status,#search_plan,#search_vendor").val('');
        oTable.draw();
    });

    $('#search_mode').change(function(){
        $('#search_vendor_container').toggleClass('d-none',!$(this).val() || ($(this).val() != 'COUPON'));
    });
    var base_transactions_csv_url = '{{ route("admin.transactions.csv-download") }}';
    $('#transactions-csv-button').attr('href',base_transactions_csv_url);
    $('#search_fromdate,#search_todate,#search_mode,#search_status,#search_plan,#search_vendor,#subscription_pan_table_filter input').change(function(){
        var transactions_csv_url = base_transactions_csv_url;
        var values = [];
        if($('#search_fromdate').val() && $('#search_fromdate').val() != ''){
            values.push('from_date='+$('#search_fromdate').val());
        }
        if($('#search_todate').val() && $('#search_todate').val() != ''){
            values.push('to_date='+$('#search_todate').val());
        }
        if($('#search_mode').val() && $('#search_mode').val() != ''){
            values.push('search_mode='+$('#search_mode').val());
        }
        if($('#search_status').val() && $('#search_status').val() != ''){
            values.push('search_status='+$('#search_status').val());
        }
        if($('#search_plan').val() && $('#search_plan').val() != ''){
            values.push('search_plan='+$('#search_plan').val());
        }
        if($('#search_vendor').val() && $('#search_vendor').val() != ''){
            values.push('search_vendor='+$('#search_vendor').val());
        }
        if($('#subscription_pan_table_filter input').val() && $('#subscription_pan_table_filter input').val() != ''){
            values.push('search_keyword='+$('#subscription_pan_table_filter input').val());
        }
        if(values.length > 0){
            transactions_csv_url += '?'+values.join('&');
        }
        $('#transactions-csv-button').attr('href',transactions_csv_url);
    });

</script>

<script type="text/javascript">
    $(document).ready(function () {
        var filter_type_url     = $("#filter_type_url").val();
        if (filter_type_url != '') {
            $.ajax({
                url: filter_type_url,
                type: "GET",
                dataType: "json",
                data: {
                    _token: $("meta[name='csrf-token']").attr("content"),
                },
                cache: false,
                beforeSend: function(){
                    $("#total_google_play").html("<p style='font-size: 18px;'>processing..</p>");
                    $("#total_upi").html("<p style='font-size: 18px;'>processing..</p>");
                    $("#total_android").html("<p style='font-size: 18px;'>processing..</p>");
                    $("#total_ios").html("<p style='font-size: 18px;'>processing..</p>");
                    $("#total_coupon").html("<p style='font-size: 18px;'>processing..</p>");
                    $("#total_razorpay").html("<p style='font-size: 18px;'>processing..</p>");
                    $("#total_cashfree").html("<p style='font-size: 18px;'>processing..</p>");
                    $(".planCountTotal").html("<p style='font-size: 18px;'>processing..</p>");
                },
                complete: function(){
                    $("#total_google_play").html();
                    $("#total_upi").html();
                    $("#total_android").html();
                    $("#total_ios").html();
                    $("#total_razorpay").html();
                    $("#total_cashfree").html();
                    $(".planCountTotal").html();
                },
                success: function (result) {
                    if (result != '') {
                        $("#total_google_play").html(result.total_google_play);
                        $("#total_upi").html(result.total_upi);
                        $("#total_android").html(result.total_android);
                        $("#total_ios").html(result.total_ios); 
                        $("#total_coupon").html(result.total_coupon); 
                        $("#total_razorpay").html(result.total_razorpay);
                        $("#total_cashfree").html(result.total_cashfree); 
                        //$("#planCountTotal_").html(result.total_ios);

                        console.log(result.plan);
                        if(result.plan){
                            for (var key of Object.keys(result.plan)) {
                                $("#planCountTotal_"+key).html(result.plan[key]);
                            }

                        }

                    }
                },
            });
        }
    });
</script>
@endpush
