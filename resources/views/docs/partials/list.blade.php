@if ($docs->count() > 0)
	<div class="list-group docs-list-group">
		@foreach ($docs as $doc)
			@include ('docs.partials.single', ['item' => $doc])
		@endforeach
	</div>
@else
	<div class="text-muted text-center">
		{{ $ifEmpty or 'No documents.' }}
	</div>
@endif
