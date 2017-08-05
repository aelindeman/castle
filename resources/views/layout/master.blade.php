<!doctype html>
<html lang="{{ config('app.locale') }}">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		@stack('meta')

		<title>@yield('title', 'Castle')</title>

		<link href="{{ elixir('images/castle.png') }}" rel="icon" type="image/png">

		<link href="https://fonts.googleapis.com/css?family=Lato:400,400italic,700,700italic|Oxygen+Mono:400,700" rel="stylesheet" type="text/css">
		<link href="{{ elixir('css/app.css') }}" rel="stylesheet">
		@stack('styles')
	</head>
	<body>

		<div class="castle-layout">
			@include('layout.common.navigation')
			@include('layout.common.messages')

			@yield('content')

			@include('layout.common.footer')
		</div>

		<script src="{{ elixir('js/app.js') }}" type="text/javascript"></script>
		<script src="{{ route('home.castlejs', ['via' => urlencode(Route::currentRouteName())]) }}" type="text/javascript"></script>
		@stack('scripts')
	</body>
</html>
