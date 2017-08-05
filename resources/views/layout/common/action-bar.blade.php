@if (!(isset($noContainer) and $noContainer))
<nav class="action-bar">
@endif

	@if (!(isset($hideDeleteButton) and $hideDeleteButton))
	@can($deletePermission[0], $deletePermission[1])
	<form class="form-inline" method="post" action="{{ $deleteRoute }}">
		{!! csrf_field() !!}
		{!! method_field('delete') !!}
	@endcan
	@endif

	@if (!(isset($hideEditButton) and $hideEditButton))
	@can($editPermission[0], $editPermission[1])
	<a class="btn {{ isset($buttonSize) ? $buttonSize : 'btn-sm' }} btn-primary" href="{{ $editRoute }}" title="Edit"{!! isset($editWarning) ? ' data-trigger="hover" data-toggle="tooltip" data-title="'.e($editWarning).'"' : '' !!}>
		<span class="glyphicon glyphicon-pencil"></span>
		<span class="sr-only">Edit</span>
	</a>
	@endcan
	@endif

	@if (isset($showRevisions) and $showRevisions)
	@can($revisePermission[0], $revisePermission[1])
		<a class="btn {{ isset($buttonSize) ? $buttonSize : 'btn-sm' }} btn-default" href="{{ $revisionsRoute }}" title="Restore an old version">
			<span class="glyphicon glyphicon-repeat"></span>
			<span class="sr-only">Restore an old version</span>
		</a>
	@endcan
	@endif

	@if (!(isset($hideDeleteButton) and $hideDeleteButton))
	@can($deletePermission[0], $deletePermission[1])
		<button type="button" class="btn {{ isset($buttonSize) ? $buttonSize : 'btn-sm' }} btn-danger" data-confirm="delete" title="Delete">
			<span class="glyphicon glyphicon-trash"></span>
			<span class="sr-only">Delete</span>
		</button>
		<button type="submit" class="btn {{ isset($buttonSize) ? $buttonSize : 'btn-sm' }} btn-danger"{!! isset($deleteWarning) ? ' data-trigger="hover" data-toggle="tooltip" data-title="'.e($deleteWarning).'"' : '' !!}>
			<strong>Are you sure?</strong>
		</button>
	</form>
	@endcan
	@endif

@if (!(isset($noContainer) and $noContainer))
</nav>
@endif
