<a class="list-group-item client-item" href="{{ route('clients.show', $item->url) }}">
	<div class="media">
		<div class="media-left media-middle">
			<span class="media-object client-logo" data-color="{{ $item->color }}">
				{{ $item->slug }}
			</span>
		</div>
		<div class="media-body media-middle">
			<h4 class="media-heading list-group-item-heading">
				{{ $item->name }}
			</h4>
			@if (!isset($hideTagbar) or (isset($hideTagbar) and !$hideTagbar))
				@include('tags.partials.bar', [
					'clients' => null,
					'tags' => $item->tags
				])
			@endif
		</div>
		<div class="media-right media-middle text-muted hidden-xs">
			<ul class="list-unstyled client-metadata" style="font-size: 12px">
				@if ($item->resources->count())
				<li>
					<span class="glyphicon glyphicon-briefcase"></span>
					<span class="sr-only">Resources:</span>
					<span>{{ $item->resources->count() }}</span>
				</li>
				@endif
				@if ($item->documents->count())
				<li>
					<span class="glyphicon glyphicon-file"></span>
					<span class="sr-only">Docs:</span>
					<span>{{ $item->documents->count() }}</span>
				</li>
				@endif
			</ul>
		</div>
	</div>
</a>
