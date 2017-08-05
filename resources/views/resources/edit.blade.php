@extends('layout.master')

@section('title', 'Edit ' . e($resource->name) . ' - ' . e($resource->client->name) . ' - Clients - Castle')

@section('breadcrumbs')
<ol class="breadcrumb">
	<li><a href="{{ route('home.index') }}">Home</a></li>
	<li><a href="{{ route('clients.index') }}">Clients</a></li>
	<li><a href="{{ route('clients.show', $resource->client->url) }}">{{ $resource->client->name }}</a></li>
	<li><a href="{{ route('clients.resources.show', ['client' => $resource->client->url, 'resource' => $resource->url]) }}">{{ $resource->name }}</a></li>
	<li class="active">Edit</li>
</ol>
@endsection

@section('content')
<div class="container item-editor resource-editor">

	<form class="form-horizontal" method="post" action="{{ route('clients.resources.update', ['client' => $resource->client->url, 'resource' => $resource->url]) }}" enctype="multipart/form-data">
		{!! csrf_field() !!}
		{!! method_field('put') !!}

		<fieldset>

			<div class="form-group{{ $errors->has('name') ? ' has-error has-feedback' : '' }}">
				<label class="col-md-2 control-label" for="name">Name</label>

				<div class="col-md-8">
					<input type="text" class="form-control" name="name" id="name" value="{{ old('name', $resource->name) }}">

					@if ($errors->has('name'))
						<span class="help-block">
							<strong>{{ $errors->first('name') }}</strong>
						</span>
					@endif
				</div>
			</div>

			<div class="form-group{{ $errors->has('slug') ? ' has-error has-feedback' : '' }}">
				<label class="col-md-2 control-label" for="slug">Slug</label>

				<div class="col-md-8">
					<input type="text" class="form-control" name="slug" id="slug" value="{{ old('slug', $resource->slug) }}">

					@if ($errors->has('slug'))
						<span class="help-block">
							<strong>{{ $errors->first('slug') }}</strong>
						</span>
					@endif
				</div>
			</div>

			<div class="form-group{{ $errors->has('client') ? ' has-error has-feedback' : '' }}">
				<label class="col-md-2 control-label" for="client">Client</label>
				<div class="col-md-8">

					<select class="form-control taggable" name="client" id="client" data-placeholder="No client">
						@foreach (Castle\Client::all() as $c)
							<option value="{{ $c->id }}"{!! old('client', $client->id) == $c->id ? ' selected="selected"' : '' !!} data-color="{{ $c->color }}">
								{{ $c->name }}
							</option>
						@endforeach
					</select>

					@if ($errors->has('client'))
					<span class="help-block">
						<strong>{{ $errors->first('client') }}</strong>
					</span>
					@endif
				</div>
			</div>

			<div class="form-group{{ $errors->has('type') ? ' has-error has-feedback' : '' }}">
				<label class="col-md-2 control-label" for="resourceType">Type</label>
				<div class="col-md-8">

					<select class="form-control taggable" name="type" id="resourceType" data-create="true" data-placeholder="No type">
						@foreach (Castle\ResourceType::all() as $type)
						<option value="{{ $type->slug }}" {!! old('type', $resource->type->slug) == $type->slug ? 'selected="selected"' : '' !!}>{{ $type->name }}</option>
						@endforeach
					</select>

					@if ($errors->has('type'))
						<span class="help-block">
							<strong>{{ $errors->first('type') }}</strong>
						</span>
					@endif
				</div>
			</div>

			<div class="form-group{{ $errors->has('description') ? ' has-error has-feedback' : '' }}">
				<label class="col-md-2 control-label" for="description">
					Description
					<br class="hidden-xs hidden-sm">
					<img src="{{ elixir('images/markdown.svg') }}" alt="supports Markdown" type="image/svg+xml" style="height: 18px; width: auto;">
				</label>

				<div class="col-md-8">
					<textarea type="text" class="form-control mono-text" name="description" id="description" rows="8" data-provide="markdown">{{ old('description', $resource->description) }}</textarea>

					@if ($errors->has('description'))
						<span class="help-block">
							<strong>{{ $errors->first('description') }}</strong>
						</span>
					@endif
				</div>
			</div>

			<div class="form-group">
				<label class="col-md-2 control-label" for="metadata">Data</label>

				<div class="col-md-8">
					<div class="resource-metadata" data-resource-editor>
						<?php list($metadataKeys, $metadataValues) = [
							old('metadata.keys', $resource->metadata->keys()),
							old('metadata.values', $resource->metadata->values()),
						]; ?>
						@for ($i = 0; $i < max(count($metadataKeys), count($metadataValues)); $i ++)
							@include ('resources.partials.editor-row', [
								'key' => old('metadata.keys.'.$i, isset($metadataKeys[$i]) ? $metadataKeys[$i] : ''),
								'value' => old('metadata.values.'.$i, isset($metadataValues[$i]) ? $metadataValues[$i] : ''),
								'index' => $i
							])
						@endfor
						@include ('resources.partials.editor-row', ['key' => '', 'value' => ''])
					</div>
					<button type="button" class="btn btn-success add-row">
						<span class="glyphicon glyphicon-plus"></span>
						<span class="sr-only">Add row</span>
					</button>
				</div>
			</div>

		</fieldset>
		<fieldset>

			<div class="form-group{{ $errors->has('tags') ? ' has-error has-feedback' : '' }}">
				<label class="col-md-2 control-label" for="tags">Tags</label>
				<div class="col-md-8">

					<?php $oldTags = collect(old('tags', $resource->tags->pluck('id'))) ?>
					<select multiple="multiple" class="form-control taggable" name="tags[]" id="tags" data-create="true" data-placeholder="No tags">
						@foreach (Castle\Tag::all() as $tag)
							<?php $selectedTag = $oldTags->contains($tag->id) ? ' selected="selected"' : '' ?>
							<option value="{{ $tag->id }}"{!! $selectedTag !!} data-color="{{ $tag->color }}">{{ $tag->name }}</option>
						@endforeach
					</select>

					@if ($errors->has('tags'))
					<span class="help-block">
						<strong>{{ $errors->first('tags') }}</strong>
					</span>
					@endif
				</div>
			</div>

			<div class="form-group{{ $errors->has('attachments') ? ' has-error has-feedback' : '' }}">
				<label class="col-md-2 control-label" for="attachments">Attachments</label>
				<div class="col-md-8">

					@include('attachments.partials.editor', ['attachments' => collect(old('attachments', $resource->attachments))])

					@if ($errors->has('attachments'))
					<span class="help-block">
						<strong>{{ $errors->first('attachments') }}</strong>
					</span>
					@endif
				</div>
			</div>

		</fieldset>
		<fieldset class="form-bottom-toolbar">

			<div class="form-group">
				<div class="col-md-8 col-md-offset-2">
					<button type="submit" class="btn btn-primary">Save changes</button>
					<a class="btn btn-default" href="{{ route('clients.resources.show', ['client' => $resource->client->url, 'resource' => $resource->url]) }}">Cancel</a>
				</div>
			</div>

		</fieldset>

	</form>

</div>
@endsection
