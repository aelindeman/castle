<nav class="navbar navbar-default castle-nav">
	<div class="container">
		<div class="navbar-header">
			@if (!Auth::guest())
			<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#castle-nav">
				<span class="sr-only">Toggle navigation</span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
			@endif
			<a class="navbar-brand{!! Route::is('home.index') ? ' active' : '' !!}" href="{{ route('home.index') }}">
				<span class="glyphicon glyphicon-tower"></span>
				<span class="sr-only">Castle</span>
			</a>
		</div>
		<div class="collapse navbar-collapse" id="castle-nav">
			@if (!Auth::guest())
			<ul class="nav navbar-nav navbar-right navbar-search">
				<li>
					<form class="navbar-form navbar-search-form" method="get" action="{{ route('home.search') }}">
						<label for="term" class="sr-only">Search</label>
						<div class="input-group">
							<input type="search" placeholder="Search" class="form-control" name="term" id="castle-search" value="{{ request()->input('term') }}" autocomplete="off" />
							<span class="input-group-btn">
								<button class="btn btn-info" type="submit">
									<span class="glyphicon glyphicon-search"></span>
									<span class="sr-only">Search</span>
								</button>
							</span>
						</div>
					</form>
				</li>
			</ul>
			<ul class="nav navbar-nav navbar-left">
				@can ('view', Castle\Client::class)
				<li{!! Route::is('clients.*') ? ' class="active"' : '' !!}><a href="{{ route('clients.index') }}">Clients</a></li>
				@endcan
				@can ('view', Castle\Document::class)
				<li{!! Route::is('docs.*') ? ' class="active"' : '' !!}><a href="{{ route('docs.index') }}">Docs</a></li>
				@endcan
				@can ('view', Castle\Tag::class)
				<li{!! Route::is('tags.*') ? ' class="active"' : '' !!}><a href="{{ route('tags.index') }}">Tags</a></li>
				@endcan
				@can ('view', Castle\Discussion::class)
				<li{!! Route::is('whiteboard.*') ? ' class="active"' : '' !!}><a href="{{ route('whiteboard.index') }}">Whiteboard</a></li>
				@endcan
				@can ('view', Castle\User::class)
				<li class="hidden-md hidden-sm{!! Route::is('users.*') ? ' active' : '' !!}"><a href="{{ route('users.index') }}">Users</a></li>
				@endcan
			</ul>
			@endif
		</div>
	</div>
</nav>

<nav class="castle-subnav">
	<div class="container">
		<div class="row">
			<div class="col-sm-4 col-sm-push-8">
				@if (Auth::check())
				<ul class="profile-links">
					<li><a href="{{ route('users.show', Auth::user()) }}">{{ Auth::user()->name }}</a></li>
					<li><a href="{{ route('auth.logout') }}">Log out</a></li>
				</ul>
				@endif
			</div>
			<div class="col-sm-8 col-sm-pull-4">
				@yield('breadcrumbs')
			</div>
		</div>
	</div>
</nav>
