@extends('layouts.master')

@section('header')
    {!! Html::style('css/play.css') !!}
    {!! Html::script('https://www.google.com/recaptcha/api.js') !!}
@stop

{? $page_name = 'Inscription' ?}

@section('background') bg-play2 @stop

@section('page')
<div class="container ak-main-container">
    <div class="ak-page-play">
        <div class="row">
            <div class="ak-form-subscription col-sm-7">
                <div class="ak-content-block">
                    <div class="ak-content-block-content">
                        <div class="ak-header-block">
                            <div class="ak-title-block"><span>1</span> créez votre compte</div>
                            <div class="ak-title-text">
                                Tout bon aventurier qui se respecte a de la curiosité, du courage, de la niaque !
                                <br /> Et... un compte. Oui, c'est la base
                            </div>
                        </div>
                        <div class="ak-inner-block">
                            <div class="ak-form ak-registerform-container">
                                {!! Form::open(['route' => 'register', 'class' => 'ak-container']) !!}
                                    <h4 style="display: none">Account</h4>
                                    <fieldset class="ak-container ak-fieldset">
                                        <div class="ak-fieldset-content">
                                            <div class="row ak-container">
                                                <div class="ak-column ak-container col-md-12">

                                                    <div class="form-group @if ($errors->has('pseudo')) has-error @endif">
                                                        <label class="control-label" for="pseudo">Votre pseudo</label>
                                                        <input type="text" class="form-control" tabindex="1" autocomplete="off" name="pseudo" placeholder="Votre pseudo" value="{{ Input::old('pseudo') }}" id="pseudo" autocapitalize="off" autocorrect="off" required="required" />
                                                        @if ($errors->has('pseudo')) <label class="error control-label">{{ $errors->first('pseudo') }}</label> @endif
                                                    </div>

                                                    <div class="form-group @if ($errors->has('firstname')) has-error @endif">
                                                        <label class="control-label" for="firstname">Votre prénom</label>
                                                        <input type="text" class="form-control" tabindex="1" autocomplete="off" name="firstname" placeholder="Votre prénom" value="{{ Input::old('firstname') }}" id="firstname" autocapitalize="off" autocorrect="off" required="required" />
                                                        @if ($errors->has('firstname')) <label class="error control-label">{{ $errors->first('firstname') }}</label> @endif
                                                    </div>

                                                    <div class="form-group @if ($errors->has('lastname')) has-error @endif">
                                                        <label class="control-label" for="lastname">Votre nom</label>
                                                        <input type="text" class="form-control" tabindex="1" autocomplete="off" name="lastname" placeholder="Votre nom" value="{{ Input::old('lastname') }}" id="lastname" autocapitalize="off" autocorrect="off" required="required" />
                                                        @if ($errors->has('lastname')) <label class="error control-label">{{ $errors->first('lastname') }}</label> @endif
                                                    </div>

                                                    <div class="form-group @if ($errors->has('email')) has-error @endif">
                                                        <label class="control-label" for="email">Votre adresse e-mail</label>
                                                        <input type="email" class="form-control" tabindex="1" autocomplete="off" name="email" placeholder="Votre adresse e-mail" value="{{ Input::old('email') }}" id="email" autocapitalize="off" autocorrect="off" required="required" />
                                                        @if ($errors->has('email')) <label class="error control-label">{{ $errors->first('email') }}</label> @endif
                                                    </div>

                                                    <div class="form-group @if ($errors->has('password')) has-error @endif">
                                                        <label class="control-label" for="password">Votre mot de passe</label>
                                                        <input type="password" class="form-control" tabindex="1" autocomplete="off" name="password" placeholder="Votre mot de passe" value="{{ Input::old('password') }}" id="password" autocapitalize="off" autocorrect="off" required="required" />
                                                        @if ($errors->has('password')) <label class="error control-label">{{ $errors->first('password') }}</label> @endif
                                                    </div>

                                                    <div class="form-group @if ($errors->has('passwordConfirmation')) has-error @endif">
                                                        <label class="control-label" for="passwordConfirmation">Confirmation</label>
                                                        <input type="password" class="form-control" tabindex="1" autocomplete="off" name="passwordConfirmation" placeholder="Confirmation" value="{{ Input::old('passwordConfirmation') }}" id="passwordConfirmation" autocapitalize="off" autocorrect="off" required="required" />
                                                        @if ($errors->has('passwordConfirmation')) <label class="error control-label">{{ $errors->first('passwordConfirmation') }}</label> @endif
                                                    </div>

                                                    <div class="ak-container ak-recaptcha-container">
                                                        <div class="ak-container block_captcha">
                                                            <div class="form-group @if ($errors->has('g-recaptcha-response')) has-error @endif">
                                                                <label class="control-label" for="recaptcha">Confirmez que vous n'êtes pas un robot</label>
                                                                {!! Recaptcha::render() !!}
                                                                @if ($errors->has('g-recaptcha-response')) <br /><label class="error control-label">{{ $errors->first('g-recaptcha-response') }}</label> @endif
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="form-group @if ($errors->has('cgu')) has-error @endif">
                                                        <div class="checkbox">
                                                            <label>
                                                                <input type="checkbox" value="1" name="cgu" required="required" /> J'ai lu et j'accepte les <a onclick="window.open('https://account.ankama.com/fr/cgu?mode=p','pop','scrollbars=yes,menubar=yes,width=740,height=600')" href="javascript:void(0);">conditions générales</a> du site.
                                                            </label>
                                                            @if ($errors->has('cgu')) <label class="error control-label">{{ $errors->first('cgu') }}</label> @endif
                                                        </div>
                                                    </div>

                                                    <div class="row ak-container">
                                                        <div class="ak-column ak-container col-md-12 text-center block-submit">
                                                            <input class="btn btn-primary ak-btn-big ak-submit" type="submit" value="Terminer l'inscription" />
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                    </fieldset>
                                {!! Form::close() !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="ak-dl-game col-sm-5">
                <div class="ak-content-block">
                    <div class="ak-content-block-content">
                        <div class="ak-header-block">
                            <div class="ak-title-block"><span>2</span> téléchargez {{ config('dofus.title') }}</div>
                            <div class="ak-title-text">
                                Tofus, wabbits, bouftous, héros, donjons, quêtes et dragons :
                                <br /> téléchargez l'intégrale du Monde des Douze !
                            </div>
                        </div>

                        <div class="ak-inner-block">
                            <a class="btn btn-info ak-btn-big" href="{{ config('dofus.download.win') }}">télécharger le jeu <span>Version : Windows</span></a>
                            <div class="clearfix"></div>
                            <div class="ak-other-version">
                                <div class="ak-list-link">
                                    <a href="{{ config('dofus.download.win') }}">Windows</a>
                                    <a href="{{ config('dofus.download.mac') }}">MacOS</a>
                                </div>
                            </div>
                            <a class="ak-problems" href="{{ URL::to('support') }}" target="_blank">Un problème d'installation ?</a>
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
                                        <a class="btn btn-info btn-lg" href="http://forum.azote.us/forum/22-tutorials/">voir les tutoriels</a>
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
                                        <a class="btn btn-info btn-lg" href="https://forum.azote.us" target="_blank">découvrir la communauté</a>
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
