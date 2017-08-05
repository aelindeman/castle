<a class="list-group-item tag-item" href="{{ route('tags.show', $item->url) }}">
	<h4 class="media-heading list-group-item-heading">
		<span class="label label-primary" data-color="{{ $item->color }}">
			{{ $item->name }}
			<small>
				&times;{{ $item->occurences }}
			</small>
		</span>
	</h4>
</a>
