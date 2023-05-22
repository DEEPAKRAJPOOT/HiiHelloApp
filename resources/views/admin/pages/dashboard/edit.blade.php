@extends('admin.layouts.app')
@push('breadcrumb')
    {!! Breadcrumbs::render('dashboard_update') !!}
@endpush
@push('extra-css-styles')
<style type="text/css">
    .full-loader-div{
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        text-align: center;
        background-color: rgb(255,255,255,0.5);
    }
    .full-loader-div i {
        font-size: 30px;
        position: absolute;
        left: 50%;
        top: 50%;
        transform: translate(-50%,-50%);
        color: #808080;
    }
    .bind-ajax-update[disabled]{
        opacity: 1!important;
    }
</style>
@endpush
@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12 mb-4 text-right">
            <a href="{{ route('admin.dashboard.preview') }}" class="btn btn-primary" target="_blank"><i class="fa fa-external-link-alt"></i> Preview</a>
            <a href="javascript:void(0)" data-toggle="modal" data-target="#publishConfirmModal" class="btn btn-success"><i class="fa fa-save"></i> Publish</a>
        </div>
    </div>
    <div class="card card-custom mb-8">
        <div class="card-header">
            <div class="card-title">
                <h2 class="card-label text-uppercase">App Traction</h2>
            </div>
        </div>
        <div class="card-body position-relative">
            <div class="form-group">
                <div class="row">
                    <div class="col-md-6">
                        <select name="app_traction" class="form-control bind-ajax-fetch">
                            <option value="">-- Select Target Month --</option>
                            @php
                                $target_date = now();
                            @endphp
                            @while($target_date->format('Ym') != '202209')
                                <option value="{{ $target_date->format('Y-m-d') }}">{{ $target_date->format('F Y') }}</option>
                                @php
                                    $target_date = $target_date->subMonth();
                                @endphp
                            @endwhile
                        </select>
                    </div>
                    <div class="col-md-6 text-right">
                        <button type="button" class="btn btn-primary px-8 bind-ajax-update" data-target-name="app_traction">Update App Traction</button>
                    </div>
                </div>
            </div>
            <div class="form-group mb-0">
                <div class="row">
                    <div class="col-md-4 my-3">
                        <label>Organic Downloads</label>
                        <input type="number" class="form-control" name="users_downloads_organic" data-fill-from="users_downloads_organic" data-field-for="app_traction" placeholder="Organic Downloads">
                    </div>
                    <div class="col-md-4 my-3">
                        <label>Paid Downloads</label>
                        <input type="number" class="form-control" name="users_downloads_paid" data-fill-from="users_downloads_paid" data-field-for="app_traction" placeholder="Paid Downloads">
                    </div>
                    <div class="col-md-4 my-3">
                        <label>Referral Downloads</label>
                        <input type="number" class="form-control" name="users_downloads_referrals" data-fill-from="users_downloads_referrals" data-field-for="app_traction" placeholder="Referral Downloads">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 my-3">
                        <label>Uninstalls</label>
                        <input type="number" class="form-control" name="users_uninstalls" data-fill-from="users_uninstalls" data-field-for="app_traction" placeholder="Uninstalls">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 my-3">
                        <label>Percentage</label>
                        <div class="row">
                            <div class="col">Male Mix <b><span data-range-from="users_percentage">50</span>%</b></div>
                            <div class="col text-right">Female Mix <b><span data-reverse-range-from="users_percentage"></span>%</b></div>
                        </div>
                        <input type="range" min="0" max="100" class="form-control p-0" name="users_percentage" data-fill-from="users_percentage" data-field-for="app_traction" step="1" value="50" placeholder="Percentage">
                    </div>
                </div>
            </div>
            <div class="full-loader-div d-none">
                <i class="fa fa-circle-notch fa-spin"></i>
            </div>
        </div>
    </div>
    <div class="card card-custom mb-8">
        <div class="card-header">
            <div class="card-title">
                <h2 class="card-label text-uppercase">App Engagement</h2>
            </div>
        </div>
        <div class="card-body position-relative">
            <div class="form-group">
                <div class="row">
                    <div class="col-md-6">
                        <select name="app_engagement" class="form-control bind-ajax-fetch">
                            <option value="">-- Select Target Month --</option>
                            @php
                                $target_date = now();
                            @endphp
                            @while($target_date->format('Ym') != '202209')
                                <option value="{{ $target_date->format('Y-m-d') }}">{{ $target_date->format('F Y') }}</option>
                                @php
                                    $target_date = $target_date->subMonth();
                                @endphp
                            @endwhile
                        </select>
                    </div>
                    <div class="col-md-6 text-right">
                        <button type="button" class="btn btn-primary px-8 bind-ajax-update" data-target-name="app_engagement">Update App Engagement</button>
                    </div>
                </div>
            </div>
            <div class="form-group mb-0">
                <div class="row">
                    <div class="col-md-12 mt-3">
                        <h5>Daily Time spent per active user</h5>
                    </div>
                    <div class="col-md-4 my-3">
                        <label>Male Subscribers</label>
                        <input type="text" class="form-control" name="daily_time_male_subscribers" data-fill-from="daily_time_male_subscribers" data-field-for="app_engagement" placeholder="Daily Time spent by Male Subscribers" autocomplete="off" spellcheck="false">
                    </div>
                    <div class="col-md-4 my-3">
                        <label>Male Non-Subscribers</label>
                        <input type="text" class="form-control" name="daily_time_male_non_subscribers" data-fill-from="daily_time_male_non_subscribers" data-field-for="app_engagement" placeholder="Daily Time spent by Male Non-Subscribers" autocomplete="off" spellcheck="false">
                    </div>
                    <div class="col-md-4 my-3">
                        <label>Female Users</label>
                        <input type="text" class="form-control" name="daily_time_female_users" data-fill-from="daily_time_female_users" data-field-for="app_engagement" placeholder="Daily Time spent by Female Users" autocomplete="off" spellcheck="false">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 mt-3">
                        <h5>Logins/App FireUps</h5>
                    </div>
                    <div class="col-md-4 my-3">
                        <label>Male Subscribers</label>
                        <input type="text" class="form-control" name="logins_male_subscribers" data-fill-from="logins_male_subscribers" data-field-for="app_engagement" placeholder="Logins by Male Subscribers" autocomplete="off" spellcheck="false">
                    </div>
                    <div class="col-md-4 my-3">
                        <label>Male Non-Subscribers</label>
                        <input type="text" class="form-control" name="logins_male_non_subscribers" data-fill-from="logins_male_non_subscribers" data-field-for="app_engagement" placeholder="Logins by Male Non-Subscribers" autocomplete="off" spellcheck="false">
                    </div>
                    <div class="col-md-4 my-3">
                        <label>Female Users</label>
                        <input type="text" class="form-control" name="logins_female_users" data-fill-from="logins_female_users" data-field-for="app_engagement" placeholder="Logins by Female Users" autocomplete="off" spellcheck="false">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 mt-3">
                        <h5>Active Users</h5>
                    </div>
                    <div class="col-md-6 my-3">
                        <label>Daily Active Users (DAU)</label>
                        <input type="number" class="form-control" name="active_users_daily" data-fill-from="active_users_daily" data-field-for="app_engagement" placeholder="Daily Active Users">
                    </div>
                    <div class="col-md-6 my-3">
                        <label>Monthly Active Users (MAU)</label>
                        <input type="number" class="form-control" name="active_users_monthly" data-fill-from="active_users_monthly" data-field-for="app_engagement" placeholder="Monthly Active Users">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 mt-3">
                        <h5>Notifications Sent to Users</h5>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 my-3">
                        <label>Email (Remarketing/Bday)</label>
                        <input type="number" class="form-control" name="notifications_sent_email" data-fill-from="notifications_sent_email" data-field-for="app_engagement" placeholder="Email Notifications Sent">
                    </div>
                    <div class="col-md-6 my-3">
                        <label>Email Notifications Clicked - <b><span data-range-from="notifications_clicked_email">0</span>%</b></label>
                        <input type="range" min="0" max="100" class="form-control p-0" name="notifications_clicked_email" data-fill-from="notifications_clicked_email" data-field-for="app_engagement" step="1" value="0" placeholder="Percentage">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 my-3">
                        <label>SMS (login)</label>
                        <input type="number" class="form-control" name="notifications_sent_sms" data-fill-from="notifications_sent_sms" data-field-for="app_engagement" placeholder="SMS Notifications Sent">
                    </div>
                    <div class="col-md-6 my-3">
                        <label>SMS Notifications Clicked - <b><span data-range-from="notifications_clicked_sms">0</span>%</b></label>
                        <input type="range" min="0" max="100" class="form-control p-0" name="notifications_clicked_sms" data-fill-from="notifications_clicked_sms" data-field-for="app_engagement" step="1" value="0" placeholder="Percentage">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 my-3">
                        <label>In-App (Remarketing/Nudge)</label>
                        <input type="number" class="form-control" name="notifications_sent_in_app" data-fill-from="notifications_sent_in_app" data-field-for="app_engagement" placeholder="In-App Notifications Sent">
                    </div>
                    <div class="col-md-6 my-3">
                        <label>In-App Notifications Clicked - <b><span data-range-from="notifications_clicked_in_app">0</span>%</b></label>
                        <input type="range" min="0" max="100" class="form-control p-0" name="notifications_clicked_in_app" data-fill-from="notifications_clicked_in_app" data-field-for="app_engagement" step="1" value="0" placeholder="Percentage">
                    </div>
                </div>
            </div>
            <div class="full-loader-div d-none">
                <i class="fa fa-circle-notch fa-spin"></i>
            </div>
        </div>
    </div>
    <div class="card card-custom mb-8">
        <div class="card-header">
            <div class="card-title">
                <h2 class="card-label text-uppercase">App Retention</h2>
            </div>
        </div>
        <div class="card-body position-relative">
            <div class="form-group">
                <div class="row">
                    <div class="col-md-6">
                        <select name="app_retention" class="form-control bind-ajax-fetch">
                            <option value="">-- Select Target Month --</option>
                            @php
                                $target_date = now();
                            @endphp
                            @while($target_date->format('Ym') != '202209')
                                <option value="{{ $target_date->format('Y-m-d') }}">{{ $target_date->format('F Y') }}</option>
                                @php
                                    $target_date = $target_date->subMonth();
                                @endphp
                            @endwhile
                        </select>
                    </div>
                    <div class="col-md-6 text-right">
                        <button type="button" class="btn btn-primary px-8 bind-ajax-update" data-target-name="app_retention">Update App Retention</button>
                    </div>
                </div>
            </div>
            <div class="form-group mb-0">
                <div class="row">
                    <div class="col-md-4 my-3">
                        <label>Day 1 - <b><span data-range-from="app_retention_d1">0</span>%</b></label>
                        <input type="range" min="0" max="100" class="form-control p-0" name="app_retention_d1" data-fill-from="app_retention_d1" data-field-for="app_retention" step="1" value="0" placeholder="Percentage">
                    </div>
                    <div class="col-md-4 my-3">
                        <label>Day 7 - <b><span data-range-from="app_retention_d7">0</span>%</b></label>
                        <input type="range" min="0" max="100" class="form-control p-0" name="app_retention_d7" data-fill-from="app_retention_d7" data-field-for="app_retention" step="1" value="0" placeholder="Percentage">
                    </div>
                    <div class="col-md-4 my-3">
                        <label>Day 30 - <b><span data-range-from="app_retention_d30">0</span>%</b></label>
                        <input type="range" min="0" max="100" class="form-control p-0" name="app_retention_d30" data-fill-from="app_retention_d30" data-field-for="app_retention" step="1" value="0" placeholder="Percentage">
                    </div>
                </div>
            </div>
            <div class="full-loader-div d-none">
                <i class="fa fa-circle-notch fa-spin"></i>
            </div>
        </div>
    </div>
    <div class="card card-custom mb-8">
        <div class="card-header">
            <div class="card-title">
                <h2 class="card-label text-uppercase">Paid Users</h2>
            </div>
        </div>
        <div class="card-body position-relative">
            <div class="form-group">
                <div class="row">
                    <div class="col-md-6">
                        <select name="app_paid_users" class="form-control bind-ajax-fetch">
                            <option value="">-- Select Target Month --</option>
                            @php
                                $target_date = now();
                            @endphp
                            @while($target_date->format('Ym') != '202209')
                                <option value="{{ $target_date->format('Y-m-d') }}">{{ $target_date->format('F Y') }}</option>
                                @php
                                    $target_date = $target_date->subMonth();
                                @endphp
                            @endwhile
                        </select>
                    </div>
                    <div class="col-md-6 text-right">
                        <button type="button" class="btn btn-primary px-8 bind-ajax-update" data-target-name="app_paid_users">Update App Retention</button>
                    </div>
                </div>
            </div>
            <div class="form-group mb-0">
                <div class="row">
                    <div class="col-md-6 my-3">
                        <label>Weekly Subscription</label>
                        <input type="number" class="form-control" name="paid_users_weekly" data-fill-from="paid_users_weekly" data-field-for="app_paid_users" placeholder="Users with Weekly Subscription">
                    </div>
                    <div class="col-md-6 my-3">
                        <label>Monthly Subscription</label>
                        <input type="number" class="form-control" name="paid_users_monthly" data-fill-from="paid_users_monthly" data-field-for="app_paid_users" placeholder="Users with Monthly Subscription">
                    </div>
                    <div class="col-md-6 my-3">
                        <label>Half Yearly Subscription</label>
                        <input type="number" class="form-control" name="paid_users_half_yearly" data-fill-from="paid_users_half_yearly" data-field-for="app_paid_users" placeholder="Users with Half Yearly Subscription">
                    </div>
                    <div class="col-md-6 my-3">
                        <label>Yearly Subscription</label>
                        <input type="number" class="form-control" name="paid_users_yearly" data-fill-from="paid_users_yearly" data-field-for="app_paid_users" placeholder="Users with Yearly Subscription">
                    </div>
                </div>
            </div>
            <div class="full-loader-div d-none">
                <i class="fa fa-circle-notch fa-spin"></i>
            </div>
        </div>
    </div>
