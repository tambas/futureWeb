@extends('layouts.contents.default')
@include('layouts.menus.base')

@section('header')
      {!! Html::style('css/shop.css') !!}
@stop

@section('breadcrumbs')
{? $page_name = 'Vendre un personnage' ?}
{!! Breadcrumbs::render('shop.page', $page_name) !!}
@stop

@section('content')
<div class="ak-title-container ak-backlink">
    <h1><span class="ak-icon-big ak-character"></span>Marché des personnages</h1>
    <a href="{{URL::route('shop.market')}}" class="ak-backlink-button">
      Retour au marché    </a>
</div>
<div class="ak-container ak-panel-stack ak-glue">
   <div class="ak-container ak-panel ak-article-infos ak-display-inline">
      <div class="ak-panel-content">
         <div class="row">
            <div class="col-sm-1">
               <div class="ak-article-illu">
                  <img onerror="this.src='{{URL::asset('imgs/shop/default_200_200.png')}}'" src="{{URL::asset('imgs/shop/buy_char_200_200.png')}}" itemprop="image" class="img-maxresponsive" data-max="200">
               </div>
            </div>
            <div class="col-sm-11">
               <div class="ak-container ak-panel ak-article-heading ak-nocontentpadding">
                  <div class="ak-panel-title">
                     <span class="ak-panel-title-icon"></span>
                     <span itemprop="name">Vendre un personnage</span>            
                  </div>
                  <div class="ak-panel-content">
                     <div class="price">
                        <span>
                        <span class="ak-display-price ak-currency-ogr"><span class="ak-nobreak">{{config('dofus.characters_market.minimal_price')}} <span class="ak-icon-small ak-ogrines-icon"></span> minimum</span></span></span>
                     </div>
                  </div>
               </div>
            </div>
         </div>
         <div>
            <div class="ak-container ak-panel ak-infos-right ak-nocontentpadding">
               <div class="ak-panel-content">
                  <div class="ak-hr"></div>
                  <div>
                     <div class="ak-container ak-panel ak-article-description ak-nocontentpadding">
                        <div class="ak-panel-content">
                           <div itemprop="description">
                              <p><strong>Vous n'avez plus besoin d'un de vos personnages et vous souhaitez le mettre en vente ?</strong><br>
                                 &nbsp;
                              </p>
                              <ul>
                                 <li>Votre personnage doit être de niveau 20 minimum et s'être connecté en jeu depuis moins de 6 mois</li>
                                 <li>Le compte de votre personnage ne peut pas être en prison ou banni</li>
                                 <li>Vous devez être déconnecté du compte de jeu du personnage</li>
                                 <li>Lors de la mise en vente, vous devrez payer une taxe de mise en vente. Cette taxe équivaut à {{config('dofus.characters_market.procent_taxe')}} % du prix de vente</li>
                                 <li>Tout ce que contient votre personnage (équipements, parchemins, etc) sera transféré avec celui-ci. Si un autre joueur vous l'achète, il vous sera impossible de les récupérer</li>
                                 <li>Tant que votre personnage n'a pas été achété, il vous sera possible de le retirer de la vente. Cependant, la taxe ne vous sera pas remboursé</li>
                              </ul>
                              <p>&nbsp;<br>
                                 <em>Attention ! Il ne suffit pas de mettre en vente son personnage pour aussitôt s'en débarasser ! Il faudra pour cela qu'un autre joueur s'y interesse et décide d'y mettre le prix.. </em><br>
                                 <br>
                                 Bonne vente&nbsp;!
                              </p>
                              <p><strong>ATTENTION : </strong></p>
                              <p><strong>En mettant en vente votre personnage, vous êtes avertis que tout ce qu'il contient (équipements, parchemins, etc) sera définitivement perdu si un autre joueur vous l'achète.</strong></p>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
   <div class="ak-container ak-panel ak-choice-transfer" id="sellplace">
      <div class="ak-panel-content">
         <div class="ak-form">
           {!! Form::open(['route' => 'shop.market.sell', 'class' => 'ak-container ak-simpleform ak-choiceform', 'id' => 'form-sell']) !!}
               <div class="ak-container ak-panel ak-nocontentpadding">
                  <div class="ak-panel-title">
                     <span class="ak-panel-title-icon"></span>
                     Quel personnage souhaitez-vous mettre en vente ?            
                  </div>
                  <p>Vous devez être déconnecté du compte en jeu pour procéder à la mise en vente</p>
                  <div class="ak-panel-content">
                     <div class="ak-container ak-content-list ak-displaymode-image-col">
                        <div class="row ak-container">
                          @if(count(Auth::user()->characters(true)) <= 0)
                            <div class="alert alert-danger">
                              Vous ne possédez aucun personnage
                            </div>
                          @else
                            @foreach(Auth::user()->characters(true) as $k => $character)
                            <div class="ak-column ak-container col-md-6">
                                <div class="ak-list-element">
                                  <div class="ak-main">
                                      <div class="ak-main-content">
                                        <div class="ak-image">
                                            <div class="ak-entitylook" alt="" style="background:url({{ DofusForge::player($character, $character->server, 'face', 2, 70, 70) }}) top left;width:70px;height:70px"></div>
                                        </div>
                                        <div class="ak-content">
                                            <div class="ak-title">
                                              {{$character->Name}} <a href="{{ URL::route('characters.view', [$character->server, $character->Id, $character->Name]) }}" target="_blank"><span class="ak-icon-small ak-filter ak-tooltip" title="Consulter"></a>                       
                                            </div>
                                            <div class="ak-text">Niv. {{$character->level($character->server)}} - @if($character->PrestigeRank > 0)- P{{$character->PrestigeRank}} - @endif {{ucfirst($character->server)}}</div>
                                            @php $account = $character->account($character->server) @endphp
                                            @if($account->IsJailed == 1 || $account->isBanned())
                                              <div class="ak-text" style="color:red;">Le compte de ce personnage est en prison ou banni</div>
                                            @endif
                                            @if($account->isStaff())
                                              <div class="ak-text" style="color:red;">Le compte de ce personnage fait partie du staff</div>
                                            @endif
                                        </div>
                                        @if($account->IsJailed != 1 && !$account->isBanned() && !$account->isStaff())
                                        <div class="ak-aside">
                                            <div class="form-group">
                                              <div class="radio">
                                                  <input required type="radio" value="{{$character->Id}}" name="character" id="{{$character->Id}}">
                                                  <input hidden type="radio" value="{{$character->server}}" name="server" id="{{$character->server}}">
                                              </div>
                                            </div>
                                        </div>
                                        @endif
                                      </div>
                                  </div>
                                </div>
                            </div>
                              @if($k % 2)
                              <div class="clearfix  visible-md visible-lg"></div>
                              @endif
                            @endforeach
                          @endif
                        </div>
                        @if ($errors->has('character')) <p class="control-label" style="color:red; font-weight:bold;">{{ $errors->first('character')}}</p>@endif
                        @if ($errors->has('server')) <p class="control-label" style="color:red; font-weight:bold;">{{ $errors->first('server')}}</p>@endif
                     </div>
                  </div>
                  <div class="ak-panel-title">
                     <span class="ak-panel-title-icon"></span>
                     A quel prix ? 
                  </div>
                  <div class="form-group @if ($errors->has('ogrines')) has-error @endif">
                        <div class="input-group">
                            <span class="input-group-addon"><span class="ak-icon-small ak-ogrines-icon"></span></span>
                            <input type="text" class="form-control" autocomplete="off" name="ogrines" placeholder="Minimum {{config('dofus.characters_market.minimal_price')}}" value="{{ Input::old('ogrines') }}" id="ogrines" autocapitalize="off" autocorrect="off" required="required" />
                        </div>
                    </div>
                    @if ($errors->has('ogrines')) <p class="control-label" style="color:red; font-weight:bold;">{{ $errors->first('ogrines') }}</p> @endif
                    <p class="hidden control-label" style="color:red; font-weight:bold;" id="errorogrines"></p>
                  <div class="ak-panel-title">
                     <span class="ak-panel-title-icon"></span>
                     Taxe de mise en vente: 
                  </div>
                  <div class="form-group @if ($errors->has('taxe')) has-error @endif">
                        <div class="input-group">
                            <span class="input-group-addon"><span class="ak-icon-small ak-ogrines-icon"></span></span>
                            <input type="text" readonly="readonly" class="form-control" autocomplete="off" name="taxe" placeholder="0" value="{{ Input::old('taxe') }}" id="taxe" autocapitalize="off" autocorrect="off" />
                        </div>
                    </div>
                    @if ($errors->has('taxe')) <p class="control-label" style="color:red; font-weight:bold;">{!! $errors->first('taxe') !!}</p> @endif
                    <p class="hidden control-label" style="color:red; font-weight:bold;" id="errortaxe"></p>
                    <div class="text-center">
                    <input id="submit_button" type="submit" role="button" class="btn btn-primary btn-lg" value="Mettre en vente">
                    </div>
               </div>
            {!!Form::close()!!}
         </div>
      </div>
   </div>
