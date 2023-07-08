@extends('layouts.contents.default')
@include('layouts.menus.base')

@section('header')
    {!! Html::style('css/encyclopedia.css') !!}
    {!! Html::style('css/directories.css') !!}
@stop

@section('breadcrumbs')
{? $page_name = 'Mon marché' ?}
{!! Breadcrumbs::render('account') !!}
@stop

@section('content')
<div class="ak-container ak-main-center">
    <div class="ak-title-container">
        <h1><span class="ak-icon-big ak-bank"></span></a> Mon compte</h1>
    </div>
    @include('account.nav.topnav')
    <div class="ak-container ak-panel-stack">
        <div class="ak-container ak-panel ak-glue">
            <div class="ak-panel-title">
                <span class="ak-panel-title-icon"></span> Mes personnages en vente
            </div>
        </div>
    </div>
    <div class="ak-responsivetable-wrapper">
         <table border="1" class="ak-table ak-responsivetable" style="white-space: nowrap; visibility: visible;">
            <thead>
               <tr>
                  <th data-priority="9"></th>
                  <th data-priority="8">Nom</th>
                  <th data-priority="7">Classe<span class="ak-picto-common"></span></th>
                  <th data-priority="6">Niveau<span class="ak-picto-common"></span></th>
                  <th data-priority="5">Prestige<span class="ak-picto-common"></span></th>                  
                  <th data-priority="4">Sexe<span class="ak-picto-common"></span></th>
                  <th data-priority="3">Serveur<span class="ak-picto-common"></span></th>
                  <th data-priority="2">Prix<span class="ak-picto-common"></span></th>
                  <th data-priority="1">Actions<span class="ak-picto-common"></span></th>
               </tr>
            </thead>
            <tbody>
                @foreach($mcInSell as $market)
                <tr class="ak-bg-odd" id="{{$market->id}}">
                    <td class="ak-ladder-avatar ak-valign-top">
                        <div class="ak-entitylook" alt="{{$market->Name}}" style="background:url({{DofusForge::player($market->character(), $market->character()->server, 'face', 1, 48, 48)}}) top left;width:48px;height:48px"></div>
                    </td>
                    <td><a target="_blank" href="{{ URL::route('characters.view',[$market->character()->server, $market->character_id, $market->character()->Name])}}">{{$market->character()->Name}}</a></td>
                    <td>{{$market->character()->classe()}}</td>
                    <td>{{$market->character()->level($market->server)}}</td>
                    <td>{{$market->character()->PrestigeRank}}</td>
                    <td>{{$market->character()->sex()}}</td>
                    <td>{{ucfirst($market->character()->server)}}</td>
                    <td>{{Utils::format_price($market->ogrines)}} <span class="ak-icon-small ak-ogrines-icon"></span></td>
                    <td>
                    <a target="_blank" href="{{ URL::route('characters.view',[$market->character()->server, $market->character_id, $market->character()->Name])}}"><span class="ak-icon-tiny ak-filter ak-tooltip" title="Consulter"></span></a>
                    <a href="javascript:void(0)"><span class="ak-icon-tiny ak-trashcan ak-tooltip" title="Retirer"></span></a>
                    <div class="ak-tooltip hide" style="display: none;">
                        <p>Êtes-vous sur de vouloir retirer votre personnage de la vente ?</p>      
                            <a data-id="{{$market->id}}" class="removelink btn btn-primary btn-sm">Confirmer</a>
                    </div>
                    <script type="application/json">{"forceOnTouch":true,"tooltip":{"show":{"event":"click"},"style":{"classes":"ak-tooltip-white-shop"},"hide":{"delay":300,"fixed":true}}}</script>
                    </td>
                </tr>
                @endforeach
            </tbody>
         </table>
    </div>
    <div class="ak-container ak-panel-stack">
        <div class="ak-container ak-panel ak-glue">
            <div class="ak-panel-content">
                <div class="text-center">
                    <a href="{{URL::route('shop.market.sell')}}"><button type="button" role="button" class="btn btn-lg btn-primary">Vendre un personnage</button></a> 
                </div>
            </div>
        </div>
    </div>
    <div class="ak-container ak-panel-stack">
        <div class="ak-container ak-panel ak-glue">
            <div class="ak-panel-title">
                <span class="ak-panel-title-icon"></span> Mes personnages vendus
            </div>
        </div>
    </div>
    <div class="ak-responsivetable-wrapper">
         <table border="1" class="ak-table ak-responsivetable" style="white-space: nowrap; visibility: visible;">
            <thead>
               <tr>
                  <th data-priority="9"></th>
                  <th data-priority="8">Nom</th>
                  <th data-priority="3">Serveur<span class="ak-picto-common"></span></th>
                  <th data-priority="2">Vendu le<span class="ak-picto-common"></span></th>
                  <th data-priority="1">Prix<span class="ak-picto-common"></span></th>
               </tr>
            </thead>
            <tbody>
                @foreach($mcSold as $market)
                <tr class="ak-bg-odd" id="{{$market->id}}">
                    <td class="ak-ladder-avatar ak-valign-top">
                        <div class="ak-entitylook" alt="{{$market->Name}}" style="background:url({{DofusForge::player($market->character(), $market->character()->server, 'face', 1, 48, 48)}}) top left;width:48px;height:48px"></div>
                    </td>
                    <td>{{$market->character_name}}</td>
                    <td>{{ucfirst($market->character()->server)}}</td>
                    <td>{!! ucwords(utf8_encode($market->buy_date->formatLocalized('%e %B %Y &agrave; %Hh%M'))) !!}</td>
                    <td>{{Utils::format_price($market->ogrines)}} <span class="ak-icon-small ak-ogrines-icon"></span></td>
                </tr>
                @endforeach
            </tbody>
         </table>
    </div>
    <div class="ak-container ak-panel-stack">
        <div class="ak-container ak-panel ak-glue">
            <div class="ak-panel-title">
                <span class="ak-panel-title-icon"></span> Mes personnages achetés
            </div>
        </div>
    </div>
    <div class="ak-responsivetable-wrapper">
         <table border="1" class="ak-table ak-responsivetable" style="white-space: nowrap; visibility: visible;">
            <thead>
               <tr>
                  <th data-priority="9"></th>
                  <th data-priority="8">Nom</th>
                  <th data-priority="3">Serveur<span class="ak-picto-common"></span></th>
                  <th data-priority="2">Acheté le<span class="ak-picto-common"></span></th>
                  <th data-priority="1">Prix<span class="ak-picto-common"></span></th>
               </tr>
            </thead>
            <tbody>
                @foreach($mcBuyed as $market)
                <tr class="ak-bg-odd" id="{{$market->id}}">
                    <td class="ak-ladder-avatar ak-valign-top">
                        <div class="ak-entitylook" alt="{{$market->Name}}" style="background:url({{DofusForge::player($market->character(), $market->character()->server, 'face', 1, 48, 48)}}) top left;width:48px;height:48px"></div>
                    </td>
                    <td>{{$market->character_name}}</td>
                    <td>{{ucfirst($market->character()->server)}}</td>
                    <td>{!! ucwords(utf8_encode($market->buy_date->formatLocalized('%e %B %Y &agrave; %Hh%M'))) !!}</td>
                    <td>{{Utils::format_price($market->ogrines)}} <span class="ak-icon-small ak-ogrines-icon"></span></td>
                </tr>
                @endforeach
            </tbody>
         </table>
    </div>
