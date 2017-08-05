@extends('layout.master')

@section('breadcrumbs')
<ol class="breadcrumb">
    <li class="active">Not found</li>
</ol>
@endsection

@section('content')
<div class="container">
    <div class="row">

        <div class="col-md-8 col-md-offset-2">
            <h2>There&rsquo;s no attachment with that name.</h2>
            <a href="{{ route('home.index') }}">Return to home page</a>
        </div>

    </div>
</div>
@endsection