</div>
<div id="publishConfirmModal" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <div class="px-5">
                    <div class="row my-5">
                        <div class="col-md-12 mb-8">
                            <h5>Publish all changes?</h5>
                        </div>
                        <div class="col-md-8 mx-auto">
                            <div class="row">
                                <div class="col-md-6">
                                    <a href="{{ route('admin.dashboard.publish') }}" class="btn btn-primary btn-block"><i class="fa fa-save"></i> Publish</a>
                                </div>
                                <div class="col-md-6">
                                    <a href="javascript:void(0)" class="btn btn-danger btn-block" data-dismiss="modal"><i class="fa fa-times"></i> Cancel</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>      
    </div>
</div>
@endsection
@push('extra-js-scripts')
<script type="text/javascript">
    $(document).on('change','.bind-ajax-fetch',function(){
        var this_element = $(this);
        var this_name = $(this).attr('name');
        var this_value = $(this).val();
        $.ajax({
            'url':'{{ route("admin.dashboard.get-fields") }}',
            'type':'post',
            'dataType':'json',
            'data':{
                '_token':'{{ csrf_token() }}',
                'name':this_name,
                'value':this_value
            },
            'beforeSend':function(){
                this_element.closest('.card').find('.full-loader-div').removeClass('d-none');
            },
            'success':function(response){
                for(var key in response.data){
                    $('[data-fill-from="'+key+'"]').val(response.data[key]);
                }
                $('[type="range"]').trigger('input');
            },
            'complete':function(){
                this_element.closest('.card').find('.full-loader-div').addClass('d-none');
            }
        });
    });
    $(document).on('click','.bind-ajax-update',function(){
        var this_button = $(this);
        var this_html = $(this).html();
        var this_name = $(this).attr('data-target-name');
        var data_fields = {
            '_token':'{{ csrf_token() }}',
            'name':this_name,
            'value':$('[name="'+this_name+'"]').val()
        };
        $('[data-field-for="'+this_name+'"]').each(function(){
            data_fields[$(this).attr('name')] = $(this).val();
        });
        $.ajax({
            'url':'{{ route("admin.dashboard.update") }}',
            'type':'post',
            'dataType':'json',
            'data':data_fields,
            'beforeSend':function(){
                this_button.prop('disabled',true);
                this_button.html('<i class="fa fa-circle-notch fa-spin p-0"></i> Updating');
            },
            'success':function(response){
                this_button.addClass('btn-success');
                this_button.removeClass('btn-primary btn-danger');
                this_button.html('<i class="fa fa-check p-0"></i> Updated');
            },
            'error':function(){
                this_button.addClass('btn-danger');
                this_button.removeClass('btn-primary btn-success');
                this_button.html('<i class="fa fa-times p-0"></i> Error Updating');
            },
            'complete':function(){
                setTimeout(function(){
                    this_button.addClass('btn-primary');
                    this_button.removeClass('btn-danger btn-success');
                    this_button.html(this_html);
                    this_button.prop('disabled',false);
                },2000);
            }
        });
    });
    $(document).on('input','[type="range"]',function(){
        $('[data-range-from="'+$(this).attr('name')+'"]').html($(this).val());
        $('[data-reverse-range-from="'+$(this).attr('name')+'"]').html(100 - parseInt($(this).val()));
    });
    $('[type="range"]').trigger('input');
</script>
@endpush