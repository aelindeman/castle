<?php

namespace Castle\Http\Controllers;

use Castle\Http\Requests;
use Castle\Tag;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class TagController extends Controller
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
		$this->authorize('view', Tag::class);

		$tags = Tag::with('clients', 'documents', 'discussions', 'resources')
			->get()
			->sortByDesc(function($tag) {
				return $tag->occurences;
			});

		$paginator = new LengthAwarePaginator(
			$tags->forPage(LengthAwarePaginator::resolveCurrentPage(), 100),
			$tags->count(),
			100
		);

		$paginator->setPath(route('tags.index'))
			->appends($request->all());

		return view('tags.index', ['tags' => $paginator]);
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function create()
	{
		$this->authorize('manage', Tag::class);

		return view('tags.create');
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request)
	{
		$this->authorize('manage', Tag::class);

		$this->validate($request, [
			'name' => ['required', 'max:255'],
			'slug' => [
				'required',
				'max:255',
				'alpha_dash',
				'unique:tags,slug',
				'not_in:create,destroy,edit,prune'
			],
			'color' => ['regex:/^#([0-9A-Fa-f]{3}){1,2}$/']
		]);

		$tag = new Tag(
			$request->only(['name', 'slug', 'description'])
		);

		$tag->setAttribute('color', $request->get('color', color_rand()));
		$tag->save();

		if ($request->wantsJson()) {
			return response()->json($tag);
		}

		return redirect()->route('tags.index')
			->with('alert-success', 'Tag created!');
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  string  $tag
	 * @return \Illuminate\Http\Response
	 */
	public function show($tag)
	{
		$tag = Tag::findBySlug($tag);

		if (!$tag) {
			return response(view('tags.404'), 404);
		}

		$this->authorize('view', $tag);

		$tag->load('clients',
			'clients.tags',
			'discussions',
			'discussions.tags',
			'documents',
			'documents.tags',
			'resources',
			'resources.type',
			'resources.tags'
		);

		return view('tags.show', ['tag' => $tag]);
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  string  $tag
	 * @return \Illuminate\Http\Response
	 */
	public function edit($tag)
	{
		$tag = Tag::findBySlug($tag);

		if (!$tag) {
			response(view('tags.404'), 404);
		}

		$this->authorize('manage', $tag);

		$tag->load('clients', 'discussions', 'documents', 'resources');

		return view('tags.edit', ['tag' => $tag]);
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  string  $tag
	 * @return \Illuminate\Http\Response
	 */
	public function update(Request $request, $tag)
	{
		$tag = Tag::findBySlug($tag);

		if (!$tag) {
			return response(view('tags.404'), 404);
		}

		$this->authorize('manage', $tag);

		$this->validate($request, [
			'name' => ['required', 'max:255'],
			'slug' => [
				'required',
				'max:255',
				'alpha_dash',
				'unique:tags,slug,'.$tag->slug.',slug',
				'not_in:create,destroy,edit,prune'
			],
			'color' => ['regex:/^#([0-9A-Fa-f]{3}){1,2}$/']
		]);

		$tag->name = $request->input('name');
		$tag->slug = $request->input('slug');
		$tag->description = $request->input('description');
		$tag->color = $request->input('color');

		$tag->save();

		return redirect()->route('tags.show', $tag->url)
			->with('alert-success', 'Tag updated!');
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  string  $tag
	 * @return \Illuminate\Http\Response
	 */
	public function destroy($tag)
	{
		$tag = Tag::findBySlug($tag);

		if (!$tag) {
			return response(view('tags.404'), 404);
		}

		$this->authorize('delete', $tag);

		$tag->delete();

		return redirect()->route('tags.index')
			->with('alert-success', 'Tag deleted!');
	}

	/**
	 * Remove unused resources from storage.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function prune()
	{
		$this->authorize('delete', Tag::class);

		$tags = Tag::with('clients', 'documents', 'discussions', 'resources')
			->get()
			->reject(function($tag) {
				return $tag->occurences > 0 or !empty($tag->description);
			})->pluck('id');

		if ($tags->isEmpty()) {
			return redirect()->back()
				->with('alert-info', 'There are no unused tags.');
		}

		$count = $tags->count();
		$s = $count == 1 ? '' : 's';

		Tag::whereIn('id', $tags)->delete();

		return redirect()->route('tags.index')
			->with('alert-success', 'Deleted '.$count.' unused tag'.$s.'.');
	}
}
