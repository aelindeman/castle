@if (!empty($item))
@if (isset($edit) and $edit)
<li class="list-group-item attachment-item" data-attachment="{{ $item }}">
	<div class="media">
		<div class="media-body media-middle">
			<span class="text-muted glyphicon glyphicon-{{ $item->icon }}"></span>
			<span class="sr-only">{{ $item->type }}</span>
			<strong class="list-group-item-heading">
				@if (isset($linkify) and $linkify)
				<a href="{{ route('attachments.show', $item) }}" target="_blank">
				@endif
					<span title="{{ $item }}">
						{{ (isset($fullPath) and $fullPath) ? $item : basename($item) }}
					</span>
				@if (isset($linkify) and $linkify)
				</a>
				@endif
			</strong>
			@if (isset($metadata) and $metadata)
			<br>
			<small class="text-muted">
				{{ $item->type }}, {{ human_filesize($item->size) }}B
			</small>
			@endif
		</div>
		<div class="media-right media-middle">
			@if (isset($destroy) and $destroy)
			@can('delete', Castle\Attachment::class)
			<form class="form-inline" action="{{ route('attachments.destroy', $item) }}" method="post">
				{!! csrf_field() !!}
				{!! method_field('delete') !!}
				<button type="button" class="btn {{ isset($buttonSize) ? $buttonSize : 'btn-sm' }} btn-danger" data-confirm="delete">
					<span class="glyphicon glyphicon-trash"></span>
					<span class="sr-only">Delete</span>
				</button>
				<button type="submit" class="btn {{ isset($buttonSize) ? $buttonSize : 'btn-sm' }} btn-danger"{!! isset($deleteWarning) ? ' data-trigger="hover" data-toggle="tooltip" data-title="'.e($deleteWarning).'"' : '' !!}>
					<strong>Are you sure?</strong>
				</button>
			</form>
			@endcan
			@else
			<button type="button" class="btn btn-sm btn-warning" data-role="remove">
				<span class="glyphicon glyphicon-minus"></span>
				<span class="sr-only">Detach</span>
			</button>
			@endif
		</div>
	</div>
</li>
@else
<a class="list-group-item attachment-item" href="{{ route('attachments.show', $item) }}">
	<span class="text-muted glyphicon glyphicon-{{ $item->icon }}"></span>
	<span class="sr-only">{{ $item->type }}</span>
	<strong class="list-group-item-heading">
		{{ basename($item) }}
	</strong>
	@if (isset($metadata) and $metadata)
	<small class="text-muted">
		{{ $item->type }}, {{ human_filesize($item->size) }}B
	</small>
	@endif
</a>
@endif
@endif
