<section class="comment-editor item-editor">
	<form method="post" action="{{ route('whiteboard.comments.store', $discussion->url) }}">
		{!! csrf_field() !!}
		<div class="form-group">
			<div class="media">
				<div class="media-body media-middle{{ $errors->has('content') ? ' has-error has-feedback' : '' }}">
					<label class="sr-only" for="content">
						Comment
						<br class="hidden-xs">
						<img src="{{ elixir('images/markdown.svg') }}" type="image/svg+xml" alt="Markdown" style="height: 18px; width: auto;" />
					</label>
					<textarea type="text" class="form-control mono-text" name="content" id="content" rows="1" placeholder="Type something." style="resize: vertical;">{{ old('content') }}</textarea>
					@if ($errors->has('content'))
					<span class="help-block">
						<strong>{{ $errors->first('content') }}</strong>
					</span>
					@endif
				</div>
				<div class="media-right media-top">
					<div class="media-object">
						<button type="submit" class="btn btn-success">
							Create comment
						</button>
					</div>
				</div>
			</div>
		</div>
	</form>
</section>
