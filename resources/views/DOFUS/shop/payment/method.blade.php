@extends('layouts.contents.default')
@include('layouts.menus.base')

@section('header')
    {!! Html::style('css/flags.css') !!}
    {!! Html::style('css/shop.css') !!}
@stop

@section('breadcrumbs')
{? $page_name = 'Choix du mode de paiement' ?}
{!! Breadcrumbs::render('shop.page', $page_name) !!}
@stop

@section('content')
<div class="ak-title-container ak-backlink">
    <h1 class="ak-return-link">
        <span class="ak-icon-big ak-shop"></span> Ogrines
    </h1>
    <a href="{{ URL::route('shop.payment.country') }}" class="ak-backlink-button">Retour aux pays</a>
</div>

<div class="ak-container ak-panel-stack ak-payments-process-choice">
    <div class="ak-category-infos">
      <div class="hr "></div>
    <img src="{{URL::asset('imgs/shop/shop_ogrines.png')}}" class="img-responsive">
  </div>
    <div class="ak-container ak-panel">
        <div class="ak-panel-title">
              <span class="ak-panel-title-icon"></span> Choisissez votre mode de paiement &nbsp;<span class="icon-flag flag-{{ $country }}">
        </div>
        <div class="ak-panel-content ak-form">
            <div class="panel-main">
                {!! Form::open(['route' => 'shop.payment.code']) !!}

                <style>
                a.btn-flag {
                    width: 150px;
                    height: 150px;
                    margin: 5px;
                }
                </style>

                @foreach ($methods as $methodName => $method)
                <label>
                    <div class="ak-container ak-content-list ak-list-paymentmode ">
                        <div class="row ak-container">
                            <div class="ak-column ak-container col-md-12">
                                <div class="ak-list-element ak-paymentmode-haspromo">
                                    <div class="ak-tablerow">
                                        <div class="ak-tablecell">
                                            <div class="ak-main">
                                                <div class="ak-main-content">
                                                    <a href="{{ URL::route('shop.payment.palier', [$country, $methodName]) }}">
                                                        <div class="ak-image">
                                                            <img src="{{ URL::asset('imgs/shop/payment/' . $methodName . '.png') }}">
                                                        </div>
                                                        <div class="ak-content">
                                                            <div class="ak-title">
                                                                {{ strtoupper($methodName) }}
                                                            </div>
                                                        </div>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="clearfix  visible-md visible-lg"></div>
                        </div>
                    </div>
                </label>
                @endforeach

                <div class="has-error">
                    @if ($errors->has('country')) <label class="error control-label">{{ $errors->first('country') }}<br></label> @endif
                    @if ($errors->has('method_')) <label class="error control-label">{{ $errors->first('method_') }}<br></label> @endif
                    @if ($errors->has('palier')) <label class="error control-label">{{ $errors->first('palier') }}<br></label> @endif
                    @if ($errors->has('cgv')) <label class="error control-label">{{ $errors->first('cgv') }}<br></label> @endif
                </div>
            </div>
        </div>
    </div>
</div>
@stop
