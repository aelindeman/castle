@extends('layout.master')

@section('title', 'Reset password - Castle')

@section('content')
<div class="container">
    <div class="row">

        <div class="col-md-8 col-md-offset-2">
            <h2>Reset password</h2>
        </div>

        @if (session('status'))
        <div class="col-md-8 col-md-offset-2">
            <div class="alert alert-success">
                {{ session('status') }}
            </div>
        </div>
        @endif

        <div class="col-md-8 col-md-offset-2">
            <form class="form-horizontal" method="post" action="{{ route('auth.reset.create') }}">
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

                <div class="form-group">
                    <div class="col-md-6 col-md-offset-4">
                        <button type="submit" class="btn btn-primary">Send</button>
                    </div>
                </div>
            </form>
        </div>

    </div>
</div>
@endsection
