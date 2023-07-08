@extends('layouts.emails.template')

@section('content')
<b>Bonjour {{ $user->firstname }} {{ $user->lastname }} !</b>
<br><br>
Vous venez de demander une réinitialisation de votre mot de passe {{ config('dofus.title') }}.
<br><br>
Si vous êtes bien l'auteur de cette demande, il reste une dernière étape pour valider le changement. Cliquez sur le lien ci-dessous:
<br><br>
<a style="color:#00bcc5;font-weight:bold;text-decoration:none;" href="{{ route('reset', $user->ticket) }}">Cliquez-ici pour changer de mot de passe</a>
<br><br>
Si vous n'avez pas fait cette demande, ignorez cet email et n'hésitez à prévenir le support.
@stop
