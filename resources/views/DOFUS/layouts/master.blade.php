<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8" />
    <title>{{ config('dofus.title') }}@if (isset($page_name)) - {!! $page_name !!}@endif</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" />
    <meta name="description" content="{!! config('dofus.description') !!}" />
    @if (isset($og))
    {!! $og->renderTags() !!}
    @else
    <meta property="og:type" content="website" />
    <meta property="og:image" content="{{ URL::asset('imgs/cover.png') }}" />
    <meta property="og:description" content="{!! config('dofus.description') !!}" />
    <meta property="og:url" content="{{ Request::url() }}" />
    <meta property="og:site_name" content="{{ config('dofus.title') }} - @if (isset($page_name)){!! $page_name !!}@else{{ config('dofus.subtitle') }}@endif" />
    @endif
    <link rel="alternate" type="application/rss+xml" title="News RSS" href="{{ URL::to('news.rss') }}" />
    <link rel="shortcut icon" type="image/png" href="{{ URL::asset('imgs/azote_simple.png') }}"/>
    <link rel="canonical" href="{{ Request::url() }}" />
    {!! Html::style('css/common.css') !!}
    {!! Html::style('css/toastr.min.css') !!}
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
    @yield('header')
    {!! Html::script('js/admin/toastr.min.js') !!}
    {!! Html::script('js/common.js') !!}
    @if (config('dofus.theme.animated'))
    {!! Html::style('imgs/carousel/'.config('dofus.theme.background').'/style.css') !!}
    @else
    <style type="text/css">
        body {
            background: url('{{ URL::asset('imgs/carousel/common/'.config('dofus.theme.background').'.jpg') }}')  center top no-repeat;
            background-color: {{ config('dofus.theme.color') }};
        }
    </style>
    @endif
    <script>
      (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
      (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
      m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
      })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');
      ga('create', 'UA-82860248-1', 'auto');
      ga('send', 'pageview');
    </script>
