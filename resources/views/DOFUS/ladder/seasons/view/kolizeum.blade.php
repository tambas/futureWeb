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
        <h1><span class="ak-icon-big ak-ladder-kolizeum"></span></a> Saisons Kolizéum: {{$server}}</h1>
        <a href="{{ URL::route('ladder.kolizeum.seasons', [$server]) }}"class="ak-backlink-button">Retour à la liste des saisons</a>
    </div>

    @include('ladder.seasons.template.menu')

    <div class="ak-container ak-banner-header">
        <img class="img-responsive" src="{{ URL::asset('imgs/assets/header_koli3v3.jpg') }}">
        <div style="background-color:#a5a288;margin: 10px;">
            <center style="font-family:bebas_neueregular;font-size: 200%;color:#c52c02;">Saison {{$season->id}}: Terminée</center>
            <div>
                <center style="font-family:bebas_neueregular;font-size: 150%;">{{$season->begin->format('d M Y')}} - {{$season->end->format('d M Y')}}</center>
            </div>
        </div>
    </div>

    <div class="ak-responsivetable-wrapper">
        <table border="1" class="ak-ladder ak-container ak-table ak-responsivetable" style="white-space: nowrap; visibility: visible;">
            <thead>
                <tr class="ak-ajaxloader">
                    <th>#</th>
                    <th>Nom</th>
                    <th>Classe</th>
                    <th class="ak-center">Niveau</th>
                    <th>Cote</th>
                    <th class="ak-xp-total">Victoires</th>
                    <th class="ak-xp-total">Défaites</th>
                    <th class="ak-xp-total">Total</th>
                    <th class="ak-xp-total">Ratio</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($characters as $i => $character)
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
                    <td class="ak-class">{{ $character->classe() }}</td>
                    <td class="ak-center">{{ $character->level($server) }}</td>
                    <td>{{$character->ArenaRank}}</td>
                    <td class="ak-xp-total">{{$character->ArenaMatchsWon}}</td>
                    <td class="ak-xp-total">{{$character->ArenaMatchsCount - $character->ArenaMatchsWon}}</td>
                    <td class="ak-xp-total">{{$character->ArenaMatchsCount}}</td>
                    @php 
                        $ratio = App\Character::KolizeumRatio($character->ArenaMatchsCount,$character->ArenaMatchsWon);
                    @endphp
                    <td class="ak-xp-total ak-ratio-{{$ratio['type']}}">{{App\Helpers\Utils::format_price($ratio['value'])}} 
                        @if(is_numeric($ratio['value'])) % @endif</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@stop
