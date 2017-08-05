@extends('layout.master')

@section('title', 'Home - Castle')

@section('breadcrumbs')
<ol class="breadcrumb">
    <li class="active">Home</li>
</ol>
@endsection

@section('content')
<div class="container">

    {{-- <div class="jumbotron">
        <div class="page-header text-center">
            <h1>Welcome to the Castle</h1>
        </div>
    </div> --}}

    <div class="row">
        <div class="col-md-4">
            <article>
            @can ('view', Castle\Client::class)
                <h3>Recent clients</h3>
                @include ('clients.partials.list', [
                    'clients' => $clients,
                    'ifEmpty' => 'You haven&rsquo;t viewed any clients recently.'
                ])
                @if ($clients->isEmpty())
                    <div class="text-center">
                        <a href="{{ route('clients.index') }}">All clients</a>
                    </div>
                @endif
            @endcan
            </article>
            <article>
            @can ('view', Castle\Document::class)
                <h3>Recent documents</h3>
                @include ('docs.partials.list', [
                    'docs' => $docs,
                    'ifEmpty' => 'You haven&rsquo;t viewed any documents recently.'
                ])
                @if ($docs->isEmpty())
                    <div class="text-center">
                        <a href="{{ route('docs.index') }}">All documents</a>
                    </div>
                @endif
            @endcan
            </article>
        </div>
        <div class="col-md-4">
            <article>
            @can ('view', Castle\Discussion::class)
                <h3>Popular discussions</h3>
                @include ('discussions.partials.list', [
                    'discussions' => $discussions,
                ])
                @if ($discussions->isEmpty())
                    <div class="text-center">
                        <a href="{{ route('whiteboard.create') }}">Start a discussion</a>
                    </div>
                @endif
            @endcan
            </article>
        </div>
        <div class="col-md-4">
            <article>
            @can ('view', Castle\Tag::class)
                <h3>Top tags</h3>
                @include ('tags.partials.list', [
                    'tags' => $tags,
                ])
                @if ($tags->isEmpty())
                    <div class="text-center">
                        <a href="{{ route('tags.create') }}">Create a tag</a>
                    </div>
                @endif
            @endcan
            </article>
        </div>
    </div>

</div>
@endsection
