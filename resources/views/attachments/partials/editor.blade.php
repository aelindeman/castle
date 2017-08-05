{{-- don't forget 'enctype="multipart/form-data"' on corresponding form --}}

@if (!(isset($noForm) and $noForm))
<select multiple="multiple" class="form-control hidden" name="attachments[]" id="attachments" hidden="hidden" data-editor="attachments">
@if (isset($attachments))
	@foreach ($attachments as $attachment)
	<option value="{{ $attachment }}" selected="selected">
		{{ $attachment }}
	</option>
	@endforeach
@endif
</select>
@endif

<ul class="list-group attachments-list-group" data-editor-target="#attachments">
@if (isset($attachments) and !$attachments->isEmpty())
	@foreach ($attachments as $attachment)
		@include('attachments.partials.single', ['item' => $attachment, 'edit' => true])
	@endforeach
@endif
@if (!(isset($noUploader) and $noUploader))
	<li class="list-group-item editor-upload">
		<div class="media">
			<div class="media-body media-middle">
				<input type="file" multiple="multiple" name="uploads[]" id="uploads" data-editor-field="files">
			</div>
			<div class="media-right media-middle" style="visibility: hidden;">
				<button type="button" class="btn btn-sm btn-success disabled" disabled="disabled" data-editor-field-submit="files">
					<span class="glyphicon glyphicon-plus"></span>
					<span class="sr-only">Upload</span>
				</button>
			</div>
		</div>
	</li>
@endif
</ul>
