@if ($users->count() > 0)
	<div class="list-group users-list-group">
		@each ('users.partials.single', $users, 'item')
	</div>
@else
	<div class="text-muted text-center">
		{{ $ifEmpty or 'No users.' }}
	</div>
@endif
