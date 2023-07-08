@extends('layouts.contents.default')
@include('layouts.menus.base')

@section('header')
    {!! Html::style('css/flags.css') !!}
    {!! Html::style('css/shop.css') !!}
@stop

@section('breadcrumbs')
{? $page_name = 'Choix de l\'offre' ?}
{!! Breadcrumbs::render('shop.page', $page_name) !!}
@stop

@section('content')
<div class="ak-title-container ak-backlink">
    <h1 class="ak-return-link">
        <span class="ak-icon-big ak-shop"></span> Ogrines
    </h1>
    <a href="{{ URL::route('shop.payment.method', $countryBackup) }}" class="ak-backlink-button">Retour aux méthodes de paiement</a>
</div>

<div class="ak-container ak-panel-stack ak-payments-process-choice">
    <div class="ak-category-infos">
      <div class="hr "></div>
        <img src="{{URL::asset('imgs/shop/shop_ogrines.png')}}" class="img-responsive">
    </div>
    <div class="ak-container ak-panel">
        <div class="ak-panel-title">
              <span class="ak-panel-title-icon"></span> Choisissez votre offre pour {{ $methodName }} &nbsp;<span class="icon-flag flag-{{ $country }}">
        </div>
        <div class="ak-panel-content ak-form">
            <div class="panel-main">
                {!! Form::open(['route' => 'shop.payment.code']) !!}
                    <input type="hidden" name="country" value="{{ $country }}" />

                    @foreach ($paliers as $palier => $data)
                    <label>
                        <div class="ak-container ak-content-list ak-list-paymentmode ">
                            <div class="row ak-container">
                                <div class="ak-column ak-container col-md-12">
                                    <div class="ak-list-element ak-paymentmode-haspromo">
                                        <div class="ak-tablerow">
                                            <div class="ak-tablecell">
                                                <div class="ak-front">
                                                    <div class="form-group">
                                                        <div class="radio">
                                                            <input type="radio" value="{{ $methodName }}_{{ $palier }}" name="method">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="ak-main">
                                                    <div class="ak-main-content">
                                                        <div class="ak-image">
                                                            <img src="{{ URL::asset('imgs/shop/payment/' . $methodName . '.png') }}">
                                                        </div>
                                                        <div class="ak-content">
                                                            <div class="ak-title">
                                                                Code {{ $methodName }} : <span class="ak-title-info"><span class="ak-price eur"><span class="ak-display-price">{{ $data->points }} - {{ $data->cost }}</span></span></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        @if (array_key_exists($country . '|' . $methodName, config('dofus.promos')))
                                        <div class="ak-tablerow">
                                            <div class="ak-tablecell">
                                                <div class="ak-paymentmode-promo">
                                                    <div class="ak-promo">
                                                        <span class="ak-promo-title">Promo</span>
                                                        <span class="ak-promo-desc">+ {{ config('dofus.promos')[$country . '|' . $methodName] }} ogrines offerts pour un achat par {{ $methodName }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @endif
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

                    <div class="ak-container ak-payment-cgu">
                        <div class="row ak-container">
                            <div class="ak-column ak-container col-md-7">
                                <div class="form-group">
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" value="1" name="cgv">En cochant cette case, vous acceptez expressément que la fourniture du contenu numérique (ogrines) commence immédiatement après l'envoi de notre mail de confirmation d'achat et renoncez donc expressément à votre droit de rétractation. Vous confirmez avoir pris connaissance des <a href="{{ URL::to('legal/cgv') }}">conditions générales de vente</a> d'{{ config('dofus.title') }} et vous confirmez que <b>{{ Auth::user()->firstname }} {{ Auth::user()->lastname }}</b> est le propriétaire du moyen de paiement ou que vous avez reçu l'autorisation du titulaire du moyen de paiement.
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="ak-column ak-container col-md-5">
                                <button type="submit" class="btn btn-lg btn-primary ak-btn-fluid ak-btn-wrap btn-pay-now">Payer maintenant<span>(Commande avec obligation de paiement)</span></button>
                            </div>
                        </div>
                    </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>
@stop
