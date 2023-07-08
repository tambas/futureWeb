@extends('layouts.contents.default')
@include('layouts.menus.base')

@section('breadcrumbs')
{? $page_name = 'Compte de Jeu' ?}
{!! Breadcrumbs::render('gameaccount', [$account->server, $account->Id]) !!}
@stop

@section('content')
<div class="ak-container ak-main-center">
    <div class="ak-title-container ak-backlink">
        <h1><span class="ak-icon-big ak-bank"></span></a> Compte de Jeu - ( {{$account->Nickname}} )</h1>
        <a href="{{ URL::route('profile') }}" class="ak-backlink-button">Retour à mon compte</a>
    </div>

    <div class="ak-container ak-panel-stack">
        <div class="ak-container ak-panel ak-glue">
            <div class="ak-panel-content">
                <div class="panel-main profile ak-form">
                    @if($account->isBanned())
                        <div class="alert alert-danger">
                            <span class="center-block text-center"><strong>Votre compte est banni @if($account->BanEndDate)jusqu'au {{ $account->BanEndDate->format('d/m/Y à H\hi') }} @else définitivement. @endif</strong></span>
                            @if($account->BanReason)<br><strong><u>Raison:</u></strong> {{$account->BanReason}}@endif
                        </div>
                    @endif
                    <div class="code">
                        Code : <span>{{ $account->SecretAnswer }}</span>
                    </div>
                    <b>Identifiant</b>: {{ $account->Login }}<br>
                    <b>Pseudo</b>: {{ $account->Nickname }}<br>
                    <b>Serveur</b>: {{ ucfirst($account->server) }}<br><br>
                    <b>Ogrines</b>:
                    <div class="form-group">
                        <div class="input-group">
                            <span class="input-group-addon"><span class="ak-icon-small ak-ogrines-icon"></span></span>
                            <input type="text" class="form-control" value="{{ Utils::format_price($account->points()) }}" id="ogrinesGame" readonly />
                        </div>
                    </div>
                    <a href="{{ URL::route('gameaccount.transfert', [$account->server, $account->Id]) }}" class="btn btn-default btn-sm">Transferer des Ogrines <span class="ak-icon-small ak-ogrines-icon"></span></a>
                    <a href="{{ URL::route('gameaccount.gifts', [$account->server, $account->Id]) }}" class="btn btn-default btn-sm">Transferer des Cadeaux <span class="ak-icon-small ak-gifts-icon"></span></a>
                    <a href="{{ URL::route('gameaccount.jetons', [$account->server, $account->Id]) }}" class="btn btn-default btn-sm">Convertir des Jetons <span class="ak-icon-small ak-votes-icon"></span></a>
                    <br><br>
                    <a href="{{ URL::route('gameaccount.edit', [$account->server, $account->Id]) }}" class="btn btn-default btn-sm">Changer de mot de passe</a>
                    <div class="clearfix"></div>
                </div>
            </div>
            <div class="ak-panel-title">
                <span class="ak-panel-title-icon"></span> Mes personnages
            </div>
        </div>
    </div>

    <table class="ak-ladder ak-container ak-table ak-responsivetable ak-characterslist" style="white-space: nowrap; visibility: visible;">
        <tr>
            <th class="ak-center"></th>
            <th>Pseudo</th>
            <th>Classe</th>
            <th class="ak-center">Niveau</th>
            <th class="ak-center" style="width: 200px;">Actions</th>
        </tr>
        @foreach ($account->characters() as $character)
            <tr>
                <td class="ak-rank"></td>
                <td class="ak-name">
                    <span class="ak-breed-icon breed{{ $character->Breed }}_{{ $character->Sex }}"></span>
                    @if(($character->LastUsage > \Carbon\Carbon::today()->subMonths(6)->toDateString()) && ($character->level($character->server) >= 20))
                    <a href="{{ URL::route('characters.view', [$account->server, $character->Id, $character->Name]) }}">{{ $character->Name }}</a>
                    @else
                    {{ $character->Name }}
                    @endif
                </td>
                <td class="ak-class">{{ $character->classe() }}</td>
                <td class="ak-center">{{ $character->level($character->server) }}</td>
                <td class="ak-center">
                    @if(($character->LastUsage > \Carbon\Carbon::today()->subMonths(6)->toDateString()) && ($character->level($character->server) >= 20))
                    <a href="{{ URL::route('characters.view', [$account->server, $character->Id, $character->Name]) }}"><span class="ak-icon-small ak-filter ak-tooltip" title="Consulter"></span>
                    </a>
                    @else
                    <span class="ak-character-info" style="font-weight:bold; font-size:1.3rem;"></span>
                                <div class="ak-tooltip hidden ak-tooltip-blue"> Pour avoir une page perso, votre personnage doit être niveau 20 minimum et s'être connecté en jeu il y a moins de 6 mois. </div><script type="application/json">{"tooltip":{"style":{"classes":"ak-tooltip-blue"}}}</script></div>
                    @endif
                </td>
            </tr>
        @endforeach
    </table>

    <div class="ak-container ak-panel-stack">
        <div class="ak-container ak-panel ak-glue">
            <div class="ak-panel-title">
                <span class="ak-panel-title-icon"></span> Mes personnages supprimés
            </div>
        </div>
    </div>

    <table class="ak-ladder ak-container ak-table ak-responsivetable" style="white-space: nowrap; visibility: visible;">
        <tr>
            <th class="ak-center"></th>
            <th>Pseudo</th>
            <th>Classe</th>
            <th class="ak-center">Niveau</th>
            <th class="ak-center" style="width: 200px;">Actions</th>
        </tr>
        @foreach ($account->DeletedCharacters() as $characterDeleted)
            <tr>
                <td class="ak-rank"></td>
                <td class="ak-name">
                    <span class="ak-breed-icon breed{{ $characterDeleted->Breed }}_{{ $characterDeleted->Sex }}"></span>
                    <a href="{{ URL::route('characters.recover', [$account->server, $account->Id, $characterDeleted->Id]) }}">{{ $characterDeleted->Name }}</a>
                </td>
                <td class="ak-class">{{ $characterDeleted->classe() }}</td>
                <td class="ak-center">{{ $characterDeleted->level() }}</td>
                <td class="ak-center"><a href="{{ URL::route('characters.recover', [$account->server, $account->Id, $characterDeleted->Id]) }}"><span class="fa fa-undo"></span> Restaurer</a></td>
            </tr>
        @endforeach
    </table>

    <div class="ak-container ak-panel-stack">
        <div class="ak-container ak-panel ak-glue">
            <div class="ak-panel-title">
                  <span class="ak-panel-title-icon"></span> Mes 10 derniers transferts
            </div>
        </div>
    </div>

    <table class="ak-container ak-table">
        <tr>
            <th class="ak-center">#</th>
            <th>Date du transfert</th>
            <th>Objet</th>
            <th>Statut</th>
        </tr>
        @foreach ($account->transferts(10) as $transfert)
        <tr>
            <td class="ak-center">{{ $transfert->id }}</td>
            <td>{{ $transfert->created_at->format('d/m/Y H:i:s') }}</td>
            @if ($transfert->type == 'Ogrines' || $transfert->type == '')
            <td>{{ Utils::format_price($transfert->amount, ' ') }} Ogrines <span class="ak-icon-small ak-ogrines-icon"></span></td>
            @elseif ($transfert->item())
            <td>{{ Utils::format_price($transfert->amount, ' ') }} {{ $transfert->item()->name($transfert->server) }} <img src="{{ $transfert->item()->image() }}" width="25" height="25"></td>
            @else
            <td>{{ Utils::format_price($transfert->amount, ' ') }} {{ $transfert->type }}
            @endif
            <td>{{ Utils::transfert_status($transfert->state) }}</td>
        </tr>
        @endforeach
    </table>
</div>
@stop
