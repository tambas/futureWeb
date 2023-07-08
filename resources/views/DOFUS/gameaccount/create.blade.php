@extends('layouts.contents.default')
@include('layouts.menus.base')

@section('breadcrumbs')
{? $page_name = 'Créer un compte de jeu' ?}
{!! Breadcrumbs::render('account.page', $page_name) !!}
@stop

@section('content')
<div class="ak-title-container">
    <h1><a href=""><span class="ak-icon-big ak-support"></span></a> Mon compte</h1>
</div>

<div class="ak-container ak-panel-stack">
    <div class="ak-panel">
        <div class="ak-panel-title">
            <span class="ak-panel-title-icon"></span> Créer un compte de jeu
        </div>
        <div class="ak-panel-content">
            <div class="panel-main">
                <div class="row">
                    <div class="col-sm-6">
                        <div class="ak-container">
                            <div class="ak-form">
                                {!! Form::open(['route' => 'gameaccount.create']) !!}

                                    <div class="form-group @if ($errors->has('server')) has-error @endif">
                                        <label class="control-label" for="server">Serveur</label>
                                        <select name="server" class="form-control" id="server" />
                                            <option value="">Selectionnez un serveur</option>
                                            @foreach (config('dofus.details') as $server)
                                            <option value="{{ $server->name }}" @if (Input::old('server') == $server->name) selected="selected" @endif)>{{ ucfirst($server->name) }} - {{ $server->desc }} - {{ $server->version }}</option>
                                            @endforeach
                                        </select>
                                        @if ($errors->has('server')) <label class="error control-label">{{ $errors->first('server') }}</label> @endif
                                    </div>

                                    <div class="form-group @if ($errors->has('login')) has-error @endif">
                                        <label class="control-label" for="login">Identifiant</label>
                                        <input type="text" class="form-control" placeholder="Votre identifiant de connexion" name="login" value="{{ Input::old('login') }}" id="login" />
                                        @if ($errors->has('login')) <label class="error control-label">{{ $errors->first('login') }}</label> @endif
                                    </div>

                                    <div class="form-group @if ($errors->has('password')) has-error @endif">
                                        <label class="control-label" for="password">Nouveau mot de passe</label>
                                        <input type="password" class="form-control" placeholder="Mot de passe" name="password" value="{{ Input::old('password') }}" id="password">
                                        @if ($errors->has('password')) <label class="error control-label">{{ $errors->first('password') }}</label> @endif
                                    </div>

                                    <div class="form-group @if ($errors->has('passwordConfirmation')) has-error @endif">
                                        <label class="control-label" for="passwordConfirmation">Confirmation</label>
                                        <input type="password" class="form-control" placeholder="Confirmation" name="passwordConfirmation" value="{{ Input::old('passwordConfirmation') }}" id="passwordConfirmation">
                                        @if ($errors->has('passwordConfirmation')) <label class="error control-label">{{ $errors->first('passwordConfirmation') }}</label> @endif
                                    </div>

                                    <div class="form-group @if ($errors->has('nickname')) has-error @endif">
                                        <label class="control-label" for="nickname">Pseudo</label>
                                        <input type="text" class="form-control" placeholder="Votre pseudonyme" name="nickname" value="{{ Input::old('nickname') }}" id="nickname" />
                                        @if ($errors->has('nickname')) <label class="error control-label">{{ $errors->first('nickname') }}</label> @endif
                                    </div>

                                    <input type="submit" role="button" class="btn btn-primary btn-lg btn-block" value="Créer le compte de jeu">

                                {!! Form::close() !!}
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="ak-container">
                            <div class="row">
                                @foreach (config('dofus.details') as $i => $server)
                                <div class="col-sm-4 @if ($i == 0) col-sm-offset-2 @endif">
                                    <h2>{{ ucfirst($server->name) }}</h2>
                                    <p>{{ $server->desc }} - {{ $server->version }}</p>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop
