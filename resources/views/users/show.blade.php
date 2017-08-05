@extends('layout.master')

@section('title', e($user->name) . ' - Users - Castle')

@section('breadcrumbs')
<ol class="breadcrumb">
    <li><a href="{{ route('home.index') }}">Home</a></li>
    <li><a href="{{ route('users.index') }}">Users</a></li>
    <li class="active">{{ $user->name }}</li>
</ol>
@endsection

@section('content')
<div class="container item-viewer user-viewer">
	<div class="row">
		<div class="col-md-8 col-md-offset-2">

			<header class="well">
				<h1>{{ $user->name }}</h1>
				<ul class="list-unstyled">
					<li>
						<strong>Email address:</strong>
						<a class="user-email" href="mailto:{{ $user->email }}">{{ $user->email }}</a>
					</li>
					@if ($user->phone)
					<li>
						<strong>Phone number:</strong>
						<a href="tel:{{ $user->phone }}">{{ $user->phone }}</a>
					</li>
					@endif
				</ul>
				@include('layout.common.action-bar', [
					'editPermission' => ['manage', $user],
					'editRoute' => route('users.edit', $user->url),
					'deletePermission' => ['delete', $user],
					'deleteRoute' => route('users.destroy', $user->url),
				])
			</header>

			<div class="action-bar">
				<ul class="nav nav-pills nav-justified">
					@if (!$user->documents->isEmpty())
					<li>
						<a href="#documents" data-toggle="tab">
							Documents
							<small class="text-muted">
								&times;{{ $user->documents->count() }}
							</small>
						</a>
					</li>
					@else
					<li class="disabled">
						<a href="javascript:;">
							No documents
						</a>
					</li>
					@endif
					@if (!$user->discussions->isEmpty())
					<li>
						<a href="#discussions" data-toggle="tab">
							Discussions
							<small class="text-muted">
								&times;{{ $user->discussions->count() }}
							</small>
						</a>
					</li>
					@else
					<li class="disabled">
						<a href="javascript:;">
							No discussions
						</a>
					</li>
					@endif
					@if (!$user->comments->isEmpty())
					<li>
						<a href="#comments" data-toggle="tab">
							Comments
							<small class="text-muted">
								&times;{{ $user->comments->count() }}
							</small>
						</a>
					</li>
					@else
					<li class="disabled">
						<a href="javascript:;">
							No comments
						</a>
					</li>
					@endif
				</ul>
			</div>

			<div class="tab-content">
				<section class="tab-pane" id="documents">
				@include('docs.partials.list', [
					'docs' => $user->documents->sortByDesc('created_at')->take(10)
				])
				</section>
				<section class="tab-pane" id="discussions">
				@include('discussions.partials.list', [
					'discussions' => $user->discussions->sortByDesc('created_at')->take(10)
				])
				</section>
				<section class="tab-pane" id="comments">
				@include('comments.partials.list', [
					'comments' => $user->comments->sortByDesc('created_at')->take(10)
				])
				</section>
			</div>

		</div>
	</div>
</div>
@endsection