</head>
<body class="@yield('background')">
    <header>
        <div class="ak-idbar">
            <div class="ak-idbar-content">
                <div class="ak-idbar-left">
                    <div class="ak-brand" data-set="ak-brand">
                        <a class="navbar-brand" href="{{ URL::route('home') }}"></a>
                    </div>
                    <a class="ak-support" href="{{ URL::to('support') }}">Support</a>
                    @if (!Auth::guest() && Auth::user()->isStaff())
                    <a class="ak-admin" href="{{ URL::route('admin.dashboard') }}" target="_blank">Admin</a>
                    @endif
                </div>
                <div class="ak-idbar-right">
                    @if (Auth::guest())
                    <div class="ak-nav-not-logged">
                        <div class="ak-connect-links">
                            <a href="{{ URL::route('login') }}" class="login">
                                <span>Connexion</span>
                                <img class="img-responsive" src="{{ URL::asset('imgs/avatar/default.jpg') }}" alt="Avatar">
                            </a>
                            <a href="{{ URL::route('register') }}" class="register">Inscription</a>
                        </div>
                    </div>
                    @else
                    <div class="ak-idbar-right">
                        <!--<a class="ak-nav-notifications ak-button-modal">
                            <span class="label label-danger">0</span>
                        </a>-->
                        <a class="ak-button-modal ak-nav-characters">
                            <span>{{count(Auth::user()->characters(true))}}</span>
                        </a>
                        <script type="application/json">{"target":".ak-box-characters"}</script>
                        <div class="ak-idbar-box ak-box-characters">
                            <div class="ak-box-title">
                                Mes personnages <span></span>
                                <div class="ak-tooltip hidden ak-tooltip-white"> 
                                Pour apparaître et avoir une page perso, votre personnage doit être niveau 20 minimum et s'être connecté en jeu il y a moins de 6 mois. 
                                </div>
                                <script type="application/json">{"tooltip":{"style":{"classes":"ak-tooltip-white"}}}</script>
                            </div>
                                    <div class="ak-characters-list">
                                    @if(count(Auth::user()->characters(true)) == 0)
                                        <p class="text-center" style="margin-top:10px;">Vous n'avez aucun personnage</p>
                                    @endif
                                        @foreach(Auth::user()->characters(true) as $character)
                                                <div class="ak-character">
                                                    <div class="ak-character-img">
                                                        <a href="{{ URL::route('characters.view', [$character->server, $character->Id, $character->Name]) }}"><div class="ak-entitylook" alt="" style="background:url({{ DofusForge::player($character, $character->server, 'face', 2, 55, 55) }}) top left;width:55px;height:55px"></div></a>
                                                    </div>
                                                    <div class="ak-character-infos">
                                                        <div class="ak-name"><a href="{{ URL::route('characters.view', [$character->server, $character->Id, $character->Name]) }}">{{$character->Name}}</a></div>
                                                        <div class="ak-level">{{$character->classe()}} - Niv : {{$character->level($character->server)}} @if($character->PrestigeRank > 0)- P{{$character->PrestigeRank}}@endif</div>
                                                        <div class="ak-server">{{ucfirst($character->server)}}</div>
                                                    </div>
                                                    @if($character->guild($character->server))
                                                    <div class="ak-character-infos ak-emblem">
                                                        <a href="{{ route('guild.view', [$character->server, $character->guild($character->server)->Id, $character->guild($character->server)->Name])}}"><div class="ak-emblem" style="background:url({{ URL::asset($character->guild($character->server)->emblem(40,40)) }}) center center;width:40px;height:40px"></div></a>
                                                    </div>
                                                    @endif
                                                    @php $account = $character->account($character->server) @endphp
                                                    <div class="ak-character-infos ak-emblem">
                                                        @if($account->IsJailed != 1 && !$account->isBanned() && !$account->isStaff())
                                                        <a href="{{route('shop.market.sell')}}"><span class="ak-tooltip ak-icon-big ak-sell" style="width:40px; height:40px;"></span><script type="application/json">{"manual":true,"tooltip":{"content":{"title":"","text":"Vendre"},"style":{"classes":"ak-tooltip-content"}},"forceOnTouch":true}</script></a>
                                                        @endif
                                                    </div>
                                                </div>
                                        @endforeach
                                    </div>
                        </div>
                        <div class="ak-button-modal ak-nav-logged">
                            <div class="ak-logged-account">
                                <span class="ak-nickname">{{ Auth::user()->pseudo }}</span>
                                <span class="avatar">
                                    <img src="{{ URL::asset(Auth::user()->avatar) }}" alt="Avatar">
                                </span>
                            </div>
                        </div>
                         <script type="application/json">{"target":".ak-box-logged"}</script>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        <nav class="navbar navbar-default" data-role="ak_navbar">
            <div class="navbar-container">
                <div class="ak-navbar-left">
                    <a class="ak-brand" href="{{ URL::route('home') }}">Azote</a>
                </div>
                <a class="ak-main-logo" href="{{ URL::route('home') }}"></a>

                <div class="navbar-header">
                    <a class="burger-btn" href="javascript:void(0)"><span></span></a>
                </div>

                <div class="navbar-collapse navbar-ex1-collapse collapse">
                    <ul class="nav navbar-nav">
                        <span class="ak-navbar-left-part">
                            <li class="lvl0 dropdown sep">
                                <a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown">Azote <b class="caret"></b></a>
                                <ul class="dropdown-menu dropdown-menu-right" role="menu">
                                    <li class="lvl1">
                                        <ul>
                                            <li class="lvl2"><a href="{{ URL::route('posts') }}">Actualités</a></li>
                                            <li class="lvl2"><a href="{{ URL::route('servers') }}">Serveurs</a></li>
                                            <li class="lvl2"><a href="{{ URL::route('ladder.general', [config('dofus.default_server_ladder')]) }}">Classements</a></li>
                                            <li class="lvl2"><a href="{{ URL::route('lottery.index') }}">Loterie</a></li>
                                        </ul>
                                    </li>
                                </ul>
                            </li>
                            <li class="lvl0 sep"><a href="{{ URL::route('register') }}">Rejoindre</a></li>
                            <li class="lvl0 sep"><a href="{{ URL::route('shop.index') }}">Boutique</a></li>
                        </span>
                        <li class="lvl0 ak-menu-brand">
                            <a class="navbar-brand" href="{{ URL::route('home') }}"></a>
                        </li>
                        <span class="ak-navbar-right-part">
                            <li class="lvl0 sep"><a href="{{ URL::route('vote.index') }}">Vote</a></li>
                            <li class="lvl0 sep"><a href="{{ config('dofus.social.forum') }}" target="_blank">Forum</a></li>
                            <li class="lvl0 sep"><a href="{{ URL::to('support') }}">Support</a></li>
                        </span>
                    </ul>

                </div>
                @if(Auth::check())
                <div class="ak-navbar-right">
                    <div class="ak-button-modal ak-nav-logged">
                        <div class="ak-logged-account">
                            <span class="ak-nickname">{{Auth::user()->pseudo}}</span>
                            <span class="avatar">
                            <img src="{{ URL::asset(Auth::user()->avatar) }}" alt="Avatar">
                            </span>
                        </div>
                    </div>
                    <script type="application/json">{"target":".ak-box-logged"}</script>
                </div>
                @endif
            </div>
        </nav>
        @if(Auth::check())
        <div class="ak-idbar-box ak-box-logged">
            <div class="ak-row ak-account-header">
                <div class="ak-row-cell ak-logged-avatar">
                    <div class="ak-logged-avatar-container">
                        <img src="{{ URL::asset(Auth::user()->avatar) }}" class="">
                        <a href="{{ URL::route('account.change_profile') }}" target="_blank" class="ak-picto ak-icon-change-avatar">
                        <div class="ak-avatar-mask">
                            <span class="ak-avatar-mask-infos">Changer d'avatar</span>
                        </div>
                        </a>
                    </div>
                </div>
                <div class="ak-row-cell ak-logged-infos">
                    <div class="ak-infos-dofus">
                        <span class="ak-infos-nickname">{{Auth::user()->pseudo}}</span>
                        <span class="ak-game-not-subscribe">@if(Auth::user()->certified == 0) <a href="{{ URL::route('account.certify') }}" style="color:red;">Compte non certifié</a> @else Compte certifié @endif</span>
                        <a href="{{ URL::route('shop.index') }}" class="ak-subscribe-link btn btn-primary btn-lg">Voir la Boutique</a>
                    </div>
                </div>
            </div>
            <div class="ak-account-infos">
                <div class="ak-row ak-infos-ogrines">
                    <div class="ak-row-cell ak-infos-logged-picto"><span class="ak-infos-picto"></span></div>
                    <div class="ak-row-cell ak-infos-logged">
                        <span class="ak-infos-title">Ogrines : </span>
                        <span class="ak-infos-nb">{{ Utils::format_price(Auth::user()->points) }}</span>
                        <a href="{{ URL::route('shop.payment.country') }}" class="ak-infos-link">
                        Acheter des Ogrines                    </a>
                    </div>
                </div>
                <div class="ak-row ak-infos-gifts">
                    <div class="ak-row-cell ak-infos-logged-picto"><span class="ak-infos-picto"></span></div>
                    <div class="ak-row-cell ak-infos-logged">
                        <span class="ak-infos-title">Cadeaux : </span>
                        <span class="ak-infos-nb">{{ Utils::format_price(Auth::user()->votes / 10) }}</span>
                        <a href="{{ URL::route('vote.index') }}" class="ak-infos-link">Obtenir des cadeaux</a>
                    </div>
                </div>
                <div class="ak-row ak-infos-jetons">
                    <div class="ak-row-cell ak-infos-logged-picto"><span class="ak-infos-picto"></span></div>
                    <div class="ak-row-cell ak-infos-logged">
                        <span class="ak-infos-title">Jetons : </span>
                        <span class="ak-infos-nb">{{ Utils::format_price(Auth::user()->jetons) }}</span>
                        <a href="{{ URL::route('vote.index') }}" class="ak-infos-link">Gagner des jetons</a>
                    </div>
                </div>
                <div class="ak-row ak-infos-tickets">
                    <div class="ak-row-cell ak-infos-logged-picto"><span class="ak-infos-picto"></span></div>
                    <div class="ak-row-cell ak-infos-logged">
                        <span class="ak-infos-title">Tickets : </span>
                        <span class="ak-infos-nb">{{ Utils::format_price(count(Auth::user()->lotteryTickets(true))) }}</span>
                        <a href="{{ URL::route('lottery.index') }}" class="ak-infos-link">Jouer à la loterie</a>
                    </div>
                </div>
                <div class="ak-infos-row ak-account-manage">
                    <div class="ak-azote-logo">
                    </div>
                    <div class="ak-infos-logged">
                        <a class="ak-infos-logged-link" href="{{ URL::route('profile') }}">
                        Gestion de compte        </a>
                        @if(Auth::user()->certified == 0)
                        <a class="ak-infos-logged-link" href="{{ URL::route('account.certify') }}">
                        Protégez votre compte !        </a>
                        @else
                        <a class="ak-infos-logged-link" target="_blank" href="{{ config('dofus.social.forum') }}">
                        Consulter le forum        </a>
                        @endif
                        <a class="btn btn-default" href="{{ URL::route('logout') }}">Déconnexion</a>
                    </div>
                </div>
            </div>
        </div>
        @endif
        <!-- Keep in order largest -> lowest device resolution -->
        <div class="largedesktop device-profile visible-lg" data-deviceprofile="largedesktop"></div>
        <div class="desktop device-profile visible-md" data-deviceprofile="desktop"></div>
        <div class="tablet device-profile visible-sm" data-deviceprofile="tablet"></div>
        <div class="mobile device-profile visible-xs" data-deviceprofile="mobile"></div>
    </header>
    @yield('beta')
    @yield('page')

    <footer>
        <div class="ak-footer-content">
            <div class="row ak-block1">
                <div class="col-md-9 ak-block-links">
                    <div class="col-md-6 clearfix">
                        <div class="col-xs-6">
                            <div class="ak-list">
                                <div>
                                    <span class="ak-link-title">{{ config('dofus.title') }}</span>
                                </div>
                                <a href="{{ URL::route('posts') }}">Actualités</a>
                                <a href="{{ URL::route('download') }}">Télécharger</a>
                                <a href="{{ URL::route('register') }}">Créer un compte</a>
                                <a href="{{ URL::route('password-lost') }}">Mot de passe oublié ?</a>
                            </div>
                        </div>
                        <div class="col-xs-6">
                            <div class="ak-list">
                                <div>
                                    <span class="ak-link-title">Serveur</span>
                                </div>
                                <a href="{{ URL::route('servers') }}">Infos serveurs</a>
                                <a href="{{ URL::route('lottery.index') }}">Loterie</a>
                                <a href="{{ URL::route('ladder.general', [config('dofus.default_server_ladder')]) }}">Classements</a>
                                <a href="{{ URL::route('vote.index') }}">Cadeaux</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 clearfix">
                        <!--<div class="col-xs-6">
                            <div class="ak-list">
                                <div>
                                    <span class="ak-link-title">Tournois</span>
                                </div>
                                <a href="{{ URL::to('pvp/fights') }}">Combats</a>
                                <a href="{{ URL::to('pvp/champions') }}">Champion</a>
                                <a href="{{ URL::to('pvp/result') }}">Résultats</a>
                                <a href="{{ URL::to('pvp/reward') }}">Récompenses</a>
                            </div>
                        </div>-->
                        <div class="col-xs-6">
                            <div class="ak-list">
                                <div>
                                    <span class="ak-link-title">Support</span>
                                </div>
                                <a href="{{ URL::to('support') }}">Aide</a>
                                <a href="{{ config('dofus.social.forum') }}">Forum</a>
                                <a href="http://forum.azote.us/contact">Contact</a>
                                <a href="http://forum.azote.us/faq">FAQ</a>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="col-md-3 ak-block-download">
                    <a href="{{ URL::route('download') }}" class="btn btn-primary btn-lg">Télécharger le jeu</a>
                    <a class="ak-problem" href="{{ URL::to('support') }}">Un problème ? Contactez le support.        </a>
                    <div class="ak-social-network">
                        <a href="{{ config('dofus.social.facebook') }}" class="fb" target="_blank"></a>
                        <a href="{{ config('dofus.social.twitter') }}" class="tw" target="_blank"></a>
                        <a href="{{ config('dofus.social.youtube') }}" class="yo" target="_blank"></a>
                    </div>
                </div>
            </div>
            <div class="row ak_legal">
                <div id="col-md-12">
                    <div class="ak-legal">
                        <div class="row">
                            <div class="col-sm-1">
                                <a href="{{ URL::route('home') }}" class="ak-logo-azote"></a>
                            </div>
                            <div class="col-sm-8">
                                <p>&copy; {{ date('Y') }} <a href="{{ URL::route('home') }}">{{ config('dofus.title') }}</a>. Tous droits réservés. <a href="{{ URL::to('cgu') }}" target="_blank">Conditions d'utilisation</a> - <a href="{{ URL::to('privacy') }}"target="_blank">Politique de confidentialité</a> - <a href="{{ URL::to('cgv') }}" target="_blank">Conditions Générales de Vente</a> - <a href="{{ URL::to('legal') }}" target="_blank">Mentions Légales</a></p>
                            </div>
                            <div class="col-sm-3"><span class="prevention"></span></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    @if (Session::has('popup'))
    {? $popup = Session::get('popup') ?}
    @endif

    @if (isset($popup))
    @include('popup.' . $popup)
    @endif

    @if (Session::has('notify'))
    {{ Toastr::add(Session::get('notify')['type'], str_replace("'", "\\'", Session::get('notify')['message'])) }}
    {!! Toastr::render() !!}
    @endif
    {!! Html::script('js/common2.js') !!}
    @yield('bottom')
    <script type="text/javascript">
        var $ = require('jquery');

        $('.ui-dialog-titlebar-close').hide();

        setTimeout(function() {
            $('.ui-dialog-titlebar-close').fadeIn();
        }, 3000);

        $('.ui-dialog-titlebar-close').on('click', function() {
            $('.ui-dialog').fadeOut();
        });
    </script>
</body>
</html>