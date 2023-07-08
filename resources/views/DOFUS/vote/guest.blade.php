@extends('layouts.contents.default')
@include('layouts.menus.base')

@section('header')
    {!! Html::style('css/vote.css') !!}
@stop

@section('breadcrumbs')
{? $page_name = 'Voter' ?}
{!! Breadcrumbs::render('page', $page_name) !!}
@stop

@section('content')
<div class="ak-container ak-main-center vote-rewards">
    <div class="ak-title-container">
        <h1 class="ak-return-link">
            <span class="ak-icon-big ak-character"></span></a> Vote pour le serveur
        </h1>
    </div>
    <div class="ak-container ak-panel panel-vote-link container-padding">
        <div class="ak-panel-content panel-main">
            <div class="row">
                <div class="col-sm-6">
                    <a href="{{ URL::to('http://www.rpg-paradize.com/?page=vote&vote=' . config("dofus.details")['sigma']->rpg) }}" target="_blank" class="btn btn-blok btn-lg btn-info">Voter</a>
                </div>
                <div class="col-sm-6">
                    <p>Vous n'êtes pas identifié, votre vote ne rapporteras aucun points. <a href="{{ URL::route('login') }}">S'identifier</a></p>
                </div>
            </div>
        </div>
    </div>
</div>
@stop
