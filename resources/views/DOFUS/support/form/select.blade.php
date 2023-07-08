<div class="form-group ">
    <label class="control-label">{{ $name }}</label>
    <span class="hint">{{ @$field->hint }}</span>
    <select name="select|{{ $name }}" class="form-control">
        <option value="reset|null"></option>
        @foreach ($data as $choice)
            @if (isset($choice->child))
                <option value="child|{{ $choice->value }}|{{ $choice->child }}">{{ $choice->value }}</option>
            @else
                <option value="final|{{ $choice->value }}">{{ $choice->value }}</option>
            @endif
        @endforeach
    </select>
</div>
