@extends('admin.layouts.app')

@push('breadcrumb')
    {!! Breadcrumbs::render('interests_update', $interest->id) !!}
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
        <form id="frmEditInterest" method="POST" action="{{ route('admin.interests.update', $interest->custom_id) }}" enctype="multipart/form-data">
            @csrf
            @method('put')

            <div class="card-body">
                {{-- Parent Interest --}}
                <div class="form-group {{ $errors->has('parent_id') ? 'has-error' : '' }}">
                    <label for="parent_id">Parent Interest</label>
                    <select class="form-control select2" id="parent_id" name="parent_id" data-error-container="#parent-id-error">
                        <option value="" selected>Select Parent Interest</option>
                        @if(!$parent_interests->isEmpty())
                            @foreach($parent_interests as $parent_interest)
                                @if($parent_interest->id == $interest->parent_id)
                                    <option value="{{ $parent_interest->id }}" selected>{{ $parent_interest->title }}</option>
                                @else
                                     <option value="{{ $parent_interest->id }}">{{ $parent_interest->title }}</option>
                                @endif
                            @endforeach
                        @endif
                    </select>
                    <span id="parent-id-error"></span>
                    @if($errors->has('parent_id'))
                        <span class="help-block">
                            {{ $errors->first('parent_id') }}
                        </span>
                    @endif
                </div>  

                {{-- Location --}}
                <div class="form-group {{ $errors->has('location_id') ? 'has-error' : '' }}">
                    <label for="location_id">Locationt</label>
                    <select class="form-control select2" id="location_id" name="location_id" data-error-container="#location-id-error">
                        <option value="" selected>Select Location</option>
                        @if(!$locations->isEmpty())
                            @foreach($locations as $location)
                                @if($location->id == $interest->location_id)
                                    <option value="{{ $location->id }}" selected>{{ $location->name }}</option>
                                @else
                                     <option value="{{ $location->id }}">{{ $location->name }}</option>
                                @endif
                            @endforeach
                        @endif
                    </select>
                    <span id="location-id-error"></span>
                    @if($errors->has('location_id'))
                        <span class="help-block">
                            {{ $errors->first('location_id') }}
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
                    <input type="text"class="form-control" 
                    id="{{ $language->getField($language->lang_code,'title') }}"
                    name="{{ $language->getField($language->lang_code,'title') }}"
                    value="@if(old($language->getField($language->lang_code,'title'))){{ old($language->getField($language->lang_code,'title')) }}@else{{ $interest->getValue($language->lang_code,'title') }}@endif"
                    placeholder="Enter {{ $language->hint }} title"
                    autocomplete="{{ $language->getField($language->lang_code,'title') }}"
                    spellcheck="false" autocapitalize="sentences" tabindex="0" autofocus />
                    @if ($errors->has($language->getField($language->lang_code,'title')))
                        <span class="help-block">
                            <strong class="form-text">{{ $errors->first($language->getField($language->lang_code,'title')) }}</strong>
                        </span>
                    @endif
                </div>

            </div>
            @endforeach

            <div class="card-footer">
                <button type="submit" class="btn btn-primary mr-2">Update {{ $custom_title }}</button>
                <a href="{{ route('admin.interests.index') }}" class="btn btn-secondary">Cancel</a>
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

    $("#frmEditInterest").validate({
        rules: {
            parent_id: {
                required: false,
                not_empty: false,
            },
            location_id: {
                required: true,
                not_empty: true,
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
    $('#frmEditInterest').submit(function () {
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
