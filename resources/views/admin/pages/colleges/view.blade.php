@extends('admin.layouts.app')

@push('breadcrumb')
    {!! Breadcrumbs::render('colleges_view', $college->id) !!}
@endpush

@section('content')
    <div class="container">
        <div class="card card-custom">
            <div class="card-header">
                <div class="card-title">
                    <span class="card-icon">
                        <i class="{{ $icon }} text-primary"></i>
                    </span>
                    <h3 class="card-label text-uppercase">{{ $custom_title }} Details</h3>
                </div>
            </div>

            <div class="profile-content">
                <div class="form-group col-md-12">
                    <div class="form-group col-md-12 row" style="margin:15px;">
                        <div class="form-group col-md-6">
                            <div class="mb-2">
                                <label class="control-label"><span class="mendatory" style="font-size:20px"></span>
                                    College Name :
                                    <b>
                                       {{ $college->name ?? '-' }}
                                    </b>
                                </label>
                            </div>
                            <div class="mb-2">
                                <label class="control-label"><span class="mendatory" style="font-size:20px"></span>
                                    University Name :
                                    <b>
                                       {{ $college->university ?? '-' }}
                                    </b>
                                </label>
                            </div>
                            <div class="mb-2">
                                <label class="control-label"><span class="mendatory" style="font-size:20px"></span>
                                    City :
                                    <b>
                                       {{ $college->district ?? '-' }}
                                    </b>
                                </label>
                            </div>
                            <div class="mb-2">
                                <label class="control-label"><span class="mendatory" style="font-size:20px"></span>
                                    State :
                                    <b>
                                       {{ $college->state ?? '-' }}
                                    </b>
                                </label>
                            </div>
                            <div class="mb-2">
                                <label class="control-label"><span class="mendatory" style="font-size:20px"></span>
                                    Created at:
                                    <b>
                                        {{ now()->create($college->created_at)->format('Y-m-d H:i:s') }}
                                    </b>
                                </label>
                            </div>
                            <div class="mb-2">
                                <label class="control-label"><span class="mendatory" style="font-size:20px"></span>
                                    Approved at:
                                    <b>
                                        @if(!empty($college->approved_at))
                                            {{ now()->create($college->approved_at)->format('Y-m-d H:i:s') }}
                                        @else
                                            N/A
                                        @endif
                                    </b>
                                </label>
                            </div>
                            <div class="mb-2">
                                <label class="control-label"><span class="mendatory" style="font-size:20px"></span>
                                    Associated Users:
                                    <b>
                                          {{ $college->users_count ?? 0 }}
                                    </b>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
