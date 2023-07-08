@extends('layouts.contents.default')
@include('layouts.menus.base')
@section('header')
    {!! Html::style('css/directories.css') !!}
    {!! Html::style('css/builders.css') !!}
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
         <div class="ak-character-banner">
            <div class="ak-character-picture">
               <div class="ak-entitylook" alt="" style="background:url({{ DofusForge::player($character, $server, 'full', 1, 150, 220, 10) }}) top left;width:150px;height:220px">
               </div>
            </div>
            @if($character->ornamentActive($server))
            <a class="ak-character-picture-show on"></a>
            <a class="ak-character-ornament-show"></a>
            <div class="ak-character-ornament ornament-{{$character->ornamentActive($server)->AssetId}}">
               <span>{{$character->Name}}</span>
            </div>
            @endif
         </div>
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
                <div class="ak-directories-property ak-directories-property-creation-date">
                    <span class="ak-directories-creation-date">Création : Le {{$character->CreationDate->format('d/m/Y')}}</span>
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
   <div class="row ak-container ak-directories">
      <div class="ak-column ak-container col-md-4 ak-directories-left">
         <div class="ak-container ak-panel-stack ak-glue">
             @if($character->guild($server))
            <div class="ak-container ak-panel ak-belong-guild">
               <div class="ak-panel-title">
                  <span class="ak-panel-title-icon"></span>
                  Appartient à la guilde
               </div>
               <div class="ak-panel-content">
                  <div class="ak-character-illu">
                     <div class="ak-emblem" style="background:url({{ URL::asset($character->guild($server)->emblem(110,110)) }}) center center;width:110px;height:110px">
                     </div>
                  </div>
                  <a href="{{ route('guild.view', [$server, $character->guild($server)->Id, $character->guild($server)->Name])}}" class="ak-infos-guildname">{{$character->guild($server)->Name}}</a><br>
                  <span class="ak-infos-guildlevel">Niveau {{$character->guild($server)->level()}}</span> - <span class="ak-infos-guildmembers">{{count($character->guild($server)->members($server)->get())}} membres</span>
               </div>
            </div>
            @endif
            @if($settings->show_alignment)
            <div class="ak-container ak-panel large ak-infos-alignement ak-{{$character->alignment()->sideFrench()}}">
                <div class="ak-panel-title">
                    <span class="ak-panel-title-icon"></span>Alignement
                </div>
                <div class="ak-panel-content">
                    <div class="ak-alignment ak-alignment-{{$character->alignment()->sideFrench()}}" style="@if($character->AlignmentSide != 0)background:url({{ URL::asset('imgs/assets/characters/wings/wings_'.$character->alignment()->sideFrench().'_'.$character->alignment()->level().'.png')}})@else background:url({{ URL::asset('imgs/assets/characters/wings/neutre.png')}})@endif 0 center no-repeat;">
                        <span class="ak-alignment-name">{{$character->alignment()->sideFrench()}} @if(!empty($character->alignment()->level()))- GRADE {{$character->alignment()->level()}}@endif</span>
                    </div>
                </div>
            </div>
            @endif
         </div>
      </div>
      <div class="ak-column ak-container col-md-8 ak-directories-right">
         <div class="ak-container ak-panel-stack ak-directories ak-glue">
             @if($settings->history)
            <div class="ak-container ak-panel ak-character-presentation">
                <div class="ak-panel-title">
                    <span class="ak-panel-title-icon"></span>Présentation
                    <span class="ak-presentation-date">Dernière mise à jour le @if($settings->historyDate){{$settings->historyDate}}@endif</span>            
                </div>
                <div class="ak-panel-content">{{$settings->history}}</div>
            </div>
            @endif
            <div class="ak-container ak-panel">
               <div class="ak-panel-title">
                  <span class="ak-panel-title-icon"></span>Dernière connexion            
               </div>
               <div class="ak-panel-content">
                  <div class="ak-character-actions">
                     <div class="ak-actions">
                        <div class="ak-actions-list">
                           <div class="ak-container ak-content-list ">
                              <div class="ak-list-element">
                                 <div class="ak-main">
                                    <div class="ak-main-content ">
                                       <div class="ak-content">
                                          <div class="ak-title">{{$character->Name}}. Dernière connexion : {{$character->LastUsage->diffForHumans()}}                            </div>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
            @if($settings->show_ladder)
            <div class="ak-container ak-panel ak-nocontentpadding ak-character-ranking">
                <div class="ak-panel-title">
                    <span class="ak-panel-title-icon"></span>
                    Statistiques            
                </div>
                <div class="ak-panel-content">
                    <div class="ak-total-xp">Total XP : <span>{{ Utils::format_price($character->Experience, ' ') }}</span></div>
                    <div class="ak-total-xp">Prestige : <span>{{ $character->PrestigeRank }}</span></div>
                    <div class="ak-total-honor">Points d'Honneur : <span>{{ Utils::format_price($character->Honor, ' ') }}</span></div>
                    <div class="ak-responsivetable-wrapper" style="overflow: hidden;">
                        <table border="1" class="ak-container ak-table ak-responsivetable" style="white-space: nowrap; visibility: visible;">
                            <thead>
                            <tr>
                                <th>Classement</th>
                                <th>Xp</th>
                                <th>PvP</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr class="ak-bg-odd">
                                <td class="ak-first-col">Général</td>
                                <td>{{$character->position('xp', 'all', $server)}}</td>
                                <td>{{$character->position('pvp', 'all', $server)}}</td>
                            </tr>
                            <tr class="ak-bg-odd">
                                <td class="ak-first-col">Parmis tous les ({{$character->classe()}})</td>
                                <td>{{$character->position('xp', 'breed', $server)}}</td>
                                <td>{{$character->position('pvp', 'breed', $server)}}</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <br>
                    <div class="ak-total-kolizeum">Cote Kolizeum : <span>{{$character->ArenaRank}}</span></div>
                    <div class="ak-total-kolizeum-max">Cote Kolizeum Maximale : <span>{{$character->ArenaMaxRank}}</span></div>
                    <div class="ak-responsivetable-wrapper" style="overflow: hidden;">
                        <table border="1" class="ak-container ak-table ak-responsivetable" style="white-space: nowrap; visibility: visible;">
                            <thead>
                            <tr>
                                <th>Kolizeum</th>
                                <th>Nombre</th>
                                <th>Gagnés</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr class="ak-bg-odd">
                                <td class="ak-first-col">Matchs du jour</td>
                                <td>{{$character->ArenaDailyMatchsCount}}</td>
                                <td>{{$character->ArenaDailyMatchsWon}}</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif
         </div>
      </div>
   </div>
</div>
@stop
