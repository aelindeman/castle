@extends('layout.master')

@section('title', 'Revisions of ' . e($doc->name) . ' - Docs - Castle')

@section('breadcrumbs')
<ol class="breadcrumb">
	<li><a href="{{ route('home.index') }}">Home</a></li>
	<li><a href="{{ route('docs.index') }}">Docs</a></li>
	<li><a href="{{ route('docs.show', $doc->url) }}">{{ $doc->name }}</a></li>
	<li class="active">Revisions</li>
</ol>
@endsection

@section('content')
<div class="container item-revisions document-revisions">
	<div class="row">
		<div class="col-md-8 col-md-offset-2">

			@if ($revisions->count() > 0)
			<ul class="list-group">
			@foreach($revisions as $history)
				<li class="list-group-item revision">
					<header class="media">
						<section class="media-left">
							<form class="form-inline" action="{{ route('docs.revisions.restore', ['doc' => $doc->url, 'revision' => $history->id]) }}" method="post">
								{!! csrf_field() !!}
								<button type="submit" class="btn btn-sm btn-primary revision-restore" title="Revert this change">
									<span class="glyphicon glyphicon-share-alt"></span>
									<span class="sr-only">Revert this change</span>
								</button>
							</form>
						</section>
						<section class="media-body text-muted text-right">
							<a class="revision-user" href="{{ route('users.show', $history->userResponsible()->url) }}">
								{{ $history->userResponsible()->name }}
							</a>
							<span class="revision-field">
								changed
								<strong class="revision-field-name">{{ $history->fieldName() }}</strong>
							</span>
							<time class="revision-date" datetime="{{ $history->created_at->toW3cString() }}" title="{{ $history->created_at }}">
								{{ $history->created_at->diffForHumans() }}
							</time>
						</section>
					</header>
					<section class="revision-values">
						<div class="row">
							<article class="col-sm-6 revision-old-value">
								<strong class="visible-xs revision-value-header from">from</strong>
								<pre>{{ $history->oldValue() }}</pre>
							</article>
							<article class="col-sm-6 revision-new-value">
								<strong class="visible-xs revision-value-header to">to</strong>
								<pre>{{ $history->newValue() }}</pre>
							</article>
						</div>
					</section>
				</li>
			@endforeach
			</ul>
			<nav class="text-center">
				{!! $revisions->links() !!}
			</nav>
			@else
			<div class="alert alert-info">
				This document has no older versions.
			</div>
			@endif

		</div>
	</div>
</div>
@endsection
