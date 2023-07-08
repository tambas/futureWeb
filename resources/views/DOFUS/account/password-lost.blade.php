@extends('layouts.contents.default')
@include('layouts.menus.base')

@section('breadcrumbs')
{? $page_name = 'Mot de passe oublié' ?}
{!! Breadcrumbs::render('page', $page_name) !!}
@stop

@section('content')
<div class="ak-title-container">
    <h1><a href=""><span class="ak-icon-big ak-support"></span></a> Mot de passe oublié</h1>
</div>
<div class="ak-container ak-panel ak-account-login">
    <div class="ak-panel-content">
        <div class="ak-login-page panel-main">
            <div class="row">
                <div class="col-sm-6">
                    <div class="ak-login-account">
                        <div class="ak-login-block">
                            <div class="ak-container">
                                <div class="ak-form">
                                    {!! Form::open(['route' => 'password-lost']) !!}
                                        <div class="form-group @if ($errors->has('email')) has-error @endif">
                                            <label class="control-label" for="email">Email</label>
                                            <input type="text" class="form-control" placeholder="Email" name="email" value="{{ Input::old('email') }}" id="email">
                                            @if ($errors->has('email')) <label class="error control-label">{{ $errors->first('email') }}</label> @endif
                                        </div>
                                        <input type="submit" role="button" class="btn btn-primary btn-lg" value="Réinitialiser le mot de passe">
                                    {!! Form::close() !!}
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
