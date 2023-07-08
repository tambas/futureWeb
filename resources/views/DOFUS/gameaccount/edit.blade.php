@extends('layouts.contents.default')
@include('layouts.menus.base')

@section('breadcrumbs')
{? $page_name = 'Changer de mot de passe' ?}
{!! Breadcrumbs::render('gameaccount.page', $page_name, [$account->server, $account->Id]) !!}
@stop

@section('content')
<div class="ak-container ak-main-center">
    <div class="ak-title-container ak-backlink">
        <h1><span class="ak-icon-big ak-bank"></span></a> Changer de mot de passe</h1>
        <a href="{{ URL::route('gameaccount.view', [$account->server, $account->Id]) }}" class="ak-backlink-button">Retour à mon compte</a>
    </div>

    <div class="ak-container ak-panel-stack">
        <div class="ak-container ak-panel ak-glue">
            <div class="ak-panel-content">
                <div class="panel-main ak-form">
                    {!! Form::open(['route' => ['gameaccount.edit', $account->server, $account->Id]]) !!}

                    <div class="form-group @if ($errors->has('password')) has-error @endif">
                        <label class="control-label" for="password">Nouveau mot de passe</label>
                        <input type="password" class="form-control" tabindex="1" autocomplete="off" name="password" placeholder="Nouveau mot de passe" value="{{ Input::old('password') }}" id="password" autocapitalize="off" autocorrect="off" required="required" />
                        @if ($errors->has('password')) <label class="error control-label">{{ $errors->first('password') }}</label> @endif
                    </div>

                    <div class="form-group @if ($errors->has('passwordConfirmation')) has-error @endif">
                        <label class="control-label" for="passwordConfirmation">Confirmation</label>
                        <input type="password" class="form-control" tabindex="1" autocomplete="off" name="passwordConfirmation" placeholder="Confirmation" value="{{ Input::old('passwordConfirmation') }}" id="passwordConfirmation" autocapitalize="off" autocorrect="off" required="required" />
                        @if ($errors->has('passwordConfirmation')) <label class="error control-label">{{ $errors->first('passwordConfirmation') }}</label> @endif
                    </div>

                    <input type="submit" role="button" class="btn btn-primary btn-lg" value="Changer de mot de passe">
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
</div>
@stop
