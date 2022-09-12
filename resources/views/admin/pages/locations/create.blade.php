@extends('admin.layouts.app')

@push('breadcrumb')
    {!! Breadcrumbs::render('locations_create') !!}
@endpush

@section('content')
<div class="container">
    <div class="card card-custom">
        <div class="card-header">
            <div class="card-title">
                <span class="card-icon">
                    <i class="{{$icon}} text-primary"></i>
                </span>
                <h3 class="card-label text-uppercase">ADD {{ $custom_title }}</h3>
            </div>
        </div>

        <!--begin::Form-->
        <form id="frmAddInterest" method="POST" action="{{ route('admin.locations.store') }}" enctype="multipart/form-data">
            @csrf

            @forelse($languages as $language)
            <div class="card-body">
                <div class="card-title">
                    <h3 class="card-label text-uppercase">{{ $language->hint }} ({{ $language->language }})</h3>
                </div>

                {{-- Name --}}
                <div class="form-group">
                    <label for="{{ $language->getField($language->lang_code,'name') }}">@if($language->lang_code == $default_lang) {!!$mend_sign!!} @endif Name:</label>
                    <input type="text" class="form-control" id="{{ $language->getField($language->lang_code,'name') }}" name="{{ $language->getField($language->lang_code,'name') }}" value="{{ old($language->getField($language->lang_code,'name')) }}" placeholder="Enter {{ $language->hint }} name" autocomplete="{{ $language->getField($language->lang_code,'name') }}" spellcheck="false" autocapitalize="sentences" tabindex="0" autofocus />
                    @if ($errors->has($language->getField($language->lang_code,'name')))
                        <span class="help-block">
                            <strong class="form-text">{{ $errors->first($language->getField($language->lang_code,'name')) }}</strong>
                        </span>
                    @endif
                </div>

            </div>
            @endforeach

            <div class="card-footer">
                <button type="submit" class="btn btn-primary mr-2 text-uppercase"> Add {{ $custom_title }}</button>
                <a href="{{ route('admin.locations.index') }}" class="btn btn-secondary text-uppercase">Cancel</a>
            </div>
        </form>
        <!--end::Form-->
    </div>
</div>
@endsection

@push('extra-js-scripts')
<script>
$(document).ready(function () {
    $("#frmAddInterest").validate({
        rules: {
            '{{ $default_lang }}_name': {
                required: true,
                not_empty: true,
                minlength: 3,
            },
        },
        messages: {
            '{{ $default_lang }}_name': {
                required: "@lang('validation.required',['attribute'=>'name'])",
                not_empty: "@lang('validation.not_empty',['attribute'=>'name'])",
                minlength:"@lang('validation.min.string',['attribute'=>'name','min'=>3])",
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
    $('#frmAddInterest').submit(function () {
        if ($(this).valid()) {
            addOverlay();
            $("input[type=submit], input[type=button], button[type=submit]").prop("disabled", "disabled");
            return true;
        } else {
            return false;
        }
    });
});
</script>
@endpush
