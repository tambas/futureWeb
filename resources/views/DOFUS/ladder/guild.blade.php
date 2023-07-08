@extends('layouts.contents.default')
@include('layouts.menus.base')

@section('header')
    {!! Html::style('css/ladder.css') !!}
    {!! Html::style('css/ladder-tiny.css') !!}
@stop

@section('breadcrumbs')
{? $page_name = 'Guilde' ?}
{!! Breadcrumbs::render('ladder.page', $page_name, $server) !!}
@stop

@section('content')
<div class="ak-container ak-main-center">
    <div class="ak-title-container">
        <h1><span class="ak-icon-big ak-ladder-alliance"></span></a> Classement {{ $page_name }}</h1>
    </div>

    @include('ladder.servers')
    @include('ladder.menu')

    <div class="ak-container ak-banner-header">
        <img class="img-responsive" src="{{ URL::asset('imgs/assets/header_guild.jpg') }}">
    </div>

    <div class="ak-responsivetable-wrapper">
        <table border="1" class="ak-ladder ak-container ak-table ak-responsivetable" style="white-space: nowrap; visibility: visible;">
            <thead>
                <tr class="ak-ajaxloader">
                    <th style="width:48px;">#</th>
                    <th style="width:100px;"></th>
                    <th style="width:48px;"></th>
                    <th>Nom</th>
                    <th class="ak-center">Niveau</th>
                    <th class="ak-xp-total">Membres</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($guilds as $i => $guild)
                <tr class="@if ($i == 0) ak-first-ladder @elseif ($i %2 == 0) ak-bg-even @else ak-bg-odd @endif">
                    <td class="ak-rank">
                        @if ($i < 3)
                        <span class="ak-icon-position ak-position-{{ $i+1 }}">{{ $i+1 }}</span>
                        @else
                        {{ $i+1 }}
                        @endif
                    </td>
                    <td></td>
                    <td class="ak-center"><div class="ak-emblem" style="background:url({{ URL::asset($guild->emblem(48,48)) }}) center center;width:48px;height:48px"></div></td>
                    <td class="ak-name"><a href="{{route('guild.view', [$server, $guild->Id, $guild->Name])}}">{{ $guild->Name }}</a></td>
                    <td class="ak-center">{{ $guild->level() }}</td>
                    <td class="ak-xp-total">{{ count($guild->members($server)->get()) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@stop
