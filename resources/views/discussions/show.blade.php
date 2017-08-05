@extends('layout.master')

@section('title', e($discussion->name) . ' - Whiteboard - Castle')

@section('breadcrumbs')
<ol class="breadcrumb">
    <li><a href="{{ route('home.index') }}">Home</a></li>
    <li><a href="{{ route('whiteboard.index') }}">Whiteboard</a></li>
    <li class="active">{{ $discussion->name }}</li>
</ol>
@endsection

@section('content')
<div class="container item-viewer document-viewer">
	<div class="row">
		<div class="col-md-8 col-md-offset-2">

			<header class="well">
				<h1>{{ $discussion->name }}</h1>

				@include('tags.partials.bar', [
					'tags' => $discussion->tags,
					'linkify' => true
				])

				<div class="row">
					<div class="col-sm-8 updated text-muted">
						@if ($discussion->trashed())
						<strong>Archived</strong>
						<br>
						@endif
						{{ $discussion->updated_at > $discussion->created_at ? 'Updated' : 'Created' }}
						<time datetime="{{ $discussion->updated_at->toW3cString() }}" title="{{ $discussion->updated_at}}">
							{{ $discussion->updated_at->diffForHumans() }}
						</time>
						by
						@if ($discussion->updatedBy)
						<a href="{{ route('users.show', $discussion->updatedBy->url) }}">
							{{ $discussion->updatedBy->name }}
						</a>
						@elseif ($discussion->createdBy)
						<a href="{{ route('users.show', $discussion->createdBy->url) }}">
							{{ $discussion->createdBy->name }}
						</a>
						@else
						<span class="text-muted">
							(deleted)
						</span>
						@endif
					</div>
					<div class="col-sm-4">
						{{-- $discussion->status or 'unspecified' --}}
					</div>
				</div>

				<nav class="row action-bar">
					<div class="col-xs-7 text-left">
						<form method="post" action="{{ route('whiteboard.vote', $discussion->url) }}">
							{!! csrf_field() !!}
							<div class="item-vote-buttons discussion-vote-buttons">
								<?php $vote = $discussion->voters->has(auth()->user()->id) ? $discussion->voters->get(auth()->user()->id) : false ?>
								@if ($vote == 1)
								<button type="submit" class="item-vote-button discussion-vote-button active" value="none" name="vote" title="Rescind vote" data-vote-button="up">
									<span class="glyphicon glyphicon-chevron-up"></span>
									<span class="sr-only">Rescind vote</span>
								@else
								<button type="submit" class="item-vote-button discussion-vote-button" value="up" name="vote" title="Vote up" data-vote-button="up">
									<span class="glyphicon glyphicon-chevron-up"></span>
									<span class="sr-only">Vote up</span>
								@endif
								</button>
								<span class="item-vote-score discussion-vote-score" data-vote-score="{{ $discussion->score or 0 }}">
									{{ $discussion->score or 0 }}
								</span>
								@if ($vote == -1)
								<button type="submit" class="item-vote-button discussion-vote-button active" value="none" name="vote" title="Rescind vote" data-vote-button="down">
									<span class="glyphicon glyphicon-chevron-down"></span>
									<span class="sr-only">Rescind vote</span>
								@else
								<button type="submit" class="item-vote-button discussion-vote-button" value="down" name="vote" title="Vote down" data-vote-button="down">
									<span class="glyphicon glyphicon-chevron-down"></span>
									<span class="sr-only">Vote down</span>
								@endif
								</button>
							</div>
						</form>
					</div>
					<div class="col-xs-5 text-right">
						@include('layout.common.action-bar', [
							'noContainer' => true,
							'showRevisions' => $discussion->revisionHistory->count() > 0,
							'revisePermission' => ['manage', $discussion],
							'revisionsRoute' => route('whiteboard.revisions', $discussion->url),
							'editPermission' => ['manage', $discussion],
							'editRoute' => route('whiteboard.edit', $discussion->url),
							'deletePermission' => ['delete', $discussion],
							'deleteRoute' => route('whiteboard.destroy', $discussion->url),
							'deleteWarning' => (isset($discussion->attachments) and !$discussion->attachments->isEmpty()) ?
								'This discussion\'s attachments will also be deleted.' :
								null,
						])
					</div>
				</nav>
			</header>

			<section class="discussion-content">
				{!! $discussion->toHtml() !!}
			</section>

			<section class="discussion-extras">
				<nav class="action-bar border-top">
					<ul class="nav nav-pills">
						@if (isset($discussion->attachments) and !$discussion->attachments->isEmpty())
						<li>
							<a href="#attachments" data-toggle="tab">
								Attachments
								<small class="text-muted">
									&times;{{ $discussion->attachments->count() }}
								</small>
							</a>
						</li>
						@endif
					</ul>
				</nav>
				<div class="tab-content">
					@if (isset($discussion->attachments) and !$discussion->attachments->isEmpty())
					<article id="attachments" class="tab-pane">
						@include('attachments.partials.list', ['attachments' => $discussion->attachments])
					</article>
					@endif
				</div>
			</section>

			@can ('participate', $discussion)
			<section class="discussion-comments">
				<h2>
					Comments
					<small>
						&times;{{ $comments->count() }}
					</small>
				</h2>

				@if (!$comments->isEmpty())
				<ul class="list-inline action-bar">
					<li>
						<form action="" method="get">
							<label for="comment-sort">Sort by</label>
							<select name="commentSort" id="comment-sort" onchange="$(this).closest('form').get(0).submit()">
							@foreach (['best' => 'best score', 'worst' => 'worst score', 'new' => 'newest', 'old' => 'oldest'] as $sortKey => $sortDescription)
								<option value="{{ $sortKey }}"{!! $sortKey == $commentSortKey ? ' selected="selected"' : '' !!}>{{ $sortDescription }}</option>
							@endforeach
							</select>
						</form>
					</li>
				</ul>
				@endif

				@if (!$discussion->trashed())
					@include ('comments.partials.create')
				@endif

				@include ('comments.partials.list', [
					'comments' => $comments,
					'hideContext' => true,
					'hasActionBar' => true,
				])
				{!! $comments->links() !!}
			</section>
			@endcan

		</div>
	</div>
</div>
@endsection
