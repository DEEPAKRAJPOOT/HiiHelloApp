@extends('admin.layouts.app')

@push('breadcrumb')
    {!! Breadcrumbs::render('app_detail_create') !!}
@endpush

@section('content')
<div class="container">
    <div class="card card-custom">
        <div class="card-header">
            <div class="card-title">
                <span class="card-icon">
                    <i class="{{$icon}} text-primary"></i>
                </span>
                <h3 class="card-label text-uppercase">Add {{ $custom_title }}</h3>
            </div>
        </div>

        <form id="frmSendPushNotification" name="frmSendPushNotification" method="POST" action="{{ route('admin.app-details.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="row">
                <div class="col-md-12">
                    <div class="card-body ">
                        <div class="profile-content">
                            <div class="col-md-12">

                                {{-- Veirification Photo (Male) --}}
                                <div class="form-group">
                                    <label for="verification_image_male">Veirification Photo (Male)</label>
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" id="verification_image_male" name="verification_image_male" tabindex="0" />
                                        <label class="custom-file-label @error('verification_image_male') is-invalid @enderror" for="customFile">Choose file</label>
                                        @if ($errors->has('verification_image_male'))
                                            <span class="text-danger">
                                                <strong class="form-text">{{ $errors->first('verification_image_male') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                @if ($app_details[0]->value)
                                    <div class="symbol symbol-120 mr-5">
                                        <div class="symbol-label" style="background-image:url({{ generateURL($app_details[0]->value)}})"></div>
                                    </div>
                                @endif

                                {{-- Veirification Video (Male) --}}
                                <br><br>
                                <div class="form-group">
                                    <label for="verification_video_male">Veirification Video (Male)</label>
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" id="verification_video_male" name="verification_video_male" tabindex="0" />
                                        <label class="custom-file-label @error('verification_video_male') is-invalid @enderror" for="customFile">Choose file</label>
                                        @if ($errors->has('verification_video_male'))
                                            <span class="text-danger">
                                                <strong class="form-text">{{ $errors->first('verification_video_male') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                @if ($app_details[1]->value)
                                    <video width="250" height="250" controls>
                                        <source src="{{ generateURL($app_details[1]->value) }}" type="video/mp4">
                                        <source src="{{ generateURL($app_details[1]->value) }}" type="video/ogg">
                                        Your browser does not support the video tag.
                                    </video>
                                @endif

                                {{--Veirification Photo (Female) --}}
                                <br><br>
                                <div class="form-group">
                                    <label for="verification_image_female">Veirification Photo (Female)</label>
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" id="verification_image_female" name="verification_image_female" tabindex="0" />
                                        <label class="custom-file-label @error('verification_image_female') is-invalid @enderror" for="customFile">Choose file</label>
                                        @if ($errors->has('verification_image_female'))
                                            <span class="text-danger">
                                                <strong class="form-text">{{ $errors->first('verification_image_female') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>  
                                 @if ($app_details[2]->value)
                                    <div class="symbol symbol-120 mr-5">
                                        <div class="symbol-label" style="background-image:url({{ generateURL($app_details[2]->value)}})"></div>
                                    </div>
                                @endif

                                {{--Veirification Video (Female) --}}
                                <br><br>
                                <div class="form-group">
                                    <label for="verification_video_female">Veirification Video (Female)</label>
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" id="verification_video_female" name="verification_video_female" tabindex="0" />
                                        <label class="custom-file-label @error('verification_video_female') is-invalid @enderror" for="customFile">Choose file</label>
                                        @if ($errors->has('verification_video_female'))
                                            <span class="text-danger">
                                                <strong class="form-text">{{ $errors->first('verification_video_female') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                @if ($app_details[3]->value)
                                    <video width="250" height="250" controls>
                                        <source src="{{ generateURL($app_details[3]->value) }}" type="video/mp4">
                                        <source src="{{ generateURL($app_details[3]->value) }}" type="video/ogg">
                                        Your browser does not support the video tag.
                                    </video>
                                @endif
                                
                            </div>
                        </div>
                    </div>
                </div>
            </div>  
            <div class="card-footer">
                <button type="submit" class="btn btn-primary mr-2 text-uppercase"> Send {{ $custom_title }}</button>
                <a href="{{ route('admin.app-details.index') }}" class="btn btn-secondary text-uppercase">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('extra-js-scripts')
<script type="text/javascript">
    $(document).ready(function () {
        $("#frmSendPushNotification").validate({
            rules: {
                verification_image_male:{
                    extension: "jpg|jpeg|png",
                },
                verification_image_female:{
                    extension: "jpg|jpeg|png",
                },
                verification_video_male:{
                    extension: "mp4,ogx,oga,ogv,ogg,webm,flv,m3u8,ts,3gp,mov,avi,wmv,m4v",
                },
                verification_video_female:{
                    extension: "mp4,ogx,oga,ogv,ogg,webm,flv,m3u8,ts,3gp,mov,avi,wmv,m4v",
                },
            },
            messages: {
                verification_image_male: {
                    extension:"@lang('validation.mimetypes',['attribute'=>'verification photo','value'=>'jpg|png|jpeg'])",
                },
                verification_image_female: {
                    extension:"@lang('validation.mimetypes',['attribute'=>'verification photo','value'=>'jpg|png|jpeg'])",
                },
                verification_video_male: {
                    extension:"@lang('validation.mimetypes',['attribute'=>'verification video','value'=>'mp4,ogx,oga,ogv,ogg,webm,flv,m3u8,ts,3gp,mov,avi,wmv,m4v'])",
                },
                verification_video_female: {
                    extension:"@lang('validation.mimetypes',['attribute'=>'verification video','value'=>'mp4,ogx,oga,ogv,ogg,webm,flv,m3u8,ts,3gp,mov,avi,wmv,m4v'])",
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
        $('#frmSendPushNotification').submit(function () {
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
