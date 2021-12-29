@extends('admin.layouts.app')
@push('breadcrumb')
    {!! Breadcrumbs::render('cms_update', $page->custom_id) !!}
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
        <form id="frmEditcms" method="POST" action="{{ route('admin.pages.update', $page->custom_id) }}" enctype="multipart/form-data">
            @csrf
            @method('put')

            @forelse($languages as $language)
            <div class="card-body">
                <div class="card-title">
                    <h3 class="card-label text-uppercase">{{ $language->hint }} ({{ $language->language }})</h3>
                </div>

                {{-- Name --}}
                <div class="form-group">
                    <label for="{{ $language->getField($language->lang_code,'title') }}">@if($language->lang_code == $default_lang) {!!$mend_sign!!} @endif Name:</label>
                    <input type="text"class="form-control" 
                    id="{{ $language->getField($language->lang_code,'title') }}"
                    name="{{ $language->getField($language->lang_code,'title') }}"
                    value="@if(old($language->getField($language->lang_code,'title'))){{ old($language->getField($language->lang_code,'title')) }}@else{{ $page->getValue($language->lang_code,'title') }}@endif"
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
                    
                    <textarea class="form-control description @error($language->getField($language->lang_code,'description')) is-invalid @enderror" id="{{ $language->getField($language->lang_code,'description') }}" name="{{ $language->getField($language->lang_code,'description') }}" placeholder="Enter {{ $language->hint }} description" autocomplete="{{ $language->getField($language->lang_code,'description') }}" spellcheck="true">@if(old($language->getField($language->lang_code,'description'))){{ old($language->getField($language->lang_code,'description')) }}@else{{ $page->getValue($language->lang_code,'description') }}@endif</textarea>

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
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary mr-2">Update {{ $custom_title }}</button>
                <a href="{{ route('admin.pages.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
        <!--end::Form-->
    </div>
</div>
@endsection

@push('extra-js-scripts')
<script type="text/javascript">
    var summernoteImageUpload = '{{ route('admin.summernote.imageUpload') }}';
    var summernoteMediaDelete = '{{ route('admin.summernote.mediaDelete') }}';
</script>
<script src="{{ asset('admin/plugins/summernote/summernotecustom.js') }}"></script>
<script>
$(document).ready(function () {
    // var summernoteElement = $('#description');
    var summernoteElement = $('.description');
    var imagePath = 'summernote/cms/image';
    summernoteElement.summernote({
            height: 300,
            callbacks: {
                onImageUpload : function(files, editor, welEditable) {
                     for(var i = files.length - 1; i >= 0; i--) {
                             sendFile(files[i], this,imagePath);
                    }
                },
                onMediaDelete : function(target) {
                    deleteFile(target[0].src);
                },
            }
    });
    $("#frmEditcms").validate({
        rules: {
            '{{ $default_lang }}_title': {
                required: true,
                not_empty: true,
                minlength: 3,
                // remote: {
                //     url: "{{ route('admin.check.title') }}",
                //     type: "post",
                //     data: {
                //         _token: "{{csrf_token()}}",
                //         id: "{{$page->id}}",
                //         type: "cms",
                //     }
                // },
            },
            '{{ $default_lang }}_description': {
                required: true,
                not_empty: true,
            },
            image:{
                required:false,
                extension: "jpg|jpeg|png",
            },
        },
        messages: {
            '{{ $default_lang }}_title': {
                required: "@lang('validation.required',['attribute'=>'title'])",
                not_empty: "@lang('validation.not_empty',['attribute'=>'title'])",
                minlength:"@lang('validation.min.string',['attribute'=>'title','min'=>3])",
                // remote:"@lang('validation.unique',['attribute'=>'title'])",
            },
            '{{ $default_lang }}_description': {
                required: "@lang('validation.required',['attribute'=>'description'])",
                not_empty: "@lang('validation.not_empty',['attribute'=>'description'])",
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
    $('#frmEditcms').submit(function (e) {
      if(summernoteElement.summernote('isEmpty')) {
        $('#description-error').remove();
        $('<span class="text-danger" id="description-error"><strong class="form-text">The description field is required.</strong></span>').insertAfter('.note-editor');
        e.preventDefault();
        return false;
      }else {
        if ($(this).valid()) {
            addOverlay();
            $("input[type=submit], input[type=button], button[type=submit]").prop("disabled", "disabled");
            return true;
        } else {
            return false;
        }
      }
    });

    //tell the validator to ignore Summernote elements
    $('form').each(function () {
        if ($(this).data('validator'))
            $(this).data('validator').settings.ignore = ".note-editor *";
    });
});
</script>
@endpush
