<div class="ak-page-menu">
    <nav class="ak-nav-expand ">
        <div class="ak-nav-expand-container">
            <ul class="ak-nav-links ak-nav-expand-links">
                @foreach($seasons as $seasoneach)
                <li @if ($season->id == $seasoneach->id) class="on" @endif>
                    <a href="{{ URL::route('ladder.'.$current.'.season', [$server, $seasoneach->id]) }}">Saison {{$seasoneach->id}}</a>
                </li>
                @endforeach
            </ul>
        </div>
    </nav>
</div>
