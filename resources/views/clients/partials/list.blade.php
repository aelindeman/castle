@if ($clients->count() > 0)
	<div class="list-group clients-list-group">
		@foreach ($clients as $client)
			@include ('clients.partials.single', ['item' => $client])
		@endforeach
	</div>
@else
	<div class="text-muted text-center">
		{{ $ifEmpty or 'No clients.' }}
	</div>
@endif
