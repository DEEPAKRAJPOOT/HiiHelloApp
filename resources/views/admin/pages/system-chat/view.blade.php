@extends('admin.layouts.app')

@push('breadcrumb')
{!! Breadcrumbs::render('system_chats_view', $user->custom_id) !!}
@endpush

@push('extra-css-styles')
<style type="text/css">
	.system-message-text{
		text-align: left;
		padding-right: 3.25rem !important;
		padding-left: 1.25rem !important;
	}
	.system-message-status{
		text-align: right;
		font-size: 0.7em;
		padding-right: 0.75rem !important;
	}
	.scrollable-chat{
		width:100%;
		max-height:500px;
		overflow-x: hidden;
		overflow-y: auto;
		padding-right: 20px;
	}
</style>
@endpush

@section('content')
<div class="container">
	<div class="card card-custom">
		<div class="card-header">
			<div class="card-title">
				<span class="card-icon">
					<i class="fas fa-users text-primary"></i>
				</span>
				<h3 class="card-label w-100">
					{{ $custom_title }}
					@if($user->userTransDefault)
					with {{ $user->userTransDefault->full_name }}
					@endif
				</h3>
			</div>
			<div class="my-6">
				<label>
					<input id="show-deleted-messages" type="checkbox" checked="checked">
					Show Deleted
				</label>
			</div>
		</div>
		<div class="card-body">
			<div class="d-flex flex-column align-items-start">
				<div class="d-flex align-items-center mb-3">
					<div class="symbol symbol-35px symbol-circle mr-3">
						<img src="{{ generateURL($system_user->profile_photo)}}">
					</div>
					<div class="ms-3">
						<b class="fs-5 fw-bold text-gray-900 text-hover-primary me-1">
							@if($system_user->userTransDefault) {{ $system_user->userTransDefault->full_name }} @else Team Hi Hello @endif
						</b>
					</div>
				</div>
				<div class="d-flex flex-column align-items-start scrollable-chat">
					@foreach($system_chats as $system_chat)
						<div class="@if(!empty($system_chat->deleted_at)) deleted-message @endif">
							<div class="d-flex flex-row align-items-center">
								<div class="py-5 px-0 rounded bg-light-primary my-3">
									<div class="system-message-text">
										{{ $system_chat->getMessage()->value }}
									</div>
									<div class="system-message-status">
										<div>Status: {{ strtr($system_chat->status,['send'=>'sent','read'=>'seen']) }}</div>
										<div>Sent: {{ $system_chat->created_at }}</div>
										@if(!empty($system_chat->deleted_at))
											<div class="text-danger">Deleted: {{ $system_chat->deleted_at }}</div>
										@endif
									</div>
								</div>
								<div class="py-5 ml-2 ms-1 system-message-actions">
									@if(empty($system_chat->deleted_at))
									<form action="{{ route('admin.system-chat.destroy',$system_chat->custom_id) }}" method="post">
										@csrf
										@method('delete')
										<a href="javascript:void(0)" class="form-submit-link" data-submit-confirm="Are you sure you want to delete this message?"><i class="fa fa-trash pr-0"></i></a>
									</form>
									@endif
								</div>
							</div>
						</div>
					@endforeach
				</div>
				<form method="post" action="{{ route('admin.system-chat.store') }}" class="w-100 mt-4">
					@csrf
					<input type="hidden" name="user_id" value="{{ $user->custom_id }}">
					<div class="form-group">
						<label for="system-message">Message</label>
						<textarea id="system-message" class="form-control" name="message" rows="3" placeholder="Your message goes here..."></textarea>
					</div>
					<div class="form-group text-right">
						<button type="button" class="btn btn-light-primary px-8 mr-4" data-toggle-erase="#system-message"><i class="fa fa-eraser"></i> Clear</button>
						<button type="submit" class="btn btn-primary px-8">Send <i class="fa fa-paper-plane"></i></button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
@endsection
@push('extra-js-scripts')
<script type="text/javascript">
	$('#show-deleted-messages').change(function(){
		$('.deleted-message').toggleClass('d-none',!$(this).is(':checked'));
	});
	$('[data-toggle-erase]').click(function(){
		$($(this).attr('data-toggle-erase')).val('');
		$($(this).attr('data-toggle-erase')).html('');
		$($(this).attr('data-toggle-erase')).trigger('focus');
	});
</script>
@endpush