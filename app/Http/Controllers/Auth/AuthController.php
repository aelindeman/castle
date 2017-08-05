<?php

namespace Castle\Http\Controllers\Auth;

use Auth;
use Castle\Http\Controllers\Controller;
use Castle\Permission;
use Castle\User;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Http\Request;
use InvalidArgumentException;
use Socialite;
use Validator;

class AuthController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Registration & Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users, as well as the
    | authentication of existing users. By default, this controller uses
    | a simple trait to add these behaviors. Why don't you explore it?
    |
    */

    use AuthenticatesAndRegistersUsers, ThrottlesLogins;

    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new authentication controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware($this->guestMiddleware(), ['except' => 'logout']);
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|min:6|confirmed',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function create(array $data)
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);

        $user->permissions()->saveMany(
            Permission::byType(Permission::DEFAULT_PERMISSION_TYPE)->get()->all()
        );

        return $user;
    }

    /**
     * Redirect the user to the provider authentication page.
     *
     * @return Response
     */
    public function redirectToProvider($provider = null)
    {
        switch ($provider) {
            case 'google':
                return Socialite::driver('google')->redirect();
        }

        return response('Authenticating via '.$provider.' isn\'t currently supported', 422);
    }

    /**
     * Obtain the user information from provider.
     *
     * @return Response
     */
    public function handleProviderCallback(Request $request, $provider)
    {
        if ($error = $request->input('error')) {
            return $this->abortLogin('Login canceled.');
        }

        $authenticated = Socialite::driver($provider)->user();
        $user = User::where('email', $authenticated->getEmail())->first();

        if (!$user) {
            return $this->abortLogin('Couldn\'t find a user with a matching email address.');
        }

        if (Auth::login($user, true) and Auth::check()) {
            return redirect()->intended();
        }

        return $this->abortLogin();
    }

    /**
     * Kick the user back to the login page.
     *
     * @return Redirect
     */
    private function abortLogin($message = null, $messageLevel = null) {
        $message = empty($message) ? 'Couldn\'t log you in.' : $message;
        $messageLevel = empty($messageLevel) ? 'alert-warning' : $messageLevel;

        return redirect()->route('auth.login')
            ->with($messageLevel, $message);
    }
}
