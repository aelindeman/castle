@extends('layout.master')

@section('title', 'Forbidden - Castle')

@section('breadcrumbs')
<ol class="breadcrumb">
    <li class="active">Forbidden</li>
</ol>
@endsection

@section('content')
<div class="container">
    <div class="row">

        <div class="col-md-8 col-md-offset-2">
            <h2>You don&rsquo;t have permission.</h2>
            <a href="{{ route('home.index') }}">Return to home page</a>
        </div>

    </div>
</div>
@endsection
