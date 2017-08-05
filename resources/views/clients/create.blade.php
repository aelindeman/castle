@extends('layout.master')

@section('title', 'Create client - Clients - Castle')

@section('breadcrumbs')
<ol class="breadcrumb">
	<li><a href="{{ route('home.index') }}">Home</a></li>
	<li><a href="{{ route('clients.index') }}">Clients</a></li>
	<li class="active">Create client</li>
</ol>
@endsection

@section('content')
<div class="container item-editor client-editor">

	<form class="form-horizontal" method="post" action="{{ route('clients.store') }}">
		<div class="row">
			<div class="col-md-8 col-md-offset-2">
				{!! csrf_field() !!}

				<fieldset>

					<div class="form-group{{ $errors->has('name') ? ' has-error has-feedback' : '' }}">
						<label class="col-md-2 control-label" for="name">Name</label>

						<div class="col-md-9">
							<input type="text" class="form-control" name="name" id="name" value="{{ old('name') }}">

							@if ($errors->has('name'))
								<span class="help-block">
									<strong>{{ $errors->first('name') }}</strong>
								</span>
							@endif
						</div>
					</div>

					<div class="form-group{{ $errors->has('slug') ? ' has-error has-feedback' : '' }}">
						<label class="col-md-2 control-label" for="slug">Slug</label>

						<div class="col-md-9">
							<input type="text" class="form-control" name="slug" id="slug" value="{{ old('slug') }}" data-short-slug="name">

							@if ($errors->has('slug'))
								<span class="help-block">
									<strong>{{ $errors->first('slug') }}</strong>
								</span>
							@endif
						</div>
					</div>

					<div class="form-group{{ $errors->has('color') ? ' has-error has-feedback' : '' }}">
						<label class="col-md-2 control-label" for="color">Color</label>

						<div class="col-md-9">
							<input type="color" class="form-control" name="color" id="color" value="{{ old('color', color_rand()) }}">

							@if ($errors->has('color'))
								<span class="help-block">
									<strong>{{ $errors->first('color') }}</strong>
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

						<div class="col-md-9">
							<textarea type="text" class="form-control mono-text" name="description" id="description" rows="8" data-provide="markdown">{{ old('description') }}</textarea>

							@if ($errors->has('description'))
								<span class="help-block">
									<strong>{{ $errors->first('description') }}</strong>
								</span>
							@endif
						</div>
					</div>

					<div class="form-group{{ $errors->has('tags') ? ' has-error has-feedback' : '' }}">
						<label class="col-md-2 control-label" for="tags">Tags</label>
						<div class="col-md-9">

							<?php $oldTags = collect(old('tags')) ?>
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

				</fieldset>
				<fieldset class="form-bottom-toolbar">

					<div class="form-group">
						<div class="col-md-9 col-md-offset-2">
							<button type="submit" class="btn btn-primary">Create client</button>
							<a class="btn btn-default" href="{{ route('clients.index') }}">Cancel</a>
						</div>
					</div>

				</fieldset>

			</div>
		</div>
	</form>

</div>
@endsection
