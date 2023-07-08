@extends('layouts.emails.template')

@section('content')
<b>Bonjour {{ $user->firstname }} {{ $user->lastname }} !</b>
<br><br>
Vous venez de demander un changement d'adresse email {{ config('dofus.title') }}.
<br><br>
Si vous êtes bien l'auteur de cette demande vous devez suivre l'étape suivante pour valider le changement. Cliquez sur l'un lien ci-dessous:
<br><br>
<a style="color:#00bcc5;font-weight:bold;text-decoration:none;" href="{{ route('account.valid-email', [$type, $token]) }}">Cliquez-ici pour autoriser le changement de l'adresse email</a>
<br><br>
Si vous n'avez pas fait cette demande, ignorez cet email et n'hésitez à prévenir le support.
@stop
