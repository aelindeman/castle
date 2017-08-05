@extends('layout.master')

@section('title', 'Edit ' . e($user->name) . ' - Users - Castle')

@section('breadcrumbs')
<ol class="breadcrumb">
	<li><a href="{{ route('home.index') }}">Home</a></li>
	<li><a href="{{ route('users.index') }}">Users</a></li>
	<li><a href="{{ route('users.show', $user->url) }}">{{ $user->name }}</a></li>
	<li class="active">Edit</li>
</ol>
@endsection

@section('content')
<div class="container item-editor user-editor">

	<form class="form-horizontal" method="post" action="{{ route('users.update', $user->url) }}">
		<div class="row">
			<div class="col-md-8 col-md-offset-2">
				{!! csrf_field() !!}
				{{ method_field('put') }}

				<fieldset>

					<div class="form-group{{ $errors->has('name') ? ' has-error has-feedback' : '' }}">
						<label class="col-md-3 control-label" for="name">Name</label>

						<div class="col-md-6">
							<input type="text" class="form-control" name="name" id="name" value="{{ old('name', $user->name) }}">

							@if ($errors->has('name'))
								<span class="help-block">
									<strong>{{ $errors->first('name') }}</strong>
								</span>
							@endif
						</div>
					</div>

					<div class="form-group{{ $errors->has('email') ? ' has-error has-feedback' : '' }}">
						<label class="col-md-3 control-label" for="email">Email address</label>

						<div class="col-md-6">
							<input type="text" class="form-control" name="email" id="email" value="{{ old('email', $user->email) }}">

							@if ($errors->has('email'))
								<span class="help-block">
									<strong>{{ $errors->first('email') }}</strong>
								</span>
							@endif
						</div>
					</div>

					<div class="form-group{{ $errors->has('phone') ? ' has-error has-feedback' : '' }}">
						<label class="col-md-3 control-label" for="phone">Phone number</label>

						<div class="col-md-6">
							<input type="tel" class="form-control" name="phone" id="phone" value="{{ old('phone', $user->phone) }}">

							@if ($errors->has('phone'))
								<span class="help-block">
									<strong>{{ $errors->first('phone') }}</strong>
								</span>
							@endif
						</div>
					</div>

				</fieldset>
				<fieldset>

					<div class="form-group">
						<label class="col-md-3 control-label">Password</label>

						<div class="col-md-6">
							<a class="btn btn-primary" href="{{ route('users.edit.password', $user->url) }}">Change password</a>
						</div>
					</div>

				</fieldset>
				<fieldset>

					<div class="form-group{{ $errors->has('permissions') ? ' has-error has-feedback' : '' }}">
						<label class="col-md-3 control-label">Permissions</label>

						<div class="col-md-6">
							@include('users.partials.permissions', [
								'permissions' => (Gate::check('manage', Castle\User::class) ?
									Castle\Permission::all() :
									$user->permissions),
								'edit' => true
							])

							@if ($errors->has('permissions'))
								<span class="help-block">
									<strong>{{ $errors->first('permissions') }}</strong>
								</span>
							@endif
						</div>
					</div>

				</fieldset>
				<fieldset class="form-bottom-toolbar">

					<div class="form-group">
						<div class="col-md-6 col-md-offset-3">
							<button type="submit" class="btn btn-primary">Save changes</button>
							<a class="btn btn-default" href="{{ route('users.show', $user->url) }}">Cancel</a>
						</div>
					</div>

				</fieldset>

			</div>
		</div>
	</form>

</div>
@endsection
