<a class="list-group-item user-item" href="{{ route('users.show', $item->url) }}">
	<div class="row">
		<div class="col-sm-4 user-name">
			<strong class="list-group-item-heading">{{ $item->name }}</strong>
		</div>
		<div class="col-sm-5 user-email">
			<span class="list-group-item-text">{{ $item->email }}</span>
		</div>
		<div class="col-sm-3 user-phone">
			<span class="list-group-item-text">{{ $item->phone }}</span>
		</div>
	</div>
</a>
