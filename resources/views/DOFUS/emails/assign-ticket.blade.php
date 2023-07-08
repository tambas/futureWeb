@extends('layouts.emails.template')

@section('content')
<h3>Bonjour {{ $user->firstname }} {{ $user->lastname }} !</h3>
<p>Un membre de l'équipe a été assigné à votre ticket n°{{$ticket->id}}.<p>
<p><u>Informations</u></p>
<p><b>Catégorie:</b> {{$ticket->category}}</p>
<p><b>Sujet:</b> {{$ticket->subject}}</p>
@if($ticket->userAssigned())
<p><b>Staff en charge:</b> {{$ticket->userAssigned()->pseudo}}</p>
@endif
<p><b>Statut du ticket:</b> {{ Utils::support_request_status($ticket->state) }}</p>
<p>Le membre de l'équipe maintenant en charge vous répondra dès que possible.<br>Vous pouvez consulter votre demande <a href="{{ route('support.show', $ticket->id) }}">en cliquant sur ce lien.</a></p>
@stop
