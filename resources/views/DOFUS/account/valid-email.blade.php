@extends('layouts.contents.default')
@include('layouts.menus.base')

@section('header')
    {!! Html::style('css/server.css') !!}
@stop

@section('breadcrumbs')
{? $page_name = 'Vérification email' ?}
{!! Breadcrumbs::render('account.page', $page_name) !!}
@stop

@section('content')
<div class="ak-title-container">
    <h1 class="ak-return-link">
        <a href=""><span class="ak-icon-big ak-support"></span></a> Vérification email
    </h1>
</div>
<div class="ak-container ak-panel ak-account-login">
    <div class="ak-panel-content">

        <div class="ak-login-page panel-main">
            <div style="text-align:center">
                <h2>Status de votre changement d'email</h2>
                Le lien de confirmation vous a été envoyé sur vos adresses email (l'ancienne et la nouvelle).<br>
                Vérifiez votre dossier de spam si vous ne le trouvez pas.
            </div>
        </div>
    </div>
</div>
<div class="ak-responsivetable-wrapper">
    <table border="1" class="ak-server-list ak-container ak-table ak-responsivetable" style="white-space: nowrap; visibility: visible;">
        <thead>
            <tr class="ak-ajaxloader">
                <th>Email</th>
                <th>Type</th>
                <th>Etat</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ $emailModification->email_old }}</td>
                <td>Ancienne adresse email</td>
                @if ($emailModification->token_old == null)
                <td class="ak-server-access yes">Vérifié</td>
                @else
                <td class="ak-server-access no">Non vérifié</td>
                @endif
            </tr>
            <tr>
                <td>{{ $emailModification->email_new }}</td>
                <td>Nouvelle adresse email</td>
                @if ($emailModification->token_new == null)
                <td class="ak-server-access yes">Vérifié</td>
                @else
                <td class="ak-server-access no">Non vérifié</td>
                @endif
            </tr>
        </tbody>
    </table>
</div>
@stop
