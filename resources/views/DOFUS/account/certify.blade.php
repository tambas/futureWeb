@extends('layouts.contents.default')
@include('layouts.menus.base')

@section('breadcrumbs')
{? $page_name = 'Certification' ?}
{!! Breadcrumbs::render('account.page', $page_name) !!}
@stop

@section('header')
<script>
var $jQuery = jQuery.noConflict();
</script>
{!! Html::script('js/jquery-ui.min.js') !!}
@stop

@section('content')
<div class="ak-container ak-main-center">
    <div class="ak-title-container ak-backlink">
        <h1><span class="ak-icon-big ak-bank"></span></a> Certification du compte</h1>
        <a href="{{ URL::route('profile') }}" class="ak-backlink-button">Retour à mon compte</a>
    </div>

    <div class="ak-container ak-panel-stack">
        <div class="ak-container ak-panel ak-glue">
            <div class="ak-panel-content">
                <h3>ATTENTION, vous êtes sur le point de certifier votre compte</h3>
                <p>Les informations renseignées ci-dessous doivent correspondrent à la <strong>réalité</strong> et ne pourront en <strong>aucun cas être modifiées.</strong> </p>
                <p>A tout moment, l'équipe se réserve le droit de vérifier ces données par le biais d'un document d'identité.</p>
                <p><a href="{{config('dofus.certify.article')}}" target="_blank" class="btn btn-info btn-sm">Plus d'informations</a></p>
                <div class="panel-main ak-form">
                    {!! Form::model($authuser, ['route' => 'account.certify']) !!}

                    <div class="form-group @if ($errors->has('firstname')) has-error @endif">
                        <label class="control-label" for="firstname">Prénom</label>
                        {!! Form::text('firstname', null, ['class' => 'form-control', 'id' => 'firstname', 'required' => 'required']) !!}
                        @if ($errors->has('firstname')) <label class="error control-label">{{ $errors->first('firstname') }}</label> @endif
                    </div>

                    <div class="form-group @if ($errors->has('lastname')) has-error @endif">
                        <label class="control-label" for="lastname">Nom</label>
                        {!! Form::text('lastname', null, ['class' => 'form-control', 'id' => 'lastname', 'required' => 'required']) !!}
                        @if ($errors->has('lastname')) <label class="error control-label">{{ $errors->first('lastname') }}</label> @endif
                    </div>

                    <div class="form-group @if ($errors->has('birthday')) has-error @endif">
                        <label class="control-label" for="birthday">Date de naissance</label>
                        {!! Form::text('birthday', Auth::user()->birthday ? Auth::user()->birthday->format('Y-m-d') : null, ['class' => 'form-control', 'id' => 'datepicker', 'required' => 'required', 'placeholder' => 'aaaa-mm-jj']) !!}
                        @if ($errors->has('birthday')) <label class="error control-label">{{ $errors->first('birthday') }}</label> @endif
                    </div>

                    <input type="submit" role="button" class="btn btn-danger btn-lg" value="Certifier mon compte">
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
</div>
@stop
@section('bottom')
    <script>
      var annee_max = parseInt({{\Carbon\Carbon::now()->year}}) - parseInt({{config('dofus.certify.min_age')}});
      var annee_min = parseInt({{\Carbon\Carbon::now()->year}}) - parseInt({{config('dofus.certify.max_age')}});
      $jQuery('#datepicker').datepicker({
        dateFormat: "yy-mm-dd",
        changeMonth: true,
        changeYear: true,
        yearRange: annee_min+":"+annee_max,
        defaultDate: ''+annee_min+'-01-01'
    });
      $jQuery('#datepicker').datepicker( $jQuery.datepicker.regional[ "fr" ] );
    </script>
@stop
