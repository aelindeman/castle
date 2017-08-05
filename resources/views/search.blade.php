@extends('layout.master')

@section('title', (isset($term) ? ('Results for ' . e($term)) : '') . ' - Search - Castle')

@section ('breadcrumbs')
<ol class="breadcrumb">
	<li><a href="{{ route('home.index') }}">Home</a></li>
@if (isset($term))
	<li><a href="{{ route('home.search') }}">Search</a></li>
	<li class="active">{{ $term }}</li>
@else
	<li class="active">Search</li>
@endif
</ol>
@endsection

@section('content')
<div class="container">
	<div class="row">
		<div class="col-md-8 col-md-offset-2">

			@if (isset($results))
			<header class="page-header">
				<h1>
				@if ($results->isEmpty())
					No results for <mark>{{ $term }}</mark>.
				@else
					Results for <mark>{{ $term }}</mark>
					<small>
						&times;{{ $results->total() }}
					</small>
				@endif
				</h1>
				<div class="action-bar">

				</div>
			</header>
			@endif

			@if (empty($term))
			<div class="alert alert-info">
				Enter something to search for.
			</div>
			@elseif (!$results->isEmpty())
			<section class="search-results-list">
				<?php $maxRelevance = $results->max('relevance'); ?>
				@foreach ($results as $result)
				<article class="row">
					<aside class="col-sm-2 search-results-result-info">
						<span class="text-muted search-results-result-info-type">
							{{ type_of($result) }}
						</span>
						<div class="search-results-result-info-relevance hidden-xs">
							<div class="text-muted text-center">
								<small>
									score:
									{{ $result->relevance }}
								</small>
							</div>
							<div class="progress">
								<div class="progress-bar" style="width: {{ round($result->relevance / $maxRelevance * 100, 1) }}%">
								</div>
							</div>
						</div>
					</aside>
					<div class="col-sm-10 search-results-result-result">
						@include(view_for_class($result).'.partials.single', ['item' => $result])
					</div>
				</article>
				@endforeach
			</section>
			<nav class="text-center">
				{!! $results->links() !!}
			</nav>
			@endif

		</div>
	</div>
</div>
@endsection
