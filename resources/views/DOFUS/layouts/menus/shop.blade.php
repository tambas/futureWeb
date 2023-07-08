@section('menu')
<aside class="col-md-3 col-md-pull-9">
    <div class="ak-container ak-main-aside">
        <div class="ak-container">
            <div id="ak-filter-characterslist" class="ak-filters">
                <div class="ak-title-filters">
                    <span class="ak-icon-med ak-filter"></span>
                    Filtrer la liste<span class="ak-picto-common ak-picto-more-less-white"></span>
                </div>
                <form action="{{URL::current()}}#jt_list" method="get" class="ak-ajaxloader" data-target="div.ak-main-page">
                    <div class="ak-filter-selection ak-filter-selected">
                        Filtres sélectionnés :
                        <a class="ak-picto-reset-filters ak-picto-common ak-tooltip" title="Tout effacer"></a>
                    </div>
                        <ul class="ak-list-filters-active ak-server-side">
                            @foreach($filters as $k => $v)
                                @if(!empty($v['values']) && $k != 'TEXT')
                                <li>
                                    <a class="ak-picto-erase ak-picto-common" data-name="item_{{$k}}"></a>
                                    {{$v['name']}} : 
                                    @foreach($v['text'] as $key => $value) 
                                        @php end($v['text']);@endphp
                                        {{ucfirst($value)}}@if(key($v['text']) != $key){{$v['separator']}}@endif
                                    @endforeach
                                </li>
                                @endif
                                @if(($k == 'TEXT') && ($v['values']))
                                <li>
                                    <a class="ak-picto-erase ak-picto-common" data-name="item_{{$k}}"></a>
                                    {{$v['name']}} : {{$v['values']}}
                                </li>
                                @endif
                            @endforeach
                        </ul>
                    <div data-name="item_TEXT" class="ak-list-filters">
                    <div class="ak-filter-selection">
                        <span class="ak-picto-puce ak-picto-common"></span>
                        Nom        <a class="ak-picto-close ak-toggle ak-picto-common"></a>
                    </div>
                    <div class="ak-list-filters-inner" style="display:block">
                        <ul class="ak-list-filters-check">
                            <li>
                                <input id="TEXT_0" type="text" name="TEXT" @if(array_key_exists('TEXT',$filters)) value="{{$filters['TEXT']['values']}}" @endif placeholder="Rechercher">
                            </li>
                        </ul>
                    </div>
                    </div>
                    <div data-name="item_character_breed_id" class="ak-list-filters ak-dofus-breed-filter">
                    <div class="ak-filter-selection">
                        <span class="ak-picto-puce ak-picto-common"></span>
                        Classe        <a class="ak-picto-close ak-toggle ak-picto-common"></a>
                    </div>
                    <div class="ak-list-filters-inner" style="display:block">
                        <ul class="ak-list-filters-check ak-full-height">
                            <li class="ak-has-icon @if(in_array(6, $filters['character_breed_id']['values'])) ak-selected @endif">
                                <input class="hide" id="character_breed_id_6" type="checkbox" name="character_breed_id[]" value="6" @if(in_array(6, $filters['character_breed_id']['values'])) checked="checked" @endif>
                                <label class="ak-label" for="character_breed_id_6">
                                <span class="ak-breed-icon ak-breed-icon-big breed6_0 ak-tooltip"></span><script type="application/json">{"text":"Ecaflip","manual":true,"tooltip":{"content":{"title":"","text":"La pi\u00e8ce d'Ecaflip"},"style":{"classes":"ak-tooltip-content"}}}</script>
                                </label>
                            </li>
                            <li class="ak-has-icon @if(in_array(7, $filters['character_breed_id']['values'])) ak-selected @endif">
                                <input class="hide" id="character_breed_id_7" type="checkbox" name="character_breed_id[]" value="7" @if(in_array(7, $filters['character_breed_id']['values'])) checked="checked" @endif>
                                <label class="ak-label" for="character_breed_id_7">
                                <span class="ak-breed-icon ak-breed-icon-big breed7_0 ak-tooltip"></span><script type="application/json">{"manual":true,"tooltip":{"content":{"title":"","text":"Les mains d'Eniripsa"},"style":{"classes":"ak-tooltip-content"}}}</script>                                        
                                </label>
                            </li>
                            <li class="ak-has-icon @if(in_array(8, $filters['character_breed_id']['values'])) ak-selected @endif">
                                <input class="hide" id="character_breed_id_8" type="checkbox" name="character_breed_id[]" value="8" @if(in_array(8, $filters['character_breed_id']['values'])) checked="checked" @endif>
                                <label class="ak-label" for="character_breed_id_8">
                                <span class="ak-breed-icon ak-breed-icon-big breed8_0 ak-tooltip"></span><script type="application/json">{"manual":true,"tooltip":{"content":{"title":"","text":"Le coeur d'Iop"},"style":{"classes":"ak-tooltip-content"}}}</script>                                        
                                </label>
                            </li>
                            <li class="ak-has-icon @if(in_array(9, $filters['character_breed_id']['values'])) ak-selected @endif">
                                <input class="hide" id="character_breed_id_9" type="checkbox" name="character_breed_id[]" value="9" @if(in_array(9, $filters['character_breed_id']['values'])) checked="checked" @endif>
                                <label class="ak-label" for="character_breed_id_9">
                                <span class="ak-breed-icon ak-breed-icon-big breed9_0 ak-tooltip"></span><script type="application/json">{"manual":true,"tooltip":{"content":{"title":"","text":"L'\u00e9tendue de Cr\u00e2"},"style":{"classes":"ak-tooltip-content"}}}</script>                                        
                                </label>
                            </li>
                            <li class="ak-has-icon @if(in_array(1, $filters['character_breed_id']['values'])) ak-selected @endif">
                                <input class="hide" id="character_breed_id_1" type="checkbox" name="character_breed_id[]" value="1" @if(in_array(1, $filters['character_breed_id']['values'])) checked="checked" @endif>
                                <label class="ak-label" for="character_breed_id_1">
                                <span class="ak-breed-icon ak-breed-icon-big breed1_0 ak-tooltip"></span><script type="application/json">{"manual":true,"tooltip":{"content":{"title":"","text":"Le bouclier F\u00e9ca"},"style":{"classes":"ak-tooltip-content"}}}</script>                                        
                                </label>
                            </li>
                            <li class="ak-has-icon @if(in_array(11, $filters['character_breed_id']['values'])) ak-selected @endif">
                                <input class="hide" id="character_breed_id_11" type="checkbox" name="character_breed_id[]" value="11" @if(in_array(11, $filters['character_breed_id']['values'])) checked="checked" @endif>
                                <label class="ak-label" for="character_breed_id_11">
                                <span class="ak-breed-icon ak-breed-icon-big breed11_0 ak-tooltip"></span><script type="application/json">{"manual":true,"tooltip":{"content":{"title":"","text":"Le sang de Sacrieur"},"style":{"classes":"ak-tooltip-content"}}}</script>                                        
                                </label>
                            </li>
                            <li class="ak-has-icon @if(in_array(10, $filters['character_breed_id']['values'])) ak-selected @endif">
                                <input class="hide" id="character_breed_id_10" type="checkbox" name="character_breed_id[]" value="10" @if(in_array(10, $filters['character_breed_id']['values'])) checked="checked" @endif>
                                <label class="ak-label" for="character_breed_id_10">
                                <span class="ak-breed-icon ak-breed-icon-big breed10_0 ak-tooltip"></span><script type="application/json">{"manual":true,"tooltip":{"content":{"title":"","text":"Le soulier de Sadida"},"style":{"classes":"ak-tooltip-content"}}}</script>                                        
                                </label>
                            </li>
                            <li class="ak-has-icon @if(in_array(2, $filters['character_breed_id']['values'])) ak-selected @endif">
                                <input class="hide" id="character_breed_id_2" type="checkbox" name="character_breed_id[]" value="2" @if(in_array(2, $filters['character_breed_id']['values'])) checked="checked" @endif>
                                <label class="ak-label" for="character_breed_id_2">
                                <span class="ak-breed-icon ak-breed-icon-big breed2_0 ak-tooltip"></span><script type="application/json">{"manual":true,"tooltip":{"content":{"title":"","text":"Le fouet d'Osamodas"},"style":{"classes":"ak-tooltip-content"}}}</script>                                        
                                </label>
                            </li>
                            <li class="ak-has-icon @if(in_array(3, $filters['character_breed_id']['values'])) ak-selected @endif">
                                <input class="hide" id="character_breed_id_3" type="checkbox" name="character_breed_id[]" value="3" @if(in_array(3, $filters['character_breed_id']['values'])) checked="checked" @endif>
                                <label class="ak-label" for="character_breed_id_3">
                                <span class="ak-breed-icon ak-breed-icon-big breed3_0 ak-tooltip"></span><script type="application/json">{"manual":true,"tooltip":{"content":{"title":"","text":"Les doigts d'Enutrof"},"style":{"classes":"ak-tooltip-content"}}}</script>                                        
                                </label>
                            </li>
                            <li class="ak-has-icon @if(in_array(4, $filters['character_breed_id']['values'])) ak-selected @endif">
                                <input class="hide" id="character_breed_id_4" type="checkbox" name="character_breed_id[]" value="4" @if(in_array(4, $filters['character_breed_id']['values'])) checked="checked" @endif>
                                <label class="ak-label" for="character_breed_id_4">
                                <span class="ak-breed-icon ak-breed-icon-big breed4_0 ak-tooltip"></span><script type="application/json">{"manual":true,"tooltip":{"content":{"title":"","text":"L'ombre de Sram"},"style":{"classes":"ak-tooltip-content"}}}</script>                                        
                                </label>
                            </li>
                            <li class="ak-has-icon @if(in_array(5, $filters['character_breed_id']['values'])) ak-selected @endif">
                                <input class="hide" id="character_breed_id_5" type="checkbox" name="character_breed_id[]" value="5" @if(in_array(5, $filters['character_breed_id']['values'])) checked="checked" @endif>
                                <label class="ak-label" for="character_breed_id_5">
                                <span class="ak-breed-icon ak-breed-icon-big breed5_0 ak-tooltip"></span><script type="application/json">{"manual":true,"tooltip":{"content":{"title":"","text":"Le sablier de X\u00e9lor"},"style":{"classes":"ak-tooltip-content"}}}</script>                                        
                                </label>
                            </li>
                            <li class="ak-has-icon @if(in_array(12, $filters['character_breed_id']['values'])) ak-selected @endif">
                                <input class="hide" id="character_breed_id_12" type="checkbox" name="character_breed_id[]" value="12" @if(in_array(12, $filters['character_breed_id']['values'])) checked="checked" @endif>
                                <label class="ak-label" for="character_breed_id_12">
                                <span class="ak-breed-icon ak-breed-icon-big breed12_0 ak-tooltip"></span><script type="application/json">{"manual":true,"tooltip":{"content":{"title":"","text":"La chopine de Pandawa"},"style":{"classes":"ak-tooltip-content"}}}</script>                                        
                                </label>
                            </li>
                            <li class="ak-has-icon @if(in_array(13, $filters['character_breed_id']['values'])) ak-selected @endif">
                                <input class="hide" id="character_breed_id_13" type="checkbox" name="character_breed_id[]" value="13" @if(in_array(13, $filters['character_breed_id']['values'])) checked="checked" @endif>
                                <label class="ak-label" for="character_breed_id_13">
                                <span class="ak-breed-icon ak-breed-icon-big breed13_0 ak-tooltip"></span><script type="application/json">{"manual":true,"tooltip":{"content":{"title":"","text":"La ruse du Roublard"},"style":{"classes":"ak-tooltip-content"}}}</script>                                        
                                </label>
                            </li>
                            <li class="ak-has-icon @if(in_array(14, $filters['character_breed_id']['values'])) ak-selected @endif">
                                <input class="hide" id="character_breed_id_14" type="checkbox" name="character_breed_id[]" value="14" @if(in_array(14, $filters['character_breed_id']['values'])) checked="checked" @endif>
                                <label class="ak-label" for="character_breed_id_14">
                                <span class="ak-breed-icon ak-breed-icon-big breed14_0 ak-tooltip"></span><script type="application/json">{"manual":true,"tooltip":{"content":{"title":"","text":"Le masque du Zobal"},"style":{"classes":"ak-tooltip-content"}}}</script>                                        
                                </label>
                            </li>
                            <li class="ak-has-icon @if(in_array(15, $filters['character_breed_id']['values'])) ak-selected @endif">
                                <input class="hide" id="character_breed_id_15" type="checkbox" name="character_breed_id[]" value="15" @if(in_array(15, $filters['character_breed_id']['values'])) checked="checked" @endif>
                                <label class="ak-label" for="character_breed_id_15">
                                <span class="ak-breed-icon ak-breed-icon-big breed15_0 ak-tooltip"></span><script type="application/json">{"manual":true,"tooltip":{"content":{"title":"","text":"La vapeur du Steamer"},"style":{"classes":"ak-tooltip-content"}}}</script>                                        
                                </label>
                            </li>
                            <li class="ak-has-icon @if(in_array(16, $filters['character_breed_id']['values'])) ak-selected @endif">
                                <input class="hide" id="character_breed_id_16" type="checkbox" name="character_breed_id[]" value="16" @if(in_array(16, $filters['character_breed_id']['values'])) checked="checked" @endif>
                                <label class="ak-label" for="character_breed_id_16">
                                <span class="ak-breed-icon ak-breed-icon-big breed16_0 ak-tooltip"></span><script type="application/json">{"manual":true,"tooltip":{"content":{"title":"","text":"Le portail Eliotrope"},"style":{"classes":"ak-tooltip-content"}}}</script>                                        
                                </label>
                            </li>
                            <li class="ak-has-icon @if(in_array(17, $filters['character_breed_id']['values'])) ak-selected @endif">
                                <input class="hide" id="character_breed_id_17" type="checkbox" name="character_breed_id[]" value="17" @if(in_array(17, $filters['character_breed_id']['values'])) checked="checked" @endif>
                                <label class="ak-label" for="character_breed_id_17">
                                <span class="ak-breed-icon ak-breed-icon-big breed17_0 ak-tooltip"></span><script type="application/json">{"manual":true,"tooltip":{"content":{"title":"","text":"La rune de l'Huppermage"},"style":{"classes":"ak-tooltip-content"}}}</script>                                        
                                </label>
                            </li>
                            <li class="ak-has-icon @if(in_array(18, $filters['character_breed_id']['values'])) ak-selected @endif">
                                <input class="hide" id="character_breed_id_18" type="checkbox" name="character_breed_id[]" value="18" @if(in_array(18, $filters['character_breed_id']['values'])) checked="checked" @endif>
                                <label class="ak-label" for="character_breed_id_18">
                                <span class="ak-breed-icon ak-breed-icon-big breed18_0 ak-tooltip"></span><script type="application/json">{"manual":true,"tooltip":{"content":{"title":"","text":"La rage d'Ouginak"},"style":{"classes":"ak-tooltip-content"}}}</script>                                        
                                </label>
                            </li>
                        </ul>
                    </div>
                    </div>
                    <div data-name="item_character_homeserv" class="ak-list-filters" style="text-transform:none;">
                    <div class="ak-filter-selection">
                        <span class="ak-picto-puce ak-picto-common"></span>
                        Serveurs        <a class="ak-picto-close ak-toggle ak-picto-common"></a>
                    </div>
                    <div class="ak-list-filters-inner">
                        <ul class="ak-list-filters-check">
                        @foreach(config('dofus.servers') as $server)
                            <li>
                                <input id="character_homeserv_{{$server}}" type="checkbox" name="character_homeserv[]" value="{{$server}}" @if(in_array($server, $filters['character_homeserv']['values'])) checked="checked" @endif>
                                <label class="ak-label" for="character_homeserv_{{$server}}">{{ucfirst($server)}}</label>
                            </li>
                        @endforeach
                        </ul>
                        <script type="application/json">{"valueNames":["ak-label"],"searchFieldCls":"ak-searchable-list-character_homeserv"}</script>      
                    </div>
                    </div>
                    <div data-name="item_character_level" class="ak-list-filters ak-display-online ak-numeric-min-max">
                    <div class="ak-filter-selection">
                        <span class="ak-picto-puce ak-picto-common"></span>
                        Niveau        <a class="ak-toggle ak-picto-common ak-picto-close"></a>
                    </div>
                    <div class="ak-list-filters-inner" style="display: block;">
                        <ul class="ak-list-filters-check">
                            <li class="ak-inline-li">
                                <label for="character_level_min_0">De</label>            
                                <select class="ak-numeric-min" id="character_level_min_0" name="character_level_min">
                                     <option value="" selected="selected">Niveau</option>
                                @for($i = 1; $i <= 200; $i++)
                                   <option value="{{$i}}" 
                                   @if(!empty($filters['character_level']['values']))
                                        @if($i == $filters['character_level']['values'][0])) 
                                        selected="selected" 
                                        @endif
                                    @endif
                                        >{{$i}}</option>
                                @endfor
                                </select>
                            </li>
                            <li class="ak-inline-li">
                                <label for="character_level_max_1">À</label>            
                                <select class="ak-numeric-max" id="character_level_max_1" name="character_level_max">
                                <option value="" selected="selected">Niveau</option>
                                @for($i = 1; $i <= 200; $i++)
                                   <option value="{{$i}}" 
                                   @if(!empty($filters['character_level']['values']))
                                        @if($i == $filters['character_level']['values'][1])) 
                                        selected="selected" 
                                        @endif
                                    @elseif((empty($filters['character_level']['values'])) && ($i == 200))
                                        selected="selected" 
                                    @endif
                                        >{{$i}}</option>
                                @endfor
                                </select>
                            </li>
                        </ul>
                    </div>
                    </div>
                    <div data-name="item_character_prestige" class="ak-list-filters ak-display-online ak-numeric-min-max">
                    <div class="ak-filter-selection">
                        <span class="ak-picto-puce ak-picto-common"></span>
                        Prestige        <a class="ak-toggle ak-picto-common ak-picto-close"></a>
                    </div>
                    <div class="ak-list-filters-inner" style="display: block;">
                        <ul class="ak-list-filters-check">
                            <li class="ak-inline-li">
                                <label for="character_prestige_min_0">De</label>            
                                <select class="ak-numeric-min" id="character_prestige_min_0" name="character_prestige_min">
                                     <option value="" selected="selected">Prestige</option>
                                @for($i = 0; $i <= 15; $i++)
                                   <option value="{{$i}}" 
                                   @if(!empty($filters['character_prestige']['values']))
                                        @if($i == $filters['character_prestige']['values'][0])) 
                                        selected="selected" 
                                        @endif
                                    @endif
                                        >{{$i}}</option>
                                @endfor
                                </select>
                            </li>
                            <li class="ak-inline-li">
                                <label for="character_prestige_max_1">À</label>            
                                <select class="ak-numeric-max" id="character_prestige_max_1" name="character_prestige_max">
                                <option value="" selected="selected">Prestige</option>
                                @for($i = 0; $i <= 15; $i++)
                                   <option value="{{$i}}" 
                                   @if(!empty($filters['character_prestige']['values']))
                                        @if($i == $filters['character_prestige']['values'][1])) 
                                        selected="selected" 
                                        @endif
                                    @elseif((empty($filters['character_prestige']['values'])) && ($i == 15))
                                        selected="selected" 
                                    @endif
                                        >{{$i}}</option>
                                @endfor
                                </select>
                            </li>
                        </ul>
                    </div>
                    </div>

                    <div data-name="item_character_price" class="ak-list-filters ak-display-online">
                    <div class="ak-filter-selection">
                        <span class="ak-picto-puce ak-picto-common"></span>
                        Prix        <a class="ak-toggle ak-picto-common ak-picto-close"></a>
                    </div>
                    <div class="ak-list-filters-inner" style="display: block;">
                        <ul class="ak-list-filters-check">
                            <li class="ak-inline-li">
                                <label for="character_price_min_0">De</label>            
                                <input id="character_price_min_0" type="text" name="character_price_min" style="width:80%; border: 1px solid #b1ac9c;">
                            </li>
                            <li class="ak-inline-li">
                                <label for="character_price_max_1">À</label>            
                                <input id="character_price_max_1" type="text" name="character_price_max" style="width:80%; border: 1px solid #b1ac9c;">
                            </li>
                        </ul>
                    </div>
                    </div>


                    <div data-name="item_character_sex" class="ak-list-filters">
                    <div class="ak-filter-selection">
                        <span class="ak-picto-puce ak-picto-common"></span>
                        Sexe        <a class="ak-toggle ak-picto-common ak-picto-close"></a>
                    </div>
                    <div class="ak-list-filters-inner" style="display: block;">
                        <ul class="ak-list-filters-check">
                            <li>
                                <input id="character_sex_0" type="checkbox" name="character_sex[]" value="0" @if(in_array(0, $filters['character_sex']['values'])) checked="checked" @endif>
                                <label class="ak-label" for="character_sex_0">
                                Mâle                                        </label>
                            </li>
                            <li>
                                <input id="character_sex_1" type="checkbox" name="character_sex[]" value="1" @if(in_array(1, $filters['character_sex']['values'])) checked="checked" @endif>
                                <label class="ak-label" for="character_sex_1">
                                Femelle                                        </label>
                            </li>
                        </ul>
                    </div>
                    </div>
                    <div class="ak-button-click">
                    <button type="submit" class="btn btn-primary btn-lg" data-text="Filtrer !"><span>Filtrer !</span></button>
                    </div>
                </form>
            </div>
            <script type="application/json">{"sMode":"buttonClick","bMobileToggleAll":false}</script>
        </div>
    </div>
</aside>
@stop
