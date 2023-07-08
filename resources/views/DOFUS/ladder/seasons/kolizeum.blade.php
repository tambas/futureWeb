@extends('layouts.contents.default')
@include('layouts.menus.base')

@section('header')
    {!! Html::style('css/ladder.css') !!}
    {!! Html::style('css/ladder-tiny.css') !!}
@stop

@section('breadcrumbs')
{? $page_name = 'Kolizéum' ?}
{!! Breadcrumbs::render('ladder.page', $page_name, $server) !!}
@stop

@section('content')
<div class="ak-container ak-main-center">
    <div class="ak-title-container ak-backlink">
        <h1><span class="ak-icon-big ak-ladder-success"></span></a> Saisons {{ $page_name }}: {{$server}}</h1>
        <a href="{{ URL::route('ladder.kolizeum', [$server]) }}"class="ak-backlink-button">Retour à la saison en cours</a>
    </div>

    <div class="ak-container ak-banner-header">
        <img class="img-responsive" src="{{ URL::asset('imgs/assets/header_koli3v3.jpg') }}">

    </div>
</div>
<div class="ak-content-sections">
   <div class="row">
    @foreach($oldSeasons as $k => $oldSeason)
      <div class="col-sm-6 col-md-6">
         <div class="ak-section-kolizeum ak-block-hp-ladder">
            <div class="ak-section-title">
               <a href="{{URL::route('ladder.kolizeum.season', [$server, $oldSeason->id])}}""><span class="ak-text ">Saison {{$oldSeason->id}} <small style="float:right;"> {{$oldSeason->begin->format('d M Y')}} - {{$oldSeason->end->format('d M Y')}}</small></span></a>
            </div>
            <a class="ak-section-illu" href="{{URL::route('ladder.kolizeum.season', [$server, $oldSeason->id])}}"" style="background:url({{URL::asset($oldSeason->image)}})"></a>
            <div class="ak-section-desc">
               <div class="ak-responsivetable-wrapper" style="overflow: hidden;">
                    <table border="1" class="ak-ladder ak-ladder-section ak-container ak-table ak-responsivetable" style="white-space: nowrap; visibility: visible;">
                        @foreach ($characters[$oldSeason->id][0] as $i => $character)
                        <tr class="@if ($i == 0) ak-first-ladder @elseif ($i %2 == 0) ak-bg-even @else ak-bg-odd @endif">
                            <td class="ak-rank">
                                @if ($i < 3)
                                <span class="ak-icon-position ak-position-{{ $i+1 }}">{{ $i+1 }}</span>
                                @else
                                {{ $i+1 }}
                                @endif
                            </td>
                            <td class="ak-name">
                                <span class="ak-breed-icon breed{{ $character->Breed }}_{{ $character->Sex }}"></span>
                                {{ Html::link(route('characters.view', [$server, $character->Id, $character->Name]), $character->Name) }}
                            </td>
                            <td class="ak-nb">{{$character->ArenaRank}}</td>
                        </tr>
                        @endforeach
                    </table>
               </div>
            </div>
            <div class="ak-section-link-block">
               <a href="{{URL::route('ladder.kolizeum.season', [$server, $oldSeason->id])}}" class="btn btn-primary btn-lg">Voir le classement complet</a>
            </div>
         </div>
      </div>
    @endforeach
   </div>
</div>
@stop
