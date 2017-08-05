<?php $container = isset($container) ? $container : 'div' ?>
@if (!(isset($noContainer) and $noContainer))
<{{ $container }} class="tag-bar {{ isset($class) ? $class : ''}}">
@endif
@if (isset($clients) and $clients instanceof Castle\Client)
	@if (isset($linkify) and $linkify)
	<a class="label label-default" data-color="{{ $clients->color }}" href="{{ route('clients.show', $clients->url) }}">
		{{ $clients->name }}
	</a>
	@else
	<span class="label label-default" data-color="{{ $clients->color }}">
		{{ $clients->name }}
	</span>
	@endif
@elseif (isset($clients))
	@foreach ($clients as $c)
	@if (isset($linkify) and $linkify)
	<a class="label label-default" data-color="{{ $c->color }}" href="{{ route('clients.show', $c->url) }}">
		{{ $c->name }}
	</a>
	@else
	<span class="label label-default" data-color="{{ $c->color }}">
		{{ $c->name }}
	</span>
	@endif
	@endforeach
@endif
@if (isset($tags) and $tags->isEmpty())
	<span class="label label-default">
		<em>No tags</em>
	</span>
@elseif (isset($tags))
	@foreach ($tags as $tag)
	@if (isset($linkify) and $linkify)
	<a class="label label-default" data-color="{{ $tag->color }}" href="{{ route('tags.show', $tag->url) }}">
		{{ $tag->name }}
	</a>
	@else
	<span class="label label-default" data-color="{{ $tag->color }}">
		{{ $tag->name }}
	</span>
	@endif
	@endforeach
@endif
@if (!(isset($noContainer) and $noContainer))
</{{ $container }}>
@endif
