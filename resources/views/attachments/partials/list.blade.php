@if (isset($attachments) and !$attachments->isEmpty())
	<div class="list-group attachments-list-group">
	@foreach ($attachments as $attachment)
		@include('attachments.partials.single', ['item' => $attachment])
	@endforeach
	</div>
@endif
