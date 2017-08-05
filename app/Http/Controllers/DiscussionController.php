<?php

namespace Castle\Http\Controllers;

use Castle\Comment;
use Castle\Discussion;
use Castle\DiscussionStatus;
use Castle\Http\Requests;
use Castle\Tag;
use Castle\Vote;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class DiscussionController extends Controller
{
	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		$this->middleware('auth');
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index(Request $request)
	{
		$this->authorize('view', Discussion::class);

		$discussions = Discussion::with('comments', 'tags', 'votes');

		if ($archived = $request->exists('withArchived')) {
			$discussions->withTrashed();
		}

		$discussions = $discussions->orderBy('updated_at', 'desc')
			->paginate(20)
			->appends($request->all());

		return view('discussions.index', [
			'discussions' => $discussions,
			'archived' => $archived
		]);
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function create()
	{
		$this->authorize('participate', Discussion::class);

		return view('discussions.create');
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request)
	{
		$this->authorize('participate', Discussion::class);

		$this->validate($request, [
			'name' => ['required', 'max:255'],
			'slug' => [
				'required',
				'max:255',
				'alpha_dash',
				'unique:discussions,slug',
				'not_in:create,destroy,edit,prune'
			],
			'content' => ['required'],

			'attachments' => ['array'],
			'uploads' => ['array'],

			'tags' => ['array'],
			'tags.*' => ['exists:tags,id'],
		]);

		$discussion = new Discussion([
			'name' => $request->input('name'),
			'slug' => $request->input('slug'),
			'content' => $request->input('content'),
		]);

		if ($request->hasFile('uploads')) {
			$discussion->attachments = $request->file('uploads');
		}

		$discussion->createdBy()->associate($request->user());

		$discussion->save();

		$discussion->tags()->sync($request->input('tags', []));

		return redirect()->route('whiteboard.show', $discussion->url)
			->with('alert-success', 'Discussion created!');
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  string  $discussion
	 * @return \Illuminate\Http\Response
	 */
	public function show(Request $request, $discussion)
	{
		$discussion = Discussion::findBySlug($discussion);

		if (!$discussion) {
			return response(view('discussions.404'), 404);
		}

		$this->authorize('view', $discussion);

		$request->user()
			->pushToHistory($discussion)
			->save();

		$discussion->load('createdBy', 'updatedBy', 'comments', 'tags', 'votes');

		$comments = $discussion->comments;
		$comments->load('author', 'discussion', 'votes');

		switch ($commentSortKey = $request->input('commentSort', 'old')) {
			case 'best':
				$comments = $comments->sortByDesc('score');
				break;
			case 'worst':
				$comments = $comments->sortBy('score');
				break;
			case 'new':
				$comments = $comments->sortByDesc('created_at');
				break;
			case 'old':
				$comments = $comments->sortBy('created_at');
				break;
		}

		$commentsPaginator = new LengthAwarePaginator(
			$comments->forPage(LengthAwarePaginator::resolveCurrentPage(), 50),
			$comments->count(),
			50
		);

		$commentsPaginator->setPath(route('whiteboard.show', $discussion->url))
			->appends($request->all());

		return view('discussions.show', [
			'discussion' => $discussion,
			'comments' => $commentsPaginator,
			'commentSortKey' => $commentSortKey,
		]);
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  string  $discussion
	 * @return \Illuminate\Http\Response
	 */
	public function edit($discussion)
	{
		$discussion = Discussion::findBySlug($discussion);

		if (!$discussion) {
			return response(view('discussions.404'), 404);
		}

		$this->authorize('manage', $discussion);

		$discussion->load('tags');

		return view('discussions.edit', ['discussion' => $discussion]);
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  string  $discussion
	 * @return \Illuminate\Http\Response
	 */
	public function chooseRevision(Request $request, $discussion)
	{
		$discussion = Discussion::findBySlug($discussion);

		if (!$discussion) {
			return response(view('discussions.404'), 404);
		}

		$this->authorize('manage', $discussion);

		$discussion->load('updatedBy', 'createdBy');

		$results = $discussion->revisionHistory->sortByDesc('created_at');

		$paginator = new LengthAwarePaginator(
			$results->forPage(LengthAwarePaginator::resolveCurrentPage(), 10),
			$results->count(),
			10
		);

		$paginator->setPath(route('whiteboard.revisions', $discussion->url))
			->appends($request->all());

		return view('discussions.revisions', ['discussion' => $discussion, 'revisions' => $paginator]);
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  string  $discussion
	 * @return \Illuminate\Http\Response
	 */
	public function update(Request $request, $discussion)
	{
		$discussion = Discussion::findBySlug($discussion);

		if (!$discussion) {
			return response(view('docs.404'), 404);
		}

		$this->authorize('manage', $discussion);

		$this->validate($request, [
			'name' => ['required', 'max:255'],
			'slug' => [
				'required',
				'max:255',
				'alpha_dash',
				'unique:discussions,slug,'.$discussion->slug.',slug',
				'not_in:create,destroy,edit,prune'
			],
			'content' => ['required'],

			'attachments' => ['array'],
			'uploads' => ['array'],

			'tags' => ['array'],
			'tags.*' => ['exists:tags,id'],
		]);

		$discussion->name = $request->input('name');
		$discussion->slug = $request->input('slug');
		$discussion->content = $request->input('content');

		$attachments = $request->input('attachments', []);

		if ($request->hasFile('uploads')) {
			$uploads = $request->file('uploads');
			$attachments = array_merge($attachments, $uploads);
		}

		$discussion->attachments = $attachments;
		$discussion->updatedBy()->associate($request->user());
		$discussion->tags()->sync($request->input('tags', []));

		$discussion->save();

		return redirect()->route('whiteboard.show', $discussion->url)
			->with('alert-success', 'Discussion updated!');
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  string  $discussion
	 * @param  string  $revision
	 * @return \Illuminate\Http\Response
	 */
	public function restoreRevision(Request $request, $discussion, $revision)
	{
		$discussion = Discussion::findBySlug($discussion);

		if (!$discussion) {
			return response(view('discussions.404'), 404);
		}

		$this->authorize('manage', $discussion);

		$revision = $discussion->revisionHistory->where('id', (int) $revision)->first();
		assert($revision->revisionable_type == Discussion::class, 'not a revision for '.Discussion::class);

		$key = $revision->key;
		$value = $revision->old_value;

		if (
			!$discussion->isFillable($key) ||
			(isset($discussion->keepRevisionOf) && !in_array($key, $discussion->keepRevisionOf))
		) {
			return redirect()->route('whiteboard.show', $discussion->url)
				->with('alert-warning', 'The property "'.$key.'" for this discussion could not be restored.');
		}

		$discussion->$key = $value;
		$discussion->save();

		return redirect()->route('whiteboard.show', $discussion->url)
			->with('alert-success', 'Document updated!');
	}
	/**
	 * Increases or decreases the discussion score.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  string  $discussion
	 * @return \Illuminate\Http\Response if JSON, \Illuminate\Http\Redirect otherwise
	 */
	public function vote(Request $request, $discussion)
	{
		$discussion = Discussion::findBySlug($discussion);

		if (!$discussion) {
			return response(view('discussions.404'), 404);
		}

		$this->authorize('participate', $discussion);

		if ($discussion->trashed()) {
			$message = 'You can\'t change your vote on archived discussions.';
			return $request->wantsJson() ?
				response()->json(['message' => $message], 403) :
				redirect()->back()
					->with('alert-warning', $message);
		}

		$vote = Vote::voted($request->user(), $discussion);

		if ($vote->exists()) {
			$vote = $vote->first();
		} else {
			$vote = new Vote;
			$vote->user()->associate($request->user());
			$vote->owner()->associate($discussion);
		}

		$voteValues = [
			'up' => 1,
			'down' => -1,
			'none' => 0,
		];

		$value = strtr($request->input('vote'), $voteValues);
		$vote->value = $value;

		$vote->save();
		$discussion = $discussion->fresh();

		$voteMessage = $value ?
			'Voted '.array_flip($voteValues)[$value].'!' :
			'Vote rescinded!';

		return $request->wantsJson() ?
			response()->json([
				'discussion' => $discussion->url,
				'vote' => ['value' => $vote->value],
				'score' => $discussion->score,
				'message' => $voteMessage,
			]) :
			redirect()->route('whiteboard.show', $discussion->url)
				->withInput($request->all())
				->with('alert-success', $voteMessage);
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  string  $discussion
	 * @return \Illuminate\Http\Response
	 */
	public function destroy(Request $request, $discussion)
	{
		$discussion = Discussion::findBySlug($discussion);

		if (!$discussion) {
			return response(view('discussions.404'), 404);
		}

		$this->authorize('delete', $discussion);

		if ($discussion->trashed()) {
			$discussion->forceDelete();

			return redirect()->route('whiteboard.index')
				->with('alert-success', 'Discussion deleted!');
		} else {
			$discussion->updatedBy()->associate($request->user());
			$discussion->delete();
		}

		return redirect()->route('whiteboard.index')
			->with('alert-success', 'Discussion archived!');
	}
}
