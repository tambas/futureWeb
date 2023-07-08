<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Code</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
    <script>
    openModal = function(e, link) {
        e.preventDefault();
        window.open(link, 'Payment', 'toolbar=0,scrollbars=1,location=0,statusbar=0,menubar=0,resizable=1,width=800,height=850,left = 420,top = 150');
        return false;
    };
    </script>
    <style>
        .col-centered{
            float: none;
            margin: 0 auto;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-5 col-centered">
                <div class="panel panel-primary" style="margin-top:10px;">
                    <div class="panel-heading">Obtenir un code</div>
						<div class="panel-body">
							<center>
								{!! $payment->legal->header !!}
								
								Cliquez sur le lien suivant pour obtenir le code :
								<a href onClick="openModal(event, '{{ $payment->link }}'); return false;" class="btn btn-default" target="_blank">Payer avec <img src="{{ URL::asset('imgs/shop/payment/audiotel.png') }}" height="30" /></a>
			
								<br>
								{!! $payment->text !!}
								<br>
								{!! $payment->legal->footer !!}
							</center>
							
							{!! Form::open(['route' => 'code', 'class' => 'form-group']) !!}
								<input type="hidden" name="ticket" value="{{ Auth::user()->ticket }}" />
								<input type="hidden" name="country" value="{{ $country }}" />
								<input type="hidden" name="pay_id" value="{{ $method }}_{{ $palier }}" />
								<div class="form-group @if ($errors->has('code')) has-error @endif">
									<div class="input-group input-group-lg">
										<span class="input-group-addon" id="sizing-addon1">Code</span>
										<input type="text" class="form-control" name="code" value="{{ Input::old('code') }}" @if ($errors->has('code')) class="has-error" @endif aria-describedby="sizing-addon1">
										<span class="input-group-btn">
											<button class="btn btn-primary" type="submit">Valider</button>
										</span>
									</div>
									@if ($errors->has('code'))<label class="error control-label">{{ $errors->first('code') }}</label> @endif
								</div>
							{!! Form::close() !!}
						</div>
                </div>

                <div class="panel panel-default">
                    <div class="panel-heading">Informations</div>
					<div class="panel-body">
                        <ol>
                            <li>Cliquez sur le lien ci-dessus.</li>
                            <li>Une fois sur la page, cliquez sur le bouton "Afficher le numéro"</li>
                            <li>Appelez le numéro affiché puis notez le code.</li>
                            <li>Retournez sur cette page puis entrez votre code.</li>
                        </ol>
					</div>
                </div>

                <p class="small">Vous devez être le propriétaire du moyen de paiement pour utiliser ce service (ou avoir obtenu son autorisation). Les mineurs doivent avoir obtenu l'accord de leurs parents ou représentants légaux avant d'utiliser ce moyen de paiement. Ne conservez pas le numéro de téléphone, il peut changer à tout moment. Vous avez 24h pour utiliser le code d'accès ou le numéro d'ordre. Le coût de l'achat sera reporté sur votre relevé téléphonique ou bancaire.</p>
                <p class="text-muted small">Utiliser un moyen de paiement à l'insu de son propriétaire ou contester indûment un paiement sont des délits sanctionnés dans le monde entier. Nous nous réservons le droit de poursuivre tout contrevenant.</p>
            </div>
        </div>
    </div>
</body>
</html>
