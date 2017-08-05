<ul class="list-group">
	@foreach ($permissions->groupBy('group') as $name => $group)
	<li class="list-group-item">
		<div class="row">
			<div class="col-xs-4">
				<strong class="list-group-item-heading">
					{{ $name }}
				</strong>
			</div>
			<div class="col-xs-8 text-right">
				@if ((isset($edit) and $edit) and Gate::check('manage', Castle\User::class))
				<div class="btn-group btn-group-xs" data-toggle="buttons">
					@foreach ($group as $pm)
					<label class="btn btn-default btn-tooltip {!! $user->permissions->contains($pm) ? ' active' : '' !!}" title="{{ $pm->permission }}">
						<input type="checkbox" name="permissions[]" value="{{ $pm->id }}"{!! $user->permissions->contains($pm) ? ' checked="checked"' : '' !!} autocomplete="off">
						<span class="glyphicon glyphicon-{{ $pm->typeIcon }}"></span>
						<span class="sr-only">{{ $pm->permission }}</span>
					</label>
					@endforeach
				</div>
				@else
                <div class="btn-group btn-group-xs" data-toggle="buttons">
					@foreach ($group as $pm)
                    <label class="btn btn-default btn-tooltip" title="{{ $pm->permission }}">
                        <input type="checkbox" name="permissions[]" value="{{ $pm->id }}" autocomplete="off">
                        <span class="glyphicon glyphicon-{{ $pm->typeIcon }}"></span>
                        <span class="sr-only">{{ $pm->permission }}</span>
                    </label>
					@endforeach
                </div>
				@endif
			</div>
		</div>
	</li>
	@endforeach
</ul>
