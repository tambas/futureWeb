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
            </div>
         </div>
      </div>
   </div>
   <div class="ak-container ak-panel">
   <div class="ak-panel-title">
      <span class="ak-panel-title-icon"></span>
      Options            
   </div>
   <div class="ak-panel-content">
       {!! Form::open(['route' => ['characters.settings', $server, $character->Id,$character->Name], 'class' => 'ak-form']) !!}
         <div class="form-group ak-form-group">
            Les modifications effectuées peuvent mettre plusieures heures à apparaître.  
         </div>
         <fieldset>
            <legend>Profil</legend>
            <div class="form-group ak-form-group">
               <div class="checkbox">
                  <label>
                  <input type="checkbox" name="show_alignment" value="1" @if($settings->show_alignment) checked="checked" @endif>
                  Afficher l'alignement        </label>
               </div>
               <div class="checkbox">
                  <label>
                  <input type="checkbox" name="show_ladder" value="1" @if($settings->show_ladder) checked="checked" @endif>
                  Afficher le ladder        </label>
               </div>
            </div>
         </fieldset>
         <fieldset>
            <legend>Caractéristiques</legend>
            <div class="form-group ak-form-group">
               <div class="checkbox">
                  <label>
                  <input type="checkbox" name="show_equipments" value="1" @if($settings->show_equipments) checked="checked" @endif>
                  Afficher les équipements</label>
               </div>
               <div class="checkbox">
                  <label>
                  <input type="checkbox" name="show_spells" value="1" @if($settings->show_spells) checked="checked" @endif>
                  Afficher les sorts</label>
               </div>
               <div class="checkbox">
                  <label>
                  <input type="checkbox" name="show_caracteristics" value="1" @if($settings->show_caracteristics) checked="checked" @endif>
                  Afficher les caractéristiques        </label>
               </div>
            </div>
         </fieldset>
         <fieldset>
            <legend>Inventaire</legend>
            <div class="form-group ak-form-group">
               <div class="checkbox">
                  <label>
                  <input type="checkbox" name="show_inventory" value="1" @if($settings->show_inventory) checked="checked" @endif>
                  Afficher l'inventaire</label>
               </div>
               <div class="checkbox">
                  <label>
                  <input type="checkbox" name="show_idols" value="1" @if($settings->show_idols) checked="checked" @endif>
                  Afficher les idoles</label> <small>(Ne fonctionne pas sur Sigma)</small>
               </div>
            </div>
         </fieldset>
         <fieldset>
            <legend>Ecrire l'histoire de mon personnage</legend>
            <div class="form-group @if ($errors->has('history')) has-error @endif">
               <textarea class="form-control" rows="3" name="history" placeholder="Présentez-vous">@if($settings->history){{$settings->history}}@endif</textarea>
                @if ($errors->has('history')) <label class="error control-label">{{ $errors->first('history') }}</label> @endif
            </div>
         </fieldset>
         <div class="form-group text-center">
            <input class="btn btn-primary btn-lg" type="submit" value="Enregistrer les modifications">
         </div>
      {!! Form::close() !!}
   </div>
</div>
</div>
@stop