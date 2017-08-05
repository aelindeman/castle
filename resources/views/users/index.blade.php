@extends('layout.master')

@section('title', 'Users - Castle')

@section('breadcrumbs')
<ol class="breadcrumb">
	<li><a href="{{ route('home.index') }}">Home</a></li>
	<li class="active">Users</li>
</ol>
@endsection

@section('content')
<div class="container item-index user-index">
	<div class="row">
		<div class="col-md-8 col-md-offset-2">

			<header class="page-header">
				<h1>
					Users
					<small>
						&times;{{ $users->total() }}
					</small>
				</h1>
			</header>

			<div class="action-bar">
				<div class="row">
					<div class="col-xs-6 action-bar-left">
						@can('manage', Castle\User::class)
						<a class="btn btn-success" href="{{ route('users.create') }}">
							Create user
						</a>
						@endcan
					</div>
					<div class="col-xs-6 action-bar-right">
						<form class="" action="{{ route('users.index') }}" method="get">
							<label for="filter" class="sr-only">Filter</label>
							<div class="btn-group">
								<button type="submit" name="order" value="asc" class="btn {{ (Request::get('order', 'asc') == 'asc') ? 'btn-info' : 'btn-default' }}">
									<span class="glyphicon glyphicon-sort-by-alphabet"></span>
									<span class="sr-only">Sort by name, A to Z</span>
								</button>
								<button type="submit" name="order" value="desc" class="btn {{ (Request::get('order', '') == 'desc') ? 'btn-info' : 'btn-default' }}">
									<span class="glyphicon glyphicon-sort-by-alphabet-alt"></span>
									<span class="sr-only">Sort by name, Z to A</span>
								</button>
							</div>
						</form>
					</div>
				</div>
			</div>

			@if ($users->isEmpty())
			<div class="alert alert-info">
				There are no users.
			</div>
			@else
			@include('users.partials.list', ['users' => $users])
			<nav class="text-center">
				{!! $users->links() !!}
			</nav>
			@endif

		</div>
	</div>

</div>
@endsection
