@if ($tags->count() > 0)
	<div class="list-group tags-list-group">
		@each ('tags.partials.single', $tags, 'item')
	</div>
@else
	<div class="text-muted text-center">
		{{ $ifEmpty or 'No tags.' }}
	</div>
@endif
