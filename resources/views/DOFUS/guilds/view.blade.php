@extends('layouts.contents.default')
@include('layouts.menus.base')
@section('header')
    {!! Html::style('css/directories.css') !!}
    {!! Html::style('css/builders.css') !!}
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
      <h1><span class="ak-icon-big ak-guild"></span></a>{{$guild->Name}}</h1>
   </div>
   <div class="ak-page-menu">
      <nav class="ak-nav-expand ">
         <div class="ak-nav-expand-container">
            <ul class="ak-nav-links ak-nav-expand-links">
               <li class="on"><a href="{{route('guild.view', [$server, $guild->Id, $guild->Name])}}">Profil</a></li>
               <li><a href="{{route('guild.members', [$server, $guild->Id, $guild->Name])}}">Membres</a></li>
            </ul>
         </div>
      </nav>
   </div>
   <div class="ak-container ak-panel ak-directories-header-guild ak-nocontentpadding">
      <div class="ak-panel-content">
         <div class="ak-character-banner">
            <div class="ak-character-picture">
                <div class="ak-entitylook" alt="" style="background:url({{ $guild->perceptor('full', '1', '150','220', '10')}}) top left;width:150px;height:220px">
               </div>
            </div>
         </div>
         <div class="ak-directories-header ">
            <div class="ak-directories-icon">
               <div class="ak-entitylook" alt="" style="background:url({{ $guild->emblem('35','35')}}) top left;width:35px;height:35px">
               </div>
            </div>
            <div class="ak-directories-main-infos">
               <div class="ak-directories-property">
                  <span class="ak-directories-level">Niveau {{$guild->level()}}</span><br>
                  <span class="ak-directories-breed">{{ count($guild->members($server)->get()) }} Membres</span>
               </div>
               <div class="ak-directories-property ak-directories-property-server">
                  <span class="server">
                  <span class="server_4001"></span>
                  <span class="ak-directories-server-name">{{ucfirst($server)}}</span>
                  </span>
               </div>
            </div>
         </div>
      </div>
   </div>
   <div class="row ak-container ak-directories">
   <div class="ak-column ak-container col-md-4 ak-directories-left">
      <div class="ak-container ak-panel-stack ak-glue">
        <div class="ak-container ak-panel ak-guilds ak-guild-members large">
            <div class="ak-panel-title">
                <span class="ak-panel-title-icon"></span>
                Membres principaux            
            </div>
            <div class="ak-panel-content">
                @foreach($members as $member)
                    <a href="{{route('characters.view', [$server, $member->character($server)->Id, $member->character($server)->Name])}}" class="ak-guild-member">
                        <span class="ak-breed-icon breed{{$member->character($server)->Breed}}_{{$member->character($server)->Sex}}" title="{{$member->character($server)->classe()}}"></span>
                        <!--  <span class="ak-breed-look">--><!--</span>-->
                        <div class="ak-tooltip hidden">
                            <div class="ak-row ak-tooltip-members">
                            <span class="ak-cell ak-guildmember-breed">
                                <!--          <span class="ak-breed-look">--><!--</span>-->
                                <span class="ak-breed-icon breed9_0" title="{{$member->character($server)->classe()}}"></span>
                            </span>
                            <div class="ak-cell">
                                <div class="ak-member-name">{{$member->character($server)->Name}}</div>
                                <div class="ak-member-status">{{$member->rankName()}}</div>
                                <div class="ak-member-level">Niveau {{$member->character($server)->level($server)}}</div>
                            </div>
                            </div>
                        </div>
                        <script type="application/json">{"tooltip":{"style":{"classes":"ak-tooltip-white"}}}</script>
                    </a>
                @endforeach
                <div class="clearfix"></div>
            </div>
            <div class="ak-panel-bottomlink">
                <a class="ak-bottom-link" href="{{route('guild.members', [$server, $guild->Id, $guild->Name])}}">
                Voir tous les membres    </a>
            </div>
            </div>
      </div>
   </div>
   <div class="ak-column ak-container col-md-8 ak-directories-right">
    <div class="ak-container ak-panel-stack ak-directories">
        <div class="ak-container ak-panel">
            <div class="ak-panel-title">
                <span class="ak-panel-title-icon"></span>
                Dernières actions            
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
                                        <div class="ak-title">
                                        <span class="date">{{ Carbon\Carbon::parse($guild->CreationDate)->diffForHumans() }} :</span> Création de la guilde.                        
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
        </div>
    </div>
    </div>
</div>
</div>
@stop