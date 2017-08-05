@extends('layout.master')

@section('title', 'Edit ' . e($tag->name) . ' - Tags - Castle')

@section('breadcrumbs')
<ol class="breadcrumb">
	<li><a href="{{ route('home.index') }}">Home</a></li>
	<li><a href="{{ route('tags.index') }}">Tags</a></li>
	<li><a href="{{ route('tags.show', $tag->url) }}">{{ $tag->name }}</a></li>
	<li class="active">Edit</li>
</ol>
@endsection

@section('content')
<div class="container item-editor tag-editor">

	<form class="form-horizontal" method="post" action="{{ route('tags.update', $tag->url) }}">
		<div class="row">
			<div class="col-md-8 col-md-offset-2">
				{!! csrf_field() !!}
				{{ method_field('put') }}

				<fieldset>

					<div class="form-group{{ $errors->has('name') ? ' has-error has-feedback' : '' }}">
						<label class="col-md-3 control-label" for="name">Name</label>

						<div class="col-md-6">
							<input type="text" class="form-control" name="name" id="name" value="{{ old('name', $tag->name) }}">

							@if ($errors->has('name'))
								<span class="help-block">
									<strong>{{ $errors->first('name') }}</strong>
								</span>
							@endif
						</div>
					</div>

					<div class="form-group{{ $errors->has('slug') ? ' has-error has-feedback' : '' }}">
						<label class="col-md-3 control-label" for="slug">Slug</label>

						<div class="col-md-6">
							<input type="text" class="form-control" name="slug" id="slug" value="{{ old('slug', $tag->slug) }}" data-slug="name">

							@if ($errors->has('slug'))
								<span class="help-block">
									<strong>{{ $errors->first('slug') }}</strong>
								</span>
							@endif
						</div>
					</div>

					<div class="form-group{{ $errors->has('color') ? ' has-error has-feedback' : '' }}">
						<label class="col-md-3 control-label" for="color">Color</label>

						<div class="col-md-6">
							<input type="color" class="form-control" name="color" id="color" value="{{ old('color', $tag->color) }}">

							@if ($errors->has('color'))
								<span class="help-block">
									<strong>{{ $errors->first('color') }}</strong>
								</span>
							@endif
						</div>
					</div>

					<div class="form-group{{ $errors->has('description') ? ' has-error has-feedback' : '' }}">
						<label class="col-md-3 control-label" for="description">Description</label>

						<div class="col-md-6">
							<textarea class="form-control" name="description" id="description" rows="3">{{ old('description', $tag->description) }}</textarea>

							@if ($errors->has('description'))
								<span class="help-block">
									<strong>{{ $errors->first('description') }}</strong>
								</span>
							@endif
						</div>
					</div>

				</fieldset>
				<fieldset class="form-bottom-toolbar">

					<div class="form-group">
						<div class="col-md-6 col-md-offset-3">
							<button type="submit" class="btn btn-primary">Save changes</button>
							<a class="btn btn-default" href="{{ route('tags.show', $tag->url) }}">Cancel</a>
						</div>
					</div>

				</fieldset>

			</div>
		</div>
	</form>

</div>
@endsection
