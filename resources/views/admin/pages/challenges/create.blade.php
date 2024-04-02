@extends('admin.layouts.app')

@push('breadcrumb')
    {!! Breadcrumbs::render('organic_matches_create') !!}
@endpush

@section('content')
<div class="container">
    <div class="card card-custom">
        <div class="card-header">
            <div class="card-title">
                <span class="card-icon">
                    <i class="{{$icon}} text-primary"></i>
                </span>
                <h3 class="card-label text-uppercase">Create {{ $custom_title }}</h3>
            </div>
        </div>
        <form id="formCreateUserLike" name="formCreateUserLike" method="post" action="{{ route('admin.organic-matches.store') }}">
            @csrf
            <div class="row">
                <div class="col-md-12">
                    <div class="card-body">
                        <div class="profile-content">
                            <div class="col-md-12">
                                <div class="form-group" {{ $errors->has('liked_by_user') ? 'has-error' : '' }}>
                                    <label for="liked_by_user">{!!$mend_sign!!}Sender :</label>
                                    <div class="custom-file">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <select id="liked_by_user" class="form-control" name="liked_by_user">
                                                    <option></option>
                                                </select>
                                            </div>
                                        </div>
                                        @if ($errors->has('liked_by_user'))
                                            <span class="help-block">
                                                <strong class="form-text">{{ $errors->first('liked_by_user') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group" {{ $errors->has('target_user') ? 'has-error' : '' }}>
                                    <label for="target_user">{!!$mend_sign!!}Receiver :</label>
                                    <div class="custom-file">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <select id="target_user" class="form-control" name="target_user">
                                                    <option></option>
                                                </select>
                                            </div>
                                        </div>
                                        @if ($errors->has('target_user'))
                                            <span class="help-block">
                                                <strong class="form-text">{{ $errors->first('target_user') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 custom-checkbox">
                                <input id="create_match" type="checkbox" class="form-control" name="create_match" value="1">
                                <label for="create_match">Add a Like Back (Create Match)</label>
                            </div>
                            @if(session()->has('error'))
                                <div class="col-md-12">
                                    <span class="help-block text-danger">
                                        <strong class="form-text">{{ session()->get('error') }}</strong>
                                    </span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary mr-2 text-uppercase">Create {{ $custom_title }}</button>
                <a href="{{ route('admin.organic-matches.index') }}" class="btn btn-secondary text-uppercase">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('extra-js-scripts')
<script type="text/javascript">
    $(document).ready(function () {
        $("#formCreateUserLike").validate({
            rules: {
                liked_by_user: {
                    required: true,
                    not_empty: true,
                },
                target_user: {
                    required: true,
                    not_empty: true,
                }
            },
            messages: {
                liked_by_user:{
                    required:"@lang('validation.required',['attribute'=>'Liked By'])",
                    not_empty:"@lang('validation.not_empty',['attribute'=>'Liked By'])"
                },
                target_user: {
                    required: "@lang('validation.required',['attribute'=>'Target User'])",
                    not_empty: "@lang('validation.not_empty',['attribute'=>'Target User'])",
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
        $('#formCreateUserLike').submit(function () {
            if($(this).valid()){
                addOverlay();
                $('input[type=submit],input[type=button],button[type=submit]').prop("disabled", "disabled");
                return true;
            } else {
                return false;
            }
        });
        $('#liked_by_user,#target_user').each(function(){
            $(this).select2({
                'tags':false,
                'width':'100%',
                'placeholder':'Select User',
                'ajax':{
                    'url':'{{ route("admin.users.selection-listing") }}',
                    'type':'get',
                    'dataType':'json',
                    'data':function(params){
                        return {
                            'search':params.term,
                            'page':params.page || 1
                        };
                    }
                },
                'escapeMarkup':function(markup){
                    return markup;
                },
                'templateResult':function(a){
                    return a.text;
                },
                'templateSelection':function(a){
                    return a.selection;
                }
            });
        });
    });

</script>
@endpush