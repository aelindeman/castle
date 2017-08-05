@extends('layout.master')

@section('title', 'Attachments - Castle')

@section('breadcrumbs')
<ol class="breadcrumb">
	<li><a href="{{ route('home.index') }}">Home</a></li>
	<li class="active">Attachments</li>
</ol>
@endsection

@section('content')
<div class="container item-index attachments-index">
	<div class="row">
		<div class="col-md-8 col-md-offset-2">

			<header class="page-header">
				<h1>
					Attachments
					<small>
						&times;{{ $attachments->total() }}
					</small>
				</h1>
			</header>

			<section>
				@if ($attachments->count())
				<div class="table-responsive">
					<table class="table table-condensed table-hover attachments-table">
						<thead>
							<tr>
								<th>Type</th>
								<th>Name</th>
								<th>Date modified</th>
								<th>Size</th>
							</tr>
						</thead>
						<tbody>
							@foreach ($attachments as $att)
								<tr class="attachment-row text-muted">
									<td class="attachment-type" title="{{ $att->type }}">
										<span class="glyphicon glyphicon-{{ $att->icon }}"></span>
										<span class="sr-only">{{ $att->type }}</span>
									</td>
									<td class="attachment-name">
										<a href="{{ route('attachments.show', $att->path) }}" target="_blank">
											{{ $att->path }}
										</a>
									</td>
									<td class="attachment-date">
										{{ $att->date->toDateTimeString() }}
									</td>
									<td class="attachment-size">
										{{ human_filesize($att->size) }}B
									</td>
								</tr>
							@endforeach
						</tbody>
					</table>
				</div>
				@else
				<div class="alert alert-info">
					There are no attachments.
				</div>
				@endif
			</section>

			<nav class="text-center">
				{!! $attachments->links() !!}
			</nav>

		</div>
	</div>
</div>
@endsection
{{--
<td class="attachment-actions">
	@can('delete', $att)
	<form action="{{ route('attachments.destroy', $att) }}" method="post">
		{!! csrf_field() !!}
		{!! method_field('delete') !!}
		<button type="button" class="btn btn-sm btn-danger" data-confirm="delete">
			<span class="sr-nly">Delete</span>
		</button>
		<button type="submit" class="btn btn-sm btn-danger">
			<strong>Are you sure?</strong>
		</button>
	</form>
	@endcan
</td>
--}}
