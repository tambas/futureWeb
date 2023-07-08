@extends('layouts.contents.default')
@include('layouts.menus.base')

@section('header')
    {!! Html::style('css/flags.css') !!}
    {!! Html::style('css/flag-icon.min.css') !!}
     {!! Html::style('css/shop.css') !!}
@stop

@section('breadcrumbs')
{? $page_name = 'Choix du pays' ?}
{!! Breadcrumbs::render('shop.page', $page_name) !!}
@stop

@section('content')
<div class="ak-title-container">
    <h1><span class="ak-icon-big ak-shop"></span> Ogrines</h1>
</div>
<div class="ak-page-menu ak-glue">
   <nav class="ak-nav-expand ak-expanded">
      <div class="ak-nav-expand-container">
         <ul class="ak-nav-links ak-nav-expand-links">
            <li>
               <a href="{{URL::route('shop.index')}}">
               Accueil      </a>
            </li>
            <li class="on">
               <a href="{{URL::route('shop.payment.country')}}">
               Ogrines      </a>
            </li>
            <li>
               <a href="{{URL::route('shop.market')}}">
               Marché des personnages      </a>
            </li>
         </ul>
      </div>
      <div class="ak-nav-expand-more">
         <span class="ak-nav-expand-icon ak-picto-common ak-picto-open" style="display: none;">+</span>
      </div>
   </nav>
</div>
<div class="ak-container ak-panel-stack ak-payments-process-choice">
  <div class="ak-category-infos">
      <div class="hr "></div>
    <img src="{{URL::asset('imgs/shop/shop_ogrines.png')}}" class="img-responsive">
  
      <div class="ak-description">
      <p style="text-align: center;">Les <strong>Ogrines</strong> vous permettent de débloquer des objets ou des services, tant en jeu que sur le site (par exemple acheter ou restaurer un personnage).</p>
        <p style="text-align:center;">Suivez les instructions ci-dessous afin de procéder au paiement. Une fois présent sur votre compte, il vous suffira de transférer le montant souhaité sur le compte de jeu de votre choix!</p>
      </div>
  </div>
    <div class="ak-container ak-panel">
        <div class="ak-panel-title">
            <span class="ak-panel-title-icon"></span> Choisissez votre pays
        </div>
        <div class="ak-panel-content">
            <div class="panel-main" style="background-color: #f8f8f6;">
                <style>
                .flag-icon {
                    float: left;
                }

                a.btn-flag {
                    width: 148px;
                    margin: 5px;
                }
                </style>
                @foreach ($rates as $country => $data)
                @if ($country != 'all' && $country != 'anre' && $country != 'uk' && $country != 'fd') <a class="btn btn-default btn-flag" href="{{ URL::route('shop.payment.method', $country) }}" title="{{ $country }}"><span class="flag-icon flag-icon-{{ $country }}"></span> {{ $country }}</a> @endif
                @if ($country == 'anre') <a class="btn btn-default btn-flag" href="{{ URL::route('shop.payment.method', $country) }}" title="DOM"><span class="flag-icon flag-icon-fr"></span> DOM</a> @endif
                @if ($country == 'fd') <a class="btn btn-default btn-flag" href="{{ URL::route('shop.payment.method', $country) }}" title="DOM"><span class="flag-icon flag-icon-fr"></span> DOM</a> @endif
                @if ($country == 'uk') <a class="btn btn-default btn-flag" href="{{ URL::route('shop.payment.method', $country) }}" title="{{ $country }}"><span class="flag-icon flag-icon-gb"></span> {{ $country }}</a> @endif
                @endforeach
            </div>
        </div>
    </div>
</div>
@stop
