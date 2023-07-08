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
               <li><a href="{{route('guild.view', [$server, $guild->Id, $guild->Name])}}">Profil</a></li>
               <li class="on"><a href="{{route('guild.members', [$server, $guild->Id, $guild->Name])}}">Membres</a></li>
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
                <div class="ak-directories-property ak-directories-property-creation-date">
                    <span class="ak-directories-creation-date">Création : Le {{ Carbon\Carbon::parse($guild->CreationDate)->format('d/m/Y') }}</span>
                </div>
            </div>
         </div>
      </div>
   </div>
   <div class="ak-container ak-panel ak-guilds">
        <div class="ak-panel-title">
            <span class="ak-panel-title-icon"></span>
            Membres            
        </div>
        <div class="ak-panel-content">
            <div class="ak-responsivetable-wrapper" style="overflow: hidden;">
                <table border="1" class="ak-container ak-table ak-responsivetable" style="white-space: nowrap; visibility: visible;">
                    <thead>
                    <tr>
                        <th data-priority="4">Nom</th>
                        <th data-priority="1">Classe</th>
                        <th data-priority="2">Niveau</th>
                        <th data-priority="3">Rang</th>
                    </tr>
                    </thead>
                    <tbody>
                        @foreach($members as $member)
                        <tr class="tr_class">
                            <td><span class="ak-breed-icon breed{{$member->character($server)->Breed}}_{{$member->character($server)->Sex}}" title="{{$member->character($server)->classe()}}"></span><a href="{{ URL::route('characters.view',[$server, $member->character($server)->Id, $member->character($server)->Name])}}">{{$member->character($server)->Name}}</a></td>
                            <td>{{$member->character($server)->classe()}}</td>
                            <td>{{$member->character($server)->level()}}</td>
                            <td>{{$member->rankName()}}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <div class="text-center ak-pagination">
                <nav>
                    {{ $members->links('pagination.default', ['target' => '.ak-guilds', 'settings' => '{"scroll":true}']) }}
                </nav>
            </div>
        </div>
    </div>
</div>
@stop