@extends('layout.master')

@section('title', 'Docs - Castle')

@section('breadcrumbs')
<ol class="breadcrumb">
	<li><a href="{{ route('home.index') }}">Home</a></li>
	<li class="active">Docs</li>
</ol>
@endsection

@section('content')
<div class="container item-index document-index">
	<div class="row">
		<div class="col-md-8 col-md-offset-2">

			<header class="page-header">
				<h1>
					Documents
					<small>
						&times;{{ $docs->total() }}
					</small>
				</h1>
			</header>

			<div class="action-bar">
				<div class="row">
					<div class="col-sm-6 action-bar-left">
						@can('manage', Castle\Document::class)
						<a class="btn btn-success" href="{{ route('docs.create') }}">
							Create document
						</a>
						@endcan
					</div>
					<div class="col-sm-6 action-bar-right">
						<form class="form-inline" method="get" action="{{ route('docs.index') }}">
							<label for="filter" class="sr-only">Filter</label>
							<div class="input-group">
								<input type="text" class="form-control" id="filter" name="filter" placeholder="Filter" value="{{ Request::input('filter', '') }}"></input>
								<span class="input-group-btn">
									<button type="submit" class="btn btn-info">
										<span class="glyphicon glyphicon-filter"></span>
										<span class="sr-only">Filter</span>
									</button>
									<a href="{{ route('docs.index') }}" class="btn btn-default">
										<span class="glyphicon glyphicon-erase"></span>
										<span class="sr-only">Clear filter</span>
									</a>
								</span>
							</div>
						</form>
					</div>
				</div>
			</div>

			@if ($docs->isEmpty())
			<div class="alert alert-info">
				@if (Request::input('filter', false))
					<span>No documents match your filter.</span>
				@else
					<span>There are no documents.</span>
				@endif
			</div>
			@else
			@include('docs.partials.list', ['docs' => $docs])
			<nav class="text-center">
				{!! $docs->links() !!}
			</nav>
			@endif

		</div>
	</div>
</div>
@endsection
