@extends('layout.master')

@section('title', e($client->name) . ' - Clients - Castle')

@section('breadcrumbs')
<ol class="breadcrumb">
    <li><a href="{{ route('home.index') }}">Home</a></li>
    <li><a href="{{ route('clients.index') }}">Clients</a></li>
    <li class="active">{{ $client->name }}</li>
</ol>
@endsection

@section('content')
<div class="container item-viewer client-viewer">
	<div class="row">
		<div class="col-md-8 col-md-offset-2">

			<header class="well">
				<h1>
					<span class="client-logo" data-color="{{ $client->color }}">{{ $client->slug }}</span>
					{{ $client->name }}
				</h1>
				@include('tags.partials.bar', [
					'tags' => $client->tags,
					'linkify' => true,
				])
				@if ($client->description)
				<section class="client-description">
					{!! $client->toHtml() !!}
				</section>
				@endif
				@include('layout.common.action-bar', [
					'editPermission' => ['manage', $client],
					'editRoute' => route('clients.edit', $client->url),
					'deletePermission' => ['delete', $client],
					'deleteRoute' => route('clients.destroy', $client->url),
					'deleteWarning' => 'All of this client\'s resources will also be deleted.',
				])
			</header>

			<div class="action-bar">
				<ul class="nav nav-pills nav-justified">
					<li class="active">
						<a href="#resources" data-toggle="tab">
							@if (!$client->resources->isEmpty())
							Resources
							<small class="text-muted">
								&times;{{ $client->resources->count() }}
							</small>
							@else
							No resources
							@endif
						</a>
					</li>
					@if (!$client->documents->isEmpty())
					<li>
						<a href="#documents" data-toggle="tab">
							Documents
							<small class="text-muted">
								&times;{{ $client->documents->count() }}
							</small>
						</a>
					</li>
					@else
					<li class="disabled">
						<a href="javascript:;">
							No documents
						</a>
					</li>
					@endif
				</ul>
			</div>

			<div class="tab-content">
				<section class="tab-pane active" id="resources" data-type-filter="resource-filter">

				<div class="action-bar">
					<div class="row">
						<div class="col-sm-6 action-bar-left">
							@can('manage', $client)
							<a class="btn btn-success" href="{{ route('clients.resources.create', $client->slug) }}">
								Create resource
							</a>
							@endcan
						</div>
						<div class="col-sm-6 action-bar-right">
							<form class="form-inline" action="{{ route('clients.show', $client->slug) }}" method="get">
								<select id="resource-filter" name="resource-filter" class="form-control">
									<option value="" selected="selected">All resources</option>
									@foreach (Castle\ResourceType::all() as $type)
									<option value="{{ $type->slug }}">{{ str_plural($type->name) }}</option>
									@endforeach
								</select>
							</form>
						</div>
					</div>
				</div>

				@if (!$client->resources->isEmpty())
					@include('resources.partials.list', ['resources' => $client->resources, 'hideClient' => true])
				@else
					<div class="alert alert-info">
						This client has no resources.
					</div>
				@endif
				</section>
				<section class="tab-pane" id="documents">
				@if (!$client->documents->isEmpty())
					@include('docs.partials.list', ['docs' => $client->documents, 'hideClient' => true])
				@endif
				</section>
			</div>

		</div>
	</div>
</div>
@endsection