</div>
@stop
@section('bottom')
<script>
      var $ = require('jquery');

    $(document).ready(function() {

        var formvalid = false;
        var charactervalid = false;
        var priceortaxevalid = false;
        
        isPriceValid = function(input) {
          $this = input;
          var userpoints = {{Auth::user()->points}};
          var minimal_price = {{config('dofus.characters_market.minimal_price')}};
          var procent = {{config('dofus.characters_market.procent_taxe')}};
          var value = $this.val();
          var taxe = $("#taxe");
          var error = $("#errorogrines");
          var errortaxe = $("#errortaxe");
        
          if($.isNumeric(value))
          {
            if(value >= minimal_price)
            {
              error.addClass('hidden');
              var taxeprice = Math.ceil(((value / 100) * procent));
              taxe.val(taxeprice);
              if(taxeprice > userpoints)
              {
                errortaxe.removeClass('hidden').text("Vous n'avez pas assez d'ogrines pour payer la taxe de mise en vente");
                priceortaxevalid = false;
                
              }
              else
              {
                 errortaxe.addClass('hidden');
                 priceortaxevalid = true;
              }
            } 
            else
            {
              taxe.val('');
              error.removeClass('hidden').text('Le prix doit être supérieur à '+minimal_price);
              priceortaxevalid = false;
            }
          }
          else if(value == '')
          {
            taxe.val('');
            error.addClass('hidden');
            priceortaxevalid = false;
          }
          else
          {
            taxe.val('');
            error.removeClass('hidden').text('Le prix est incorrect');
            priceortaxevalid = false;
          }
        }

        if($("#ogrines").val() != '')
        {
          isPriceValid($("#ogrines"));
        }

        $('.ak-list-element').click(function() {
          $(this).find("input[type='radio']").prop("checked", true).trigger("change");
          charactervalid = true;
        });

        $("#form-sell").submit(function(e){
         $("#submit_button").attr("disabled", true);
          if(charactervalid && priceortaxevalid)
            formvalid = true;

          if(formvalid === false)
          {
              e.stopPropagation();
              toastr.error('Veuillez vérifier le formulaire');
              $("#submit_button").attr("disabled", false);
              return false;
          }
        });

        $("#ogrines").on("change paste keyup", function() {
          isPriceValid($("#ogrines"));
        });
    });
</script>
@endsection