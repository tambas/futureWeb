<div class="ak-page-menu">
    <nav class="ak-nav-expand ">
        <div class="ak-nav-expand-container" style="height: 42px;">
            <ul class="ak-nav-links ak-nav-expand-links">
                <li @if ($page_name == 'Général') class="on" @endif>
                    <a href="{{ URL::route('ladder.general', [$server]) }}">Ladder Général</a>
                </li>
                <li @if ($page_name == 'PvP') class="on" @endif>
                    <a href="{{ URL::route('ladder.pvp', [$server]) }}">Ladder PvP</a>
                </li>
                <li @if ($page_name == 'Guilde') class="on" @endif>
                    <a href="{{ URL::route('ladder.guild', [$server]) }}">Ladder Guilde</a>
                </li>
                <li @if ($page_name == 'Kolizéum') class="on" @endif>
                    <a href="{{ URL::route('ladder.kolizeum', [$server]) }}">Ladder Kolizéum</a>
                </li>
                @if(config('dofus.details')[$server]->version != "2.10")
                <li @if ($page_name == 'Kolizéum 1vs1') class="on" @endif>
                    <a href="{{ URL::route('ladder.kolizeum1v1', [$server]) }}">Ladder Kolizéum 1vs1</a>
                </li>
                @endif
            </ul>
        </div>
    </nav>
</div>
