@extends('layout.master')

@section ('breadcrumbs')
<ol class="breadcrumb">
	<li><a href="{{ route('home.index') }}">Home</a></li>
	<li><a href="{{ route('clients.index') }}">Clients</a></li>
	<li class="active">Not found</li>
</ol>
@endsection

@section('content')
<div class="container">
	<div class="row">

		<div class="col-md-8 col-md-offset-2">
			<h2>There&rsquo;s no resource with that name.</h2>
			<a href="{{ route('clients.index') }}">Return to clients</a>
		</div>

	</div>
</div>
@endsection
