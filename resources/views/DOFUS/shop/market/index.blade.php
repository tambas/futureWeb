@extends('layouts.contents.default')
@include('layouts.menus.shop')

@section('header')
    {!! Html::style('css/encyclopedia.css') !!}
    {!! Html::style('css/directories.css') !!}
@stop

@section('breadcrumbs')
{? $page_name = 'Marché des personnages' ?}
{!! Breadcrumbs::render('shop.page', $page_name) !!}
@stop

@section('content')
<div id="ak-filters-move-target" class="ak-filters-move-target"></div><a id="list" class="ak-anchor"></a>
<div class="ak-title-container">
    <h1><span class="ak-icon-big ak-character"></span> Marché des personnages</h1>
</div>
<div class="ak-page-menu ak-glue">
   <nav class="ak-nav-expand ak-expanded">
      <div class="ak-nav-expand-container">
         <ul class="ak-nav-links ak-nav-expand-links">
            <li>
               <a href="{{URL::route('shop.index')}}">
               Accueil      </a>
            </li>
            <li>
               <a href="{{URL::route('shop.payment.country')}}">
               Ogrines      </a>
            </li>
            <li class="on">
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
<div class="ak-container ak-panel ak-nocontentpadding">
    <div class="text-center" style="padding-top:10px">
    <a href="{{URL::route('shop.market.sell')}}"><button type="button" role="button" class="btn btn-lg btn-info">Vendre un personnage</button></a>              
    </div>
    @if($market_characters->total() == 0)
        <div class="ak-panel-content">
                <div class="ak-list-options ak-listoptions-actions ak-ajaxloader" data-target="div.main">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="ak-list-info ak-list-info-no-item">
                            <img src="{{URL::asset('imgs/assets/no-result.png')}}" class="img-max-responsive">
                            <div><br>
                                <strong>Aucun</strong> élément ne correspond à vos critères.<br>
                                Il est possible que la liste mette 30 minutes à se mettre à jour.<br>
                            </div>
                            </div>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                </div>
                <div class="ak-list-options">
                    <div class="ak-list-info ak-list-info-no-item">
                        <strong>0</strong> éléments correspondent à vos critères  
                    </div>
                </div>
        </div>
    @else
   <div class="ak-panel-content">
      <div class="ak-list-options ak-listoptions-actions ak-ajaxloader" data-target="div.main">
         <div class="row">
            <div class="col-sm-12">
               <div class="ak-list-info" style="text-align:center;">
                @if($market_characters->hasMorePages())
                    @if($market_characters->total() > 200)
                    Plus de <strong>200</strong> résultats ont été trouvé. Veuillez affiner votre recherche en utilisant les filtres : Nom de personnage, Classe, Niveau et Sexe
                    @else
                    <strong>{{$market_characters->total()}}</strong> résultats ont été trouvé. Vous pouvez affiner votre recherche en utilisant les filtres : Nom de personnage, Classe, Niveau et Sexe
                    @endif
                @else
                    @if($market_characters->total() == 1)
                     <strong>{{$market_characters->total()}}</strong> résultat a été trouvé.
                    @else
                    <strong>{{$market_characters->total()}}</strong> résultats ont été trouvé.
                    @endif
                @endif
                </div>
            </div>
         </div>
         <div class="clearfix"></div>
      </div>
      <div class="ak-responsivetable-wrapper" style="overflow: hidden;">
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
                
                @foreach($market_characters as $market)
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
                    @if(Auth::user()->id != $market->user_id)
                        @if(Auth::user()->points < $market->ogrines)
                        <a class="ak-tooltip" style="cursor:pointer;"><span class="ak-icon-tiny ak-cart ak-tooltip" title="Acheter"></span></a>
                        <script type="application/json">{"manual":true,"tooltip":{"content":{"title":"","text":"<p>Votre réserve d'Ogrines est insuffisante pour faire cet achat: Il vous manque {{(int)ceil($market->ogrines - Auth::user()->points)}} Ogrines.<\/p><a href=\"{{URL::route('shop.payment.country')}}\" class=\"btn btn-primary\">Acheter des Ogrines<\/a>"},"style":{"classes":"ak-tooltip-white-shop"},"position":{"my":"bottom center","at":"top center","adjust":{"scroll":false}},"show":{"event":"click"},"hide":{"fixed":true,"delay":400}},"forceOnTouch":true}</script>                      
                        @else
                        <a href="{{route('shop.market.buy',$market->id)}}"><span class="ak-icon-tiny ak-cart ak-tooltip" title="Acheter"></span></a>
                        @endif
                    @else
                    <a href="javascript:void(0)"><span class="ak-icon-tiny ak-trashcan ak-tooltip" title="Retirer"></span></a>
                    <div class="ak-tooltip hide" style="display: none;">
                        <p>Êtes-vous sur de vouloir retirer votre personnage de la vente ?</p>      
                            <a data-id="{{$market->id}}" class="removelink btn btn-primary btn-sm">Confirmer</a>
                    </div>
                    <script type="application/json">{"forceOnTouch":true,"tooltip":{"show":{"event":"click"},"style":{"classes":"ak-tooltip-white-shop"},"hide":{"delay":300,"fixed":true}}}</script>
                    @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
         </table>
      </div>
   </div>
   <div class="ak-panel-footer">
        @if($market_characters->links())
            <div class="text-center ak-pagination">
                <nav>
                    {{ $market_characters->appends(Request::capture()->except('page'))->links('pagination.default', ['target' => 'div.main', 'settings' => '{"scroll":true}']) }}
                </nav>
            </div>
        @endif
   </div>
   @endif
</div>
@stop
@section('bottom')
<script>
    var $ = require('jquery');
                    
                    $('body').on('click', '.removelink', function () {
                        // Find ID of the post
                        var id = $(this).data('id');
                        $(this).attr('disabled', true).removeClass('removelink');
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
                                    $(this).attr('disabled', false).addClass('removelink');
                                    toastr.error(errorsHtml);
                                }
                        });
                    });


</script>
@endsection