<?php

namespace Castle\Http\Controllers;

use Castle\Http\Requests;
use Castle\Permission;
use Castle\User;
use Illuminate\Http\Request;

class UserController extends Controller
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
		$this->authorize('view', User::class);

		$order = $request->input('order') == 'desc' ?
			'desc' :
			'asc';

		$users = User::orderBy('name', $order)
			->paginate(50)
			->appends(['order' => $order]);

		return view('users.index', ['users' => $users]);
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function create(Request $request)
	{
		$this->authorize('manage', User::class);

		return view('users.create');
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request)
	{
		$this->authorize('manage', User::class);

		$this->validate($request, [
			'name' => ['required', 'max:255'],
			'email' => [
				'required',
				'email',
				'unique:users,email'
			],
			'phone' => ['max:255'],
			'password' => [
				'required',
				'min:6',
				'confirmed'
			],
		]);

		$user = new User(
			$request->only(['name', 'email', 'phone'])
		);

		$user->password = bcrypt($request->input('password'));

		$user->save();

		$user->permissions()->sync(
			$request->input(
				'permissions',
				Permission::byType(Permission::DEFAULT_TYPE)->get()->all()
			)
		);

		return redirect()->route('users.index')
			->with('alert-success', 'User added!');
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $user
	 * @return \Illuminate\Http\Response
	 */
	public function show(Request $request, $user)
	{
		$user = User::with('permissions')->find($user);

		if (!$user) {
			return response(view('users.404'), 404);
		}

		$this->authorize('view', $user);

		return view('users.show', ['user' => $user]);
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $user
	 * @return \Illuminate\Http\Response
	 */
	public function edit(Request $request, $user)
	{
		$user = User::with('permissions')->find($user);

		if (!$user) {
			return response(view('users.404'), 404);
		}

		$this->authorize('manage', $user);

		return view('users.edit', ['user' => $user]);
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $user
	 * @return Response
	 */
	public function update(Request $request, $user)
	{
		$user = User::with('permissions')->find($user);

		if (!$user) {
			return response(view('users.404'), 404);
		}

		$this->authorize('manage', $user);

		$this->validate($request, [
			'name' => ['required', 'max:255'],
			'email' => [
				'required',
				'email',
				'unique:users,email,'.$user->email.',email'
			],
			'phone' => ['max:255'],
		]);

		$user->name = $request->input('name');
		$user->email = $request->input('email');
		$user->phone = $request->input('phone');

		if ($request->user()->can('manage', User::class)) {
			$user->permissions()->sync(
				$request->input('permissions', [])
			);
		}

		$user->save();

		return redirect()->route('users.show', $user->url)
			->with('alert-success', 'User updated!');
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $user
	 * @return Response
	 */
	public function destroy($user)
	{
		$user = User::find($user);

		if (!$user) {
			return response(view('users.404'), 404);
		}

		$this->authorize('delete', $user);

		if (auth()->id() == $user->id) {
			return redirect()->back()
				->with('alert-warning', 'You can\'t delete yourself.');
		}

		$user->delete();

		return redirect()->route('users.index')
			->with('alert-success', 'User deleted!');
	}

	/**
	 * Displays the form for users to change their password.
	 *
	 * @param Request $request
	 * @param int $user
	 * @return Response
	 */
	public function password(Request $request, $user)
	{
		$user = User::find($user);

		if (!$user) {
			return response(view('users.404'), 404);
		}

		$this->authorize('manage', $user);

		return view('users.password', ['user' => $user]);
	}

	/**
	 * Stores a new password for a user.
	 *
	 * @param Request $request
	 * @param int $user
	 * @return Response
	 */
	public function editPassword(Request $request, $user)
	{
		$user = User::find($user);

		if (!$user) {
			return response(view('users.404'), 404);
		}

		$this->authorize('manage', $user);

		$this->validate($request, [
			'password' => [
				'required',
				'min:6',
				'confirmed'
			],
		]);

		$user->password = bcrypt($request->input('password'));

		$user->save();

		return redirect()->route('users.show', $user->url)
			->with('alert-success', 'Password changed!');
	}
}
