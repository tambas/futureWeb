<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Code</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
    <script>
    var isValidated = false;

    openModal = function(e, link) {
        e.preventDefault();
        window.open(link, 'Payment', 'toolbar=0,scrollbars=1,location=0,statusbar=0,menubar=0,resizable=1,width=800,height=850,left = 420,top = 150');
        $('.loader').fadeIn();
		@if ($method == 'carte bancaire')
			setTimeout(startCheck, 3000);
		@endif
        return false;
    };

    function startCheck() {
        jQuery.ajax({
            url: "{{ $check_url }}/{{ $key }}?ticket={{ $ticket }}",
            success: function (result) {
                if (result == "true") {
                    isValidated = true;
                    $('.loader').html("Paiement validé, vous pouvez fermer cette page");
                }
            },
            async: false
        });

        if (!isValidated) setTimeout(startCheck, 3000);
    }
    </script>
    <style>
        .col-centered{
            float: none;
            margin: 0 auto;
        }

        .loader {
            text-align: center;
            display: none;
        }

        .recursos-cb-fallback {
            text-align: center;
            font-size: 10px;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-5 col-centered">
                <div class="panel panel-primary" style="margin-top:10px;">
                    <div class="panel-heading">Obtenir un code</div>
					@if ($method == 'carte bancaire')
						<div class="panel-body">
							<center>
								{!! $payment->legal->header !!}

								Cliquez sur le lien suivant pour obtenir le code :
								<a href onClick="openModal(event, '{{ $payment->link }}/{{ $key }}/{{ $palier }}?ticket={{ $ticket }}'); return false;" class="btn btn-default" target="_blank">Payer avec <img src="{{ URL::asset('images/' . $method . '.png') }}" height="30" /></a>

								<br>
								{!! $payment->text !!}
								<br>
								{!! $payment->legal->footer !!}
							</center>
							<br>
							<div class="loader">
								En attente de la validation du paiement<br>
								<svg width='38px' height='38px' xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid" class="uil-default"><rect x="0" y="0" width="100" height="100" fill="none" class="bk"></rect><rect  x='46.5' y='40' width='7' height='20' rx='5' ry='5' fill='#00b2ff' transform='rotate(0 50 50) translate(0 -30)'>  <animate attributeName='opacity' from='1' to='0' dur='1s' begin='0s' repeatCount='indefinite'/></rect><rect  x='46.5' y='40' width='7' height='20' rx='5' ry='5' fill='#00b2ff' transform='rotate(30 50 50) translate(0 -30)'>  <animate attributeName='opacity' from='1' to='0' dur='1s' begin='0.08333333333333333s' repeatCount='indefinite'/></rect><rect  x='46.5' y='40' width='7' height='20' rx='5' ry='5' fill='#00b2ff' transform='rotate(60 50 50) translate(0 -30)'>  <animate attributeName='opacity' from='1' to='0' dur='1s' begin='0.16666666666666666s' repeatCount='indefinite'/></rect><rect  x='46.5' y='40' width='7' height='20' rx='5' ry='5' fill='#00b2ff' transform='rotate(90 50 50) translate(0 -30)'>  <animate attributeName='opacity' from='1' to='0' dur='1s' begin='0.25s' repeatCount='indefinite'/></rect><rect  x='46.5' y='40' width='7' height='20' rx='5' ry='5' fill='#00b2ff' transform='rotate(120 50 50) translate(0 -30)'>  <animate attributeName='opacity' from='1' to='0' dur='1s' begin='0.3333333333333333s' repeatCount='indefinite'/></rect><rect  x='46.5' y='40' width='7' height='20' rx='5' ry='5' fill='#00b2ff' transform='rotate(150 50 50) translate(0 -30)'>  <animate attributeName='opacity' from='1' to='0' dur='1s' begin='0.4166666666666667s' repeatCount='indefinite'/></rect><rect  x='46.5' y='40' width='7' height='20' rx='5' ry='5' fill='#00b2ff' transform='rotate(180 50 50) translate(0 -30)'>  <animate attributeName='opacity' from='1' to='0' dur='1s' begin='0.5s' repeatCount='indefinite'/></rect><rect  x='46.5' y='40' width='7' height='20' rx='5' ry='5' fill='#00b2ff' transform='rotate(210 50 50) translate(0 -30)'>  <animate attributeName='opacity' from='1' to='0' dur='1s' begin='0.5833333333333334s' repeatCount='indefinite'/></rect><rect  x='46.5' y='40' width='7' height='20' rx='5' ry='5' fill='#00b2ff' transform='rotate(240 50 50) translate(0 -30)'>  <animate attributeName='opacity' from='1' to='0' dur='1s' begin='0.6666666666666666s' repeatCount='indefinite'/></rect><rect  x='46.5' y='40' width='7' height='20' rx='5' ry='5' fill='#00b2ff' transform='rotate(270 50 50) translate(0 -30)'>  <animate attributeName='opacity' from='1' to='0' dur='1s' begin='0.75s' repeatCount='indefinite'/></rect><rect  x='46.5' y='40' width='7' height='20' rx='5' ry='5' fill='#00b2ff' transform='rotate(300 50 50) translate(0 -30)'>  <animate attributeName='opacity' from='1' to='0' dur='1s' begin='0.8333333333333334s' repeatCount='indefinite'/></rect><rect  x='46.5' y='40' width='7' height='20' rx='5' ry='5' fill='#00b2ff' transform='rotate(330 50 50) translate(0 -30)'>  <animate attributeName='opacity' from='1' to='0' dur='1s' begin='0.9166666666666666s' repeatCount='indefinite'/></rect></svg>
							</div>
							<div class="recursos-cb-fallback">En cas de problème: <a href="{{ URL::route('code_re_fallback') }}?ticket={{ $ticket }}">saisir le code manuellement</a></div>
						</div>
					@else
						<div class="panel-body">
							<center>
								{!! $payment->legal->header !!}
								
								Cliquez sur le lien suivant pour obtenir le code :
								<a href onClick="openModal(event, '{{ $payment->link }}&x={{ $userid }}'); return false;" class="btn btn-default" target="_blank">Payer avec <img src="{{ URL::asset('images/' . $method . '.png') }}" height="30" /></a>

								<br>
								{!! $payment->text !!}
								<br>
								{!! $payment->legal->footer !!}
							</center>
						</div>
					@endif
                </div>

                <div class="panel panel-default">
                    <div class="panel-heading">Informations</div>
                    <div class="panel-body">
                        <ol>
                            <li>Cliquez sur le lien ci-dessus.</li>
                            <li>Une fois vos informations entrées, validez et normalement le code sera validé automatiquement et vos Ogrines seront créditées sur votre compte site.</li>
                            <li>En cas d'erreur, veuillez vérifier vos e-mails afin de rentrer manuellement le code avec le bouton ci-dessus.</li>
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
