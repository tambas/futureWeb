@extends('layouts.contents.default')
@include('layouts.menus.base')

@section('header')
    {!! Html::style('css/vote.css') !!}
@stop

@section('breadcrumbs')
{? $page_name = 'Maintenance' ?}
{!! Breadcrumbs::render('gameaccount.page', $page_name, [$account->server, $account->Id]) !!}
@stop

@section('content')
<div class="ak-container ak-main-center">
    <div class="ak-title-container ak-backlink">
        <h1><span class="ak-icon-big ak-bank"></span></a> Maintenance</h1>
        <a href="{{ URL::route('gameaccount.view', [$account->server, $account->Id]) }}" class="ak-backlink-button">Retour à mon compte</a>
    </div>

    <div class="ak-container ak-panel panel-vote-link container-padding">
        <div class="ak-panel-content panel-main">
            <div class="row">
                <div class="col-sm-12">
                    <center>
                        <img src="{{ URL::asset('imgs/assets/maintenance.png') }}" alt="Maintenance">
                        <br><br>
                        <h3>Serveur en maintenance</h3>
                        Le transfert d'Ogrines/Cadeaux est actuellement<br>
                        indisponible suite à une maintenance en cours du serveur.<br>
                        Merci de ressayer ulterieurement.<br><br>
                        <i>L'équipe {{ config('dofus.title') }}</i>.
                    </center>
                </div>
            </div>
        </div>
    </div>
</div>
@stop
