@extends('layouts.contents.default')
@include('layouts.menus.base')
@section('header')
    {!! Html::style('css/directories.css') !!}
    {!! Html::style('css/encyclopedia.css') !!}
@stop
@section('breadcrumbs')
{? $page_name = config('dofus.subtitle') ?}
{!! Breadcrumbs::render('home') !!}
@stop
@section('beta')
    <div class="ak-beta"></div>
@endsection
@section('content')
<div class="ak-container ak-main-center">
   <div class="ak-title-container ak-backlink">
      <h1><span class="ak-icon-big ak-character"></span></a>{{$character->Name}}</h1>
   </div>
   @include('gameaccount.character.nav.topnav')
   @if($marketCharacter)
   <div class="ak-container ak-panel-stack ak-directories ak-glue" style="margin-bottom:12px;">
    <div class="ak-container ak-panel ak-success-content">        
        <div class="ak-panel-title">
              <span class="ak-panel-title-icon"></span>Marché des personnages            
        </div>
        <div class="ak-panel-content">
            <div>
            <h3 class="text-center text-success">{{$character->Name}} est en vente sur le <a style="color:#337ab7;" href="{{URL::route('shop.market')}}">marché des personnages</a> !</h3>
                <div class="ak-character-score">
                    <div class="ak-character-score-layer">
                        <span class="ak-icon-big ak-ogrines-icon"></span>
                        <span class="ak-score-text">{{Utils::format_price($marketCharacter->ogrines)}}</span>
                    </div>
                </div>
                <div class="text-center ak-points-block-container">
                    <div class="ak-points-block ak-success-category-0">
                    @if(Auth::user()->id != $marketCharacter->user_id)
                        @if(Auth::user()->points < $marketCharacter->ogrines)
                        <a class="btn btn-xxl btn-info ak-tooltip">Acheter</a>
                        <script type="application/json">{"manual":true,"tooltip":{"content":{"title":"","text":"<p>Votre réserve d'Ogrines est insuffisante pour faire cet achat: Il vous manque {{(int)ceil($marketCharacter->ogrines - Auth::user()->points)}} Ogrines.<\/p><a href=\"{{URL::route('shop.payment.country')}}\" class=\"btn btn-primary\">Acheter des Ogrines<\/a>"},"style":{"classes":"ak-tooltip-white-shop"},"position":{"my":"bottom center","at":"top center","adjust":{"scroll":false}},"show":{"event":"click"},"hide":{"fixed":true,"delay":400}},"forceOnTouch":true}</script>
                        @else
                        <a class="btn btn-xxl btn-info" href="{{route('shop.market.buy',$marketCharacter->id)}}">Acheter</a>
                        @endif
                    @else
                        <a class="btn btn-xxl btn-info ak-tooltip">Acheter</a>
                        <script type="application/json">{"manual":true,"tooltip":{"content":{"title":"","text":"<p>Vous ne pouvez pas acheter votre propre personnage.<\/p>"},"style":{"classes":"ak-tooltip-white-shop"},"position":{"my":"bottom center","at":"top center","adjust":{"scroll":false}},"show":{"event":"click"},"hide":{"fixed":true,"delay":400}},"forceOnTouch":true}</script> 
                    @endif               
                    </div>
                </div>
            </div>
        </div>
    </div>
   </div>
   @endif
   <div class="ak-container ak-panel ak-nocontentpadding">
      <div class="ak-panel-content">
         <div class="ak-directories-header ">
            <div class="ak-directories-icon">
               <div class="ak-entitylook" alt="" style="background:url({{ DofusForge::player($character, $server, 'face', 2, 35, 35) }}) top left;width:35px;height:35px">
               </div>
            </div>
            <div class="ak-directories-main-infos">
               <div class="ak-directories-property">
                  <span class="ak-directories-level">Niveau {{$character->level($server)}}</span>@if($character->PrestigeRank > 0)<span class="ak-directories-prestige"> - Prestige {{$character->PrestigeRank}}</span>@endif<br>
                  <span class="ak-directories-breed">{{$character->classe()}}</span>
               </div>
               @if($character->titleActive($server))
                <div class="ak-directories-property ak-directories-property-grade">
                    <span class="ak-directories-grade">{!! $character->titleActive($server)->name() !!}</span>
                </div>
               @endif
               <div class="ak-directories-property ak-directories-property-server">
                  <span class="server">
                  <span class="server_4001"></span>
                  <span class="ak-directories-server-name">{{ucfirst($server)}}</span>
                  </span>
               </div>
                @if(Auth::user()->id == $character->user()->id)
                <div class="pull-right" style="margin-top:7px;">
                    <a href="{{route('characters.settings', [$server,$character->Id,$character->Name])}}"><span class="ak-icon-specific ak-settings ak-tooltip" title="Paramètres"></span>
                    </a>
                </div>
                @endif
            </div>
         </div>
      </div>
   </div>
   <div class="ak-container ak-panel-stack ak-directories ak-glue">
       @if($settings->show_equipments)
       <div class="ak-container ak-panel ak-caracteristics-content">
            <div class="ak-panel-title">
                <span class="ak-panel-title-icon"></span>
                Équipement            
            </div>
            <div class="ak-panel-content">
                <div class="hidden-xs">
                    <div class="ak-equipment-columns">
                        <div class="ak-equipment-left">
                            <div class="ak-equipment-item ak-equipment-shield" id="ak-dofus-character-equipment-item-shield">
                                @if($itemsleft->containsStrict('Position', \App\ItemPosition::ACCESSORY_POSITION_SHIELD))
                                @php $item = $itemsleft->where('Position', \App\ItemPosition::ACCESSORY_POSITION_SHIELD)->first() @endphp
                                <span class="ak-linker" data-hasqtip="linker_item_{{$item->Id}}"><img src="{{DofusForge::item($item->Template->IconId, 52)}}"></span><script type="application/json">{"iShowDelay":250,"iHideDelay":250,"linker-id":"linker_item_{{$item->Id}}","linker-path":"{{URL::route('linker.get', 'item')}}","linker-display-type":"TOOLTIP","linker-query-datas":{"l":"fr","id":"{{$item->Id}}","character":"{{$character->Id}}","server":"{{$server}}","position":"left","builder":""}}</script>                        
                                @endif
                            </div>
                            <div class="ak-equipment-item ak-equipment-amulet" id="ak-dofus-character-equipment-item-amulet">
                                @if($itemsleft->containsStrict('Position', \App\ItemPosition::ACCESSORY_POSITION_AMULET))
                                @php $item = $itemsleft->where('Position', \App\ItemPosition::ACCESSORY_POSITION_AMULET)->first() @endphp
                                <span class="ak-linker" data-hasqtip="linker_item_{{$item->Id}}"><img src="{{DofusForge::item($item->Template->IconId, 52)}}"></span><script type="application/json">{"iShowDelay":250,"iHideDelay":250,"linker-id":"linker_item_{{$item->Id}}","linker-path":"{{URL::route('linker.get', 'item')}}","linker-display-type":"TOOLTIP","linker-query-datas":{"l":"fr","id":"{{$item->Id}}","character":"{{$character->Id}}","server":"{{$server}}","position":"left","builder":""}}</script>                      
                                @endif                    
                            </div>
                            <div class="ak-equipment-item ak-equipment-ring1" id="ak-dofus-character-equipment-item-ring1">
                                @if($itemsleft->containsStrict('Position', \App\ItemPosition::INVENTORY_POSITION_RING_LEFT))
                                @php $item = $itemsleft->where('Position', \App\ItemPosition::INVENTORY_POSITION_RING_LEFT)->first() @endphp
                                <span class="ak-linker" data-hasqtip="linker_item_{{$item->Id}}"><img src="{{DofusForge::item($item->Template->IconId, 52)}}"></span><script type="application/json">{"iShowDelay":250,"iHideDelay":250,"linker-id":"linker_item_{{$item->Id}}","linker-path":"{{URL::route('linker.get', 'item')}}","linker-display-type":"TOOLTIP","linker-query-datas":{"l":"fr","id":"{{$item->Id}}","character":"{{$character->Id}}","server":"{{$server}}","position":"left","builder":""}}</script>                        
                                @endif 
                            </div>
                            <div class="ak-equipment-item ak-equipment-cap" id="ak-dofus-character-equipment-item-cap">
                                @if($itemsleft->containsStrict('Position', \App\ItemPosition::ACCESSORY_POSITION_CAPE))
                                @php $item = $itemsleft->where('Position', \App\ItemPosition::ACCESSORY_POSITION_CAPE)->first() @endphp
                                <span class="ak-linker" data-hasqtip="linker_item_{{$item->Id}}"><img src="{{DofusForge::item($item->Template->IconId, 52)}}"></span><script type="application/json">{"iShowDelay":250,"iHideDelay":250,"linker-id":"linker_item_{{$item->Id}}","linker-path":"{{URL::route('linker.get', 'item')}}","linker-display-type":"TOOLTIP","linker-query-datas":{"l":"fr","id":"{{$item->Id}}","character":"{{$character->Id}}","server":"{{$server}}","position":"left","builder":""}}</script>                      
                                @endif 
                            </div>
                            <div class="ak-equipment-item ak-equipment-boots" id="ak-dofus-character-equipment-item-boots">
                                @if($itemsleft->containsStrict('Position', \App\ItemPosition::ACCESSORY_POSITION_BOOTS))
                                @php $item = $itemsleft->where('Position', \App\ItemPosition::ACCESSORY_POSITION_BOOTS)->first() @endphp
                                <span class="ak-linker" data-hasqtip="linker_item_{{$item->Id}}"><img src="{{DofusForge::item($item->Template->IconId, 52)}}"></span><script type="application/json">{"iShowDelay":250,"iHideDelay":250,"linker-id":"linker_item_{{$item->Id}}","linker-path":"{{URL::route('linker.get', 'item')}}","linker-display-type":"TOOLTIP","linker-query-datas":{"l":"fr","id":"{{$item->Id}}","character":"{{$character->Id}}","server":"{{$server}}","position":"left","builder":""}}</script>                        
                                @endif 
                            </div>
                        </div>
                        <div class="ak-equipment-middle">
                            <div class="ak-character-equipments">
                                <div class="ak-chracter-illustration">
                                    <div class="ak-set-illu">
                                        <div class="ak-entitylook" alt="" style="background:url({{ DofusForge::player($character, $server, 'full', 1, 270, 361, 10) }}) top left;width:270px;height:361px"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="ak-equipment-right">
                            <div class="ak-equipment-item ak-equipment-weapon" id="ak-dofus-character-equipment-item-weapon">
                                @if($itemsright->containsStrict('Position', \App\ItemPosition::ACCESSORY_POSITION_WEAPON))
                                @php $item = $itemsright->where('Position', \App\ItemPosition::ACCESSORY_POSITION_WEAPON)->first() @endphp
                                <span class="ak-linker" data-hasqtip="linker_item_{{$item->Id}}"><img src="{{DofusForge::item($item->Template->IconId, 52)}}"></span><script type="application/json">{"iShowDelay":250,"iHideDelay":250,"linker-id":"linker_item_{{$item->Id}}","linker-path":"{{URL::route('linker.get', 'item')}}","linker-display-type":"TOOLTIP","linker-query-datas":{"l":"fr","id":"{{$item->Id}}","character":"{{$character->Id}}","server":"{{$server}}","position":"right","builder":""}}</script>                          
                                @endif
                            </div>
                            <div class="ak-equipment-item ak-equipment-hat" id="ak-dofus-character-equipment-item-hat">
                                @if($itemsright->containsStrict('Position', \App\ItemPosition::ACCESSORY_POSITION_HAT))
                                @php $item = $itemsright->where('Position', \App\ItemPosition::ACCESSORY_POSITION_HAT)->first() @endphp
                                <span class="ak-linker" data-hasqtip="linker_item_{{$item->Id}}"><img src="{{DofusForge::item($item->Template->IconId, 52)}}"></span><script type="application/json">{"iShowDelay":250,"iHideDelay":250,"linker-id":"linker_item_{{$item->Id}}","linker-path":"{{URL::route('linker.get', 'item')}}","linker-display-type":"TOOLTIP","linker-query-datas":{"l":"fr","id":"{{$item->Id}}","character":"{{$character->Id}}","server":"{{$server}}","position":"right","builder":""}}</script>                      
                                @endif
                            </div>
                            <div class="ak-equipment-item ak-equipment-ring2" id="ak-dofus-character-equipment-item-ring2">
                                @if($itemsright->containsStrict('Position', \App\ItemPosition::INVENTORY_POSITION_RING_RIGHT))
                                @php $item = $itemsright->where('Position', \App\ItemPosition::INVENTORY_POSITION_RING_RIGHT)->first() @endphp
                                <span class="ak-linker" data-hasqtip="linker_item_{{$item->Id}}"><img src="{{DofusForge::item($item->Template->IconId, 52)}}"></span><script type="application/json">{"iShowDelay":250,"iHideDelay":250,"linker-id":"linker_item_{{$item->Id}}","linker-path":"{{URL::route('linker.get', 'item')}}","linker-display-type":"TOOLTIP","linker-query-datas":{"l":"fr","id":"{{$item->Id}}","character":"{{$character->Id}}","server":"{{$server}}","position":"right","builder":""}}</script>                       
                                @endif                   
                            </div>
                            <div class="ak-equipment-item ak-equipment-belt" id="ak-dofus-character-equipment-item-belt">
                                @if($itemsright->containsStrict('Position', \App\ItemPosition::ACCESSORY_POSITION_BELT))
                                @php $item = $itemsright->where('Position', \App\ItemPosition::ACCESSORY_POSITION_BELT)->first() @endphp
                                <span class="ak-linker" data-hasqtip="linker_item_{{$item->Id}}"><img src="{{DofusForge::item($item->Template->IconId, 52)}}"></span><script type="application/json">{"iShowDelay":250,"iHideDelay":250,"linker-id":"linker_item_{{$item->Id}}","linker-path":"{{URL::route('linker.get', 'item')}}","linker-display-type":"TOOLTIP","linker-query-datas":{"l":"fr","id":"{{$item->Id}}","character":"{{$character->Id}}","server":"{{$server}}","position":"right","builder":""}}</script>                     
                                @endif                  
                            </div>
                            <div class="ak-equipment-item ak-equipment-pet" id="ak-dofus-character-equipment-item-pet">
                                @if($itemsright->containsStrict('Position', \App\ItemPosition::ACCESSORY_POSITION_PETS))
                                @php $item = $itemsright->where('Position', \App\ItemPosition::ACCESSORY_POSITION_PETS)->first() @endphp
                                <span class="ak-linker" data-hasqtip="linker_item_{{$item->Id}}"><img src="{{DofusForge::item($item->Template->IconId, 52)}}"></span><script type="application/json">{"iShowDelay":250,"iHideDelay":250,"linker-id":"linker_item_{{$item->Id}}","linker-path":"{{URL::route('linker.get', 'item')}}","linker-display-type":"TOOLTIP","linker-query-datas":{"l":"fr","id":"{{$item->Id}}","character":"{{$character->Id}}","server":"{{$server}}","position":"right","builder":""}}</script>                       
                                @endif                  
                            </div>
                        </div>
                    </div>
                    <div class="ak-equipment-bottom">
                        <div class="ak-equipment-item ak-equipment-dofus" id="ak-dofus-character-equipment-item-dofus">
                                @if($itemsbottom->containsStrict('Position', \App\ItemPosition::INVENTORY_POSITION_DOFUS_1))
                                @php $item = $itemsbottom->where('Position', \App\ItemPosition::INVENTORY_POSITION_DOFUS_1)->first() @endphp
                                <span class="ak-linker" data-hasqtip="linker_item_{{$item->Id}}"><img src="{{DofusForge::item($item->Template->IconId, 52)}}"></span><script type="application/json">{"iShowDelay":250,"iHideDelay":250,"linker-id":"linker_item_{{$item->Id}}","linker-path":"{{URL::route('linker.get', 'item')}}","linker-display-type":"TOOLTIP","linker-query-datas":{"l":"fr","id":"{{$item->Id}}","character":"{{$character->Id}}","server":"{{$server}}","position":"bottom","builder":""}}</script>                       
                                @endif               
                        </div>
                        <div class="ak-equipment-item ak-equipment-dofus" id="ak-dofus-character-equipment-item-dofus">
                                @if($itemsbottom->containsStrict('Position', \App\ItemPosition::INVENTORY_POSITION_DOFUS_2))
                                @php $item = $itemsbottom->where('Position', \App\ItemPosition::INVENTORY_POSITION_DOFUS_2)->first() @endphp
                                <span class="ak-linker" data-hasqtip="linker_item_{{$item->Id}}"><img src="{{DofusForge::item($item->Template->IconId, 52)}}"></span><script type="application/json">{"iShowDelay":250,"iHideDelay":250,"linker-id":"linker_item_{{$item->Id}}","linker-path":"{{URL::route('linker.get', 'item')}}","linker-display-type":"TOOLTIP","linker-query-datas":{"l":"fr","id":"{{$item->Id}}","character":"{{$character->Id}}","server":"{{$server}}","position":"bottom","builder":""}}</script>                      
                                @endif             
                        </div>
                        <div class="ak-equipment-item ak-equipment-dofus" id="ak-dofus-character-equipment-item-dofus">
                                @if($itemsbottom->containsStrict('Position', \App\ItemPosition::INVENTORY_POSITION_DOFUS_3))
                                @php $item = $itemsbottom->where('Position', \App\ItemPosition::INVENTORY_POSITION_DOFUS_3)->first() @endphp
                                <span class="ak-linker" data-hasqtip="linker_item_{{$item->Id}}"><img src="{{DofusForge::item($item->Template->IconId, 52)}}"></span><script type="application/json">{"iShowDelay":250,"iHideDelay":250,"linker-id":"linker_item_{{$item->Id}}","linker-path":"{{URL::route('linker.get', 'item')}}","linker-display-type":"TOOLTIP","linker-query-datas":{"l":"fr","id":"{{$item->Id}}","character":"{{$character->Id}}","server":"{{$server}}","position":"bottom","builder":""}}</script>                       
                                @endif           
                        </div>
                        <div class="ak-equipment-item ak-equipment-dofus" id="ak-dofus-character-equipment-item-dofus">
                                @if($itemsbottom->containsStrict('Position', \App\ItemPosition::INVENTORY_POSITION_DOFUS_4))
                                @php $item = $itemsbottom->where('Position', \App\ItemPosition::INVENTORY_POSITION_DOFUS_4)->first() @endphp
                                <span class="ak-linker" data-hasqtip="linker_item_{{$item->Id}}"><img src="{{DofusForge::item($item->Template->IconId, 52)}}"></span><script type="application/json">{"iShowDelay":250,"iHideDelay":250,"linker-id":"linker_item_{{$item->Id}}","linker-path":"{{URL::route('linker.get', 'item')}}","linker-display-type":"TOOLTIP","linker-query-datas":{"l":"fr","id":"{{$item->Id}}","character":"{{$character->Id}}","server":"{{$server}}","position":"bottom","builder":""}}</script>                       
                                @endif            
                        </div>
                        <div class="ak-equipment-item ak-equipment-dofus" id="ak-dofus-character-equipment-item-dofus">
                                @if($itemsbottom->containsStrict('Position', \App\ItemPosition::INVENTORY_POSITION_DOFUS_5))
                                @php $item = $itemsbottom->where('Position', \App\ItemPosition::INVENTORY_POSITION_DOFUS_5)->first() @endphp
                                <span class="ak-linker" data-hasqtip="linker_item_{{$item->Id}}"><img src="{{DofusForge::item($item->Template->IconId, 52)}}"></span><script type="application/json">{"iShowDelay":250,"iHideDelay":250,"linker-id":"linker_item_{{$item->Id}}","linker-path":"{{URL::route('linker.get', 'item')}}","linker-display-type":"TOOLTIP","linker-query-datas":{"l":"fr","id":"{{$item->Id}}","character":"{{$character->Id}}","server":"{{$server}}","position":"bottom","builder":""}}</script>                        
                                @endif              
                        </div>
                        <div class="ak-equipment-item ak-equipment-dofus" id="ak-dofus-character-equipment-item-dofus">
                                @if($itemsbottom->containsStrict('Position', \App\ItemPosition::INVENTORY_POSITION_DOFUS_6))
                                @php $item = $itemsbottom->where('Position', \App\ItemPosition::INVENTORY_POSITION_DOFUS_6)->first() @endphp
                                <span class="ak-linker" data-hasqtip="linker_item_{{$item->Id}}"><img src="{{DofusForge::item($item->Template->IconId, 52)}}"></span><script type="application/json">{"iShowDelay":250,"iHideDelay":250,"linker-id":"linker_item_{{$item->Id}}","linker-path":"{{URL::route('linker.get', 'item')}}","linker-display-type":"TOOLTIP","linker-query-datas":{"l":"fr","id":"{{$item->Id}}","character":"{{$character->Id}}","server":"{{$server}}","position":"bottom","builder":""}}</script>                       
                                @endif             
                        </div>
                    </div>
                    @if(config('dofus.details')[$server]->version != "2.10")
                    <div class="ak-equipment-bottom">
                        <div class="ak-equipment-item ak-equipment-idol" id="ak-dofus-character-equipment-item-idol">
                                @if($costume->containsStrict('Position', \App\ItemPosition::INVENTORY_POSITION_COSTUME))
                                @php $item = $costume->where('Position', \App\ItemPosition::INVENTORY_POSITION_COSTUME)->first() @endphp
                                <span class="ak-linker" data-hasqtip="linker_item_{{$item->Id}}"><img src="{{DofusForge::item($item->Template->IconId, 52)}}"></span><script type="application/json">{"iShowDelay":250,"iHideDelay":250,"linker-id":"linker_item_{{$item->Id}}","linker-path":"{{URL::route('linker.get', 'item')}}","linker-display-type":"TOOLTIP","linker-query-datas":{"l":"fr","id":"{{$item->Id}}","character":"{{$character->Id}}","server":"{{$server}}","position":"costume","builder":""}}</script>                       
                                @endif               
                        </div>                        
                    </div>
                    @endif
                </div>
                <div class="ak-equipment-mobile visible-xs">
                    <div class="ak-equipment-middle">
                    <div class="ak-character-equipments">
                        <div class="ak-chracter-illustration">
                            <div class="ak-set-illu">
                                <div class="ak-entitylook" alt="" style="background:url({{ DofusForge::player($character, $server, 'full', 1, 270, 361, 10) }}) top left;width:270px;height:361px"></div>
                            </div>
                        </div>
                    </div>
                    </div>
                    <div class="ak-equipment-list">
                    <div class="row">
                        <div class="col-xs-3 ">
                            <div class="ak-equipment-item ak-equipment-shield" id="ak-dofus-character-equipment-item-shield">
                                @if($itemsleft->containsStrict('Position', \App\ItemPosition::ACCESSORY_POSITION_SHIELD))
                                @php $item = $itemsleft->where('Position', \App\ItemPosition::ACCESSORY_POSITION_SHIELD)->first() @endphp
                                <span class="ak-linker" data-hasqtip="linker_item_{{$item->Id}}"><img src="{{DofusForge::item($item->Template->IconId, 52)}}"></span><script type="application/json">{"iShowDelay":250,"iHideDelay":250,"linker-id":"linker_item_{{$item->Id}}","linker-path":"{{URL::route('linker.get', 'item')}}","linker-display-type":"TOOLTIP","linker-query-datas":{"l":"fr","id":"{{$item->Id}}","character":"{{$character->Id}}","server":"{{$server}}","position":"left","builder":""}}</script>                        
                                @endif
                            </div>
                        </div>
                        <div class="col-xs-3 ">
                            <div class="ak-equipment-item ak-equipment-amulet" id="ak-dofus-character-equipment-item-amulet">
                                @if($itemsleft->containsStrict('Position', \App\ItemPosition::ACCESSORY_POSITION_AMULET))
                                @php $item = $itemsleft->where('Position', \App\ItemPosition::ACCESSORY_POSITION_AMULET)->first() @endphp
                                <span class="ak-linker" data-hasqtip="linker_item_{{$item->Id}}"><img src="{{DofusForge::item($item->Template->IconId, 52)}}"></span><script type="application/json">{"iShowDelay":250,"iHideDelay":250,"linker-id":"linker_item_{{$item->Id}}","linker-path":"{{URL::route('linker.get', 'item')}}","linker-display-type":"TOOLTIP","linker-query-datas":{"l":"fr","id":"{{$item->Id}}","character":"{{$character->Id}}","server":"{{$server}}","position":"left","builder":""}}</script>                      
                                @endif   
                            </div>
                        </div>
                        <div class="col-xs-3 ">
                            <div class="ak-equipment-item ak-equipment-ring1" id="ak-dofus-character-equipment-item-ring1">
                                @if($itemsleft->containsStrict('Position', \App\ItemPosition::INVENTORY_POSITION_RING_LEFT))
                                @php $item = $itemsleft->where('Position', \App\ItemPosition::INVENTORY_POSITION_RING_LEFT)->first() @endphp
                                <span class="ak-linker" data-hasqtip="linker_item_{{$item->Id}}"><img src="{{DofusForge::item($item->Template->IconId, 52)}}"></span><script type="application/json">{"iShowDelay":250,"iHideDelay":250,"linker-id":"linker_item_{{$item->Id}}","linker-path":"{{URL::route('linker.get', 'item')}}","linker-display-type":"TOOLTIP","linker-query-datas":{"l":"fr","id":"{{$item->Id}}","character":"{{$character->Id}}","server":"{{$server}}","position":"left","builder":""}}</script>                        
                                @endif
                            </div>
                        </div>
                        <div class="col-xs-3 ">
                            <div class="ak-equipment-item ak-equipment-cap" id="ak-dofus-character-equipment-item-cap">
                                @if($itemsleft->containsStrict('Position', \App\ItemPosition::ACCESSORY_POSITION_CAPE))
                                @php $item = $itemsleft->where('Position', \App\ItemPosition::ACCESSORY_POSITION_CAPE)->first() @endphp
                                <span class="ak-linker" data-hasqtip="linker_item_{{$item->Id}}"><img src="{{DofusForge::item($item->Template->IconId, 52)}}"></span><script type="application/json">{"iShowDelay":250,"iHideDelay":250,"linker-id":"linker_item_{{$item->Id}}","linker-path":"{{URL::route('linker.get', 'item')}}","linker-display-type":"TOOLTIP","linker-query-datas":{"l":"fr","id":"{{$item->Id}}","character":"{{$character->Id}}","server":"{{$server}}","position":"left","builder":""}}</script>                      
                                @endif
                            </div>
                        </div>
                        <div class="ak-row-break clearfix visible-xs visible-sm visible-md visible-lg "></div>
                        <div class="col-xs-3 ">
                            <div class="ak-equipment-item ak-equipment-boots" id="ak-dofus-character-equipment-item-boots">
                                @if($itemsleft->containsStrict('Position', \App\ItemPosition::ACCESSORY_POSITION_BOOTS))
                                @php $item = $itemsleft->where('Position', \App\ItemPosition::ACCESSORY_POSITION_BOOTS)->first() @endphp
                                <span class="ak-linker" data-hasqtip="linker_item_{{$item->Id}}"><img src="{{DofusForge::item($item->Template->IconId, 52)}}"></span><script type="application/json">{"iShowDelay":250,"iHideDelay":250,"linker-id":"linker_item_{{$item->Id}}","linker-path":"{{URL::route('linker.get', 'item')}}","linker-display-type":"TOOLTIP","linker-query-datas":{"l":"fr","id":"{{$item->Id}}","character":"{{$character->Id}}","server":"{{$server}}","position":"left","builder":""}}</script>                        
                                @endif
                            </div>
                        </div>
                        <div class="col-xs-3 ">
                            <div class="ak-equipment-item ak-equipment-weapon" id="ak-dofus-character-equipment-item-weapon">
                                @if($itemsright->containsStrict('Position', \App\ItemPosition::ACCESSORY_POSITION_WEAPON))
                                @php $item = $itemsright->where('Position', \App\ItemPosition::ACCESSORY_POSITION_WEAPON)->first() @endphp
                                <span class="ak-linker" data-hasqtip="linker_item_{{$item->Id}}"><img src="{{DofusForge::item($item->Template->IconId, 52)}}"></span><script type="application/json">{"iShowDelay":250,"iHideDelay":250,"linker-id":"linker_item_{{$item->Id}}","linker-path":"{{URL::route('linker.get', 'item')}}","linker-display-type":"TOOLTIP","linker-query-datas":{"l":"fr","id":"{{$item->Id}}","character":"{{$character->Id}}","server":"{{$server}}","position":"right","builder":""}}</script>                          
                                @endif
                            </div>
                        </div>
                        <div class="col-xs-3 ">
                            <div class="ak-equipment-item ak-equipment-hat" id="ak-dofus-character-equipment-item-hat">
                                @if($itemsright->containsStrict('Position', \App\ItemPosition::ACCESSORY_POSITION_HAT))
                                @php $item = $itemsright->where('Position', \App\ItemPosition::ACCESSORY_POSITION_HAT)->first() @endphp
                                <span class="ak-linker" data-hasqtip="linker_item_{{$item->Id}}"><img src="{{DofusForge::item($item->Template->IconId, 52)}}"></span><script type="application/json">{"iShowDelay":250,"iHideDelay":250,"linker-id":"linker_item_{{$item->Id}}","linker-path":"{{URL::route('linker.get', 'item')}}","linker-display-type":"TOOLTIP","linker-query-datas":{"l":"fr","id":"{{$item->Id}}","character":"{{$character->Id}}","server":"{{$server}}","position":"right","builder":""}}</script>                      
                                @endif
                            </div>
                        </div>
                        <div class="col-xs-3 ">
                            <div class="ak-equipment-item ak-equipment-ring2" id="ak-dofus-character-equipment-item-ring2">
                                @if($itemsright->containsStrict('Position', \App\ItemPosition::INVENTORY_POSITION_RING_RIGHT))
                                @php $item = $itemsright->where('Position', \App\ItemPosition::INVENTORY_POSITION_RING_RIGHT)->first() @endphp
                                <span class="ak-linker" data-hasqtip="linker_item_{{$item->Id}}"><img src="{{DofusForge::item($item->Template->IconId, 52)}}"></span><script type="application/json">{"iShowDelay":250,"iHideDelay":250,"linker-id":"linker_item_{{$item->Id}}","linker-path":"{{URL::route('linker.get', 'item')}}","linker-display-type":"TOOLTIP","linker-query-datas":{"l":"fr","id":"{{$item->Id}}","character":"{{$character->Id}}","server":"{{$server}}","position":"right","builder":""}}</script>                       
                                @endif
                            </div>
                        </div>
                        <div class="ak-row-break clearfix visible-xs visible-sm visible-md visible-lg "></div>
                        <div class="col-xs-3 ">
                            <div class="ak-equipment-item ak-equipment-belt" id="ak-dofus-character-equipment-item-belt">
                                @if($itemsright->containsStrict('Position', \App\ItemPosition::ACCESSORY_POSITION_BELT))
                                @php $item = $itemsright->where('Position', \App\ItemPosition::ACCESSORY_POSITION_BELT)->first() @endphp
                                <span class="ak-linker" data-hasqtip="linker_item_{{$item->Id}}"><img src="{{DofusForge::item($item->Template->IconId, 52)}}"></span><script type="application/json">{"iShowDelay":250,"iHideDelay":250,"linker-id":"linker_item_{{$item->Id}}","linker-path":"{{URL::route('linker.get', 'item')}}","linker-display-type":"TOOLTIP","linker-query-datas":{"l":"fr","id":"{{$item->Id}}","character":"{{$character->Id}}","server":"{{$server}}","position":"right","builder":""}}</script>                     
                                @endif
                            </div>
                        </div>
                        <div class="col-xs-3 ">
                            <div class="ak-equipment-item ak-equipment-pet" id="ak-dofus-character-equipment-item-pet">
                                @if($itemsright->containsStrict('Position', \App\ItemPosition::ACCESSORY_POSITION_PETS))
                                @php $item = $itemsright->where('Position', \App\ItemPosition::ACCESSORY_POSITION_PETS)->first() @endphp
                                <span class="ak-linker" data-hasqtip="linker_item_{{$item->Id}}"><img src="{{DofusForge::item($item->Template->IconId, 52)}}"></span><script type="application/json">{"iShowDelay":250,"iHideDelay":250,"linker-id":"linker_item_{{$item->Id}}","linker-path":"{{URL::route('linker.get', 'item')}}","linker-display-type":"TOOLTIP","linker-query-datas":{"l":"fr","id":"{{$item->Id}}","character":"{{$character->Id}}","server":"{{$server}}","position":"right","builder":""}}</script>                       
                                @endif
                            </div>
                        </div>
                        <div class="col-xs-3 ">
                            <div class="ak-equipment-item ak-equipment-dofus" id="ak-dofus-character-equipment-item-dofus">
                                @if($itemsbottom->containsStrict('Position', \App\ItemPosition::INVENTORY_POSITION_DOFUS_1))
                                @php $item = $itemsbottom->where('Position', \App\ItemPosition::INVENTORY_POSITION_DOFUS_1)->first() @endphp
                                <span class="ak-linker" data-hasqtip="linker_item_{{$item->Id}}"><img src="{{DofusForge::item($item->Template->IconId, 52)}}"></span><script type="application/json">{"iShowDelay":250,"iHideDelay":250,"linker-id":"linker_item_{{$item->Id}}","linker-path":"{{URL::route('linker.get', 'item')}}","linker-display-type":"TOOLTIP","linker-query-datas":{"l":"fr","id":"{{$item->Id}}","character":"{{$character->Id}}","server":"{{$server}}","position":"bottom","builder":""}}</script>                       
                                @endif
                            </div>
                        </div>
                        <div class="col-xs-3 ">
                            <div class="ak-equipment-item ak-equipment-dofus" id="ak-dofus-character-equipment-item-dofus">
                                @if($itemsbottom->containsStrict('Position', \App\ItemPosition::INVENTORY_POSITION_DOFUS_2))
                                @php $item = $itemsbottom->where('Position', \App\ItemPosition::INVENTORY_POSITION_DOFUS_2)->first() @endphp
                                <span class="ak-linker" data-hasqtip="linker_item_{{$item->Id}}"><img src="{{DofusForge::item($item->Template->IconId, 52)}}"></span><script type="application/json">{"iShowDelay":250,"iHideDelay":250,"linker-id":"linker_item_{{$item->Id}}","linker-path":"{{URL::route('linker.get', 'item')}}","linker-display-type":"TOOLTIP","linker-query-datas":{"l":"fr","id":"{{$item->Id}}","character":"{{$character->Id}}","server":"{{$server}}","position":"bottom","builder":""}}</script>                      
                                @endif 
                            </div>
                        </div>
                        <div class="ak-row-break clearfix visible-xs visible-sm visible-md visible-lg "></div>
                        <div class="col-xs-3 ">
                            <div class="ak-equipment-item ak-equipment-dofus" id="ak-dofus-character-equipment-item-dofus">
                                @if($itemsbottom->containsStrict('Position', \App\ItemPosition::INVENTORY_POSITION_DOFUS_3))
                                @php $item = $itemsbottom->where('Position', \App\ItemPosition::INVENTORY_POSITION_DOFUS_3)->first() @endphp
                                <span class="ak-linker" data-hasqtip="linker_item_{{$item->Id}}"><img src="{{DofusForge::item($item->Template->IconId, 52)}}"></span><script type="application/json">{"iShowDelay":250,"iHideDelay":250,"linker-id":"linker_item_{{$item->Id}}","linker-path":"{{URL::route('linker.get', 'item')}}","linker-display-type":"TOOLTIP","linker-query-datas":{"l":"fr","id":"{{$item->Id}}","character":"{{$character->Id}}","server":"{{$server}}","position":"bottom","builder":""}}</script>                       
                                @endif
                            </div>
                        </div>
                        <div class="col-xs-3 ">
                            <div class="ak-equipment-item ak-equipment-dofus" id="ak-dofus-character-equipment-item-dofus">
                                @if($itemsbottom->containsStrict('Position', \App\ItemPosition::INVENTORY_POSITION_DOFUS_4))
                                @php $item = $itemsbottom->where('Position', \App\ItemPosition::INVENTORY_POSITION_DOFUS_4)->first() @endphp
                                <span class="ak-linker" data-hasqtip="linker_item_{{$item->Id}}"><img src="{{DofusForge::item($item->Template->IconId, 52)}}"></span><script type="application/json">{"iShowDelay":250,"iHideDelay":250,"linker-id":"linker_item_{{$item->Id}}","linker-path":"{{URL::route('linker.get', 'item')}}","linker-display-type":"TOOLTIP","linker-query-datas":{"l":"fr","id":"{{$item->Id}}","character":"{{$character->Id}}","server":"{{$server}}","position":"bottom","builder":""}}</script>                       
                                @endif
                            </div>
                        </div>
                        <div class="col-xs-3 ">
                            <div class="ak-equipment-item ak-equipment-dofus" id="ak-dofus-character-equipment-item-dofus">
                                @if($itemsbottom->containsStrict('Position', \App\ItemPosition::INVENTORY_POSITION_DOFUS_5))
                                @php $item = $itemsbottom->where('Position', \App\ItemPosition::INVENTORY_POSITION_DOFUS_5)->first() @endphp
                                <span class="ak-linker" data-hasqtip="linker_item_{{$item->Id}}"><img src="{{DofusForge::item($item->Template->IconId, 52)}}"></span><script type="application/json">{"iShowDelay":250,"iHideDelay":250,"linker-id":"linker_item_{{$item->Id}}","linker-path":"{{URL::route('linker.get', 'item')}}","linker-display-type":"TOOLTIP","linker-query-datas":{"l":"fr","id":"{{$item->Id}}","character":"{{$character->Id}}","server":"{{$server}}","position":"bottom","builder":""}}</script>                        
                                @endif 
                            </div>
                        </div>
                        <div class="col-xs-3 ">
                            <div class="ak-equipment-item ak-equipment-dofus" id="ak-dofus-character-equipment-item-dofus">
                                @if($itemsbottom->containsStrict('Position', \App\ItemPosition::INVENTORY_POSITION_DOFUS_6))
                                @php $item = $itemsbottom->where('Position', \App\ItemPosition::INVENTORY_POSITION_DOFUS_6)->first() @endphp
                                <span class="ak-linker" data-hasqtip="linker_item_{{$item->Id}}"><img src="{{DofusForge::item($item->Template->IconId, 52)}}"></span><script type="application/json">{"iShowDelay":250,"iHideDelay":250,"linker-id":"linker_item_{{$item->Id}}","linker-path":"{{URL::route('linker.get', 'item')}}","linker-display-type":"TOOLTIP","linker-query-datas":{"l":"fr","id":"{{$item->Id}}","character":"{{$character->Id}}","server":"{{$server}}","position":"bottom","builder":""}}</script>                       
                                @endif
                            </div>
                        </div>
                        <div class="ak-row-break clearfix visible-xs visible-sm visible-md visible-lg "></div>
                    </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
        @if($settings->show_spells)
        <div class="ak-container ak-panel ak-caracteristics-spells-content">
            <h2 class="ak-panel-title">
                <span class="ak-panel-title-icon"></span>
                Sorts    
            </h2>
            <div class="ak-panel-content">
            @if(config('dofus.details')[$server]->version == 2.10)
            <p class="alert alert-info">En raison de la version du serveur ({{config('dofus.details')[$server]->version}}), il est possible que certaines icônes ne correspondent pas.</p>
            @endif
                <div class="ak-spells-list">
                    <a class="ak-anchor" id="spell-details"></a>
                    <div>
                        <div class="ak-spell-list-row">
                        @foreach($spells as $spell)
                            <div class="ak-list-block ak-spell ak-spell-selected">
                                <span class="ak-spell-nb">{{$spell->Level}}</span>
                                <img src="{{$spell->template($server)->image('55')}}" alt="{{$spell->template($server)->name()}}">
                                <span class="ak-tooltip hidden" style="display: none;">{{$spell->template($server)->name()}}</span>
                            </div>
                        @endforeach 
                        </div>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </div>
        </div>
        @endif
        @if($settings->show_caracteristics)
        <div class="ak-container ak-panel ak-caracteristics-details">
        <div class="ak-panel-title">
            <span class="ak-panel-title-icon"></span>
            Caractéristiques            
        </div>
        <div class="ak-panel-content">
            <div class="ak-caracteristics-progress row">
                <div class="ak-progress ak-progress-left col-xs-6">
                    <span class="ak-progress-exp-label">Expérience : </span>
                    <div class="progress">
                    <div class="progress-bar ak-progress-bar-exp" style="width:{{ceil(($character->tempExp() / $character->expNextLevel()) * 100)}}%"></div>
                    <span class="ak-progress-text">{{min(ceil(($character->tempExp() / $character->expNextLevel()) * 100),100)}} <sup>%</sup> ({{$character->tempExp()}} / {{$character->expNextLevel()}})</span>
                    </div>
                </div>
                <div class="ak-progress ak-progress-left col-xs-6">
                    <span class="ak-progress-hp-label">Points de vie : </span>
                    <div class="progress">
                    <div class="progress-bar ak-progress-bar-health" style="width:100%"></div>
                    <span class="ak-progress-text">{{$character->BaseHealth + $character->Vitality + $character->PermanentAddedVitality + $character->countStuff($server,'vitality',['125','153'])}}</span>
                    </div>
                </div>
            </div>
            <div class="ak-caracteristics-table-container row">
                <div class="col-md-6 ak-primary-caracteristics">
                    <table border="1" class="ak-container ak-table ak-displaymode-alternative">
                    <thead>
                        <tr>
                            <th data-priority="4" colspan="2">Caractéristiques primaires</th>
                            <th data-priority="4">Base</th>
                            <th data-priority="4">Bonus</th>
                            <th data-priority="4">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="ak-bg-odd">
                            <td><span class="ak-icon-small ak-tx-vitality"></span></td>
                            <td><strong>Vitalité</strong></td>
                            <td><span class="ak-tooltip" title="Base (Naturel + Additionnel) : {{$character->Vitality + $character->PermanentAddedVitality}}">{{$character->Vitality}} + {{$character->PermanentAddedVitality}}</span></td>
                            <td>{{$character->countStuff($server,'vitality',['125','153'])}}</td>
                            <td>{{$character->Vitality + $character->PermanentAddedVitality + $character->countStuff($server,'vitality',['125','153'])}}</td>
                        </tr>
                        <tr class="ak-bg-even">
                            <td><span class="ak-icon-small ak-tx-wisdom"></span></td>
                            <td><strong>Sagesse</strong></td>
                            <td><span class="ak-tooltip" title="Base (Naturel + Additionnel) : {{$character->Wisdom + $character->PermanentAddedWisdom}}">{{$character->Wisdom}} + {{$character->PermanentAddedWisdom}}</span></td></span></td>
                            <td>{{$character->countStuff($server,'wisdom',['124','156'])}}</td>
                            <td>{{$character->Wisdom + $character->PermanentAddedWisdom + $character->countStuff($server,'wisdom',['124','156'])}}</td>
                        </tr>
                        <tr class="ak-bg-odd">
                            <td><span class="ak-icon-small ak-tx-strength"></span></td>
                            <td><strong>Force</strong></td>
                            <td><span class="ak-tooltip" title="Base (Naturel + Additionnel) : {{$character->Strength + $character->PermanentAddedStrength}}">{{$character->Strength}} + {{$character->PermanentAddedStrength}}</span></td>
                            <td>{{$character->countStuff($server,'strength',['118','157'])}}</td>
                            <td>{{$character->Strength + $character->PermanentAddedStrength + $character->countStuff($server,'strength',['118','157'])}}</td>
                        </tr>
                        <tr class="ak-bg-even">
                            <td><span class="ak-icon-small ak-tx-intelligence"></span></td>
                            <td><strong>Intelligence</strong></td>
                            <td><span class="ak-tooltip" title="Base (Naturel + Additionnel) : {{$character->Intelligence + $character->PermanentAddedIntelligence}}">{{$character->Intelligence}} + {{$character->PermanentAddedIntelligence}}</span></td>
                            <td>{{$character->countStuff($server,'intelligence',['126','155'])}}</td>
                            <td>{{$character->Intelligence + $character->PermanentAddedIntelligence + $character->countStuff($server,'intelligence',['126','155'])}}</td>
                        </tr>
                        <tr class="ak-bg-odd">
                            <td><span class="ak-icon-small ak-tx-chance"></span></td>
                            <td><strong>Chance</strong></td>
                            <td><span class="ak-tooltip" title="Base (Naturel + Additionnel) : {{$character->Chance + $character->PermanentAddedChance}}">{{$character->Chance}} + {{$character->PermanentAddedChance}}</span></td>
                            <td>{{$character->countStuff($server,'chance',['123','152'])}}</td>
                            <td>{{$character->Chance + $character->PermanentAddedChance + $character->countStuff($server,'chance',['123','152'])}}</td>
                        </tr>
                        <tr class="ak-bg-even">
                            <td><span class="ak-icon-small ak-tx-agility"></span></td>
                            <td><strong>Agilité</strong></td>
                            <td><span class="ak-tooltip" title="Base (Naturel + Additionnel) : {{$character->Agility + $character->PermanentAddedAgility}}">{{$character->Agility}} + {{$character->PermanentAddedAgility}}</span></td>
                            <td>{{$character->countStuff($server,'agility',['119','154'])}}</td>
                            <td>{{$character->Agility + $character->PermanentAddedAgility + $character->countStuff($server,'agility',['119','154'])}}</td>
                        </tr>
                        <tr class="ak-bg-odd">
                            <td><span class="ak-icon-small ak-tx-actionpoints"></span></td>
                            <td><strong>Points d'action (PA)</strong></td>
                            <td>{{$character->AP}}</td>
                            <td>{{$character->countStuff($server,'ap',['111','168'])}}</td>
                            <td>{{$character->AP + $character->countStuff($server,'ap',['111','168'])}}</td>
                        </tr>
                        <tr class="ak-bg-even">
                            <td><span class="ak-icon-small ak-tx-movementpoints"></span></td>
                            <td><strong>Points de mouvement (PM)</strong></td>
                            <td>{{$character->MP}}</td>
                            <td>{{$character->countStuff($server,'mp',['128','169'])}}</td>
                            <td>{{$character->MP + $character->countStuff($server,'mp',['128','169'])}}</td>
                        </tr>
                        <tr class="ak-bg-odd">
                            <td><span class="ak-icon-small ak-tx-initiative"></span></td>
                            <td><strong>Initiative</strong></td>
                            <td>{{$character->countBaseInitiative()}}</td>
                            <td>{{$character->countStuff($server,'initiative',['174','175'])}}</td>
                            <td>{{floor(($character->countBaseInitiative() + $character->countStuff($server,'initiative',['174','175'])) * ($character->actualLifePoints() / $character->maxLifePoints()))}}</td>
                        </tr>
                        <tr class="ak-bg-even">
                            <td><span class="ak-icon-small ak-tx-prospecting"></span></td>
                            <td><strong>Prospection</strong></td>
                            <td>{{$character->Prospection}}</td>
                            <td>{{$character->countStuff($server,'prospection',['176','177'])}}</td>
                            <td>{{$character->Prospection + $character->countStuff($server,'prospection',['176','177'])}}</td>
                        </tr>
                        <tr class="ak-bg-odd">
                            <td><span class="ak-icon-small ak-tx-range"></span></td>
                            <td><strong>Portée</strong></td>
                            <td>0</td>
                            <td>{{$character->countStuff($server,'range',['117','116'])}}</td>
                            <td>{{$character->countStuff($server,'range',['117','116'])}}</td>
                        </tr>
                        <tr class="ak-bg-even">
                            <td><span class="ak-icon-small ak-tx-invocation"></span></td>
                            <td><strong>Invocation</strong></td>
                            <td>1</td>
                            <td>{{$character->countStuff($server,'invocation',['182'])}}</td>
                            <td>{{1 + $character->countStuff($server,'invocation',['182'])}}</td>
                        </tr>
                    </tbody>
                    </table>
                </div>
                <div class="col-md-6 ak-secondary-caracteristics">
                    <table border="1" class="ak-container ak-table ak-displaymode-alternative">
                    <thead>
                        <tr>
                            <th data-priority="4" colspan="2">Caractéristiques secondaires</th>
                            <th data-priority="4">Base</th>
                            <th data-priority="4">Bonus</th>
                            <th data-priority="4">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="ak-bg-odd">
                            <td><span class="ak-icon-small ak-tx-attackmp"></span></td>
                            <td><strong>Retrait (PA)</strong></td>
                            <td>{{floor(($character->Wisdom + $character->PermanentAddedWisdom + $character->countStuff($server,'wisdom',['124','156'])) / 10)}}</td>
                            <td>{{$character->countStuff($server,'attackmp',['410','411'])}}</td>
                            <td>{{floor(($character->Wisdom + $character->PermanentAddedWisdom + $character->countStuff($server,'wisdom',['124','156'])) / 10) + $character->countStuff($server,'attackmp',['410','411'])}}</td>
                        </tr>
                        <tr class="ak-bg-even">
                            <td><span class="ak-icon-small ak-tx-dodgeap"></span></td>
                            <td><strong>Esquive (PA)</strong></td>
                            <td>{{floor(($character->Wisdom + $character->PermanentAddedWisdom + $character->countStuff($server,'wisdom',['124','156'])) / 10)}}</td>
                            <td>{{$character->countStuff($server,'dodgeap',['160','162'])}}</td>
                            <td>{{floor(($character->Wisdom + $character->PermanentAddedWisdom + $character->countStuff($server,'wisdom',['124','156'])) / 10) + $character->countStuff($server,'dodgeap',['160','162'])}}</td>
                        </tr>
                        <tr class="ak-bg-odd">
                            <td><span class="ak-icon-small ak-tx-attackap"></span></td>
                            <td><strong>Retrait (PM)</strong></td>
                            <td>{{floor(($character->Wisdom + $character->PermanentAddedWisdom + $character->countStuff($server,'wisdom',['124','156'])) / 10)}}</td>
                            <td>{{$character->countStuff($server,'attackap',['412','413'])}}</td>
                            <td>{{floor(($character->Wisdom + $character->PermanentAddedWisdom + $character->countStuff($server,'wisdom',['124','156'])) / 10) + $character->countStuff($server,'attackap',['412','413'])}}</td>
                        </tr>
                        <tr class="ak-bg-even">
                            <td><span class="ak-icon-small ak-tx-dodgemp"></span></td>
                            <td><strong>Esquive (PM)</strong></td>
                            <td>{{floor(($character->Wisdom + $character->PermanentAddedWisdom + $character->countStuff($server,'wisdom',['124','156'])) / 10)}}</td>
                            <td>{{$character->countStuff($server,'dodgemp',['161','163'])}}</td>
                            <td>{{floor(($character->Wisdom + $character->PermanentAddedWisdom + $character->countStuff($server,'wisdom',['124','156'])) / 10) + $character->countStuff($server,'dodgemp',['161','163'])}}</td>
                        </tr>
                        <tr class="ak-bg-odd">
                            <td><span class="ak-icon-small ak-tx-criticalhit"></span></td>
                            <td><strong>Coups critiques</strong></td>
                            <td>0</td>
                            <td>{{$character->countStuff($server,'criticalhit',['115','171'])}}</td>
                            <td>{{$character->countStuff($server,'criticalhit',['115','171'])}}</td>
                        </tr>
                        <tr class="ak-bg-even">
                            <td><span class="ak-icon-small ak-tx-heal"></span></td>
                            <td><strong>Soins</strong></td>
                            <td>0</td>
                            <td>{{$character->countStuff($server,'heal',['178','179'])}}</td>
                            <td>{{$character->countStuff($server,'heal',['178','179'])}}</td>
                        </tr>
                        <tr class="ak-bg-odd">
                            <td><span class="ak-icon-small ak-tx-tackle"></span></td>
                            <td><strong>Tacle</strong></td>
                            <td>{{floor(($character->Agility + $character->PermanentAddedAgility + $character->countStuff($server,'agility',['119','154'])) / 10)}}</td>
                            <td>{{$character->countStuff($server,'tackle',['753','755'])}}</td>
                            <td>{{floor(($character->Agility + $character->PermanentAddedAgility + $character->countStuff($server,'agility',['119','154'])) / 10) + $character->countStuff($server,'tackle',['753','755'])}}</td>
                        </tr>
                        <tr class="ak-bg-even">
                            <td><span class="ak-icon-small ak-tx-escape"></span></td>
                            <td><strong>Fuite</strong></td>
                            <td>{{floor(($character->Agility + $character->PermanentAddedAgility + $character->countStuff($server,'agility',['119','154'])) / 10)}}</td>
                            <td>{{$character->countStuff($server,'escape',['752','754'])}}</td>
                            <td>{{floor(($character->Agility + $character->PermanentAddedAgility + $character->countStuff($server,'agility',['119','154'])) / 10) + $character->countStuff($server,'escape',['752','754'])}}</td>
                        </tr>
                    </tbody>
                    </table>
                    <table border="1" class="ak-container ak-table ak-displaymode-alternative">
                    <thead>
                        <tr>
                            <th data-priority="4" colspan="2">Autres</th>
                            <th data-priority="4"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="ak-bg-odd">
                            <td><span class="ak-icon-small ak-tx-spellscroll"></span></td>
                            <td><strong>Points de sorts</strong></td>
                            <td>{{$character->SpellsPoints}}</td>
                        </tr>
                        <tr class="ak-bg-even">
                            <td><span class="ak-icon-small ak-tx-caracscroll"></span></td>
                            <td><strong>Points de carractéristiques</strong></td>
                            <td>{{$character->StatsPoints}}</td>
                        </tr>
                        <tr class="ak-bg-odd">
                            <td><span class="ak-icon-small ak-tx-stalskamas"></span></td>
                            <td><strong>Kamas</strong></td>
                            <td>{{$character->Kamas}}</td>
                        </tr>
                    </tbody>
                    </table>
                </div>
            </div>
            <div class="ak-caracteristics-table-container row">
                <div class="col-md-6 ak-primary-caracteristics">
                    <table border="1" class="ak-container ak-table ak-displaymode-alternative">
                    <thead>
                        <tr>
                            <th data-priority="4" colspan="2">Dommages</th>
                            <th data-priority="4"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="ak-bg-odd">
                            <td><span class="ak-icon-small ak-tx-damage"></span></td>
                            <td><strong>Dommages</strong></td>
                            <td>{{$character->countStuff($server,'damage',['112','145'])}}</td>
                        </tr>
                        <tr class="ak-bg-even">
                            <td><span class="ak-icon-small ak-tx-damagespercent"></span></td>
                            <td><strong>Puissance</strong></td>
                            <td>{{$character->countStuff($server,'damagespercent',['138','186'])}}</td>
                        </tr>
                        <tr class="ak-bg-odd">
                            <td><span class="ak-icon-small ak-tx-criticaldamage"></span></td>
                            <td><strong>Dommages critiques</strong></td>
                            <td>{{$character->countStuff($server,'criticaldamage',['418','419'])}}</td>
                        </tr>
                        <tr class="ak-bg-even">
                            <td><span class="ak-icon-small ak-tx-neutral"></span></td>
                            <td><strong>Neutre (fixe)</strong></td>
                            <td>{{$character->countStuff($server,'damageneutral',['430','431'])}}</td>
                        </tr>
                        <tr class="ak-bg-odd">
                            <td><span class="ak-icon-small ak-earth"></span></td>
                            <td><strong>Terre (fixe)</strong></td>
                            <td>{{$character->countStuff($server,'damageearth',['422','423'])}}</td>
                        </tr>
                        <tr class="ak-bg-even">
                            <td><span class="ak-icon-small ak-fire"></span></td>
                            <td><strong>Feu (fixe)</strong></td>
                            <td>{{$character->countStuff($server,'damagefire',['424','425'])}}</td>
                        </tr>
                        <tr class="ak-bg-odd">
                            <td><span class="ak-icon-small ak-water"></span></td>
                            <td><strong>Eau (fixe)</strong></td>
                            <td>{{$character->countStuff($server,'damagewater',['426','427'])}}</td>
                        </tr>
                        <tr class="ak-bg-even">
                            <td><span class="ak-icon-small ak-air"></span></td>
                            <td><strong>Air (fixe)</strong></td>
                            <td>{{$character->countStuff($server,'damageair',['428','429'])}}</td>
                        </tr>
                        <tr class="ak-bg-odd">
                            <td><span class="ak-icon-small ak-tx-return"></span></td>
                            <td><strong>Renvoi</strong></td>
                            <td>{{$character->countStuff($server,'damagereturn',['220'])}}</td>
                        </tr>
                        <tr class="ak-bg-even">
                            <td><span class="ak-icon-small ak-tx-trap"></span></td>
                            <td><strong>Pièges (fixe)</strong></td>
                            <td>{{$character->countStuff($server,'damagetrap',['225'])}}</td>
                        </tr>
                        <tr class="ak-bg-odd">
                            <td><span class="ak-icon-small ak-tx-trappercent"></span></td>
                            <td><strong>Pièges (Puissance)</strong></td>
                            <td>{{$character->countStuff($server,'damagetrappercent',['226'])}}</td>
                        </tr>
                        <tr class="ak-bg-even">
                            <td><span class="ak-icon-small ak-tx-push"></span></td>
                            <td><strong>Poussée</strong></td>
                            <td>{{$character->countStuff($server,'damagepush',['414','415'])}}</td>
                        </tr>
                        @if(config('dofus.details')[$server]->version != "2.10")
                            <tr class="ak-bg-even">
                                <td><span class="ak-icon-small"></span></td>
                                <td><strong>Sorts</strong></td>
                                <td>{{$character->countStuff($server,'damagespells',['2812','2813'])}}</td>
                            </tr>
                            <tr class="ak-bg-even">
                                <td><span class="ak-icon-small"></span></td>
                                <td><strong>Arme</strong></td>
                                <td>{{$character->countStuff($server,'damageweapon',['2808','2809'])}}</td>
                            </tr>
                            <tr class="ak-bg-even">
                                <td><span class="ak-icon-smal"></span></td>
                                <td><strong>Distance</strong></td>
                                <td>{{$character->countStuff($server,'damagedistance',['2804','2805'])}}</td>
                            </tr>
                            <tr class="ak-bg-even">
                                <td><span class="ak-icon-smallh"></span></td>
                                <td><strong>Mêlée</strong></td>
                                <td>{{$character->countStuff($server,'damagemelee',['2800','2801'])}}</td>
                            </tr>
                        @endif
                    </tbody>
                    </table>
                </div>
                <div class="col-md-6 ak-secondary-caracteristics">
                    <table border="1" class="ak-container ak-table ak-displaymode-alternative">
                    <thead>
                        <tr>
                            <th data-priority="4" colspan="2">Résistances</th>
                            <th data-priority="4"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="ak-bg-odd">
                            <td><span class="ak-icon-small ak-tx-neutral"></span></td>
                            <td><strong>Neutre (fixe)</strong></td>
                            <td>{{$character->countStuff($server,'resneutral',['244','249'])}}</td>
                        </tr>
                        <tr class="ak-bg-even">
                            <td><span class="ak-icon-small ak-tx-neutral"></span></td>
                            <td><strong>Neutre (%)</strong></td>
                            <td>{{$character->countStuff($server,'resneutralpercent',['214','219'])}}</td>
                        </tr>
                        <tr class="ak-bg-odd">
                            <td><span class="ak-icon-small ak-earth"></span></td>
                            <td><strong>Terre (fixe)</strong></td>
                            <td>{{$character->countStuff($server,'researth',['240','245'])}}</td>
                        </tr>
                        <tr class="ak-bg-even">
                            <td><span class="ak-icon-small ak-earth"></span></td>
                            <td><strong>Terre (%)</strong></td>
                            <td>{{$character->countStuff($server,'researthpercent',['210','215'])}}</td>
                        </tr>
                        <tr class="ak-bg-odd">
                            <td><span class="ak-icon-small ak-fire"></span></td>
                            <td><strong>Feu (fixe)</strong></td>
                            <td>{{$character->countStuff($server,'resfire',['243','248'])}}</td>
                        </tr>
                        <tr class="ak-bg-even">
                            <td><span class="ak-icon-small ak-fire"></span></td>
                            <td><strong>Feu (%)</strong></td>
                            <td>{{$character->countStuff($server,'resfirepercent',['213','218'])}}</td>
                        </tr>
                        <tr class="ak-bg-odd">
                            <td><span class="ak-icon-small ak-water"></span></td>
                            <td><strong>Eau (fixe)</strong></td>
                            <td>{{$character->countStuff($server,'reswater',['241','246'])}}</td>
                        </tr>
                        <tr class="ak-bg-even">
                            <td><span class="ak-icon-small ak-water"></span></td>
                            <td><strong>Eau (%)</strong></td>
                            <td>{{$character->countStuff($server,'reswaterpercent',['211','216'])}}</td>
                        </tr>
                        <tr class="ak-bg-odd">
                            <td><span class="ak-icon-small ak-air"></span></td>
                            <td><strong>Air (fixe)</strong></td>
                            <td>{{$character->countStuff($server,'resair',['242','247'])}}</td>
                        </tr>
                        <tr class="ak-bg-even">
                            <td><span class="ak-icon-small ak-air"></span></td>
                            <td><strong>Air (%)</strong></td>
                            <td>{{$character->countStuff($server,'resairpercent',['242','217'])}}</td>
                        </tr>
                        <tr class="ak-bg-odd">
                            <td><span class="ak-icon-small ak-tx-criticalhit"></span></td>
                            <td><strong>Coups critiques (fixe)</strong></td>
                            <td>{{$character->countStuff($server,'rescriticalhit',['420','421'])}}</td>
                        </tr>
                        <tr class="ak-bg-even">
                            <td><span class="ak-icon-small ak-tx-push"></span></td>
                            <td><strong>Poussée (fixe)</strong></td>
                            <td>{{$character->countStuff($server,'respush',['416','417'])}}</td>
                        </tr>
                        @if(config('dofus.details')[$server]->version != "2.10")
                            <tr class="ak-bg-even">
                                <td><span class="ak-icon-small"></span></td>
                                <td><strong>Distance</strong></td>
                                <td>{{$character->countStuff($server,'resdist',['2806','2807'])}}</td>
                            </tr>
                            <tr class="ak-bg-even">
                                <td><span class="ak-icon-small"></span></td>
                                <td><strong>Mêlée</strong></td>
                                <td>{{$character->countStuff($server,'resmelee',['2802','2803'])}}</td>
                            </tr>
                        @endif
                    </tbody>
                    </table>
                </div>
            </div>
            <div class="ak-caracteristics-table-container row">
                <div class="col-md-6 ak-primary-caracteristics">
                </div>
                <div class="col-md-6 ak-secondary-caracteristics">
                </div>
            </div>
            <small>Les caractéristiques pourraient ne pas être tout à fait exactes (version BETA !)</small>
        </div>
        </div>
        @endif
    </div>
</div>
@stop