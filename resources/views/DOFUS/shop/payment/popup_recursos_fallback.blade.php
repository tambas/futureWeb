<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Code</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
    <style>
        .col-centered{
            float: none;
            margin: 0 auto;
        }

        .loader {
            text-align: center;
            display: none;
        }
    </style>
    <script>
        $(function() {
            var loader = $('.loader').html();

            $('#code_fallback').on('submit', function(event) {
                event.preventDefault();
                $('.loader').html(loader);
                $('.loader').fadeIn();
                $.ajax({
                    url: "{{ URL::route('code_re_fallback_process') }}?ticket={{ $ticket }}",
                    data: $("#code_fallback :input").serializeArray(),
                    success: function (result) {
                        if (result == "true") {
                            $('.loader').html("Paiement validé, vous pouvez fermer cette page");
                        }
                        else {
                            $('.loader').html(result);
                        }
                    },
                    async: true
                });
                return false;
            });
        });
    </script>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-5 col-centered">
                <div class="panel panel-primary" style="margin-top:10px;">
                    <div class="panel-heading">Sasir le code</div>
                    <div class="panel-body">
                        {!! Form::open(['route' => 'code_re_fallback_process', 'class' => 'form-group', 'id' => 'code_fallback']) !!}
                        <input type="hidden" name="ticket" value="{{ Auth::user()->ticket }}">
                        <div class="form-group @if ($errors->has('palier')) has-error @endif">
                            <div class="input-group input-group-lg">
                                <span class="input-group-addon" id="sizing-addon1">Palier</span>
                                <select class="form-control" name="palier" id="palier" @if ($errors->has('palier')) class="has-error" @endif>
                                    <option value="">Selectionnez le palier</option>
                                    @foreach ($paliers as $palierId => $palier)
                                        <option value="{{ $palierId }}">{{ $palier->cost }} - {{ $palier->points }} Ogrines</option>
                                    @endforeach
                                </select>
                            </div>
                            @if ($errors->has('palier'))<label class="error control-label">{{ $errors->first('palier') }}</label> @endif
                        </div>
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

                        <br>
                        <div class="loader">
                            En attente de la validation du paiement<br>
                            <svg width='38px' height='38px' xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid" class="uil-default"><rect x="0" y="0" width="100" height="100" fill="none" class="bk"></rect><rect  x='46.5' y='40' width='7' height='20' rx='5' ry='5' fill='#00b2ff' transform='rotate(0 50 50) translate(0 -30)'>  <animate attributeName='opacity' from='1' to='0' dur='1s' begin='0s' repeatCount='indefinite'/></rect><rect  x='46.5' y='40' width='7' height='20' rx='5' ry='5' fill='#00b2ff' transform='rotate(30 50 50) translate(0 -30)'>  <animate attributeName='opacity' from='1' to='0' dur='1s' begin='0.08333333333333333s' repeatCount='indefinite'/></rect><rect  x='46.5' y='40' width='7' height='20' rx='5' ry='5' fill='#00b2ff' transform='rotate(60 50 50) translate(0 -30)'>  <animate attributeName='opacity' from='1' to='0' dur='1s' begin='0.16666666666666666s' repeatCount='indefinite'/></rect><rect  x='46.5' y='40' width='7' height='20' rx='5' ry='5' fill='#00b2ff' transform='rotate(90 50 50) translate(0 -30)'>  <animate attributeName='opacity' from='1' to='0' dur='1s' begin='0.25s' repeatCount='indefinite'/></rect><rect  x='46.5' y='40' width='7' height='20' rx='5' ry='5' fill='#00b2ff' transform='rotate(120 50 50) translate(0 -30)'>  <animate attributeName='opacity' from='1' to='0' dur='1s' begin='0.3333333333333333s' repeatCount='indefinite'/></rect><rect  x='46.5' y='40' width='7' height='20' rx='5' ry='5' fill='#00b2ff' transform='rotate(150 50 50) translate(0 -30)'>  <animate attributeName='opacity' from='1' to='0' dur='1s' begin='0.4166666666666667s' repeatCount='indefinite'/></rect><rect  x='46.5' y='40' width='7' height='20' rx='5' ry='5' fill='#00b2ff' transform='rotate(180 50 50) translate(0 -30)'>  <animate attributeName='opacity' from='1' to='0' dur='1s' begin='0.5s' repeatCount='indefinite'/></rect><rect  x='46.5' y='40' width='7' height='20' rx='5' ry='5' fill='#00b2ff' transform='rotate(210 50 50) translate(0 -30)'>  <animate attributeName='opacity' from='1' to='0' dur='1s' begin='0.5833333333333334s' repeatCount='indefinite'/></rect><rect  x='46.5' y='40' width='7' height='20' rx='5' ry='5' fill='#00b2ff' transform='rotate(240 50 50) translate(0 -30)'>  <animate attributeName='opacity' from='1' to='0' dur='1s' begin='0.6666666666666666s' repeatCount='indefinite'/></rect><rect  x='46.5' y='40' width='7' height='20' rx='5' ry='5' fill='#00b2ff' transform='rotate(270 50 50) translate(0 -30)'>  <animate attributeName='opacity' from='1' to='0' dur='1s' begin='0.75s' repeatCount='indefinite'/></rect><rect  x='46.5' y='40' width='7' height='20' rx='5' ry='5' fill='#00b2ff' transform='rotate(300 50 50) translate(0 -30)'>  <animate attributeName='opacity' from='1' to='0' dur='1s' begin='0.8333333333333334s' repeatCount='indefinite'/></rect><rect  x='46.5' y='40' width='7' height='20' rx='5' ry='5' fill='#00b2ff' transform='rotate(330 50 50) translate(0 -30)'>  <animate attributeName='opacity' from='1' to='0' dur='1s' begin='0.9166666666666666s' repeatCount='indefinite'/></rect></svg>
                        </div>
                    </div>
                </div>

                <div class="panel panel-default">
                    <div class="panel-heading">Informations</div>
                    <div class="panel-body">
                        <ol>
                            <li>Selectionnez le palier utiliser lors de l'achat.</li>
                            <li>Entrez le code dans le formulaire.</li>
                            <li>Enfin, cliquez sur "<b>Valider</b>".</li>
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
