@section('menu')
<aside class="col-md-3 col-md-pull-9">
    <div class="ak-container ak-main-aside" style="margin: 0 6px;">

        @if (Auth::guest())
        <div class="row ak-container ak-panel ak-panel-blue">
            <div class="ak-panel-title">
                <span class="ak-panel-title-icon ak-icon-med ak-bank"></span> Connexion
            </div>
            <div class="ak-panel-content ak-login-panel">
                <div class="ak-form">
                    {!! Form::open(['route' => 'login']) !!}
                        <div class="form-group @if ($errors->has('auth')) has-error @endif">
                            <label class="control-label" for="email">Email</label>
                            <input type="text" class="form-control" placeholder="Email" name="email" value="{{ Input::old('email') }}" id="email">
                        </div>

                        <div class="form-group @if ($errors->has('auth')) has-error @endif">
                            <label class="control-label" for="userpass">Mot de passe</label>
                            <input type="password" class="form-control" placeholder="Mot de passe" name="password" value="{{ Input::old('password') }}" id="userpass" data-hasqtip="0">
                        </div>

                        <div class="form-group">
                            <div class="checkbox">
                                <label><input type="checkbox" value="1" name="remember" checked="checked">Rester connecté</label>
                                <a href="{{ URL::route('register') }}" style="color:white;float:right">S'inscrire</a>
                            </div>
                        </div>
                        <input type="submit" role="button" class="btn btn-primary btn-lg btn-block" id="login_sub" value="Se connecter">
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
        @else
        <div class="row ak-container ak-panel ak-panel-blue">
            <div class="ak-panel-title">
                <span class="ak-panel-title-icon ak-icon-med ak-bank"></span> Mon compte
            </div>
            <div class="ak-panel-content ak-profile-panel">
                <div class="account-avatar"><a href="{{ URL::route('account.change_profile')}}"><img src="{{ URL::asset(Auth::user()->avatar) }}" /></a></div>
                <div class="account-details">
                    <div class="account-name">{{ Auth::user()->pseudo }}</div>
                    <div class="account-info">
                        <a href="{{ URL::route('profile') }}"><button class="btn btn-primary btn-sm">Gestion de compte</button></a>
                    </div>
                </div>
                <div class="clearfix"></div>
                <div class="ak-reserve-container">
                    <div class="row">
                        <div class="col-xs-4">
                            <div class="ak-ogrine-reserve-critical ak-ogrines-reserve">
                                <a href="{{ URL::route('shop.payment.country') }}">
                                    <span class="ak-reserve">{{ Utils::format_price(Auth::user()->points) }}</span>
                                    <span class="ak-icon-small ak-ogrines-icon"></span>
                                </a>
                                <div class="ak-tooltip hidden" style="display: none;">Acheter des Ogrines</div>
                                 <script type="application/json">{"tooltip":{"style":{"classes":"ak-tooltip-blue"}}}</script>   
                            </div>
                        </div>
                        <div class="col-xs-4">
                            <div class="ak-ogrine-reserve-critical ak-votes-reserve">
                                <a href="{{ URL::route('vote.index') }}">
                                    <span class="ak-reserve">{{ Utils::format_price(Auth::user()->jetons) }}</span>
                                    <span class="ak-icon-small ak-votes-icon"></span>
                                </a>
                                <div class="ak-tooltip hidden" style="display: none;">Gagner des Jetons</div>
                                 <script type="application/json">{"tooltip":{"style":{"classes":"ak-tooltip-blue"}}}</script>
                            </div>
                        </div>
                        <div class="col-xs-4">
                            <div class="ak-ogrine-reserve-critical ak-gifts-reserve">
                                <a href="{{ URL::route('lottery.index') }}">
                                    <span class="ak-reserve">{{ Utils::format_price(count(Auth::user()->lotteryTickets(true))) }}</span>
                                    <span class="ak-icon-small ak-ticket-icon"></span>
                                </a>
                                <div class="ak-tooltip hidden" style="display: none;">Jouer à la loterie</div>
                                 <script type="application/json">{"tooltip":{"style":{"classes":"ak-tooltip-blue"}}}</script>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="logout">
                    <a href="{{ URL::route('logout') }}"><button class="btn btn-primary btn-lg btn-block">Déconnexion</button></a>
                </div>
            </div>
        </div>
        @endif

        <div class="row ak-container">
            <div class="ak-block-shop">
                <div class="text-center ak-shop-top">
                    <a href="{{ URL::route('shop.index') }}" class="ak-shop-title"> La Boutique</a> 
                </div>
                <div class="ak-shop-articles">
                    <div class="row ak-container">
                        <div class="ak-column ak-container col-xs-12 col-sm-4 col-md-12">
                            <div class="ak-shop-article">
                                <div class="ak-shop-article-container">
                                    <div class="ak-shop-article-illustration">
                                        <a href="{{route('shop.market.buy',$shopCharacter[0]->id)}}">
                                        <img src="{{ DofusForge::playerId($shopCharacter[0]->character_id, $shopCharacter[0]->server, 'full', 1, 80, 171) }}" class="img-maxresponsive">
                                        </a>
                                    </div>
                                    <div class="ak-shop-article-infos">
                                        <span class="ak-shop-article-title"><a href="{{route('shop.market.buy',$shopCharacter[0]->id)}}">{{$shopCharacter[0]->character_name}}</a></span>
                                        <span class="ak-shop-article-server">{{ ucfirst($shopCharacter[0]->server)}}</span>
                                        <span class="ak-shop-article-price"><a href="{{route('shop.market.buy',$shopCharacter[0]->id)}}"><span class="ak-nobreak">{{Utils::format_price($shopCharacter[0]->ogrines)}} <span class="ak-icon-ogrines"></span></span></a></span>
                                    </div>
                                    <div class="ak-shop-article-action">
                                        <a href="{{route('shop.market.buy',$shopCharacter[0]->id)}}" class="btn btn-primary btn-lg ak-btn-unlock"><span class="ak-icon-med ak-unlock"></span> Acheter </a> 
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row ak-container" style="margin-top:10px;">
                        <div class="ak-column ak-container col-xs-12 col-sm-4 col-md-12">
                            <div class="ak-shop-article">
                                <div class="ak-shop-article-container">
                                    <div class="ak-shop-article-action">
                                        <a href="{{ URL::route('shop.payment.country') }}" class="btn btn-primary btn-lg ak-btn-unlock"><span class="ak-icon-med ak-unlock"></span> Acheter des <span class="ak-icon-ogrines"></span></a>
                                        <a href="{{ URL::route('shop.market') }}" class="btn btn-primary btn-lg ak-btn-unlock-character"><span class="ak-icon-med ak-unlock"></span> Acheter un <span class="ak-icon-character"></span></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="text-center">
                    <a href="{{ URL::route('shop.index') }}" class="ak-shop-link"> Aller sur la boutique </a> 
                </div>
            </div>
        
            <a class="ak-btn-code" href="{{ URL::route('vote.index') }}" style="padding-left:70px;font-size:22px;">Vote et gagne des cadeaux</a>

            <a class="ak-home-facebook" href="{{ config('dofus.social.facebook') }}" target="_blank">Nous suivre<br>sur <span>Facebook</span></a>
            <a class="ak-home-youtube" href="{{ config('dofus.social.youtube') }}" target="_blank">visiter la chaine<br>youtube <span>{{ config('dofus.title') }}</span></a>
            <a class="ak-home-discord" href="{{ config('dofus.social.discord') }}" target="_blank">Nous rejoindre <br>sur <span>Discord</span></a>
            <!--<a class="ak-home-twitter" href="{{ config('dofus.social.twitter') }}" target="_blank">Nous suivre<br>sur <span>Twitter</span></a>-->
            <div class="ak-twitter-timeline">
                <a class="twitter-timeline" height="350" href="{{ config('dofus.social.twitter') }}">Nous suivre<br>sur <span>Twitter</span></a>
                <script async src="//platform.twitter.com/widgets.js" charset="utf-8"></script>
            </div>

        </div>
    </div>
</aside>
@stop
