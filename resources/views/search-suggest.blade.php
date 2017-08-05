<?php if (!isset($limit)) $limit = 6; ?>
<div class="list-group">
	@if ($results->isEmpty())
		<div class="list-group-item text-center no-results">
			<strong>No results for <mark>{{ $term }}</mark>.</strong>
		</div>
	@else
		@foreach ($results->splice(0, $limit) as $result)
			@include(view_for_class($result).'.partials.single', ['item' => $result])
		@endforeach
		@if ($results->total() > $limit)
		<a href="/search/{{ $term }}" class="list-group-item text-center more-results">
			<strong>+{{ $results->total() - $limit }} more...</strong>
		</a>
		@endif
	@endif
</ul>