</div>
@stop
@section('bottom')
<script>
    var $ = require('jquery');
                    
                    $('body').on('click', '.removelink:not([disabled])', function () {
                        // Find ID of the post
                        var button = $(this);
                        var id = $(this).data('id');
                        button.attr('disabled', true);
                        // Some variables
                        var url_market_base = '{{ route('shop.market')}}';
                        var token = '{{ Session::token() }}';

                        $.ajax({
                                method: 'DELETE',
                                url: ''+url_market_base+'/retirer',
                                dataType: 'json',
                                data: { _token: token, marketid: id},

                                success: function () {
                                    $('#'+ id +'').fadeOut();
                                    toastr.success('Votre personnage a été retiré de la vente et restauré sur votre compte de jeu');
                                },

                                error: function(jqXhr, json, errorThrown) {
                                    var errors = jqXhr.responseJSON;
                                    var errorsHtml;
                                    if(errors)
                                    {
                                        errorsHtml= '';
                                        $.each( errors, function( key, value ) {
                                            errorsHtml += '<li>' + value[0] + '</li>';
                                        });
                                    }
                                    else
                                    {
                                        errorsHtml = 'Unknown error';
                                    }
                                    button.attr('disabled', false);
                                    toastr.error(errorsHtml);
                                }
                        });
                    });


</script>
@endsection