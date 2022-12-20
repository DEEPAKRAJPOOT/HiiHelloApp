@extends('admin.layouts.app')
@push('breadcrumb')
    {!! Breadcrumbs::render('subscription_plans_create') !!}
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
        <form id="frmAddSubscriptionPlan" method="POST" action="{{ route('admin.subscription-plans.store') }}">
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

                {{-- Description --}}
                <div class="form-group">
                    <label for="{{ $language->getField($language->lang_code,'description') }}">@if($language->lang_code == $default_lang)@endif Description:</label>
                    <input type="text" class="form-control" id="{{ $language->getField($language->lang_code,'description') }}" name="{{ $language->getField($language->lang_code,'description') }}" value="{{ old($language->getField($language->lang_code,'description')) }}" placeholder="Enter {{ $language->hint }} description" autocomplete="{{ $language->getField($language->lang_code,'description') }}" spellcheck="false" autocapitalize="sentences" tabindex="0" autofocus />
                    @if ($errors->has($language->getField($language->lang_code,'description')))
                        <span class="help-block">
                            <strong class="form-text">{{ $errors->first($language->getField($language->lang_code,'description')) }}</strong>
                        </span>
                    @endif
                </div>

                {{-- Note --}}
                <div class="form-group">
                    <label for="{{ $language->getField($language->lang_code,'note') }}">@if($language->lang_code == $default_lang)@endif Note:</label>
                    <input type="text" class="form-control" id="{{ $language->getField($language->lang_code,'note') }}" name="{{ $language->getField($language->lang_code,'note') }}" value="{{ old($language->getField($language->lang_code,'note')) }}" placeholder="Enter {{ $language->hint }} note" autocomplete="{{ $language->getField($language->lang_code,'note') }}" spellcheck="false" autocapitalize="sentences" tabindex="0" autofocus />
                    @if ($errors->has($language->getField($language->lang_code,'note')))
                        <span class="help-block">
                            <strong class="form-text">{{ $errors->first($language->getField($language->lang_code,'note')) }}</strong>
                        </span>
                    @endif
                </div>

            </div>
            @endforeach

            <div class="card-body">
                
                {{-- months --}}
                <div class="form-group">
                    <label for="months">Months{!!$mend_sign!!}</label>
                    <input type="text" class="form-control @error('months') is-invalid @enderror" id="months" name="months" value="{{ old('months') }}" placeholder="Enter months" autocomplete="months" spellcheck="false" autocapitalize="sentences" tabindex="0" autofocus />
                    @if ($errors->has('months'))
                        <span class="help-block">
                            <strong class="form-text">{{ $errors->first('months') }}</strong>
                        </span>
                    @endif
                </div>

                {{-- Day --}}
                <div class="form-group">
                    <label for="day">Day{!!$mend_sign!!}</label>
                    <input type="text" class="form-control @error('day') is-invalid @enderror" id="day" name="day" value="{{ old('day') }}" placeholder="Enter day" autocomplete="day" spellcheck="false" autocapitalize="sentences" tabindex="0" autofocus required />
                    @if ($errors->has('day'))
                        <span class="help-block">
                            <strong class="form-text">{{ $errors->first('day') }}</strong>
                        </span>
                    @endif
                </div>

                {{-- Amount --}}
                <div class="form-group">
                    <label for="amount">Amount{!!$mend_sign!!}</label>
                    <input type="text" class="form-control @error('amount') is-invalid @enderror" id="amount" name="amount" value="{{ old('amount') }}" placeholder="Enter amount" autocomplete="amount" spellcheck="false" autocapitalize="sentences" tabindex="0" autofocus />
                    @if ($errors->has('amount'))
                        <span class="help-block">
                            <strong class="form-text">{{ $errors->first('amount') }}</strong>
                        </span>
                    @endif
                </div>

                {{-- Android Product --}}
                <div class="form-group">
                    <label for="android_product">Android Product{!!$mend_sign!!}</label>
                    <input type="text" class="form-control @error('android_product') is-invalid @enderror" id="android_product" name="android_product" value="{{ old('android product') }}" placeholder="Enter android_product" autocomplete="android_product" spellcheck="false" autocapitalize="sentences" tabindex="0" autofocus />
                    @if ($errors->has('android_product'))
                        <span class="help-block">
                            <strong class="form-text">{{ $errors->first('android_product') }}</strong>
                        </span>
                    @endif
                </div>

                {{-- Ios Product --}}
                <div class="form-group">
                    <label for="ios_product">Ios Product{!!$mend_sign!!}</label>
                    <input type="text" class="form-control @error('ios_product') is-invalid @enderror" id="ios_product" name="ios_product" value="{{ old('ios_product') }}" placeholder="Enter ios product" autocomplete="ios_product" spellcheck="false" autocapitalize="sentences" tabindex="0" autofocus />
                    @if ($errors->has('ios_product'))
                        <span class="help-block">
                            <strong class="form-text">{{ $errors->first('ios_product') }}</strong>
                        </span>
                    @endif
                </div>

                {{-- Popular --}}
                <div class="form-group" {{ $errors->has('is_popular') ? 'has-error' : '' }}>
                    <label for="is_popular">{!!$mend_sign!!}Popular:</label>
                    <div class="custom-file">
                        <div class="row">
                            <div class="col-md-1">
                                <input type="radio" id="yes" name="is_popular" value="y" data-error-container="#error-authorizable">
                                <label for="Yes">Yes</label><br> 
                            </div>
                            <div class="col-md-1">
                                <input type="radio" id="no" name="is_popular" value="n" data-error-container="#error-authorizable">
                                <label for="No">No</label><br>
                            </div>
                        </div>
                        <span id="error-authorizable"></span>
                        @if ($errors->has('is_popular'))
                            <span class="help-block">
                                <strong class="form-text">{{ $errors->first('is_popular') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>

            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary mr-2"> Add {{ $custom_title }}</button>
                <a href="{{ route('admin.subscription-plans.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
        <!--end::Form-->
    </div>
</div>
@endsection

@push('extra-js-scripts')
<script>
$(document).ready(function () {
    $("#frmAddSubscriptionPlan").validate({
        rules: {
           '{{ $default_lang }}_name': {
                required: true,
                not_empty: true,
                minlength: 3,
                maxlength: 150,
            },
            '{{ $default_lang }}_description': {
                required: false,
                not_empty: false,
                minlength: 3,
                maxlength: 150,
            },
            '{{ $default_lang }}_note': {
                required: false,
                not_empty: false,
                minlength: 3,
                maxlength: 150,
            },
            months: {
                required: true,
                not_empty: true,
            },
            amount: {
                required: true,
                not_empty: true,
            },
            android_product: {
                required: true,
                not_empty: true,
            },
            ios_product: {
                required: true,
                not_empty: true,
            },
            is_popular: {
                required: true,
                not_empty: true,
            },
        },
        messages: {
            '{{ $default_lang }}_name': {
                required: "@lang('validation.required',['attribute'=>'name'])",
                not_empty: "@lang('validation.not_empty',['attribute'=>'name'])",
                minlength:"@lang('validation.min.string',['attribute'=>'name','min'=>3])",
                maxlength:"@lang('validation.max.string',['attribute'=>'name','max'=>150])",
            },
            '{{ $default_lang }}_description': {
                required: "@lang('validation.required',['attribute'=>'description'])",
                not_empty: "@lang('validation.not_empty',['attribute'=>'description'])",
                minlength:"@lang('validation.min.string',['attribute'=>'description','min'=>3])",
                maxlength:"@lang('validation.max.string',['attribute'=>'description','max'=>150])",
            },
            '{{ $default_lang }}_note': {
                required: "@lang('validation.required',['attribute'=>'note'])",
                not_empty: "@lang('validation.not_empty',['attribute'=>'note'])",
                minlength:"@lang('validation.min.string',['attribute'=>'note','min'=>3])",
                maxlength:"@lang('validation.max.string',['attribute'=>'note','max'=>150])",
            },
            months: {
                required: "@lang('validation.required',['attribute'=>'months'])",
                not_empty: "@lang('validation.not_empty',['attribute'=>'months'])",
            },
            amount: {
                required: "@lang('validation.required',['attribute'=>'amount'])",
                not_empty: "@lang('validation.not_empty',['attribute'=>'amount'])",
            },
            android_product: {
                required: "@lang('validation.required',['attribute'=>'android product'])",
                not_empty: "@lang('validation.not_empty',['attribute'=>'android product'])",
            },
            ios_product: {
                required: "@lang('validation.required',['attribute'=>'ios product'])",
                not_empty: "@lang('validation.not_empty',['attribute'=>'ios product'])",
            },
            is_popular: {
                required: "@lang('validation.required',['attribute'=>'popular'])",
                not_empty: "@lang('validation.not_empty',['attribute'=>'popular'])",
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
    $('#frmAddSubscriptionPlan').submit(function () {
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
