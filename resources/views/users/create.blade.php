@extends('layout.master')

@section('title', 'Create user - Users - Castle')

@section('breadcrumbs')
<ol class="breadcrumb">
	<li><a href="{{ route('home.index') }}">Home</a></li>
	<li><a href="{{ route('users.index') }}">Users</a></li>
	<li class="active">Create user</li>
</ol>
@endsection

@section('content')
<div class="container item-editor user-editor">

	<form class="form-horizontal" method="post" action="{{ route('users.store') }}">
		<div class="row">
			<div class="col-md-8 col-md-offset-2">
				{!! csrf_field() !!}

				<fieldset>
					<div class="form-group{{ $errors->has('name') ? ' has-error has-feedback' : '' }}">
						<label class="col-md-3 control-label" for="name">Name</label>

						<div class="col-md-6">
							<input type="text" class="form-control" name="name" id="name" value="{{ old('name') }}">

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
							<input type="text" class="form-control" name="email" id="email" value="{{ old('email') }}">

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
							<input type="tel" class="form-control" name="phone" id="phone" value="{{ old('phone') }}">

							@if ($errors->has('phone'))
								<span class="help-block">
									<strong>{{ $errors->first('phone') }}</strong>
								</span>
							@endif
						</div>
					</div>

				</fieldset>
				<fieldset>

					<div class="form-group{{ $errors->has('password') ? ' has-error has-feedback' : '' }}">
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

					<div class="form-group{{ $errors->has('password_confirmation') ? ' has-error has-feedback' : '' }}">
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
				<fieldset>

					<div class="form-group{{ $errors->has('permissions') ? ' has-error has-feedback' : '' }}">
						<label class="col-md-3 control-label">Permissions</label>

						<div class="col-md-6">
							<?php $defaults = Castle\Permission::defaults() ?>
                            @include('users.partials.permissions', [
                                'permissions' => Castle\Permission::all(),
                                'edit' => false
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
							<button type="submit" class="btn btn-primary">Create user</button>
							<a class="btn btn-default" href="{{ route('users.index') }}">Cancel</a>
						</div>
					</div>

				</fieldset>

			</div>
		</div>
	</form>

</div>
@endsection
