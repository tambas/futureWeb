<div class="ak-page-menu" style="margin-bottom:0;">
    <nav class="ak-nav-expand ">
        <div class="ak-nav-expand-container" style="height: 42px;">
            <ul class="ak-nav-links ak-nav-expand-links">
                @foreach (config('dofus.servers') as $srv)
                <li @if ($server == $srv) class="on" @endif>
                    <a href="{{ URL::route('ladder.'.$current, [$srv]) }}">{{ ucfirst($srv) }}</a>
                </li>
                @endforeach
            </ul>
        </div>
    </nav>
</div>
