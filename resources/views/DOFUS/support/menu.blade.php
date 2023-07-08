<div class="ak-page-menu">
    <nav class="ak-nav-expand ">
        <div class="ak-nav-expand-container" style="height: 42px;">
            <ul class="ak-nav-links ak-nav-expand-links">
                <li class="{{ active_class(if_route('support')) == 'active' ? 'on' : ''}}">
                    <a href="{{ URL::route('support') }}">Ouverts</a>
                </li>
                <li class="{{ active_class(if_route('support.closed')) == 'active' ? 'on' : ''}}">
                    <a href="{{ URL::route('support.closed') }}">Fermés</a>
                </li>
            </ul>
        </div>
    </nav>
</div>
