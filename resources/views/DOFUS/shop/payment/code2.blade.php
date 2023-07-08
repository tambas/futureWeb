@extends('layouts.contents.default')
@include('layouts.menus.base')

@section('header')
    {!! Html::style('css/flags.css') !!}
    {!! Html::style('css/shop.css') !!}
@stop

@section('breadcrumbs')
{? $page_name = 'Paiement' ?}
{!! Breadcrumbs::render('shop.page', $page_name) !!}
@stop

@section('content')
<div class="ak-title-container ak-backlink">
    <h1 class="ak-return-link">
        <span class="ak-icon-big ak-shop"></span> Ogrines
    </h1>
    <a href="{{ URL::route('shop.payment.palier', [$country, $method]) }}" class="ak-backlink-button">Retour aux offres</a>
</div>

<div class="ak-container ak-panel-stack ak-payments-process">
    <div class="ak-category-infos">
      <div class="hr "></div>
        <img src="{{URL::asset('imgs/shop/shop_ogrines.png')}}" class="img-responsive">
    </div>
    <div class="ak-container ak-panel">
        <div class="ak-panel-title">
              <span class="ak-panel-title-icon"></span> Paiement par {{ $method }} pour {{ $payment->points }} Ogrines &nbsp;<span class="icon-flag flag-{{ $country }}"></span>
        </div>
        <div class="ak-panel-content">
            <div class="panel-main text-center">
                @if ($canBuy)
                    @if (config('app.env') == 'production')
                        <a href="http://{{ config('dofus.domain.fake') }}/code?ticket={{ Auth::user()->ticket }}&country={{ $country }}&pay_id={{ $method }}_{{ $palier }}" target="_blank"><btton class="btn btn-primary btn-lg">Procéder au paiement</button></a>
                    @else
                        <a href="http://{{ config('dofus.domain.fake') }}/code?ticket={{ Auth::user()->ticket }}&country={{ $country }}&pay_id={{ $method }}_{{ $palier }}" target="_blank"><btton class="btn btn-primary btn-lg">Procéder au paiement</button></a>
                    @endif
                @else
                    <center>Vous devez disposer d'au moins un personnage sur votre compte pour acheter des Ogrines.</center>
                @endif
            </div>
        </div>
    </div>
</div>
@stop
