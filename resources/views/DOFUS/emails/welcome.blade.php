@extends('layouts.emails.template')

@section('content')
<h3>Bienvenue {{ $user->firstname }} {{ $user->lastname }} !</h3>
<p>Vous venez de créer un compte Azote.us, et nous vous en remercions.<p>
<p>Il reste une dernière étape pour valider votre compte. Aidez-nous à vérifier votre compte en cliquant sur le lien ci-dessous:</p>
<br>
<a href="{{ route('activation', $user->ticket) }}">Cliquez-ici pour vérifier votre compte</a>
<br><br>
<p><b>Avertissements et conseils sur la sécurité</b></p>
<p>Toutes les mesures de sécurité que nous pouvons prendre sont inutiles si vous-même ne respectez pas certaines consignes. La responsabilité de Azote.us ne saurait être engagée si vous ne les respectiez pas.</p>
<p>&bull; Ne rendez jamais publiques les informations contenues sur votre compte Azote.us.</p>
<p>Vous ne devez jamais communiquer votre nom de compte, votre mot de passe. Des personnes mal intentionnées pourraient s'en servir pour usurper votre identité, supprimer des informations ou voler des éléments de votre compte.</p>
<p>&bull; Ne partagez jamais votre compte avec une autre personne.</p>
<p>Il est parfois tentant de partager son compte avec un proche, un ami ou même un membre de sa famille. Dans l'idéal, cela permet éventuellement de progresser plus vite ou de profiter à moindre coût d'un accès à nos produits et services. Le problème est qu'en cas de désaccords en jeu ou ailleurs, voire d'indiscrétions vis-à-vis de tiers, vous risquez de tout perdre. C'est pour vous protéger que nous interdisons cette pratique. Si vous passez outre cette interdiction, soyez prêt à en assumer les conséquences.</p>
<p>&bull; Fuyez les personnes, les sites, les forums qui prétendent vous donner des "passe-droits".</p></p>
<p>Arnaque, phishing (site utilisant la charte graphique d'un site officiel), vol, usurpation d'identité, mensonge, mauvaise foi sont hélas des pratiques qui existent sur Internet comme partout ailleurs. Soyez vigilant, ne croyez jamais des personnes qui vous proposent des choses manifestement illégales ou contraires à nos conditions d'utilisation. En cas de doute, tâchez de vous renseigner sur ce genre d'interlocuteurs auprès de personnes en qui vous avez une confiance absolue et si vous ne parvenez pas à avoir de certitudes, renoncez à échanger avec ces personnes. Il vaut mieux passer à côté d'une bonne affaire que de perdre l'intégralité de votre compte.</p>
<p>&bull; Apprenez à identifier les sites et le personnel Azote.us.</p>
<p>Nos sites web utilisent des adresses officielles et ne sont jamais hébergés sur des sites gratuits. Nos administrateurs et nos modérateurs se présenteront toujours à vous en utilisant des éléments clairement identifiables. Dans tous les cas, jamais ils ne vous demanderont vos identifiants.</p>
@stop
