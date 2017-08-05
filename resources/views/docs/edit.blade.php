@extends('layout.master')

@section('title', 'Edit ' . e($doc->name) . ' - Docs - Castle')

@section('breadcrumbs')
<ol class="breadcrumb">
	<li><a href="{{ route('home.index') }}">Home</a></li>
	<li><a href="{{ route('docs.index') }}">Docs</a></li>
	<li><a href="{{ route('docs.show', $doc->url) }}">{{ $doc->name }}</a></li>
	<li class="active">Edit</li>
</ol>
@endsection

@section('content')
<div class="container item-editor document-editor">

	<form class="form-horizontal" method="post" action="{{ route('docs.update', $doc->url) }}" enctype="multipart/form-data">
		{!! csrf_field() !!}
		{{ method_field('put') }}

		<fieldset>
			<div class="form-group{{ $errors->has('name') ? ' has-error has-feedback' : '' }}">
				<label class="col-md-2 control-label" for="name">Name</label>

				<div class="col-md-8">
					<input type="text" class="form-control" name="name" id="name" value="{{ old('name', $doc->name) }}">
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
					<input type="text" class="form-control" name="slug" id="slug" value="{{ old('slug', $doc->slug) }}" data-slug="name">
					@if ($errors->has('slug'))
					<span class="help-block">
						<strong>{{ $errors->first('slug') }}</strong>
					</span>
					@endif
				</div>
			</div>

			<div class="form-group{{ $errors->has('content') ? ' has-error has-feedback' : '' }}">
				<label class="col-md-2 control-label" for="content">
					Content
					<br class="hidden-xs hidden-sm">
					<img src="{{ elixir('images/markdown.svg') }}" type="image/svg+xml" alt="Markdown" style="height: 18px; width: auto;" />
				</label>

				<div class="col-md-8">
					<textarea class="form-control mono-text" name="content" id="content" rows="16" data-provide="markdown">{{ old('content', $doc->content) }}</textarea>
					@if ($errors->has('content'))
					<span class="help-block">
						<strong>{{ $errors->first('content') }}</strong>
					</span>
					@endif
				</div>

			</div>

		</fieldset>
		<fieldset>

			<div class="form-group{{ $errors->has('clients') ? ' has-error has-feedback' : '' }}">
				<label class="col-md-2 control-label" for="clients">Clients</label>
				<div class="col-md-8">

					<?php $oldClients = collect(old('clients', $doc->clients->pluck('id'))) ?>
					<select multiple="multiple" class="form-control taggable" name="clients[]" id="clients" data-placeholder="No clients">
						@foreach (Castle\Client::all() as $client)
							<?php $selectedClient = $oldClients->contains($client->id) ? ' selected="selected"' : '' ?>
							<option value="{{ $client->id }}"{!! $selectedClient !!} data-color="{{ $client->color }}">
								{{ $client->name }}
							</option>
						@endforeach
					</select>

					@if ($errors->has('clients'))
					<span class="help-block">
						<strong>{{ $errors->first('clients') }}</strong>
					</span>
					@endif
				</div>
			</div>

			<div class="form-group{{ $errors->has('tags') ? ' has-error has-feedback' : '' }}">
				<label class="col-md-2 control-label" for="tags">Tags</label>
				<div class="col-md-8">

					<?php $oldTags = collect(old('tags', $doc->tags->pluck('id'))) ?>
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

					@include('attachments.partials.editor', ['attachments' => old('attachments', $doc->attachments)])

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
					<a class="btn btn-default" href="{{ route('docs.show', $doc->url) }}">Cancel</a>
				</div>
			</div>

		</fieldset>

	</form>

</div>
@endsection
