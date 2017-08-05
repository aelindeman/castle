<?php

namespace Castle\Http\Controllers;

use Castle\Client;
use Castle\Document;
use Castle\Http\Requests;
use Castle\Tag;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class DocsController extends Controller
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
		$this->authorize('view', Document::class);

		$filter = $request->input('filter');

		$docs = Document::with('clients', 'tags')
			->filter($filter)
			->orderBy('updated_at', 'desc')
			->paginate(20)
			->appends(['filter' => $filter]);

		return view('docs.index', ['docs' => $docs]);
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function create()
	{
		$this->authorize('manage', Document::class);

		return view('docs.create');
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request)
	{
		$this->authorize('manage', Document::class);

		$this->validate($request, [
			'name' => ['required', 'max:255'],
			'slug' => [
				'required',
				'max:255',
				'alpha_dash',
				'unique:docs,slug',
				'not_in:create,destroy,edit,prune'
			],
			'content' => ['required'],

			'metadata' => ['array'],
			'metadata.*' => ['distinct'],

			'attachments' => ['array'],
			'uploads' => ['array'],

			'clients' => ['array'],
			'clients.*' => ['exists:clients,id', 'distinct'],

			'tags' => ['array'],
			'tags.*' => ['exists:tags,id'],
		]);

		$doc = new Document([
			'name' => $request->input('name'),
			'slug' => $request->input('slug'),
			'content' => $request->input('content'),
			'metadata' => $request->input('metadata', []),
		]);

		if ($request->hasFile('uploads')) {
			$doc->attachments = $request->file('uploads');
		}

		$doc->createdBy()->associate($request->user());

		$doc->save();

		$doc->clients()->sync($request->input('clients', []));
		$doc->tags()->sync($request->input('tags', []));

		return redirect()->route('docs.index')
			->with('alert-success', 'Document created!');
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  string  $doc
	 * @return \Illuminate\Http\Response
	 */
	public function show(Request $request, $doc)
	{
		$doc = Document::findBySlug($doc);

		if (!$doc) {
			return response(view('docs.404'), 404);
		}

		$this->authorize('view', $doc);

		$request->user()->pushToHistory($doc)->save();

		$doc->load('clients', 'tags');

		return view('docs.show', ['doc' => $doc]);
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  string  $doc
	 * @return \Illuminate\Http\Response
	 */
	public function edit($doc)
	{
		$doc = Document::findBySlug($doc);

		if (!$doc) {
			return response(view('docs.404'), 404);
		}

		$this->authorize('manage', $doc);

		$doc->load('clients', 'tags');

		return view('docs.edit', ['doc' => $doc]);
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  string  $doc
	 * @return \Illuminate\Http\Response
	 */
	public function chooseRevision(Request $request, $doc)
	{
		$doc = Document::findBySlug($doc);

		if (!$doc) {
			return response(view('docs.404'), 404);
		}

		$this->authorize('manage', $doc);

		$doc->load('updatedBy', 'createdBy');

		$results = $doc->revisionHistory->sortByDesc('created_at');

		$paginator = new LengthAwarePaginator(
			$results->forPage(LengthAwarePaginator::resolveCurrentPage(), 10),
			$results->count(),
			10
		);

		$paginator->setPath(route('docs.revisions', $doc->url))
			->appends($request->all());

		return view('docs.revisions', ['doc' => $doc, 'revisions' => $paginator]);
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  string  $doc
	 * @return \Illuminate\Http\Response
	 */
	public function update(Request $request, $doc)
	{
		$doc = Document::findBySlug($doc);

		if (!$doc) {
			return response(view('docs.404'), 404);
		}

		$this->authorize('manage', $doc);

		$this->validate($request, [
			'name' => ['required', 'max:255'],
				'slug' => [
				'required',
				'max:255',
				'alpha_dash',
				'unique:docs,slug,'.$doc->slug.',slug',
				'not_in:create,destroy,edit,prune'
			],
			'content' => ['required'],

			'metadata' => ['array'],
			'metadata.*' => ['distinct'],

			'attachments' => ['array'],
			'uploads' => ['array'],

			'clients' => ['array'],
			'clients.*' => ['exists:clients,id', 'distinct'],

			'tags' => ['array'],
			'tags.*' => ['exists:tags,id'],
		]);

		$doc->name = $request->input('name');
		$doc->slug = $request->input('slug');
		$doc->content = $request->input('content');

		if ($metadata = $request->input('metadata')) {
			$doc->metadata = $metadata;
		}

		$attachments = $request->input('attachments', []);

		if ($request->hasFile('uploads')) {
			$uploads = $request->file('uploads');
			$attachments = array_merge($attachments, $uploads);
		}

		$doc->attachments = $attachments;

		$doc->updatedBy()->associate($request->user());

		$doc->save();

		$doc->clients()->sync($request->input('clients', []));
		$doc->tags()->sync($request->input('tags', []));

		return redirect()->route('docs.show', $doc->url)
			->with('alert-success', 'Document updated!');
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  string  $doc
	 * @param  string  $revision
	 * @return \Illuminate\Http\Response
	 */
	public function restoreRevision(Request $request, $doc, $revision)
	{
		$doc = Document::findBySlug($doc);

		if (!$doc) {
			return response(view('docs.404'), 404);
		}

		$this->authorize('manage', $doc);

		$revision = $doc->revisionHistory->where('id', (int) $revision)->first();
		assert($revision->revisionable_type == Document::class, 'not a revision for '.Document::class);

		$key = $revision->key;
		$value = $revision->old_value;

		if (
			!$doc->isFillable($key) ||
			(isset($doc->keepRevisionOf) && !in_array($key, $doc->keepRevisionOf))
		) {
			return redirect()->route('docs.show', $doc->url)
				->with('alert-warning', 'The property "'.$key.'" for this document could not be restored.');
		}

		$doc->$key = $value;
		$doc->save();

		return redirect()->route('docs.show', $doc->url)
			->with('alert-success', 'Document updated!');
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  string  $doc
	 * @return \Illuminate\Http\Response
	 */
	public function destroy($doc)
	{
		$doc = Document::findBySlug($doc);

		if (!$doc) {
			return response(view('docs.404'), 404);
		}

		$this->authorize('delete', $doc);

		$doc->delete();

		return redirect()->route('docs.index')
			->with('alert-success', 'Document deleted!');
	}
}
