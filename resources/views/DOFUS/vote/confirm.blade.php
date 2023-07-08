@extends('layouts.contents.default')
@include('layouts.menus.base')

@section('header')
    {!! Html::style('css/vote.css') !!}
@stop

@section('breadcrumbs')
{? $page_name = 'Confirmation vote' ?}
{!! Breadcrumbs::render('page', $page_name) !!}
@stop

@section('content')
<div class="ak-container ak-main-center">
    <div class="ak-title-container">
        <h1 class="ak-return-link">
            <span class="ak-icon-big ak-character"></span></a> Vote pour le serveur
        </h1>
    </div>
    <div class="ak-container ak-panel panel-vote-link container-padding">
        <div class="ak-panel-content panel-main">
            <div class="row">
                <div class="col-sm-6">
                    {!! Form::open(['route' => 'vote.process']) !!}

                    <div class="form-group @if ($errors->has('out')) has-error @endif">
                        <label class="control-label" for="out">Valeur OUT</label>
                        <input type="text" class="form-control" placeholder="Valeur OUT" name="out" value="{{ Input::old('out') }}" id="out">
                        @if ($errors->has('out')) <label class="error control-label">{{ $errors->first('out') }}</label> @endif
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

                    <input type="submit" role="button" class="btn btn-primary btn-lg" value="Valider">

                    {!! Form::close() !!}
                </div>
                <div class="col-sm-6">
                    <p>Pour obtenir vos Ogrines vous devez récupérer la valeur OUT sur une des lignes: @foreach(config('dofus.servers') as $k => $server) « {{strtoupper($server)}} {{config('dofus.details')[$server]->version}} » @if(($k == 0) || ($k % 2 == 0)) ou @endif @endforeach et la reporter dans le formulaire</p>
                    <img src="{{ URL::asset('imgs/help/out.jpg') }}" alt="Valeur OUT" width="300px">
                </div>
            </div>
        </div>
    </div>
</div>
@stop
