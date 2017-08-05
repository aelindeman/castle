<?php

namespace Castle\Http\Controllers;

use Castle\Client;
use Castle\Http\Requests;
use Castle\Resource;
use Castle\ResourceType;
use Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Log;
use Session;

class ResourceController extends Controller
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
	public function index($client)
	{
		return redirect()->route('clients.show', $client);
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function create($client)
	{
		$this->authorize('manage', Resource::class);

		$client = Client::findBySlug($client);

		return view('resources.create', ['client' => $client]);
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request, $client)
	{
		$this->authorize('manage', Resource::class);

		$client = Client::findBySlug($client);

		$this->validate($request, [
			'name' => ['required', 'max:255'],
			'slug' => [
				'required',
				'max:255',
				'alpha_dash',
				'unique:resources,slug,NULL,slug,client_id,'.$client->id,
				'not_in:create,destroy,edit,prune'
			],

			'metadata.*' => ['array'],
			'metadata.keys.*' => ['distinct', 'required_with:metadata.values.*'],

			'attachments' => ['array'],
			'uploads' => ['array'],

			'type' => ['required'],

			'client' => ['required', 'exists:clients,id'],

			'tags' => ['array'],
			'tags.*' => ['exists:tags,id'],
		]);

		// metadata gets set here
		$resource = new Resource([
			'name' => $request->input('name'),
			'slug' => $request->input('slug'),
			'description' => $request->input('description'),
		]);

		$metadata = static::parseMetadataFromForm($request->input('metadata', []));
		$resource->metadata = Crypt::encrypt($metadata);

		if ($request->hasFile('uploads')) {
			$resource->attachments = $request->file('uploads');
		}

		$type = ResourceType::firstOrNew([
			'slug' => str_slug($request->input('type'))
		]);

		if (!$type->exists) {
			$type->name = $request->input('type');
			$type->save();
		}

		$resource->type()->associate($type);
		$resource->client()->associate($client);

		$resource->save();

		$resource->tags()->sync($request->input('tags', []));

		return redirect()->route('clients.show', $client->url)
			->with('alert-success', 'Resource created!');
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  string  $resource
	 * @return \Illuminate\Http\Response
	 */
	public function show(Request $request, $client, $resource)
	{
		$resource = Resource::findBySlug($client, $resource);

		if (!$resource) {
			return response(view('resources.404'), 404);
		}

		$this->authorize('view', $resource);

		$request->user()->pushToHistory($resource)->save();

		$resource->load('client', 'tags', 'type');

		if ($resource->hasCorrectlyEncryptedMetadata()) {
			try {
				$resource->metadata = Crypt::decrypt($resource->metadata);
			} catch (DecryptException $e) {
				Session::flash('alert-danger', 'There was a problem decrypting this resource\'s metadata.');
				Log::notice('Could not decrypt resource metadata', ['exception' => $e]);
			}
		} else {
			Session::flash('alert-info', 'This resource\'s metadata is not encrypted (or is encrypted incorrectly).');
		}

		return view('resources.show', ['resource' => $resource]);
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  string  $resource
	 * @return \Illuminate\Http\Response
	 */
	public function edit(Request $request, $client, $resource)
	{
		$resource = Resource::findBySlug($client, $resource);

		if (!$resource) {
			return response(view('resources.404'), 404);
		}

		$this->authorize('manage', $resource);

		$resource->load('client', 'tags', 'type');

		if ($resource->hasCorrectlyEncryptedMetadata()) {
			try {
				$resource->metadata = Crypt::decrypt($resource->metadata);
			} catch (DecryptException $e) {
				Session::flash('alert-danger', 'There was a problem decrypting this resource\'s metadata.');
				Log::notice('Could not decrypt resource metadata', ['exception' => $e]);
			}
		} else {
			Session::flash('alert-info', 'This resource\'s metadata is not encrypted (or is encrypted incorrectly).');
		}

		return view('resources.edit', ['client' => $resource->client, 'resource' => $resource]);
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  string  $client
	 * @param  string  $resource
	 * @return \Illuminate\Http\Response
	 */
	public function chooseRevision(Request $request, $client, $resource)
	{
		$resource = Resource::findBySlug($client, $resource);

		if (!$resource) {
			return response(view('resources.404'), 404);
		}

		$this->authorize('manage', $resource);

		$results = $resource->revisionHistory->sortByDesc('created_at');

		// decrypt the metadata or revision list won't show anything useful
		$results->map(function($r) {
			if ($r->key == 'metadata') {

				try {
					$r->old_value = json_encode(Crypt::decrypt($r->old_value));
				} catch (DecryptException $e) { }

				try {
					$r->new_value = json_encode(Crypt::decrypt($r->new_value));
				} catch (DecryptException $e) { }

			}

			return $r;
		});

		$paginator = new LengthAwarePaginator(
			$results->forPage(LengthAwarePaginator::resolveCurrentPage(), 10),
			$results->count(),
			10
		);

		$paginator->setPath(route('clients.resources.revisions', [
				'client' => $resource->client->url,
				'resource' => $resource->url
			]))
			->appends($request->all());

		return view('resources.revisions', [
				'client' => $resource->client,
				'resource' => $resource,
				'revisions' => $paginator
			]);
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  string  $resource
	 * @return \Illuminate\Http\Response
	 */
	public function update(Request $request, $client, $resource)
	{
		$resource = Resource::findBySlug($client, $resource);

		if (!$resource) {
			return response(view('resources.404'), 404);
		}

		$resource->load('client');
		$client = $resource->client;

		$this->authorize('manage', $resource);

		$this->validate($request, [
			'name' => ['required', 'max:255'],
			'slug' => [
				'required',
				'max:255',
				'alpha_dash',
				'unique:resources,slug,'.$request->input('slug', $resource->slug).',slug,client_id,'.$resource->client->id,
				'not_in:create,destroy,edit,prune'
			],

			'metadata.*' => ['array'],
			'metadata.keys.*' => ['distinct', 'required_with:metadata.values.*'],

			'attachments' => ['array'],
			'uploads' => ['array'],

			'type' => ['required'],

			'client' => ['required', 'exists:clients,id'],

			'tags' => ['array'],
			'tags.*' => ['exists:tags,id'],
		]);

		if (($newClient = $request->input('client', $resource->client->id)) != $resource->client->id) {
			$client = Client::find($newClient);
			$resource->client()->associate($client);
		}

		$resource->name = $request->input('name');
		$resource->slug = $request->input('slug');
		$resource->description = $request->input('description');

		$metadata = static::parseMetadataFromForm($request->input('metadata', []));
		$resource->metadata = Crypt::encrypt($metadata);

		$attachments = $request->input('attachments', []);

		if ($request->hasFile('uploads')) {
			$uploads = $request->file('uploads');
			$attachments = array_merge($attachments, $uploads);
		}

		$resource->attachments = $attachments;

		// set type
		if (!($type = ResourceType::findBySlug($request->input('type')))) {
			$type = ResourceType::create([
				'name' => $request->input('type'),
				'slug' => str_slug($request->input('type')),
			]);
		}

		$resource->type()->associate($type);

		$resource->save();

		$resource->tags()->sync($request->input('tags', []));

		return redirect()->route('clients.resources.show', ['client' => $resource->client->url, 'resource' => $resource->url])
			->with('alert-success', 'Resource updated!');
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  string  $client
	 * @param  string  $resource
	 * @param  string  $revision
	 * @return \Illuminate\Http\Response
	 */
	public function restoreRevision(Request $request, $client, $resource, $revision)
	{
		$resource = Resource::findBySlug($client, $resource);

		if (!$resource) {
			return response(view('docs.404'), 404);
		}

		$this->authorize('manage', $resource);

		$revision = $resource->revisionHistory->where('id', (int) $revision)->first();
		assert($revision->revisionable_type == Resource::class, 'not a revision for '.Resource::class);

		$key = $revision->key;
		$value = $revision->old_value;

		if (
			!$resource->isFillable($key) ||
			(isset($resource->keepRevisionOf) && !in_array($key, $resource->keepRevisionOf))
		) {
			return redirect()->route('clients.resources.show', [
					'client' => $resource->client->url,
					'resource' => $resource->url
				])
				->with('alert-warning', 'The property "'.$key.'" for this resource could not be restored.');
		}

		$resource->$key = $value;
		$resource->save();

		return redirect()->route('clients.resources.show', ['client' => $resource->client->url, 'resource' => $resource->url])
			->with('alert-success', 'Resource updated!');
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function destroy(Request $request, $client, $resource)
	{
		$resource = Resource::findBySlug($client, $resource);

		if (!$resource) {
			return response(view('resources.404'), 404);
		}

		$this->authorize('delete', $resource);

		$resource->delete();

		return redirect()->route('clients.show', $client)
			->with('alert-success', 'Resource deleted!');
	}

	/**
	 * Parses metadata input from an HTML form into a key-value-paired array.
	 *
	 * @return array
	 */
	private static function parseMetadataFromForm($input)
	{
		$keys = array_values($input['keys']);
		$values = array_values($input['values']);
		$metadata = [];

		$iter = max(count($keys), count($values));

		for ($i = 0; $i < $iter; $i ++) {
			$key = array_key_exists($i, $keys) ? $keys[$i] : null;
			$value = array_key_exists($i, $values) ? $values[$i] : null;

			if (empty($key)) {
				continue;
			}

			$metadata[$key] = $value;
		}

		return $metadata;
	}

}
