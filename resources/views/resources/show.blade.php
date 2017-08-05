@extends('layout.master')

@section('title', e($resource->name) . ' - ' . e($resource->client->name) . ' - Clients - Castle')

@section('breadcrumbs')
<ol class="breadcrumb">
	<li><a href="{{ route('home.index') }}">Home</a></li>
	<li><a href="{{ route('clients.index') }}">Clients</a></li>
	<li><a href="{{ route('clients.show', $resource->client->url) }}">{{ $resource->client->name }}</a></li>
	<li class="active">{{ $resource->name }}</li>
</ol>
@endsection

@section('content')
<div class="container item-viewer resource-viewer">
	<div class="row">
		<div class="col-md-8 col-md-offset-2">

			<header class="well">
				<h1>{{ $resource->name }}</h1>
				@include('tags.partials.bar', [
					'clients' => $resource->client,
					'tags' => $resource->tags,
					'linkify' => true
				])
				<span class="text-muted resource-name">
					{{ $resource->type->name }}
				</span>
				@if ($resource->description)
				<section class="resource-description">
					{!! $resource->toHtml() !!}
				</section>
				@endif
				@include('layout.common.action-bar', [
					'showRevisions' => $resource->revisionHistory->count() > 0,
					'revisePermission' => ['manage', $resource],
					'revisionsRoute' => route('clients.resources.revisions', ['client' => $resource->client->url, 'resource' => $resource->url]),
					'editPermission' => ['manage', $resource],
					'editRoute' => route('clients.resources.edit', ['client' => $resource->client->url, 'resource' => $resource->url]),
					'deletePermission' => ['delete', $resource],
					'deleteRoute' => route('clients.resources.destroy', ['client' => $resource->client->url, 'resource' => $resource->url]),
					'deleteWarning' => (isset($resource->attachments) and !$resource->attachments->isEmpty()) ?
						'This resource\'s attachments will also be deleted.' :
						null,
				])
			</header>

			<section>
				@if (isset($resource->metadata) and !$resource->metadata->isEmpty())
				<article class="resource-metadata">
					@foreach ($resource->metadata as $key => $value)
					<div class="row metadata-row">
						<div class="col-sm-3 metadata-key">
							<span class="metadata-key-name">
								{{ $key }}
							</span>
						</div>
						<div class="col-sm-9 metadata-value">
							<div class="input-group">
                                <input type="text" class="form-control mono-text" value="{{ $value }}" readonly="readonly"/>
								<span class="input-group-btn">
									<button type="button" class="btn btn-default copy-me">
										<span class="glyphicon glyphicon-copy"></span>
										<span class="sr-only">Copy to clipboard</span>
									</button>
								</span>
                            </div>
						</div>
					</div>
					@endforeach
				</article>
				@endif
				@if (isset($resource->attachments) and !$resource->attachments->isEmpty())
				<article class="resource-attachments">
					<h3>
						Attachments
						<small>&times;{{ $resource->attachments->count() }}</small>
					</h3>
					@include('attachments.partials.list', ['attachments' => $resource->attachments])
				</article>
				@endif
			</section>

		</div>
	</div>
</div>
@endsection
