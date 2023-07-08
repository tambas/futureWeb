<!DOCTYPE html>
<html lang="fr">
<head>
	<meta content="width=device-width, initial-scale=1" name="viewport" />
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Akarlys Serveur</title>
	{!! Html::favicon('images/favicon.png') !!}
	<meta name="author" content="wb x dsg" />
	<meta name="description" content="Arkalys Serveur" />
	<meta name="keywords" content="" />
	<meta name="Resource-type" content="Document" />
	{!! Html::style('css/app.css') !!}
	{!! Html::style('css/font-awesome.css') !!}
	{!! Html::script('js/jquery-2.1.4.min.js') !!}
	{!! Html::script('js/jquery-ui.min.js') !!}
	{!! Html::script('js/arkalys-functions.js') !!}
	{!! Html::script('js/arkalys-interface.js') !!}
</head>
<body>
	<header role="header">
		<div>
			<a href="">
				<h1 id="logo" title="Arkalys Serveur" alt="Arkalys Serveur"></h1>
			</a>
			<nav role="navigation">
				<ul>
					<li active><a href="">Accueil</a></li>
					<li><a href="#">Actualités</a></li>
					<li><a href="#">Boutique</a></li>
					<li><a href="#">Communauté</a></li>
					<li><a href="#">Assistance</a></li>
				</ul>
			</nav>
			<ul id="panel">
				<li data-nav></li>
				<li></li>
				<li role="search"></li>
			</ul>
		</div>
	</header>

	<section role="banner">
		<h2>Bienvenue sur notre nouveau</h2>
		<h1>Site internet</h1>
		<a href="">
				Découvrez-le maintenant !
				<span></span><span></span><span></span><span></span>
			</a>
	</section>

	<main role="main">

@yield('page')

	</main>

	<section role="footer">
		<section class="content">
			<ul>
				<li>Arkalys</li>
				<li><a href="">Accueil</a></li>
				<li><a href="">Actualités</a></li>
				<li><a href="">Boutique</a></li>
				<li><a href="">Voter</a></li>
				<li><a href="">Forum</a></li>
			</ul>
			<ul>
				<li>Aides de jeu</li>
				<li><a href="">Réglements</a></li>
				<li><a href="">F.A.Q.</a></li>
				<li><a href="">Mentions légales</a></li>
				<li><a href="">Support client</a></li>
				<li><a href="">L'équipe</a></li>
			</ul>
			<ul class="video">
				<li>Nos dernières vidéos</li>
				<li style="background-image:url('http://orig06.deviantart.net/9e0e/f/2009/185/1/c/christmas_by_arnaud_o.jpg');"><a href="">Live du 10/04</a></li>
				<li style="background-image:url('http://img13.deviantart.net/875e/i/2008/328/1/4/ankama_convention_3_by_ntamak.jpg');"><a href="">Live du 29/03</a></li>
				<li style="background-image:url('http://img14.deviantart.net/378e/i/2009/277/8/a/mini_wakfu_miss_moches_by_ntamak.jpg');"><a href="">Live du 17/03</a></li>
			</ul>
			<ul class="social">
				<li>
					<a href="" class="ico_rss"></a>
				</li>
				<li>
					<a href="" class="ico_facebook"></a>
				</li>
				<li>
					<a href="" class="ico_twitter"></a>
				</li>
				<li>
					<a href="" class="ico_twitch"></a>
				</li>
				<li>
					<a href="" class="ico_youtube"></a>
				</li>
			</ul>
			<div class="clear"></div>
		</section>
		<footer>
			<p>
				{!! Html::image('images/_logo_text.png', 'logo', ['class' => 'logo']) !!}
				<br /> © 2015 <strong>Arkalys</strong>. Tous droits réservés. Design by <strong>Nicow</strong>. <a href="">Conditions d'utilisations</a> - <a href="">Conditions générales de vente</a>.
			</p>
		</footer>
	</section>
</body>
</html>
