@extends('admin.layouts.app')

@push('breadcrumb')
    {!! Breadcrumbs::render('personality_update', $personality->id) !!}
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
        <form id="frmEditPersonality" method="POST" action="{{ route('admin.personalities.update', $personality->custom_id) }}" enctype="multipart/form-data">
            @csrf
            @method('put')

            @forelse($languages as $language)
            <div class="card-body">
                <div class="card-title">
                    <h3 class="card-label text-uppercase">{{ $language->hint }} ({{ $language->language }})</h3>
                </div>

                {{-- Title --}}
                <div class="form-group">
                    <label for="{{ $language->getField($language->lang_code,'title') }}">@if($language->lang_code == $default_lang) {!!$mend_sign!!} @endif Title:</label>
                    <input type="text"class="form-control" 
                    id="{{ $language->getField($language->lang_code,'title') }}"
                    name="{{ $language->getField($language->lang_code,'title') }}"
                    value="@if(old($language->getField($language->lang_code,'title'))){{ old($language->getField($language->lang_code,'title')) }}@else{{ $personality->getValue($language->lang_code,'title') }}@endif"
                    placeholder="Enter {{ $language->hint }} title"
                    autocomplete="{{ $language->getField($language->lang_code,'title') }}"
                    spellcheck="false" autocapitalize="sentences" tabindex="0" autofocus />
                    @if ($errors->has($language->getField($language->lang_code,'title')))
                        <span class="help-block">
                            <strong class="form-text">{{ $errors->first($language->getField($language->lang_code,'title')) }}</strong>
                        </span>
                    @endif
                </div>

                {{-- Description --}}
                <div class="form-group">
                    <label for="{{ $language->getField($language->lang_code,'description') }}">@if($language->lang_code == $default_lang) {!!$mend_sign!!} @endif Description:</label>
                    <textarea type="text"class="form-control" 
                    id="{{ $language->getField($language->lang_code,'description') }}"
                    name="{{ $language->getField($language->lang_code,'description') }}"
                    placeholder="Enter {{ $language->hint }} description"
                    autocomplete="{{ $language->getField($language->lang_code,'description') }}"
                    spellcheck="false" autocapitalize="sentences" tabindex="0" autofocus >@if(old($language->getField($language->lang_code,'description'))){{ old($language->getField($language->lang_code,'description')) }}@else{{ $personality->getValue($language->lang_code,'description') }}@endif</textarea>
                    @if ($errors->has($language->getField($language->lang_code,'description')))
                        <span class="help-block">
                            <strong class="form-text">{{ $errors->first($language->getField($language->lang_code,'description')) }}</strong>
                        </span>
                    @endif
                </div>
            </div>
            @endforeach

            <div class="card-body">
                {{-- Image --}}
                <div class="form-group">
                    <label for="image">Image</label>
                    <div class="custom-file">
                        <input type="file" class="custom-file-input" id="image" name="image" tabindex="0" />
                        <label class="custom-file-label @error('image') is-invalid @enderror" for="customFile">Choose file</label>
                        @if ($errors->has('image'))
                        <span class="text-danger">
                            <strong class="form-text">{{ $errors->first('image') }}</strong>
                        </span>
                    @endif
                    </div>
                </div>
                @if ($personality->image)
                    <div class="symbol symbol-120 mr-5">
                        <div class="symbol-label" style="background-image:url({{ generateURL($personality->image)}})"></div>
                    </div>
                @endif
            </div>

            <div class="card-footer">
                <button type="submit" class="btn btn-primary mr-2">Update {{ $custom_title }}</button>
                <a href="{{ route('admin.personalities.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
        <!--end::Form-->
    </div>
</div>
@endsection

@push('extra-js-scripts')
<script>
$(document).ready(function () {
    $("#frmEditPersonality").validate({
        rules: {
            '{{ $default_lang }}_title': {
                required: true,
                not_empty: true,
                minlength: 3,
            },
            '{{ $default_lang }}_description': {
                required: true,
                not_empty: true,
                minlength: 3,
            },
            image:{
                required: false,
                extension: "jpg|jpeg|png",
            },
        },
        messages: {
            '{{ $default_lang }}_title': {
                required: "@lang('validation.required',['attribute'=>'title'])",
                not_empty: "@lang('validation.not_empty',['attribute'=>'title'])",
                minlength:"@lang('validation.min.string',['attribute'=>'title','min'=>3])",
            },
            '{{ $default_lang }}_description': {
                required: "@lang('validation.required',['attribute'=>'description'])",
                not_empty: "@lang('validation.not_empty',['attribute'=>'description'])",
                minlength:"@lang('validation.min.string',['attribute'=>'description','min'=>3])",
            },
            image: {
                required: "@lang('validation.required',['attribute'=>'image'])",
                extension:"@lang('validation.mimetypes',['attribute'=>'image','value'=>'jpg|png|jpeg'])",
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
    $('#frmEditPersonality').submit(function () {
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
