@extends('layouts.contents.default')
@include('layouts.menus.base')

@section('breadcrumbs')
{? $page_name = 'Connexion' ?}
{!! Breadcrumbs::render('page', $page_name) !!}
@stop

@section('content')
<div class="ak-title-container">
    <h1 class="ak-return-link">
        <a href=""><span class="ak-icon-big ak-game"></span></a>Se connecter
    </h1>
</div>
<div class="ak-container ak-panel ak-account-login">
    <div class="ak-panel-content">

        <div class="ak-login-page panel-main">
            <div class="row">
                <div class="col-sm-6">
                    <div class="ak-login-account">
                        <div class="ak-login-block">
                            <div class="ak-container">
                                <div class="ak-account-connect">
                                    @if($errors->has('auth'))
                                    <div class="infos_content">
                                        <div class="infos_box infos_box_login bg-danger text-danger" >
                                            <span class="warning">{{ $errors->first('auth') }}</span>
                                        </div>
                                    </div>
                                    @endif

                                    <div class="ak-form">
                                        {!! Form::open(['route' => 'login']) !!}
                                            <div class="form-group @if ($errors->has('auth')) has-error @endif">
                                                <label class="control-label" for="email">Email</label>
                                                <input type="text" class="form-control" placeholder="Email" name="email" value="{{ Input::old('email') }}" id="email">
                                            </div>

                                            <div class="form-group @if ($errors->has('auth')) has-error @endif">
                                                <label class="control-label" for="userpass">Mot de passe</label>
                                                <input type="password" class="form-control" placeholder="Mot de passe" name="password" value="{{ Input::old('password') }}" id="userpass" data-hasqtip="0">
                                            </div>

                                            <div class="form-group">
                                                <div class="checkbox">
                                                    <label>
                                                        <input type="checkbox" value="1" name="remember" checked="checked">Rester connecté
                                                    </label>
                                                </div>
                                            </div>
                                            <input type="submit" role="button" class="btn btn-primary btn-lg" id="login_sub" value="Se connecter">
                                        {!! Form::close() !!}
                                    </div>
                                </div>
                                <div class="ak-login-links">
                                    <ul>
                                        <li><a href="{{ URL::route('password-lost') }}">Mot de passe oublié ?</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6">
                    <div class="ak-create-account" style="padding-top:5px">
                        <h2>Créer un compte</h2>
                        <p>
                            <a class="btn btn-lg btn-info" href="{{ URL::route('register') }}">Créer un compte</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@stop
