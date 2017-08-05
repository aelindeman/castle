@if ($comments->count() > 0)
	<ul class="list-group comments-list-group">
	@foreach ($comments as $comment)
		@include('comments.partials.single', [
			'item' => $comment,
			'hideContext' => (isset($hideContext) and $hideContext)
		])
	@endforeach
	</ul>
@else
	<div class="text-muted text-center">
		{{ $ifEmpty or 'No comments.' }}
	</div>
@endif
