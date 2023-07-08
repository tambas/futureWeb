@extends('layouts.emails.template')

@section('content')
<h3>Bonjour {{ $user->firstname }} {{ $user->lastname }} !</h3>
<p>Un membre de l'équipe a répondu à votre ticket n°{{$ticket->id}}.<p>
<p><u>Informations</u></p>
<p><b>Catégorie:</b> {{$ticket->category}}</p>
<p><b>Sujet:</b> {{$ticket->subject}}</p>
@if($ticket->userAssigned())
<p><b>Staff en charge:</b> {{$ticket->userAssigned()->pseudo}}</p>
@endif
<p><b>Statut du ticket:</b> {{ Utils::support_request_status($ticket->state) }}</p>
<p>@if($ticket->isOpen()) Il est possible qu'une action ou une réponse de votre part soit nécéssaire. @else Votre ticket est maintenant cloturé. Si nécéssaire, il vous est toujours possible de le ré-ouvrir.@endif<br>Vous pouvez consulter votre demande <a href="{{ route('support.show', $ticket->id) }}">en cliquant sur ce lien.</a></p>
@if(!$ticket->isOpen())
<p>Nous espérons que l'équipe a répondu favorablement à votre demande et ce dans un délai raisonnable.</p>
@endif
@stop
