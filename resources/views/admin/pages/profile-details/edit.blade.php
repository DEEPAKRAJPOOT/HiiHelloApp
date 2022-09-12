@extends('admin.layouts.app')

@push('breadcrumb')
    {!! Breadcrumbs::render('profile_details_update', $profile_detail->id) !!}
@endpush

@section('content')
<div class="container">
    <div class="card card-custom">
        <div class="card-header">
            <div class="card-title">
                <span class="card-icon">
                    <i class="{{$icon}} text-primary"></i>
                </span>
                <h3 class="card-label text-uppercase">Edit {{ $custom_title }}</h3>
            </div>
        </div>

        <!--begin::Form-->
        <form id="frmEditProfileDetails" method="POST" action="{{ route('admin.profile-details.update', $profile_detail->slug) }}" enctype="multipart/form-data">
            @csrf
            @method('put')

            <div class="card-body">
                {{-- Attribute --}}
                <div class="form-group">
                    <label for="attribute">{!!$mend_sign!!} Attribute:</label>
                    <input type="text" class="form-control" id="attribute" name="attribute" value="{{ old('attribute',$profile_detail->attribute) }}" placeholder="Enter Attribute title (Like: relationship-status)" autocomplete="attribute" spellcheck="false" autocapitalize="sentences" tabindex="0" autofocus />
                    @if ($errors->has('attribute'))
                        <span class="help-block">
                            <strong class="form-text">{{ $errors->first('attribute') }}</strong>
                        </span>
                    @endif
                </div>
            </div>

            @forelse($languages as $language)
            <div class="card-body">
                <div class="card-title">
                    <h3 class="card-label text-uppercase">{{ $language->hint }} ({{ $language->language }})</h3>
                </div>

                {{-- Value --}}
                <div class="form-group">
                    <label for="{{ $language->getField($language->lang_code,'value') }}">@if($language->lang_code == $default_lang) {!!$mend_sign!!} @endif Value:</label>
                    <input type="text"class="form-control" 
                    id="{{ $language->getField($language->lang_code,'value') }}"
                    name="{{ $language->getField($language->lang_code,'value') }}"
                    value="@if(old($language->getField($language->lang_code,'value'))){{ old($language->getField($language->lang_code,'value')) }}@else{{ $profile_detail->getValue($language->lang_code,'value') }}@endif"
                    placeholder="Enter {{ $language->hint }} value"
                    autocomplete="{{ $language->getField($language->lang_code,'value') }}"
                    spellcheck="false" autocapitalize="sentences" tabindex="0" autofocus />
                    @if ($errors->has($language->getField($language->lang_code,'value')))
                        <span class="help-block">
                            <strong class="form-text">{{ $errors->first($language->getField($language->lang_code,'value')) }}</strong>
                        </span>
                    @endif
                </div>

            </div>
            @endforeach

            <div class="card-footer">
                <button type="submit" class="btn btn-primary mr-2">Update {{ $custom_title }}</button>
                <a href="{{ route('admin.profile-details.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
        <!--end::Form-->
    </div>
</div>
@endsection

@push('extra-js-scripts')
<script>
$(document).ready(function () {
    $("#frmEditProfileDetails").validate({
        rules: {
            attribute: {
                required: true,
                not_empty: true,
                no_space: true,
            },
            '{{ $default_lang }}_value': {
                required: true,
                not_empty: true,
                minlength: 3,
            },
        },
        messages: {
            attribute:{
                required:"@lang('validation.required',['attribute'=>'attribute'])",
                not_empty:"@lang('validation.not_empty',['attribute'=>'attribute'])",
                no_space:"@lang('validation.no_space',['attribute'=>'attribute'])",
            },
            '{{ $default_lang }}_value': {
                required: "@lang('validation.required',['attribute'=>'value'])",
                not_empty: "@lang('validation.not_empty',['attribute'=>'value'])",
                minlength:"@lang('validation.min.string',['attribute'=>'value','min'=>3])",
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
            $(element).siblings('label').removeClass('text-danger');
        },
        errorPlacement: function (error, element) {
            if (element.attr("data-error-container")) {
                error.appendTo(element.attr("data-error-container"));
            } else {
                error.insertAfter(element);
            }
        }
    });
    $('#frmEditProfileDetails').submit(function () {
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
