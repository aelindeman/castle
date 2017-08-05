@extends('layout.master')

@section('title', 'Change password - Edit ' . e($user->name) . ' - Users - Castle')

@section('breadcrumbs')
<ol class="breadcrumb">
	<li><a href="{{ route('home.index') }}">Home</a></li>
	<li><a href="{{ route('users.index') }}">Users</a></li>
	<li><a href="{{ route('users.show', $user->url) }}">{{ $user->name }}</a></li>
	<li><a href="{{ route('users.edit', $user->url) }}/edit">Edit</a></li>
	<li class="active">Change password</li>
</ol>
@endsection

@section('content')
<div class="container item-editor user-editor user-password-editor">

	<form class="form-horizontal" method="post" action="{{ route('users.update.password', $user->url) }}">
		<div class="row">
			<div class="col-md-8 col-md-offset-2">
				{!! csrf_field() !!}

				<fieldset>

					<div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
						<label class="col-md-3 control-label">Password</label>

						<div class="col-md-6">
							<input type="password" class="form-control" name="password">

							@if ($errors->has('password'))
								<span class="help-block">
									<strong>{{ $errors->first('password') }}</strong>
								</span>
							@endif
						</div>
					</div>

					<div class="form-group{{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
						<label class="col-md-3 control-label">Password (again)</label>
						<div class="col-md-6">
							<input type="password" class="form-control" name="password_confirmation">

							@if ($errors->has('password_confirmation'))
								<span class="help-block">
									<strong>{{ $errors->first('password_confirmation') }}</strong>
								</span>
							@endif
						</div>
					</div>

				</fieldset>
				<fieldset class="form-bottom-toolbar">

					<div class="form-group">
						<div class="col-md-6 col-md-offset-3">
							<button type="submit" class="btn btn-primary">Change password</button>
							<a class="btn btn-default" href="{{ route('users.edit', $user->url) }}">Cancel</a>
						</div>
					</div>

				</fieldset>

			</div>
		</div>
	</form>

</div>
@endsection
