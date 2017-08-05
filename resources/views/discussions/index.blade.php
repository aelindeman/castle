@extends('layout.master')

@section('title', 'Whiteboard - Castle')

@section('breadcrumbs')
<ol class="breadcrumb">
	<li><a href="{{ route('home.index') }}">Home</a></li>
	<li class="active">Whiteboard</li>
</ol>
@endsection

@section('content')
<div class="container item-index discussion-index">
	<div class="row">
		<div class="col-md-8 col-md-offset-2">

			<header class="page-header">
				<h1>
					Whiteboard
					<small>
						&times;{{ $discussions->total() }}
					</small>
				</h1>
			</header>

			<div class="row action-bar">
				@can('participate', Castle\Discussion::class)
				<div class="col-sm-6 action-bar-left">
					<a class="btn btn-success" href="{{ route('whiteboard.create') }}">
						Create discussion
					</a>
				</div>
				@endcan
				<div class="col-sm-6 action-bar-right">
					@can('view', Castle\Discussion::class)
					<a class="btn btn-default{{ $archived ? ' active' : '' }}" href="{{ route('whiteboard.index', $archived ? null : 'withArchived') }}">
						Include archived
					</a>
					@endcan
				</div>
			</div>

			@if ($discussions->isEmpty())
			<div class="alert alert-info">
				@if (Request::input('filter', false))
				<span>No discussions match your filter.</span>
				@else
				<span>There are no discussions.</span>
				@endif
			</div>
			@else
			@include('discussions.partials.list', ['discussions' => $discussions])
			<nav class="text-center">
				{!! $discussions->links() !!}
			</nav>
			@endif

		</div>
	</div>
</div>
@endsection
