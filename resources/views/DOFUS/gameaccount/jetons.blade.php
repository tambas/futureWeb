@extends('layouts.contents.default')
@include('layouts.menus.base')

@section('breadcrumbs')
{? $page_name = 'Convertir des jetons en Ogrines' ?}
{!! Breadcrumbs::render('gameaccount.page', $page_name, [$account->server, $account->Id]) !!}
@stop

@section('content')
<div class="ak-container ak-main-center">
    <div class="ak-title-container ak-backlink">
        <h1><span class="ak-icon-big ak-bank"></span></a> Convertir des jetons en Ogrines</h1>
        <a href="{{ URL::route('gameaccount.view', [$account->server, $account->Id]) }}" class="ak-backlink-button">Retour à mon compte</a>
    </div>

    <div class="ak-container ak-panel-stack">
        <div class="ak-container ak-panel ak-glue">
            <div class="ak-panel-content">
                <div class="panel-main ak-form">
                    {!! Form::open(['route' => ['gameaccount.jetons', $account->server, $account->Id]]) !!}

                    1 Jeton <span class="ak-icon-small ak-votes-icon"></span> = {{ config('dofus.points_by_vote') }} Ogrines <span class="ak-icon-small ak-ogrines-icon"></span>

                    <div class="form-group @if ($errors->has('offline')) has-error @endif">
                        @if ($errors->has('offline')) <label class="error control-label">{{ $errors->first('offline') }}</label> @endif
                    </div>

                    <div class="form-group">
                        <label class="control-label" for="ogrinesWeb">Jetons disponible</label>
                        <div class="input-group">
                            <span class="input-group-addon"><span class="ak-icon-small ak-votes-icon"></span></span>
                            <input type="text" class="form-control" value="{{ Utils::format_price(Auth::user()->jetons) }}" id="jetonsWeb" readonly />
                        </div>
                    </div>

                    <div class="form-group @if ($errors->has('jetons')) has-error @endif">
                        <label class="control-label" for="ogrines">Convertir des jetons</label>
                        <div class="input-group">
                            <span class="input-group-addon"><span class="ak-icon-small ak-votes-icon"></span></span>
                            <input type="text" class="form-control" tabindex="1" autocomplete="off" name="jetons" placeholder="Saisir le nombre de jetons à convertir" value="{{ Input::old('jetons') }}" id="jetons" autocapitalize="off" autocorrect="off" required="required" />
                        </div>
                        @if ($errors->has('jetons')) <label class="error control-label">{{ $errors->first('jetons') }}</label> @endif
                    </div>

                    <input type="submit" role="button" class="btn btn-primary btn-lg" value="Convertir">
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
</div>
@stop
