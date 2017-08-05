@extends('layout.master')

@section('title', 'Tags - Castle')

@section('breadcrumbs')
<ol class="breadcrumb">
	<li><a href="{{ route('home.index') }}">Home</a></li>
	<li class="active">Tags</li>
</ol>
@endsection

@section('content')
<div class="container item-index tag-index">
	<div class="row">
		<div class="col-md-8 col-md-offset-2">

			<header class="page-header">
				<h1>
					Tags
					<small>
						&times;{{ $tags->total() }}
					</small>
				</h1>
			</header>

			@can('manage', Castle\Tag::class)
			<div class="action-bar">
				<div class="row">
					<div class="col-sm-6 action-bar-left">
						<a class="btn btn-success" href="{{ route('tags.create') }}">
							Create tag
						</a>
					</div>
					<div class="col-sm-6 action-bar-right">
						<form class="form-inline" method="post" action="{{ route('tags.prune') }}">
							{!! csrf_field() !!}
							{!! method_field('delete') !!}
							<button type="button" class="btn btn-default" data-confirm="delete">
								Delete unused tags
							</button>
							<button type="submit" class="btn btn-default" data-toggle="tooltip" data-title="Tags which have no description and no occurences will be deleted.">
								Are you sure?
							</button>
						</form>
					</div>
				</div>
			</div>
			@endcan

			@if ($tags->isEmpty())
			<div class="alert alert-info">
				@if (Request::input('filter', false))
					<span>No tags match your filter.</span>
				@else
					<span>There are no tags.</span>
				@endif
			</div>
			@else
				<div class="row">
				@foreach ($tags->chunk(4) as $chunk)
					@foreach ($chunk as $tag)
					<article class="col-md-3 col-sm-4 col-xs-6 tag">
						<h4 data-color="{{ $tag->color }}" data-color-properties="border-bottom-color,color">
							<a href="{{ route('tags.show', $tag->slug) }}">{{ $tag->name }}</a>
							<small>&times;{{ $tag->occurences }}</small>
						</h4>
						<p>{{ $tag->description ?: '(no description)' }}</p>
					</article>
					@endforeach
				@endforeach
				</div>
				<nav class="text-center">
					{!! $tags->links() !!}
				</nav>
			@endif

		</div>
	</div>
</div>
@endsection
