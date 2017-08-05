@if ($resources->count() > 0)
	<div class="list-group resources-list-group">
		@foreach ($resources as $resource)
			@include ('resources.partials.single', ['item' => $resource])
		@endforeach
	</div>
@else
	<div class="text-muted text-center">
		{{ $ifEmpty or 'No resources.' }}
	</div>
@endif
