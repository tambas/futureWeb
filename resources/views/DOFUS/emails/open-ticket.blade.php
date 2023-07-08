@extends('layouts.emails.template')

@section('content')
<h3>Bonjour {{ $user->firstname }} {{ $user->lastname }} !</h3>
<p>Vous venez d'ouvrir un nouveau ticket sur le <a href="{{route('support')}}">support</a> d'Azote. Nous sommes navrés que vous rencontriez un problème.<p>
<p>Sachez que l'équipe met tout en oeuvre afin de répondre aux demandes le plus rapidement possible. Cependant, le délai de réponse peut varier entre <b>1 et 48 heures.</b></p>
<p><u>Ticket n°{{$ticket->id}} - Informations:</u></p>
<p><b>Catégorie:</b> {{$ticket->category}}</p>
<p><b>Sujet:</b> {{$ticket->subject}}</p>
<p><b>Statut du ticket:</b> {{ Utils::support_request_status($ticket->state) }}</p>
<p>Vous pouvez très facilement consulter l'avancement de votre demande <a href="{{ route('support.show', $ticket->id) }}">en cliquant sur ce lien.</a></p>
<h3><b>Tutoriels & F.A.Q</b></h3>
<p>Savez-vous que la réponse à la majorité des problèmes se trouve déjà sur notre site?</p>
<p>Nous disposons d'une foire aux questions complète, qui peut répondre à certaines de vos questions ou problèmes</p>
<p>&bull; Consultez notre <a href="{{ config('dofus.social.forum') }}faq">F.A.Q</a> sur notre Forum.</p>
<p>N'hésitez pas à consulter les tutoriels proposés par notre équipe ainsi que par la communauté</p>
<p>&bull; Consultez les <a href="{{ config('dofus.social.forum') }}22-tutoriels">tutoriels</a> sur notre Forum.</p>
@stop
