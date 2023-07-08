<div class="ak-page-menu ak-glue">
        <nav class="ak-nav-expand ak-expanded">
            <div class="ak-nav-expand-container">
                <ul class="ak-nav-links ak-nav-expand-links">
                    <li class="{{ active_class(if_route('profile'), 'on')}}">
                    <a href="{{route('profile')}}">
                    Accueil      </a>
                    </li>
                    <li class="{{ active_class(if_route('history.purchases'), 'on')}}">
                    <a href="{{route('history.purchases')}}">
                    Mes Achats      </a>
                    </li>
                    <li class="{{ active_class(if_route('history.votes'), 'on')}}">
                    <a href="{{route('history.votes')}}">
                    Mes Votes      </a>
                    </li>
                    <li class="{{ active_class(if_route('history.market'), 'on')}}">
                    <a href="{{route('history.market')}}">
                    Marché      </a>
                    </li>
                </ul>
            </div>
            <div class="ak-nav-expand-more">
                <span class="ak-nav-expand-icon ak-picto-common ak-picto-open" style="display: none;">+</span>
            </div>
        </nav>
</div>