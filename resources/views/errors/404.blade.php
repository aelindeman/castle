@extends('layout.master')

@section('title', 'Not found - Castle')

@section('breadcrumbs')
<ol class="breadcrumb">
    <li class="active">Not found</li>
</ol>
@endsection

@section('content')
<div class="container">
    <div class="row">

        <div class="col-md-8 col-md-offset-2">
            <h2>Nothing to see here.</h2>
            <a href="{{ route('home.index') }}">Return to home page</a>
        </div>

    </div>
</div>
@endsection
