@extends('admin.layouts.app')

@push('breadcrumb')
    {!! Breadcrumbs::render('interests_create') !!}
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
        <form id="frmAddInterest" method="POST" action="{{ route('admin.interests.store') }}" enctype="multipart/form-data">
            @csrf

            <div class="card-body">
                {{-- Parent Interest --}}
                <div class="form-group {{ $errors->has('parent_id') ? 'has-error' : '' }}">
                    <label for="parent_id">Parent Interest</label>
                    <select class="form-control select2" id="parent_id" name="parent_id" data-error-container="#parent-id-error">
                        <option value="" selected>Select Parent Interest</option>
                        @if(!$parent_interests->isEmpty())
                            @foreach($parent_interests as $parent_interest)
                                <option value="{{ $parent_interest->id }}">{{ $parent_interest->title }}</option>
                            @endforeach
                        @endif
                    </select>
                    <span id="parent-id-error"></span>
                    @if($errors->has('parent_id'))
                        <span class="help-block">
                            <strong class="form-text"> {{ $errors->first('parent_id') }}</strong>
                        </span>
                    @endif
                </div>

                {{-- Location --}}
                <div class="form-group {{ $errors->has('location_id') ? 'has-error' : '' }}">
                    <label for="location_id">Location</label>
                    <select class="form-control select2" id="location_id" name="location_id" data-error-container="#location-id-error">
                        <option value="" selected>Select Location</option>
                        @if(!$locations->isEmpty())
                            @foreach($locations as $location)
                                <option value="{{ $location->id }}">{{ $location->name }}</option>
                            @endforeach
                        @endif
                    </select>
                    <span id="location-id-error"></span>
                    @if($errors->has('location_id'))
                        <span class="help-block">
                            <strong class="form-text"> {{ $errors->first('location_id') }}</strong>
                        </span>
                    @endif
                </div>

                {{-- Sub Level --}}
                <div class="form-group {{ $errors->has('level') ? 'has-error' : '' }}">
                    <label for="level">Sub Level</label>
                    <input type="text" class="form-control" id="level" name="level" value="{{ old('title') }}" placeholder="Enter Sub Level" spellcheck="false" autocapitalize="sentences" tabindex="0" autofocus />
                    @if ($errors->has('level')))
                        <span class="help-block">
                            <strong class="form-text">{{ $errors->first('level') }}</strong>
                        </span>
                    @endif
                </div>
            </div>

            @forelse($languages as $language)
            <div class="card-body">
                <div class="card-title">
                    <h3 class="card-label text-uppercase">{{ $language->hint }} ({{ $language->language }})</h3>
                </div>

                {{-- Title --}}
                <div class="form-group">
                    <label for="{{ $language->getField($language->lang_code,'title') }}">@if($language->lang_code == $default_lang) {!!$mend_sign!!} @endif Title:</label>
                    <input type="text" class="form-control" id="{{ $language->getField($language->lang_code,'title') }}" name="{{ $language->getField($language->lang_code,'title') }}" value="{{ old($language->getField($language->lang_code,'title')) }}" placeholder="Enter {{ $language->hint }} title" autocomplete="{{ $language->getField($language->lang_code,'title') }}" spellcheck="false" autocapitalize="sentences" tabindex="0" autofocus />
                    @if ($errors->has($language->getField($language->lang_code,'title')))
                        <span class="help-block">
                            <strong class="form-text">{{ $errors->first($language->getField($language->lang_code,'title')) }}</strong>
                        </span>
                    @endif
                </div>
            </div>
            @endforeach

            <div class="card-footer">
                <button type="submit" class="btn btn-primary mr-2 text-uppercase"> Add {{ $custom_title }}</button>
                <a href="{{ route('admin.interests.index') }}" class="btn btn-secondary text-uppercase">Cancel</a>
            </div>
        </form>
        <!--end::Form-->
    </div>
</div>
@endsection

@push('extra-js-scripts')
<script src="{{ asset('admin/plugins/select2/js/select2.full.js') }}" type="text/javascript"></script>
<script type="text/javascript">
$(document).ready(function () {
    $('#parent_id').select2({
        placeholder: 'Select Parent Interest',
    });
    $('#location_id').select2({
        placeholder: 'Select Location',
    });

    $("#frmAddInterest").validate({
        rules: {
            parent_id: {
                required: false,
                not_empty: false,
            },
            location_id: {
                required: true,
                not_empty: true,
            },
            level: {
                required: false,
                number: true,
                not_empty: false,
            },
            '{{ $default_lang }}_title': {
                required: true,
                not_empty: true,
                minlength: 3,
            },
        },
        messages: {
            parent_id:{
                required:"@lang('validation.required',['attribute'=>'parent interest'])",
                not_empty:"@lang('validation.not_empty',['attribute'=>'parent interest'])",
            },
            location_id:{
                required:"@lang('validation.required',['attribute'=>'location'])",
                not_empty:"@lang('validation.not_empty',['attribute'=>'location'])",
            },
            level:{
                required:"@lang('validation.required',['attribute'=>'sub level'])",
                number:"@lang('validation.numeric',['attribute'=>'sub level'])",
                not_empty:"@lang('validation.not_empty',['attribute'=>'sub level'])",
            },
            '{{ $default_lang }}_title': {
                required: "@lang('validation.required',['attribute'=>'title'])",
                not_empty: "@lang('validation.not_empty',['attribute'=>'title'])",
                minlength:"@lang('validation.min.string',['attribute'=>'title','min'=>3])",
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
