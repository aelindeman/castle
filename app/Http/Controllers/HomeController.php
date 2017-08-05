<?php
namespace Castle\Http\Controllers;

use Castle\Client;
use Castle\Comment;
use Castle\Discussion;
use Castle\Document;
use Castle\Resource;
use Castle\Tag;
use Castle\User;
use Gate;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class HomeController extends Controller
{
	/**
	 * Classes to search through.
	 *
	 * @var array
	 */
	protected $searchable = [
		Tag::class,
		Document::class,
		Client::class,
		Resource::class,
		Discussion::class,
		Comment::class,
		User::class,
	];

	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		$this->middleware('auth', ['except' => ['jsinit']]);
	}

	/**
	 * Get a user's viewing history.
	 *
	 * @param $user User The user whose history to fetch
	 * @param $forClass string Restrict history items to a particular class
	 * @return array
	 */
	public function getUserRecents(User $user, $forClass = null)
	{
		$history = $user->history;

		if ($forClass) {
			return array_key_exists($forClass, $history) ?
				$history[$forClass] :
				[];
		}

		return $history;
	}

	/**
	 * Show the home page.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function home(Request $request)
	{
		$page = [];

		if (Gate::allows('view', Client::class)) {
			$page['clients'] = collect(
				$this->getUserRecents($request->user(), Client::class)
			);
		}

		if (Gate::allows('view', Document::class)) {
			$page['docs'] = collect(
				$this->getUserRecents($request->user(), Document::class)
			);
		}

		if (Gate::allows('view', Discussion::class)) {
			$page['discussions'] = Discussion::with('comments', 'tags', 'votes')
				->get()
				->sortByDesc(function($d) {
					return $d->score + ($d->comments()->count() * 0.8);
				})
				->take(5);
		}

		if (Gate::allows('view', Tag::class)) {
			$page['tags'] = Tag::with('clients', 'documents', 'discussions', 'resources')
				->get()
				->sortByDesc('occurences')
				->take(10);
		}

		return view('home', $page);
	}

	/**
	 * Performs a search.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function search(Request $request)
	{
		$term = trim($request->input('term', $request->route('term')));
		$results = collect();

		if (empty($term)) {
			return ($request->wantsJson() or $request->ajax()) ?
				null :
				view('search');
		}

		foreach ($this->searchable as $class) {
			if (
				Gate::allows('view', $class) and
				property_exists($class, 'searchable')
			) {
				$search = $class::search($term, null, true)->get();

				// delete duplicates, but sum relevance scores for each duplicate item
				$search->each(function($item) use ($search) {
					$duplicates = $search->where('id', $item->id);

					$item->relevance = $duplicates->sum('relevance');

					$duplicates->keys()
						->splice(1)
						->each(function($dupe) use ($search) {
							$search->forget($dupe);
						});
				});

				$results->push($search);
			}
		}

		$results = $results->collapse()->sortByDesc('relevance');

		$paginator = new LengthAwarePaginator(
			$results->forPage(LengthAwarePaginator::resolveCurrentPage(), 20),
			$results->count(),
			20
		);

		$paginator->setPath(route('home.search'))
			->appends($request->input('term'));

		if ($request->wantsJson()) {
			return $paginator;
		}

		$view = $request->ajax() ? 'search-suggest' : 'search';

		// return the search results page
		return view($view, ['term' => $term, 'results' => $paginator])
			->withInput($request->only('term'));
	}

	/**
	 * Initializes Javascript, optionally for a specific page.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return Response JSONP callback to CastleJS.init
	 */
	public function jsinit(Request $request)
	{
		$data = [
			'debug'  => config('app.debug'),
			'route'  => $request->input('via', $request->route('via')),
		];

		return response()->json($data)
			->setCallback('CastleJS.init')
			->header('Cache-Control', 'private, max-age=604800');
	}

}
