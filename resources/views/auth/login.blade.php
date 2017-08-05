@extends('layout.master')

@section('title', 'Login - Castle')

@section('content')
<div class="container">
    <div class="row">

        <div class="col-md-8 col-md-offset-2">
            <h2>Login</h2>
        </div>

        <div class="col-md-8 col-md-offset-2 text-center">
            <ul class="list-inline">
                <li>
                    <a href="{{ route('auth.oauth', 'google') }}">
                        <img src="{{ elixir('images/google-oauth.png') }}" alt="Sign in with Google">
                    </a>
                </li>
                {{-- <li>
                    <a href="{{ route('auth.oauth', 'bitbucket') }}">
                        <img src="{{ elixir('images/bitbucket-oauth.png') }}" alt="Sign in with BitBucket">
                    </a>
                </li> --}}
            </ul>
        </div>

        <div class="col-md-8 col-md-offset-2 login-or-prompt">
            <span class="separator">Or</span>
        </div>

        <div class="col-md-8 col-md-offset-2">
            <form class="form-horizontal" method="post" action="{{ route('auth.login.do') }}">
                {!! csrf_field() !!}

                <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                    <label class="col-md-4 control-label">Email address</label>

                    <div class="col-md-6">
                        <input type="email" class="form-control" name="email" value="{{ old('email') }}">

                        @if ($errors->has('email'))
                            <span class="help-block">
                                <strong>{{ $errors->first('email') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>

                <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                    <label class="col-md-4 control-label">Password</label>

                    <div class="col-md-6">
                        <input type="password" class="form-control" name="password">

                        @if ($errors->has('password'))
                            <span class="help-block">
                                <strong>{{ $errors->first('password') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-md-6 col-md-offset-4">
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="remember"> Stay logged in
                            </label>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-md-6 col-md-offset-4">
                        <button type="submit" class="btn btn-primary">Log in</button>
                        <a class="btn btn-default" href="{{ route('auth.reset') }}">Reset password</a>
                    </div>
                </div>
            </form>
        </div>

    </div>
</div>
@endsection
