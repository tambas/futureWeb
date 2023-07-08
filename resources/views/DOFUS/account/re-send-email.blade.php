@extends('layouts.contents.default')
@include('layouts.menus.base')

@section('breadcrumbs')
{? $page_name = 'Email d\'activation' ?}
{!! Breadcrumbs::render('page', $page_name) !!}
@stop

@section('content')
<div class="ak-title-container">
    <h1 class="ak-return-link">
        <a href=""><span class="ak-icon-big ak-support"></span></a> Email d'activation
    </h1>
</div>
<div class="ak-container ak-panel ak-account-login">
    <div class="ak-panel-content">

        <div class="ak-login-page panel-main">
            <div style="text-align:center">
                <h2>Votre compte n'est pas activé !</h2>
                Le lien d'activation vous a été envoyé par email.<br>
                Vérifiez votre dossier de spam si vous ne le trouvez pas.<br>
                Vous pouvez également envoyer un nouvel email d'activation.
            </div>
            <div class="row">
                <div class="col-sm-6 col-sm-offset-3">
                    <div class="">
                        <div class="ak-login-block">
                            <div class="ak-container">
                                <div class="ak-form">
                                    {!! Form::open(['route' => 're-send-email']) !!}
                                        <input type="hidden" name="email" value="{{ $user->email }}">
                                        <input type="submit" role="button" class="btn btn-primary btn-lg btn-block" value="Renvoyer l'email">
                                    {!! Form::close() !!}
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
