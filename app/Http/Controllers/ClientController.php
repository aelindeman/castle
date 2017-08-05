<?php

namespace Castle\Http\Controllers;

use Castle\Client;
use Castle\Http\Requests;
use Castle\Tag;
use DB;
use Illuminate\Http\Request;

class ClientController extends Controller
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
		$this->authorize('view', Client::class);

		list($sort_by, $sort_order) = array_values($request->only('sort_by', 'sort_order'));

		$sort_by = in_array($sort_by, ['name']) ? $sort_by : 'name';
		$sort_order = $sort_order == 'desc' ? 'desc' : 'asc';

		$clients = Client::with('documents', 'resources', 'tags')
			->orderBy($sort_by, $sort_order)
			->paginate(20)
			->appends(['sort_by' => $sort_by, 'sort_order' => $sort_order]);

		return view('clients.index', [
			'clients' => $clients,
			'sort_by' => $sort_by,
			'sort_order' => $sort_order
		]);
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function create()
	{
		$this->authorize('manage', Client::class);

		return view('clients.create');
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request)
	{
		$this->authorize('manage', Client::class);

		$this->validate($request, [
			'name' => ['required', 'max:255'],
			'slug' => [
				'required',
				'max:255',
				'alpha_dash',
				'unique:clients,slug',
				'not_in:create,destroy,edit,purge'
			],
			'color' => ['regex:/^#([0-9A-Fa-f]{3}){1,2}$/'],

			'tags' => ['array'],
			'tags.*' => ['exists:tags,id']
		]);

		$client = Client::create(
			$request->only(['name', 'slug', 'color', 'description'])
		);

		$client->tags()->sync($request->input('tags', []));

		return redirect()->route('clients.index')
			->with('alert-success', 'Client created!');
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  string  $slug
	 * @return \Illuminate\Http\Response
	 */
	public function show(Request $request, $slug)
	{
		$client = Client::findBySlug($slug);

		if (!$client) {
			return response(view('clients.404'), 404);
		}

		$this->authorize('view', $client);

		$request->user()->pushToHistory($client)->save();

		$client->load('documents', 'documents.tags', 'resources', 'resources.tags', 'tags');

		return view('clients.show', ['client' => $client]);
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  string  $slug
	 * @return \Illuminate\Http\Response
	 */
	public function edit($slug)
	{
		$client = Client::findBySlug($slug);

		if (!$client ) {
			return response(view('clients.404'), 404);
		}

		$this->authorize('manage', $client);

		$client->load('tags');

		return view('clients.edit', ['client' => $client]);
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  string  $slug
	 * @return \Illuminate\Http\Response
	 */
	public function update(Request $request, $slug)
	{
		$client = Client::findBySlug($slug);

		if (!$client) {
			return response(view('clients.404'), 404);
		}

		$this->authorize('manage', $client);

		$this->validate($request, [
			'name' => ['required', 'max:255'],
			'slug' => [
				'required',
				'max:255',
				'alpha_dash',
				'unique:clients,slug,'.$client->slug.',slug',
				'not_in:create,destroy,edit,purge'
			],
			'color' => ['regex:/^#([0-9A-Fa-f]{3}){1,2}$/'],

			'tags' => ['array'],
			'tags.*' => ['exists:tags,id']
		]);

		$client->name = $request->input('name');
		$client->slug = $request->input('slug');
		$client->color = $request->input('color');
		$client->description = $request->input('description');
		$client->tags()->sync($request->input('tags', []));

		$client->save();

		return redirect()->route('clients.show', $client->url)
			->with('alert-success', 'Client updated!');
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  string  $slug
	 * @return \Illuminate\Http\Response
	 */
	public function destroy($slug)
	{
		$client = Client::findBySlug($slug);

		if (!$client) {
			return response(view('clients.404'), 404);
		}

		$this->authorize('delete', $client);

		$client->load('resources');

		$client->resources()->delete();
		$client->delete();

		return redirect()->route('clients.index')
			->with('alert-success', 'Client deleted!');
	}
}
