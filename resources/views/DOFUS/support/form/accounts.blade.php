<div class="form-group ">
    <label class="control-label">{{ $name }}</label>
    <span class="hint">{{ @$field->hint }}</span>
    <select class="special form-control" name="account|{{ $name }}">
        <option value="reset|null"></option>
        @foreach ($accounts as $account)
            @if ($child)
                <option value="child|{{ $account->Id }}|{{ $child }}">{{ $account->Nickname }}</option>
            @else
                <option value="final|{{ $account->Id }}">{{ $account->Nickname }}</option>
            @endif
        @endforeach
    </select>
</div>
