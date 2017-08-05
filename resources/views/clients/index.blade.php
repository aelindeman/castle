@extends('layout.master')

@section('title', 'Clients - Castle')

@section('breadcrumbs')
<ol class="breadcrumb">
	<li><a href="{{ route('home.index') }}">Home</a></li>
	<li class="active">Clients</li>
</ol>
@endsection

@section('content')
<div class="container item-index client-index">
	<div class="row">
		<div class="col-md-8 col-md-offset-2">

			<header class="page-header">
				<h1>
					Clients
					<small>
						&times;{{ $clients->total() }}
					</small>
				</h1>
			</header>

			<div class="action-bar row">
				<div class="col-xs-6 action-bar-left">
					@can('manage', Castle\Client::class)
					<a class="btn btn-success" href="{{ route('clients.create') }}">
						Create client
					</a>
					@endcan
				</div>
				<div class="col-xs-6 action-bar-right">
					<label for="filter" class="sr-only">Filter</label>
					{{-- <div class="btn-group">
						<a href="{{ route('clients.index', ['sort_by' => 'recent', 'sort_order' => 'desc']) }}" class="btn {{ ($sort_by == 'recent' and $sort_order == 'desc') ? 'btn-info' : 'btn-default' }}"
							data-title="Sort by most recently used" data-trigger="hover" data-toggle="tooltip">
							<span class="glyphicon glyphicon-sort-by-attributes-alt"></span>
							<span class="sr-only">Sort by most recently used</span>
						</a>
						<a href="{{ route('clients.index', ['sort_by' => 'recent', 'sort_order' => 'asc']) }}" class="btn {{ ($sort_by == 'recent' and $sort_order == 'asc') ? 'btn-info' : 'btn-default' }}"
							data-title="Sort by least recently used" data-trigger="hover" data-toggle="tooltip">
							<span class="glyphicon glyphicon-sort-by-attributes"></span>
							<span class="sr-only">Sort by least recently used</span>
						</a>
					</div> --}}
					<div class="btn-group">
						<a href="{{ route('clients.index', ['sort_by' => 'name', 'sort_order' => 'asc']) }}" class="btn {{ ($sort_by == 'name' and $sort_order == 'asc') ? 'btn-info' : 'btn-default' }}"
							data-title="Sort A to Z" data-trigger="hover" data-toggle="tooltip">
							<span class="glyphicon glyphicon-sort-by-alphabet"></span>
							<span class="sr-only">Sort A to Z</span>
						</a>
						<a href="{{ route('clients.index', ['sort_by' => 'name', 'sort_order' => 'desc']) }}" class="btn {{ ($sort_by == 'name' and $sort_order == 'desc') ? 'btn-info' : 'btn-default' }}"
							data-title="Sort Z to A" data-trigger="hover" data-toggle="tooltip">
							<span class="glyphicon glyphicon-sort-by-alphabet-alt"></span>
							<span class="sr-only">Sort Z to A</span>
						</a>
					</div>
				</div>
			</div>

			@if ($clients->isEmpty())
			<div class="alert alert-info">
				@if (Request::input('filter', false))
				<span>No clients match your filter.</span>
				@else
				<span>There are no clients.</span>
				@endif
			</div>
			@else
			@include('clients.partials.list', ['clients' => $clients])
			<nav class="text-center">
				{!! $clients->links() !!}
			</nav>
			@endif

		</div>
	</div>
</div>
@endsection
