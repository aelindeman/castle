<div class="row metadata-row{{ (isset($index) and ($errors->has('metadata.keys.'.$index) or $errors->has('metadata.values.'.$index))) ? ' has-error has-feedback' : '' }}">
    <div class="col-sm-4 metadata-key">
        <div class="input-group">
            <span class="input-group-btn">
                <button type="button" class="btn btn-default remove-row" tabindex="-1">
                    <span class="glyphicon glyphicon-minus"></span>
                    <span class="sr-only">Delete row</span>
                </button>
            </span>
            <input type="text" class="form-control" name="metadata[keys][]" value="{{ $key }}">
        </div>
        @if (isset($index) and $errors->has('metadata.keys.'.$index))
        <p class="help-block">
            <strong>{{ $errors->first('metadata.keys.'.$index) }}</strong>
        </p>
        @endif
    </div>
    <div class="col-sm-8 metadata-value">
        <input type="text" class="form-control mono-text" name="metadata[values][]" value="{{ $value }}">
        @if (isset($index) and $errors->has('metadata.values.'.$index))
        <p class="help-block">
            <strong>{{ $errors->first('metadata.values.'.$index) }}</strong>
        </p>
        @endif
    </div>
</div>
