@extends('layout.master')

@section('title', 'Uncaught exception - Castle')

@section('breadcrumbs')
<ol class="breadcrumb">
    <li class="active">Uncaught exception</li>
</ol>
@endsection

@section('content')
<div class="container">
    <div class="row">

        <div class="col-md-8 col-md-offset-2">
            <h2>Something&rsquo;s gone horribly wrong.</h2>
            <p>It&rsquo;s probably not your fault, if that makes you feel any better.</p>
            <a href="{{ route('home.index') }}">Return to home page</a>

            <div class="panel panel-default" style="margin-top: 30px;">
                <div class="panel-heading" data-toggle="collapse" data-target="#stacktrace" style="cursor: pointer;">
                    Technical details
                </div>
                <div class="panel-body collapse{{ config('app.debug', false) ? ' in' : ''}}" id="stacktrace">
                    <h4 style="font-weight: bold; margin: 0;">
                        {{ $exception->getMessage() ?: 'No message provided' }}
                    </h4>
                    <p>
                        <samp>
                            {{ str_replace(base_path() . '/', '', $exception->getFile()) }}<span class="text-muted">:{{ $exception->getLine() }}</samp>
                        </span>
                    </p>
                    <ul class="list-unstyled" style="font-size: 11px">
                    @foreach ($exception->getTrace() as $index => $trace)
                        <li style="margin-bottom: 1px;">
                            <div class="media">
                                <div class="media-left media-top">
                                    <samp class="media-object text-right" style="font-weight: bold; min-width: 24px;">
                                        {{ $index }}
                                    </samp>
                                </div>
                                <div class="media-body media-top">
                                    @if (isset($trace['class']) and isset($trace['function']))
                                    <code>
                                        {!! isset($trace['class']) ? $trace['class'] . $trace['type'] . $trace['function'] . '()' : '' !!}
                                    </code>
                                    <br>
                                    @endif
                                    <samp>
                                        {{ isset($trace['file']) ? str_replace(base_path() . '/', '', $trace['file']) : '' }}<span class="text-muted">{{ isset($trace['line']) ? ':' . $trace['line'] : '' }}</span>
                                    </samp>
                                </div>
                            </div>
                        </li>
                    @endforeach
                    </ul>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
