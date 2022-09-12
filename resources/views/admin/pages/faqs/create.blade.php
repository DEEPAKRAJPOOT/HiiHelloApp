@extends('admin.layouts.app')

@push('breadcrumb')
    {!! Breadcrumbs::render('faqs_create') !!}
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
        <form id="frmAddFaq" method="POST" action="{{ route('admin.faqs.store') }}" enctype="multipart/form-data">
            @csrf

            @forelse($languages as $language)
            <div class="card-body">
                <div class="card-title">
                    <h3 class="card-label text-uppercase">{{ $language->hint }} ({{ $language->language }})</h3>
                </div>

                {{-- Question --}}
                <div class="form-group">
                    <label for="{{ $language->getField($language->lang_code,'question') }}">@if($language->lang_code == $default_lang) {!!$mend_sign!!} @endif Question:</label>
                    <input type="text" class="form-control" id="{{ $language->getField($language->lang_code,'question') }}" name="{{ $language->getField($language->lang_code,'question') }}" value="{{ old($language->getField($language->lang_code,'question')) }}" placeholder="Enter {{ $language->hint }} question" autocomplete="{{ $language->getField($language->lang_code,'question') }}" spellcheck="false" autocapitalize="sentences" tabindex="0" autofocus />
                    @if ($errors->has($language->getField($language->lang_code,'question')))
                        <span class="help-block">
                            <strong class="form-text">{{ $errors->first($language->getField($language->lang_code,'question')) }}</strong>
                        </span>
                    @endif
                </div>

                {{-- Answer --}}
                <div class="form-group">
                    <label for="{{ $language->getField($language->lang_code,'answer') }}">@if($language->lang_code == $default_lang) {!!$mend_sign!!} @endif Answer:</label>
                    <textarea type="text" class="form-control" id="{{ $language->getField($language->lang_code,'answer') }}" name="{{ $language->getField($language->lang_code,'answer') }}" placeholder="Enter {{ $language->hint }} answer" autocomplete="{{ $language->getField($language->lang_code,'answer') }}" spellcheck="false" autocapitalize="sentences" tabindex="0" autofocus >{{ old($language->getField($language->lang_code,'answer')) }}</textarea>
                    @if ($errors->has($language->getField($language->lang_code,'answer')))
                        <span class="help-block">
                            <strong class="form-text">{{ $errors->first($language->getField($language->lang_code,'answer')) }}</strong>
                        </span>
                    @endif
                </div>
            </div>
            @endforeach

            <div class="card-footer">
                <button type="submit" class="btn btn-primary mr-2 text-uppercase"> Add {{ $custom_title }}</button>
                <a href="{{ route('admin.faqs.index') }}" class="btn btn-secondary text-uppercase">Cancel</a>
            </div>
        </form>
        <!--end::Form-->
    </div>
</div>
@endsection

@push('extra-js-scripts')
<script>
$(document).ready(function () {
    $("#frmAddFaq").validate({
        rules: {
            '{{ $default_lang }}_question': {
                required: true,
                not_empty: true,
                minlength: 3,
            },
            '{{ $default_lang }}_answer': {
                required: true,
                not_empty: true,
                minlength: 3,
            }
        },
        messages: {
            '{{ $default_lang }}_question': {
                required: "@lang('validation.required',['attribute'=>'question'])",
                not_empty: "@lang('validation.not_empty',['attribute'=>'question'])",
                minlength:"@lang('validation.min.string',['attribute'=>'question','min'=>3])",
            },
            '{{ $default_lang }}_answer': {
                required: "@lang('validation.required',['attribute'=>'answer'])",
                not_empty: "@lang('validation.not_empty',['attribute'=>'answer'])",
                minlength:"@lang('validation.min.string',['attribute'=>'answer','min'=>3])",
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
    $('#frmAddFaq').submit(function () {
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
