<div class="form-group ">
    <label class="control-label">{{ $name }}</label>
    <span class="hint">{{ @$field->hint }}</span>
    @foreach ($characters as $character)
    <label for="{{ $character->Id }}">
        <div class="character">
            @if ($child)
                <input class="special" type="radio" name="character|{{ $name }}" id="{{ $character->Id }}" value="child|{{ $character->Id }}|{{ $child }}">
            @else
                <input class="special" type="radio" name="character|{{ $name }}" id="{{ $character->Id }}" value="final|{{ $character->Id }}">
            @endif
            <img src="{{ DofusForge::player($character, $server, 'face', 1, 50, 50) }}">
            {{ $character->Name }} - {{ $character->classe() }} niveau {{ $character->level() }}
        </div>
    </label>
    @endforeach
</div>
