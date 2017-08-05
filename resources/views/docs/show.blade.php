@extends('layout.master')

@section('title', e($doc->name) . ' - Docs - Castle')

@section('breadcrumbs')
<ol class="breadcrumb">
    <li><a href="{{ route('home.index') }}">Home</a></li>
    <li><a href="{{ route('docs.index') }}">Docs</a></li>
    <li class="active">{{ $doc->name }}</li>
</ol>
@endsection

@section('content')
<div class="container item-viewer document-viewer">
	<div class="row">
		<div class="col-md-8 col-md-offset-2">

			<header class="well">
				<h1>{{ $doc->name }}</h1>

				@include('tags.partials.bar', [
					'clients' => $doc->clients,
					'tags' => $doc->tags,
					'linkify' => true
				])

				<div class="updated text-muted">
					{{ $doc->updated_at > $doc->created_at ? 'Updated' : 'Created' }}
					<time datetime="{{ $doc->updated_at->toW3cString() }}" title="{{ $doc->updated_at }}">
						{{ $doc->updated_at->diffForHumans() }}
					</time>
					by
					@if ($doc->updatedBy)
					<a href="{{ route('users.show', $doc->updatedBy->url) }}">
						{{ $doc->updatedBy->name }}
					</a>
					@elseif ($doc->createdBy)
					<a href="{{ route('users.show', $doc->createdBy->url) }}">
						{{ $doc->createdBy->name }}
					</a>
					@else
					<span class="text-muted">
						(deleted)
					</span>
					@endif
				</div>

				@include('layout.common.action-bar', [
					'showRevisions' => $doc->revisionHistory->count() > 0,
					'revisePermission' => ['manage', $doc],
					'revisionsRoute' => route('docs.revisions', $doc->url),
					'editPermission' => ['manage', $doc],
					'editRoute' => route('docs.edit', $doc->url),
					'deletePermission' => ['delete', $doc],
					'deleteRoute' => route('docs.destroy', $doc->url),
					'deleteWarning' => (isset($doc->attachments) and !$doc->attachments->isEmpty()) ?
						'This document\'s attachments will also be deleted.' :
						null,
				])
			</header>

		</div>
	</div>
	<div class="row">

		<div class="col-md-2">
			<aside class="document-toc" data-toc="#document-content">
				<span class="text-muted">
					Contents
				</span>
			</aside>
		</div>

		<div class="col-md-8">
			<section id="document-content" class="document-content">
				{!! $doc->toHtml() !!}
			</section>
		</div>

	</div>
	<div class="row">
		<div class="col-md-8 col-md-offset-2">

			<section class="document-extras">
				<nav class="action-bar border-top">
					<ul class="nav nav-pills">
						@if (isset($doc->attachments) and !$doc->attachments->isEmpty())
						<li>
							<a href="#attachments" data-toggle="tab">
								Attachments
								<small class="text-muted">
									&times;{{ $doc->attachments->count() }}
								</small>
							</a>
						</li>
						@endif
						@if (false and isset($doc->metadata) and !$doc->metadata->isEmpty())
						<li>
							<a href="#metadata" data-toggle="tab">
								Metadata
								<small class="text-muted">
									&times;{{ $doc->metadata->count() }}
								</small>
							</a>
						</li>
						@endif
					</ul>
				</nav>
				<div class="tab-content">
					@if (isset($doc->attachments) and !$doc->attachments->isEmpty())
					<article id="attachments" class="tab-pane">
						@include('attachments.partials.list', ['attachments' => $doc->attachments])
					</article>
					@endif
					@if (false and isset($doc->metadata) and !$doc->metadata->isEmpty())
					<article id="metadata" class="tab-pane">
						{{ dump($doc->metadata) }}
					</article>
					@endif
				</div>
			</section>

		</div>
	</div>
</div>
@endsection
