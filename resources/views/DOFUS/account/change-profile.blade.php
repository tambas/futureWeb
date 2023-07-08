@extends('layouts.contents.default')
@include('layouts.menus.base')

@section('breadcrumbs')
{? $page_name = 'Éditer le profil' ?}
{!! Breadcrumbs::render('account.page', $page_name) !!}
@stop

@section('header')
<style>
.ak-avatar-radio-container {
    overflow: hidden;
    display: inline-block;
    margin: 7px;
    margin: 0.7rem;
}
.ak-avatar-radio-container .form-group {
    margin-bottom: 40px;
    margin-bottom: 4rem;
}
.ak-avatar-radio-container .control-label {
    border: 2px solid #c7c3b4;
    padding: 0;
    display: inline-block;
}
.ak-avatar-radio-container .control-label .ak-character-name {
    font-weight: normal;
    overflow: hidden;
    position: absolute;
    text-align: center;
    text-overflow: ellipsis;
    top: 125px;
    width: 100px;
}
.ak-avatar-radio-container .radio {
    position: absolute;
    top: 0;
    left: 0;
}
.ak-avatar-radio-container .radio input[type="radio"] {
    position: absolute;
    top: 100px;
    left: 46px;
    margin: 0;
}
.ak-form .hint {
    display: block;
    font-size: 12px;
    color: #777777;
}
</style>
@endsection
@section('content')
<div class="ak-container ak-main-center">
    <div class="ak-title-container ak-backlink">
        <h1><span class="ak-icon-big ak-bank"></span></a> Éditer le profil</h1>
        <a href="{{ URL::route('profile') }}" class="ak-backlink-button">Retour à mon compte</a>
    </div>

    <div class="ak-container ak-panel-stack">
        <div class="ak-container ak-panel ak-glue">
            <div class="ak-panel-content">
                <div class="panel-main ak-form">
                    {!! Form::model($authuser, ['route' => 'account.change_profile']) !!}

                    <div class="form-group @if ($errors->has('firstname')) has-error @endif">
                        <label class="control-label" for="firstname">Prénom</label>
                        {!! Form::text('firstname', null, ['class' => 'form-control', 'id' => 'firstname', 'required' => 'required', $authuser->isCertified() ? 'readonly' :'']) !!}
                        @if ($errors->has('firstname')) <label class="error control-label">{{ $errors->first('firstname') }}</label> @endif
                    </div>

                    <div class="form-group @if ($errors->has('lastname')) has-error @endif">
                        <label class="control-label" for="lastname">Nom</label>
                        {!! Form::text('lastname', null, ['class' => 'form-control', 'id' => 'lastname', 'required' => 'required', $authuser->isCertified() ? 'readonly' :'']) !!}
                        @if ($errors->has('lastname')) <label class="error control-label">{{ $errors->first('lastname') }}</label> @endif
                    </div>
                    <div class="form-group @if ($errors->has('avatar')) has-error @endif">
                        <label class="control-label">Avatar</label>
                        <span class="hint">Mis à jour toutes les 10 minutes</span>
                         @if ($errors->has('avatar')) <br><label class="error control-label">{{ $errors->first('avatar') }}</label> @endif
                    </div>
                        @foreach ($characters as $character)
                        <div class="ak-avatar-radio-container">
                            <div class="form-group">
                                <label class="control-label" for="{{ $character->Id }}-{{ $character->server }}">
                                    <img src="{{ DofusForge::player($character, $character->server, 'face', 1, 100, 100) }}">
                                    <span class="ak-character-name">{{$character->Name}}</span>
                                </label>
                                <div class="radio">
                                    <input @if($authuser->avatarName() == ''.$character->Id.'-'.$character->server) checked @endif type="radio" name="avatar" id="{{ $character->Id }}-{{ $character->server }}" value="{{ $character->Id }}-{{ $character->server }}">
                                </div>
                            </div>
                        </div>
                        @endforeach
                        <div class="ak-avatar-radio-container">
                            <div class="form-group">
                                <label class="control-label" for="default-default">
                                    <img src="{{ URL::asset(config('dofus.default_avatar')) }}">
                                    <span style="font-weight:bold;" class="ak-character-name">Defaut</span>
                                </label>
                                <div class="radio">
                                    <input @if($authuser->avatarName() == 'default-default') checked @endif type="radio" name="avatar" id="default-default" value="default-default">
                                </div>
                            </div>
                        </div>
                    <div class="form-group">
                    <input type="submit" role="button" class="btn btn-primary btn-lg" value="Éditer le profil">
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
</div>
@stop
