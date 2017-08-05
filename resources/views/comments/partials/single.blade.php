<li class="list-group-item comment-item">

	<div class="media">
		<div class="media-left media-middle">
			@if (!empty($item->content))
			<div class="media-object">
				<form method="post" action="{{ route('whiteboard.comments.vote', ['discussion' => $item->discussion->url, 'comment' => $item->url]) }}">
					{!! csrf_field() !!}
					<div class="item-vote-buttons comment-vote-buttons">
						<?php $vote = $item->voters->has(auth()->user()->id) ? $item->voters->get(auth()->user()->id) : false ?>
						@if ($vote == 1)
						<button type="submit" class="item-vote-button comment-vote-button active" value="none" name="vote" title="Rescind vote" data-vote-button="up">
							<span class="glyphicon glyphicon-chevron-up"></span>
							<span class="sr-only">Rescind vote</span>
						@else
						<button type="submit" class="item-vote-button comment-vote-button" value="up" name="vote" title="Vote up" data-vote-button="up">
							<span class="glyphicon glyphicon-chevron-up"></span>
							<span class="sr-only">Vote up</span>
						@endif
						</button>
						<span class="item-vote-score comment-vote-score" data-vote-score="{{ $item->score or 0 }}">
							{{ $item->score or 0 }}
						</span>
						@if ($vote == -1)
						<button type="submit" class="item-vote-button comment-vote-button active" value="none" name="vote" title="Rescind vote" data-vote-button="down">
							<span class="glyphicon glyphicon-chevron-down"></span>
							<span class="sr-only">Rescind vote</span>
						@else
						<button type="submit" class="item-vote-button comment-vote-button" value="down" name="vote" title="Vote down" data-vote-button="down">
							<span class="glyphicon glyphicon-chevron-down"></span>
							<span class="sr-only">Vote down</span>
						@endif
						</button>
					</div>
				</form>
			</div>
			@else
			<div class="media-object">
				<div class="item-vote-buttons comment-vote-buttons">
					<span class="item-vote-score comment-vote-score">
						{{ $item->score or 0 }}
					</span>
				</div>
			</div>
			@endif
		</div>

		<div class="media-body media-top">
			<header>
				@if (isset($hasActionBar) and $hasActionBar)
				@include('layout.common.action-bar', [
					'editPermission' => ['manage', $item],
					'editRoute' => route('whiteboard.comments.edit', ['discussion' => $item->discussion->url, 'comment' => $item->url]),
					'deletePermission' => ['delete', $item],
					'deleteRoute' => route('whiteboard.comments.destroy', ['discussion' => $item->discussion->url, 'comment' => $item->url])
				])
				@endif
				<ul class="list-inline">
					<li>
						@if (!isset($item->author->name))
							<strong class="text-muted">(deleted)</strong>
						@else
						<a href="{{ route('users.show', $item->author->url) }}">
							<strong>
								{{ $item->author->name }}
							</strong>
						</a>
						@endif
					</li>
					<li class="text-muted">
						<time datetime="{{ $item->created_at->toW3cString() }}" title="{{ $item->created_at }}">
							{{ $item->created_at->diffForHumans() }}
						</time>
					</li>
					@if (!(isset($hideContext) and $hideContext))
					<li class="text-muted">
						on
						<a href="{{ route('whiteboard.show', $item->discussion->url) }}">
							{{ $item->discussion->name }}
						</a>
					</li>
					@endif
				</ul>
			</header>
			<section id="comment-{{ $item->url }}">
				@if (empty($item->content))
				<span class="text-muted">(deleted)</span>
				@else
				{!! $item->toHtml() !!}
				@endif
			</section>
		</div>
	</div>

</li>
