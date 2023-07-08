<div class="ak-page-menu">
      <nav class="ak-nav-expand ak-expanded">
         <div class="ak-nav-expand-container">
            <ul class="ak-nav-links ak-nav-expand-links">
               <li class="{{ active_class(if_route('characters.view'), 'on')}}">
                   <a href="{{route('characters.view', [$server, $character->Id, $character->Name])}}">Profil</a>
                </li>
                @if($settings->show_equipments || $settings->show_spells || $settings->show_caracteristics)
               <li class="{{ active_class(if_route('characters.caracteristics'), 'on')}}">
                <a href="{{route('characters.caracteristics', [$server, $character->Id, $character->Name])}}">Caractéristiques</a>
                </li>
                @endif
                @if($settings->show_inventory || $settings->show_idols)
               <li class="{{ active_class(if_route('characters.inventory'), 'on')}}">
                   <a href="{{route('characters.inventory', [$server, $character->Id, $character->Name])}}">Inventaire</a>
                </li>
                @endif
            </ul>
         </div>
      </nav>
</div>