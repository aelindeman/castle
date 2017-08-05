@if ($discussions->count() > 0)
	<div class="list-group discussions-list-group">
		@foreach ($discussions as $discussion)
			@include ('discussions.partials.single', ['item' => $discussion])
		@endforeach
	</div>
@else
	<div class="text-muted text-center">
		{{ $ifEmpty or 'No discussions.' }}
	</div>
@endif
