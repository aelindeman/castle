@foreach(['danger', 'warning', 'success', 'info'] as $message)
	@if (Session::has('alert-'.$message))
	<div class="container">
		<div class="row">
			<div class="col-md-8 col-md-offset-2">
				<div class="alert alert-dismissible alert-{{ $message }}">
					<button class="close" data-dismiss="alert">&times;</button>
					{{ Session::get('alert-'.$message) }}
				</div>
			</div>
		</div>
	</div>
	@endif
@endforeach
