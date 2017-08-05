<a class="list-group-item doc-item" href="{{ route('docs.show', $item->url) }}">
	<h4 class="list-group-item-heading">
		{{ $item->name }}
		@if (
			(isset($item->attachments) and !$item->attachments->isEmpty()) or
			(isset($item->metadata) and !$item->metadata->isEmpty())
		)
		<ul class="list-inline pull-right">
			@if (isset($item->metadata) and !$item->metadata->isEmpty())
			<li>
				<small>
					<span class="glyphicon glyphicon-asterisk"></span>
					<span class="sr-only">Has metadata</span>
				</small>
			</li>
			@endif
			@if (isset($item->attachments) and !$item->attachments->isEmpty())
			<li>
				<small>
					<span class="glyphicon glyphicon-paperclip"></span>
					<span class="sr-only">Has attachments</span>
				</small>
			</li>
			@endif
		</ul>
		@endif
	</h4>
	@include('tags.partials.bar', [
		'clients' => (isset($hideClient) and $hideClient) ?
			null :
			$item->clients,
		'tags' => (isset($hideTags) and $hideTags) ?
			null :
			$item->tags
	])
</a>
