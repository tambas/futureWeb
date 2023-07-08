@extends('layouts.contents.default')
@include('layouts.menus.base')

@section('breadcrumbs')
{? $page_name = 'Changer d\'email' ?}
{!! Breadcrumbs::render('account.page', $page_name) !!}
@stop

@section('content')
<div class="ak-container ak-main-center">
    <div class="ak-title-container ak-backlink">
        <h1><span class="ak-icon-big ak-bank"></span></a> Changer d'email</h1>
        <a href="{{ URL::route('profile') }}" class="ak-backlink-button">Retour à mon compte</a>
    </div>

    <div class="ak-container ak-panel-stack">
        <div class="ak-container ak-panel ak-glue">
            <div class="ak-panel-content">
                <div class="panel-main ak-form">
                    {!! Form::open(['route' => 'account.change_email']) !!}

                    <div class="form-group @if ($errors->has('passwordOld')) has-error @endif">
                        <label class="control-label" for="passwordOld">Mot de passe actuel</label>
                        <input type="password" class="form-control" tabindex="1" autocomplete="off" name="passwordOld" placeholder="Mot de passe" value="{{ Input::old('passwordOld') }}" id="passwordOld" autocapitalize="off" autocorrect="off" required="required" />
                        @if ($errors->has('passwordOld')) <label class="error control-label">{{ $errors->first('passwordOld') }}</label> @endif
                    </div>

                    <div class="form-group @if ($errors->has('email')) has-error @endif">
                        <label class="control-label" for="email">Nouvelle adresse email</label>
                        <input type="email" class="form-control" tabindex="2" autocomplete="off" name="email" placeholder="Votre adresse e-mail" value="{{ Input::old('email') }}" id="email" autocapitalize="off" autocorrect="off" required="required" />
                        @if ($errors->has('email')) <label class="error control-label">{{ $errors->first('email') }}</label> @endif
                    </div>

                    <input type="submit" role="button" class="btn btn-primary btn-lg" value="Changer d'email">
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
</div>
@stop
