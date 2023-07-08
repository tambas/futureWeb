@extends('layouts.master')
@include('layouts.tops.simple')

@section('header')
    {!! Html::style('css/download.css') !!}
@stop

{? $page_name = 'Télécharger' ?}

@section('page')
@yield('top')
<div class="container ak-main-container ">
    <div class="ak-main-page ak-page-download">
        <div class="row">
            <div class="main col-md-12">
                <div class="ak-page-header">
                    <div class="ak-title-container">
                    <h1>
                        <span class="ak-icon-big ak-download"></span>télécharger {{ config('dofus.title') }}
                    </h1>
                    </div>
                </div>
                <div class="ak-content-download">
                    <div class="ak-banner">
                            <div class="ak-content-block">
                            <div class="col-sm-6"></div>
                            <div class="col-sm-5 ak-inner-block">
                                <a class="btn btn-info ak-btn-big" href="{{ config('dofus.download.win') }}">télécharger le jeu <span>Version : Windows</span></a>
                                <div class="ak-other-version">
                                    <div class="ak-list-link">
                                        <i class="fa fa-windows"></i><a href="{{ config('dofus.download.win') }}">Windows</a>
                                        <i class="fa fa-apple"></i><a href="{{ config('dofus.download.mac') }}">MacOS</a>
                                    </div>
                                </div>
                                <a class="ak-problems" href="{{ URL::to('support') }}">Un problème d'installation ?</a>
                                <div class="ak-create-account-block">
                                    @if (Auth::guest())
                                    Pour jouer à {{ config('dofus.title') }}, vous devez posséder un compte. <a href="{{ URL::route('register') }}">Créer un compte {{ config('dofus.title') }}</a>
                                    @else
                                    Bon jeu sur {{ config('dofus.title') }}, {{ Auth::user()->pseudo }} !<br><br>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="ak-first-links-container row">
            <div class="ak-first-links">
                <div class="ak-content-block">
                    <div class="ak-content-block-content">
                        <div class="row">
                            <div class="ak-header-block col-md-5">
                                <div class="ak-title-block"><span>3</span> EN ROUTE !</div>
                                <div class="ak-illu"></div>
                                <div class="ak-title-text">
                                    <p>Votre quête peut maintenant
                                        <br /> commencer. Faites preuve de
                                        <br /> bravoure, d'intelligence
                                        <br /> et d'héroïsme.</p>
                                    <p>Préparez-vous à vivre une
                                        <br /> aventure hors du commun :
                                        <br /> les Dofus n'attendent que vous !</p>
                                </div>
                            </div>
                            <div class="ak-links-block col-md-7">
                                <div class="ak-link-block col-sm-4">
                                    <div class="ak-link-title">
                                        <div>Premiers pas dans
                                        <br /> le monde des douze ?</div>
                                    </div>
                                    <div class="ak-illu-link ak-illu-link1"></div>
                                    <div class="ak-link-text">Sachez qu'ici tout est possible à l'intrépide, il vous suffit de suivre le guide !</div>
                                    <div class="ak-bottom-link">
                                        <a class="btn btn-info btn-lg" href="{{ config('dofus.social.forum') }}22-tutoriels">voir les tutoriels</a>
                                    </div>
                                </div>
                                <div class="ak-link-block col-sm-4">
                                    <div class="ak-link-title">
                                        <div>Vous n'êtes
                                        <br /> pas seul !</div>
                                    </div>
                                    <div class="ak-illu-link ak-illu-link2"></div>
                                    <div class="ak-link-text">Communiquez librement avec toute la population du Monde des Douze.</div>
                                    <div class="ak-bottom-link">
                                        <a class="btn btn-info btn-lg" href="{{ config('dofus.social.forum') }}" target="_blank">découvrir la communauté</a>
                                    </div>
                                </div>
                                <div class="ak-link-block col-sm-4">
                                    <div class="ak-link-title">
                                        <div>des questions ?</div>
                                    </div>
                                    <div class="ak-illu-link ak-illu-link3"></div>
                                    <div class="ak-link-text">Le support est là pour vous répondre !</div>
                                    <div class="ak-bottom-link">
                                        <a class="btn btn-info btn-lg" href="{{ URL::to('support') }}">contacter le support</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop