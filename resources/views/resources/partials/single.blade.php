<a class="list-group-item resource-item" href="{{ route('clients.resources.show', ['client' => $item->client->url, 'resource' => $item->url]) }}" data-type="{{ $item->type->slug }}">
	<h4 class="list-group-item-heading">
		{{ $item->name }}
		<small class="resource-type">
			{{ $item->type->name }}
		</small>
		@if (count($item->attachments) > 0)
		<small class="pull-right">
			<span class="glyphicon glyphicon-paperclip">
				<span class="sr-only">Has attachments</span>
			</span>
		</small>
		@endif
	</h4>
	@include('tags.partials.bar', [
		'clients' => (isset($hideClient) and $hideClient) ?
			null :
			$item->client,
		'tags' => (isset($hideTags) and $hideTags) ?
			null :
			$item->tags
	])
</a>
