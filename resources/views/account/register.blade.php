@extends('layouts.boostrap')

@section('breadcrumbs')
<h1>Rejoindre la communauté</h1>
{!! Breadcrumbs::render('page', 'Rejoindre la communauté') !!}
@stop

@section('page')

<div class="col-md-6 col-md-offset-1 col-xs-12">

    @if($errors->has())

    <div class="panel panel-danger">
        <div class="panel-heading">Erreurs</div>
        <div class="panel-body">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{!! $error !!}</li>
                @endforeach
            </ul>
        </div>
    </div>

    @endif

    <div class="panel panel-default">

        <div class="panel-heading">Création d'un compte</i></div>
        <div class="panel-body">

            <p>Déjà inscrit? <b><a href="{{ route('login') }}">Connectez-vous</a></b> à votre compte.</p>

            {{ Form::open(['class' => 'form-horizontal', 'style' => 'padding: 15px;']) }}

                <div class="form-group @if ($errors->has('firstname')) has-error @endif">
                    <label for="firstname">Votre prénom :</label>
                    <span class="help-block">Cette information restera confidentielle.</span>
                    <div class="input-group">
                        <span class="input-group-addon"><i class="fa fa-user" aria-hidden="true"></i></span>
                        <input type="text" class="form-control input-lg" name="firstname" id="firstname" placeholder="Prénom" value="{{ old('firstname') }}" autofocus required>
                    </div>
                </div>

                <div class="form-group @if ($errors->has('lastname')) has-error @endif">
                    <label for="lasrtname">Votre nom :</label>
                    <span class="help-block">Cette information restera confidentielle.</span>
                    <div class="input-group">
                        <span class="input-group-addon"><i class="fa fa-user" aria-hidden="true"></i></span>
                        <input type="text" class="form-control input-lg" name="lastname" id="lastname" placeholder="Nom" value="{{ old('lastname') }}" required>
                    </div>
                </div>

                <div class="form-group @if ($errors->has('email')) has-error @endif">
                    <label for="email">Votre adresse e-mail :</label>
                    <span class="help-block">Vous permettra de vous connecter au site.</span>
                    <div class="input-group">
                        <span class="input-group-addon"><i class="fa fa-envelope" aria-hidden="true"></i></span>
                        <input type="email" class="form-control input-lg" name="email" id="email" placeholder="Adresse E-mail" value="{{ old('email') }}" required>
                    </div>
                </div>

                <div class="row" >

                    <label for="email">Votre mot de passe :</label>
                    <span class="help-block">Vous permettra de vous connecter au site.</span>

                    <div class="col-md-6 col-xs-12" style="margin-left: -15px;">
                        <div class="input-group @if ($errors->has('password')) has-error @endif">
                            <span class="input-group-addon"><i class="fa fa-key" aria-hidden="true"></i></span>
                            <input type="password" class="form-control input-lg" name="password" placeholder="Mot de passe" required>
                        </div>
                    </div>

                    <div class="col-md-6 col-xs-12 pull-right" style="margin-right: -15px;">
                        <div class="input-group @if ($errors->has('passwordConfirmation')) has-error @endif">
                            <span class="input-group-addon"><i class="fa fa-key" aria-hidden="true"></i></span>
                            <input type="password" class="form-control input-lg" name="passwordConfirmation" placeholder="Confirmation" required>
                        </div>
                    </div>

                </div>

                <hr>

                <div style="margin-left: -15px;">
                    {!! Recaptcha::render(['lang' => 'fr']) !!}
                </div>

                <div class="form-group @if ($errors->has('cgu')) has-error @endif">
                    <div class="checkbox">
                        <label><input type="checkbox" name="cgu" required> J'ai lu et j'accepte les <b><a href="">Conditions d'utilisation</a></b></label>
                    </div>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary"><i class="fa fa-user-plus"></i> <b>S'inscrire</b></button>

                </div>

            {{ Form::close() }}

        </div>
    </div>

</div>

<div class="col-md-4 col-xs-12">

    <div class="panel panel-default">

        <div class="panel-heading">Création d'un compte de jeu</div>
        <div class="panel-body">
            <p>Sur Arkalys nous faisons une différence entre un compte web et un compte de jeu.</p>
            <p>Un compte web va permettre de créer et gérer les comptes jeu.</p>
            <p>Un compte web peux contenir au maximum 4 compte de jeu.</p>
            <p><a href="">Accéder au <b>Gestionnaire de Comptes</b></a></p>
        </div>
    </div>

    <div class="panel panel-primary">

        <div class="panel-heading">Téléchargement du jeu</div>
        <div class="panel-body">
            <p>La dernière étape pour accéder au serveur est le téléchargement et l'installation du jeu.</p>
            <p>Pour ce faire nous avons développé un launcher qui va se charger de télécharger et d'assurer la mise à jour des fichiers de votre installation.</p>
        </div>
    </div>

</div>
@stop