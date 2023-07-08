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
            <div class="panel-main">
                <div class="row">
                    <div class="col-sm-7">
                        @if ($method == 'audiotel' || $method == 'mobilecall')
                        Pour obtenir votre code, appelez le
                        <div class="payment-number">{{ $payment->number }}</div>
                        @elseif ($method == 'sms')
                        <div class="payment-number">Envoyer <b>{{ $payment->keyword }}</b> au <b>{{ $payment->number }}</b></div>
                        @else
                        Cliquez sur le lien suivant pour obtenir le code :
                        <div class="payment-number"><a href="{{ $payment->link }}&email={{ Auth::user()->email }}" target="_blank">Payer avec <img src="{{ URL::asset('imgs/shop/payment/' . $method . '.png') }}" /></a></div>
                        @endif
                        <div class="payment-cost">{!! $payment->text !!}</div>
                    </div>
                    <div class="col-sm-5">
                        En cas de problème, veuillez contacter le <b><a href="#">support</a></b>
                        <div class="payment-code ak-form">
                            {!! Form::open(['route' => 'shop.payment.process', 'class' => 'form-group']) !!}
                                <input type="hidden" name="country" value="{{ $country }}" />
                                <input type="hidden" name="method" value="{{ $method }}_{{ $palier }}" />
                                <input type="hidden" name="cgv" value="{{ $cgv }}" />
                                <div class="form-group @if ($errors->has('code')) has-error @endif">
                                    Entrez votre code : <input type="text" name="code" value="{{ Input::old('code') }}" @if ($errors->has('code')) class="has-error" @endif />
                                    @if ($errors->has('code')) <br><br><p style="text-align:center;"><label class="error control-label">{{ $errors->first('code') }}</label></p> @endif
                                </div>
                                <div class="payment-submit"><input type="submit" class="btn btn-primary btn-lg" value="Valider" /></div>
                            {!! Form::close() !!}
                        </div>
                    </div>
                </div>

                <div class="payment-steps">
                    <ul>
                        @if ($method == 'audiotel' || $method == 'mobilecall')
                        <li><span class="step">1</span>Préparez un crayon et une feuille de papier pour noter le <b>code d'accès</b>.</li>
                        <li><span class="step">2</span>Appelez le numéro de téléphone de votre pays et suivez les instructions.</li>
                        <li><span class="step">3</span>Notez précisément le code d'accès obtenu, puis raccrochez sans attendre.</li>
                        <li><span class="step">4</span>Entrez le code dans le formulaire.</li>
                        <li><span class="step">5</span>Enfin, cliquez sur "<b>Valider</b>".</li>
                        @elseif ($method == 'sms')
                        <li><span class="step">1</span>Envoyez le texte au numéro ci-dessus à partir de votre téléphone mobile.</li>
                        <li><span class="step">2</span>En réponse, vous recevrez un code d'accès.</li>
                        <li><span class="step">3</span>Entrez le code dans le formulaire.</li>
                        <li><span class="step">4</span>Enfin, cliquez sur "<b>Valider</b>".</li>
                        @else
                        <li><span class="step">1</span>Cliquez sur le lien ci-dessus.</li>
                        <li><span class="step">2</span>Suivez les instructions indiqués.</li>
                        <li><span class="step">3</span>Vous recevrez un code d'accès.</li>
                        <li><span class="step">4</span>Entrez le code dans le formulaire.</li>
                        <li><span class="step">5</span>Enfin, cliquez sur "<b>Valider</b>".</li>
                        @endif
                    </ul>

                    <p class="payment_bottom_infos">Vous devez être le propriétaire du moyen de paiement pour utiliser ce service (ou avoir obtenu son autorisation). Les mineurs doivent avoir obtenu l'accord de leurs parents ou représentants légaux avant d'utiliser ce moyen de paiement. Ne conservez pas le numéro de téléphone, il peut changer à tout moment. Vous avez 24h pour utiliser le code d'accès ou le numéro d'ordre. Le coût de l'achat sera reporté sur votre relevé téléphonique ou bancaire.</p>
                    <div class="hr3"></div>
                    <p class="grey payment_bottom_infos text-muted small">Votre paiement au serveur {{ config('dofus.title') }} sera assuré par {{ config('dofus.payment.' . config('dofus.payment.used') . '.name') }}. Utiliser un moyen de paiement à l'insu de son propriétaire ou contester indûment un paiement sont des délits sanctionnés dans le monde entier. {{ config('dofus.title') }} et ses représentants se réservent le droit de poursuivre tout contrevenant.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@stop
