@extends('layouts.contents.default')
@include('layouts.menus.base')

@section('header')
      {!! Html::style('css/shop.css') !!}
      {!! Html::style('css/directories.css') !!}
      {!! Html::style('css/builders.css') !!}
      <style>
        .loader {
            display: inline-block;
            border: 7px solid #f3f3f3; /* Light grey */
            border-top: 7px solid #3498db; /* Blue */
            border-radius: 50%;
            width: 25px;
            height: 25px;
            animation: spin 2s linear infinite;
        }
        </style>
@stop

@section('breadcrumbs')
{? $page_name = 'Acheter un personnage' ?}
{!! Breadcrumbs::render('shop.page', $page_name) !!}
@stop
@php
if(Input::old('charactername'))
  $charactername = Input::old('charactername');
else
  $charactername = $character->Name;
@endphp
@section('content')
<div class="ak-title-container ak-backlink">
    <h1><span class="ak-icon-big ak-character"></span>Acheter {{$character->Name}} sur {{ucfirst($server)}}</h1>
    <a href="{{URL::route('shop.market')}}" class="ak-backlink-button">
      Retour au marché    </a>
</div>

   <div class="ak-container ak-panel ak-nocontentpadding">
     <div class="ak-panel-content">
         <div class="ak-character-banner">
            <div class="ak-character-picture">
               <div class="ak-entitylook" alt="" style="background:url({{ DofusForge::player($character, $server, 'full', 1, 150, 220, 10) }}) top left;width:150px;height:220px">
               </div>
            </div>
            @if($character->ornamentActive($server))
            <a class="ak-character-picture-show on"></a>
            <a class="ak-character-ornament-show"></a>
            <div class="ak-character-ornament ornament-{{$character->ornamentActive($server)->AssetId}}">
               <span>{{$character->Name}}</span>
            </div>
            @endif
         </div>
         <div class="ak-directories-header ">
            <div class="ak-directories-icon">
               <div class="ak-entitylook" alt="" style="background:url({{ DofusForge::player($character, $server, 'face', 2, 35, 35) }}) top left;width:35px;height:35px">
               </div>
            </div>
            <div class="ak-directories-main-infos">
               <div class="ak-directories-property">
                  <span class="ak-directories-level">Niveau {{$character->level()}}</span>@if($character->PrestigeRank > 0)<span class="ak-directories-prestige"> - Prestige {{$character->PrestigeRank}}</span>@endif<br>
                  <span class="ak-directories-breed">{{$character->classe()}}</span>
               </div>
               @if($character->titleActive($server))
                <div class="ak-directories-property ak-directories-property-grade">
                    <span class="ak-directories-grade">{!! $character->titleActive($server)->name() !!}</span>
                </div>
               @endif
               <div class="ak-directories-property ak-directories-property-server">
                  <span class="server">
                  <span class="server_4001"></span>
                  <span class="ak-directories-server-name">{{ucfirst($server)}}</span>
                  </span>
               </div>
                <div class="ak-directories-property ak-directories-property-creation-date">
                    <span class="ak-directories-creation-date">Mis en vente : Le {{$marketCharacter->created_at->format('d/m/Y')}}</span>
                </div>
            </div>
         </div>
      </div>
   </div>
   <div class="ak-container ak-panel-stack ak-glue">
   <div class="ak-container ak-panel ak-choice-transfer" id="buyplace">
      <div class="ak-panel-content">
         <div class="ak-form ak-bag">
           {!! Form::open(['route' => ['shop.market.buy', $marketCharacter->id], 'class' => 'ak-container ak-simpleform ak-choiceform', 'id' => 'form-buy']) !!}
               <div class="ak-container ak-panel ak-nocontentpadding">
                  <div class="ak-panel-title">
                     <span class="ak-panel-title-icon"></span>
                     Fiche du personnage 
                  </div>
                  <p>N'hésitez pas à consulter une dernière fois la page perso de {{$marketCharacter->character_name}} avant de procéder à l'achat:</p>
                  <div class="text-center">
                  <a href="{{ URL::route('characters.view',[$server, $marketCharacter->character_id, $marketCharacter->character_name])}}" target="_blank" class="btn btn-info btn-lg">Consulter</a>
                  </div>
                  <div class="ak-panel-title">
                     <span class="ak-panel-title-icon"></span>
                     Sur quel compte souhaitez-vous placer le personnage ? 
                  </div>
                  <p>Vous devez être déconnecté du compte en jeu pour procéder à l'achat.<br>Celui-ci doit contenir au moins un slot disponible</p>
                  @if(count(Auth::user()->accounts($server)) > 0)
                    <div class="form-group @if ($errors->has('account')) has-error @endif">
                      <select class="form-control" name="account" id="account" style="border: 1px solid #c7c3b4;height: 44px;">
                          @foreach(Auth::user()->accounts($server) as $account)
                            @if($account->availableSlots() > 0)
                            <option data-slot="{{$account->availableSlots()}}" value="{{$account->Id}}" style="color:green;">{{$account->Login}} - {{$account->availableSlots()}} slot(s) disponible(s)</option>
                            @else
                            <option data-slot="{{$account->availableSlots()}}" value="{{$account->Id}}" style="color:red;">{{$account->Login}} - {{$account->availableSlots()}} slot disponible</option>
                            @endif
                          @endforeach
                      </select>
                    </div>
                    @if ($errors->has('account')) <p class="control-label" style="color:red; font-weight:bold;">{!! $errors->first('account') !!}</p> @endif
                    <p class="hidden control-label" id="erroraccount" style="color:red; font-weight:bold;"></p>
                  @else
                  <div class="alert alert-danger">
                      Vous ne possédez aucun compte de jeu
                  </div>
                  @endif
                  <div class="ak-panel-title">
                     <span class="ak-panel-title-icon"></span>
                     Changement de Nom ? Gratuit ! <span class="ak-icon-small ak-ogrines-icon"></span>                  </div>
                  <p>Si le pseudo est déjà utilisé en jeu ou simplement si vous le souhaitez, vous pouvez changer le nom du personnage</p>
                    <div class="form-group @if ($errors->has('charactername')) has-error @endif">
                        <div class="input-group">
                            <span class="input-group-addon"><span class="loader hidden"></span><span class="ak-icon-small ak-character"></span></span>
                            <input type="text" class="form-control" autocomplete="off" name="charactername" value="{{$charactername}}" id="charactername" autocapitalize="off" autocorrect="off" required="required" />
                        </div>
                    </div>
                    @if ($errors->has('charactername')) <p class="control-label" style="color:red; font-weight:bold;">{{ $errors->first('charactername') }}</p> @endif
                    <p class="hidden control-label" id="errorname" style="color:red; font-weight:bold;"></p>
                    <label class="hidden control-label" id="successname" style="color:green;"></label>
                    <div class="ak-panel-title">
                     <span class="ak-panel-title-icon"></span>
                     Recapitulatif 
                  </div>
                  <div id="ak-basket-content">
                <div class="ak-responsivetable-wrapper" style="overflow: hidden;">
                  <table border="1" id="recap_buy" class="ak-container ak-table ak-responsivetable" style="white-space: nowrap; visibility: visible;">
                      <thead>
                        <tr>
                            <th data-priority="1" class="img-first-column"></th>
                            <th data-priority="2">Article</th>
                            <th data-priority="3" class="ak-money-value">Valeur</th>
                            <th data-priority="4">Serveur</th>
                            <th data-priority="5" class="ak-money-value">Sous-total</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr class="ak-bg-odd">
                            <td>
                              <img onerror="this.src='{{URL::asset('imgs/shop/default_50_50.png')}}" src="{{ DofusForge::player($character, $server, 'face', 2, 50, 50) }}">
                            </td>
                            <td>
                              <a href="{{ URL::route('characters.view',[$server, $marketCharacter->character_id, $marketCharacter->character_name])}}" target="_blank">{{$character->Name}} </a>          
                            </td>
                            <td class="ak-money-value">
                              <span class="ak-display-price ak-currency-ogr"><span class="ak-nobreak">{{Utils::format_price($marketCharacter->ogrines)}} <span class="ak-icon-small ak-ogrines-icon"></span></span></span>      
                            </td>
                            <td>
                              {{ucfirst($server)}}
                            </td>
                            <td class="ak-subtotal ak-money-value" colspan="">
                              <span class="ak-display-price ak-currency-ogr"><span class="ak-nobreak">{{Utils::format_price($marketCharacter->ogrines)}} <span class="ak-icon-small ak-ogrines-icon"></span></span></span>  
                            </td>
                        </tr>
                         <tr class="ak-bg-even hidden" id="tr_change_name">
                            <td>
                              <img onerror="this.src='{{URL::asset('imgs/shop/default_50_50.png')}}" src="{{URL::asset('imgs/shop/change_name.png')}}" style="width:50px; height:50px;">
                            </td>
                            <td>
                              Changement de Nom      
                            </td>
                            <td class="ak-money-value">
                              <span class="ak-display-price ak-currency-ogr"><span class="ak-nobreak">Gratuit <span class="ak-icon-small ak-ogrines-icon"></span></span></span>      
                            </td>
                            <td>
                           
                            </td>
                            <td class="ak-subtotal ak-money-value" colspan="">
                              <span class="ak-display-price ak-currency-ogr"><span class="ak-nobreak">Gratuit <span class="ak-icon-small ak-ogrines-icon"></span></span></span>  
                            </td>
                        </tr>
                      </tbody>
                  </table>
                </div>
                <div class="ak-bag-total">
                  <strong>Total :</strong> <span class="ak-display-price ak-currency-ogr">{{Utils::format_price($marketCharacter->ogrines)}} <span class="ak-icon-small ak-ogrines-icon"></span></span>
                </div>
                 @if ($errors->has('recap')) <p class="control-label" style="color:red; font-weight:bold;">{!! $errors->first('recap') !!}</p> @endif
                <div class="ak-bag-actions">
                  <input type="submit" id="buy_button" role="button" class="btn btn-primary btn-lg" value="Acheter">               
                </div>
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
      
              checkNameAvailability = function(nameInput) {
                $this = nameInput;
                var validName = new RegExp('^[A-Z][a-z]{2,9}(?:-[A-Za-z][a-z]{2,9}|[a-z]{1,10})$');
                var name = $this.val();
                if(validName.test(name))
                {
                    var server = '{{ $server }}';
                    var route_base = '{{ route('home') }}';
                    var route = ''+route_base+'/utils/'+server+'/'+name+'';
                    $.ajax({
                        url: route,
                        method: "GET",
                        beforeSend: function() {
                            $("#errorname").addClass('hidden');
                            $("#successname").addClass('hidden');
                            $('span.ak-character').addClass('hidden');
                            $('.loader').removeClass('hidden');
                        },
                        complete: function(){
                            $('.loader').addClass('hidden');
                            $('span.ak-character').removeClass('hidden');
                        },
                        success: function(data, textStatus, jqXHR)
                        {
                          if(data.error)
                          {
                            validname = false;
                            $("#errorname").removeClass('hidden').text(data.error);
                            $("#successname").addClass('hidden');
                            $("#charactername").parents('.form-group').addClass('has-error');
                          }
                          else if(data.success)
                          {
                            validname = true;
                            $("#errorname").addClass('hidden');
                            $("#successname").removeClass('hidden').text(data.success);
                            $("#charactername").parents('.form-group').removeClass('has-error');
                          }
                        },
                    });
                }
                else
                {
                  validname = false;
                  $("#successname").addClass('hidden');
                  $("#charactername").parents('.form-group').addClass('has-error');
                  $("#errorname").removeClass('hidden').text("Le pseudo est invalide");
                }
            }

        // SOME VARIABLES
        var token = '{{ Session::token() }}';
        var typingTimer;
        var doneTypingInterval = 1000;

        var validform = false;
        var validaccount = false;
        var validname = false;

        var characterName = '{{$character->Name}}';
        // SET COLOR AFTER LOADING PAGE
        var color = $("#account option:first").css('color');
        var slot = $("#account option:first").data('slot');
        if(slot > 0)
        {
          validaccount = true;
        }
        $("#account").css('color', color);
        $("#account").css('font-weight','bold');
        $("#account").css('border-color', color);

        // CHECK NAME AVAILABILITE AFTER LOADING PAGE
        checkNameAvailability($("#charactername"));

        // SET COLOR AFTER CHANGE OPTION
        $( "#account" ).change(function(e) {
            var optionSelected = $("option:selected", this);
            if(optionSelected.data('slot') > 0)
            {
              validaccount = true;
            }
            else
            {
              validaccount = false;
            }
            $(this).css('color', optionSelected.css('color'));
            $(this).css('font-weight','bold');
            $(this).css('border-color', optionSelected.css('color'));
        });
        // ON CHARACTER NAME CHANGE
         $("#charactername").on("keyup paste", function() {
            var self = $(this);
                clearTimeout(typingTimer);
                    typingTimer = setTimeout(function() {
                        checkNameAvailability(self);
                    }, doneTypingInterval);
            if($(this).val() != characterName)
            {
              $("#tr_change_name").removeClass('hidden');
            }
            else
            {
              $("#tr_change_name").addClass('hidden');
            }
         });

        // BLOCK FORM SUBMIT IF NECESSARY
        $("#form-buy").submit(function(e){
          $("#buy_button").attr('disabled', true);
          if(validaccount && validname)
          {
            validform = true;
          }

          if(validform === false)
          {
              e.stopPropagation();
              toastr.error('Veuillez vérifier le formulaire');
              $("#buy_button").attr('disabled', false);
              return false;
          }
        });
      });
</script>
@endsection