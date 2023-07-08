<div class="form-group ">
    <label class="control-label">{{ $name }}</label>
    <span class="hint">{{ @$field->hint }}</span>
    @if (isset($choice->child))
        <input type="radio" class="form-control" name="select|{{ $name }}" value="child|{{ $choice->value }}|{{ $choice->child }}"> {{ $choice->value }}<br>
    @else
        <input type="radio" class="form-control" name="select|{{ $name }}" value="final|{{ $choice->value }}"> {{ $choice->value }}<br>
    @endif
</div>
