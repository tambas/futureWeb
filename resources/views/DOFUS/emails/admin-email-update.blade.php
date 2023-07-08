@extends('layouts.emails.template')

@section('content')
<b>Bonjour {{ $user->firstname }} {{ $user->lastname }} !</b>
<br><br>
L'adresse e-mail de votre compte {{ config('dofus.title') }} a été modifié par un Administrateur.
<br><br>
Vous pouvez désormais vous <a style="color:#00bcc5;font-weight:bold;text-decoration:none;" href="{{ route('login') }}">connecter</a> avec cette nouvelle adresse.
<br><br>
Bon jeu!
@stop
