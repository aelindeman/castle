@extends('layout.master')

@section('title', e($tag->name) . ' - Tags - Castle')

@section('breadcrumbs')
<ol class="breadcrumb">
    <li><a href="{{ route('home.index') }}">Home</a></li>
    <li><a href="{{ route('tags.index') }}">Tags</a></li>
    <li class="active">{{ $tag->name }}</li>
</ol>
@endsection

@section('content')

<div class="container item-viewer tag-viewer">
	<div class="row">
		<div class="col-md-8 col-md-offset-2">

			<header class="well">
				<h1 data-color="{{ $tag->color }}" data-color-properties="border-bottom-color,color">
					{{ $tag->name }}
				</h1>
				<p>{{ $tag->description }}</p>
				@include('layout.common.action-bar', [
					'editPermission' => ['manage', $tag],
					'editRoute' => route('tags.edit', $tag->url),
					'deletePermission' => ['delete', $tag],
					'deleteRoute' => route('tags.destroy', $tag->url),
					'deleteWarning' => 'The tag will be removed from all of the things it\'s attached to. Nothing else will be deleted.'
				])
			</header>

			@if ($tag->occurences > 0)
			<div class="action-bar">
				<ul class="nav nav-pills nav-justified">

					@if (!$tag->clients->isEmpty())
					<li>
						<a href="#clients" data-toggle="tab">
							Clients
							<small class="text-muted">
								&times;{{ $tag->clients->count() }}
							</small>
						</a>
					</li>
					@else
					<li class="disabled">
						<a href="#clients">
							No clients
						</a>
					</li>
					@endif

					@if (!$tag->resources->isEmpty())
					<li>
						<a href="#resources" data-toggle="tab">
							Resources
							<small class="text-muted">
								&times;{{ $tag->resources->count() }}
							</small>
						</a>
					</li>
					@else
					<li class="disabled">
						<a href="#resources">
							No resources
						</a>
					</li>
					@endif

					@if (!$tag->documents->isEmpty())
					<li>
						<a href="#documents" data-toggle="tab">
							Documents
							<small class="text-muted">
								&times;{{ $tag->documents->count() }}
							</small>
						</a>
					</li>
					@else
					<li class="disabled">
						<a href="#documents">
							No documents
						</a>
					</li>
					@endif

					@if (!$tag->discussions->isEmpty())
					<li>
						<a href="#discussions" data-toggle="tab">
							Discussions
							<small class="text-muted">
								&times;{{ $tag->discussions->count() }}
							</small>
						</a>
					</li>
					@else
					<li class="disabled">
						<a href="#discussions">
							No discussions
						</a>
					</li>

					@endif

				</ul>
			</div>

			<div class="tab-content">
				@if (!$tag->clients->isEmpty())
				<section class="tab-pane" id="clients">
					@include('clients.partials.list', ['clients' => $tag->clients])
				</section>
				@endif
				@if (!$tag->resources->isEmpty())
				<section class="tab-pane" id="resources">
					@include('resources.partials.list', ['resources' => $tag->resources])
				</section>
				@endif
				@if (!$tag->documents->isEmpty())
				<section class="tab-pane" id="documents">
					@include('docs.partials.list', ['docs' => $tag->documents])
				</section>
				@endif
				@if (!$tag->discussions->isEmpty())
				<section class="tab-pane" id="discussions">
					@include('discussions.partials.list', ['discussions' => $tag->discussions])
				</section>
				@endif
			</div>
			@else
			<div class="alert alert-info">
				This tag isn&rsquo;t used anywhere.
			</div>
			@endif

		</div>
	</div>

</div>
@endsection
