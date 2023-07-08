@extends('layouts.contents.default')
@include('layouts.menus.base')

@section('breadcrumbs')
{? $page_name = 'Récupérer un personnage' ?}
{!! Breadcrumbs::render('gameaccount.page', $page_name, [$account->server, $account->Id]) !!}
@stop

@section('content')
<div class="ak-container ak-main-center">
    <div class="ak-title-container ak-backlink">
        <h1><span class="ak-icon-big ak-bank"></span></a> Récupérer un personnage</h1>
        <a href="{{ URL::route('gameaccount.view', [$account->server, $account->Id]) }}" class="ak-backlink-button">Retour à mon compte</a>
    </div>

    <div class="ak-container ak-panel-stack">
        <div class="ak-container ak-panel ak-glue">
            <div class="ak-panel-content">
                <div class="panel-main profile ak-form">
                    <div class="code">
                        <span class="ak-breed-icon breed{{ $character->Breed }}_{{ $character->Sex }}"></span>
                    </div>
                    Vous avez supprimé ce personnage le <b>{{ $character->DeletedDate->format('d/m/Y à H\hi') }}</b><br><br>
                    {!! Form::open(['route' => ['characters.recover', $account->server, $account->Id, $character->Id]]) !!}
                    <label class="control-label" for="nickname">Pseudo</label>
                    <div class="form-group @if ($errors->has('nickname')) has-error @endif">
                            <input type="text" class="form-control" value="{{ $character->Name }}" id="nickname" name="nickname"/><br>
                        @if ($errors->has('nickname')) <label class="error control-label">{{ $errors->first('nickname') }}</label> @endif
                    </div>
                    <b>Classe</b>: {{ $character->classe() }}<br>
                    <b>Niveau</b>: {{ $character->level($account->server) }}<br>
                    <b>Serveur</b>: {{ ucfirst($account->server) }}<br><br>
                    <div class="form-group @if ($errors->has('price')) has-error @endif">
                        <label class="control-label" for="price">Prix</label>
                        <div class="input-group">
                            <span class="input-group-addon"><span class="ak-icon-small ak-ogrines-icon"></span></span>
                            <input type="text" class="form-control" value="{{ Utils::format_price($character->recoverPrice()) }}" id="price" name="price" readonly />
                        </div>
                        @if ($errors->has('price')) <label class="error control-label">{{ $errors->first('price') }}</label> @endif
                    </div>
                    <input type="submit" role="button" class="btn btn-primary btn-lg" value="Récupérer le personnage">
                    {!! Form::close() !!}
                    <div class="clearfix"></div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop
