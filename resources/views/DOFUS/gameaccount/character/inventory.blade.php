@extends('layouts.contents.default')
@include('layouts.menus.base')
@section('header')
{!! Html::style('css/directories.css') !!}
{!! Html::style('css/encyclopedia.css') !!}
<style>
    .ak-pagination{
        background-color: inherit;
    }
    .ak-item-nb{
    color:white;
    border-radius: 4px;
    font-size: 10px;
    font-size: 1rem;
    font-weight: bold;
    line-height: 12px;
    padding: 0.2em 0.3em;
    position: absolute;
    text-align: center;
    }
    </style>
@stop
@section('breadcrumbs')
{? $page_name = config('dofus.subtitle') ?}
{!! Breadcrumbs::render('home') !!}
@stop
@section('beta')
<div class="ak-beta"></div>
@endsection
@section('content')
<div class="ak-container ak-main-center ak-list-paginated">
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
      <div class="ak-container ak-panel ak-caracteristics-content">
        @if($settings->show_inventory)
         <div class="ak-panel-title">
            <span class="ak-panel-title-icon"></span>
            Inventaire <small>(Ne contient pas les ressources)</small>         
         </div>
         <div class="ak-panel-content">
            <div class="hidden-xs">
               <div class="ak-equipment-middle">
                  <div class="ak-character-equipments">
                     <div class="ak-chracter-illustration">
                        <div class="ak-set-illu">
                           <div class="ak-entitylook" alt="" style="background:url({{ DofusForge::player($character, $server, 'full', 1, 270, 361, 10) }}) top left;width:270px;height:361px"></div>
                        </div>
                     </div>
                  </div>
               </div>
               <div class="ak-equipment-bottom">
                @if(count($items) == 0)
                <p class="text-center alert alert-info">L'inventaire est vide.</p>
                @endif
                    @foreach($items as $k => $item)
                        @if($k == 0 || $k == 9 || $k == 18 || $k == 27 || $k == 36)
                        <div class="row">
                        @endif
                        <div class="ak-equipment-item ak-equipment-idol" id="">
                        <span class="ak-item-nb">{{$item->Stack}}</span>
                            <span class="ak-linker" data-hasqtip="linker_item_{{$item->Id}}"><img src="{{DofusForge::item($item->Template->IconId, 52)}}"></span><script type="application/json">{"iShowDelay":250,"iHideDelay":250,"linker-id":"linker_item_{{$item->Id}}","linker-path":"{{URL::route('linker.get', 'item')}}","linker-display-type":"TOOLTIP","linker-query-datas":{"l":"fr","id":"{{$item->Id}}","character":"{{$character->Id}}","server":"{{$server}}","position":"inventory","builder":""}}</script>                       
                        </div>
                        @if(($k == 0 && count($items) == 1) || $k == 8 || $k == 17 || $k == 26 || $k == 35 || $k == 44 || $k == (count($items) - 1))
                        </div>
                        @endif
                    @endforeach
               </div>
            </div>
            @if($items->links())
                <div class="text-center ak-pagination">
                    <nav>
                        {{ $items->links('pagination.default', ['target' => '.ak-list-paginated', 'settings' => '{"scroll":false}']) }}
                    </nav>
                </div>
            @endif
         </div>
         @endif
         @if($settings->show_idols)
            @if(config('dofus.details')[$server]->version != "2.10")
                <div class="ak-panel-title">
                    <span class="ak-panel-title-icon"></span>
                    Idoles            
                </div>
                <div class="ak-panel-content">
                    <div class="hidden-xs">
                    <div class="ak-equipment-bottom">
                    @if(count($idols) == 0)
                    <p class="text-center alert alert-info">Le personnage ne possède aucune idole.</p>
                    @endif
                    @foreach($idols as $k => $idol)
                                @if($k == 0 || $k == 9 || $k == 18 || $k == 27 || $k == 36)
                                <div class="row">
                                @endif
                                <div class="ak-equipment-item ak-equipment-idol" id="">
                                    <span class="ak-linker" data-hasqtip="linker_item_{{$idol->Id}}"><img src="{{DofusForge::item($idol->Template->IconId, 52)}}"></span><script type="application/json">{"iShowDelay":250,"iHideDelay":250,"linker-id":"linker_item_{{$idol->Id}}","linker-path":"{{URL::route('linker.get', 'item')}}","linker-display-type":"TOOLTIP","linker-query-datas":{"l":"fr","id":"{{$idol->Id}}","character":"{{$character->Id}}","server":"{{$server}}","position":"idols","builder":""}}</script>                       
                                </div>
                                @if(($k == 0 && count($idols) == 1) || $k == 8 || $k == 17 || $k == 26 || $k == 35 || $k == 44)
                                </div>
                                @endif
                        @endforeach
                    </div>
                    </div>
                </div>
            @endif
        @endif
      </div>
      
   </div>
</div>
@stop