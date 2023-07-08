@extends('layouts.contents.default')
@include('layouts.menus.base')

@section('header')
    {!! Html::style('css/login.css') !!}
@stop

@section('breadcrumbs')
{? $page_name = 'Mot de passe oublié' ?}
{!! Breadcrumbs::render('page', $page_name) !!}
@stop

@section('content')
<div class="ak-title-container">
    <h1 class="ak-return-link">
        <a href=""><span class="ak-icon-big ak-support"></span></a> Mot de passe oublié
    </h1>
</div>
<div class="ak-container ak-panel ak-account-login">
    <div class="ak-panel-content">

        <div class="ak-login-page panel-main">
            <div class="row">
                <div class="col-sm-6 col-sm-offset-3">
                    <div class="">
                        <div class="ak-login-block">
                            <div class="ak-container">
                                <div class="ak-form">
                                    {!! Form::open(['route' => 'reset']) !!}
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
                                        <input type="submit" role="button" class="btn btn-primary btn-lg btn-block" value="Réinitialiser le mot de passe">
                                    {!! Form::close() !!}
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
