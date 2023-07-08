@extends('layouts.boostrap')

@section('breadcrumbs')
<h1>Gestion de compte</h1>
{!! Breadcrumbs::render('page', 'Gestion de compte') !!}
@stop

@section('page')

<div class="col-md-9">

    <div class="panel panel-default">
        <div class="panel-heading">Mes comptes de jeu</div>
        <div class="panel-body simple">

            <p>Voici la liste des comptes de jeu que vous possédez.</p>

            <div class="row">

                @foreach ($accounts as $account)

                <div class="col-md-3 col-xs-12">
                    <div class="thumbnail">
                        <img src="{{ Gravatar::src($account->Nickname, 171) }}">
                        <div class="caption" style="text-align: center;">
                            <h4>{{ $account->Nickname }}</h4>
                            <p>{{ count($account->characters()) }} personnage(s)</p>
                            <p><a href="" class="btn btn-default btn-block" role="button"><i class="fa fa-search" aria-hidden="true"></i> Visualiser</a></p>
                        </div>
                    </div>
                </div>

                @endforeach

                @can('user-edit')
                CREATE
                @endcan

                @for ($i = 0; $i < 2; $i++)

                <div class="col-md-3 col-xs-12">
                    <a href="">
                        <div class="thumbnail used">
                            <h1><i class="fa fa-users"></i></h1>
                            <div class="caption">
                                <h4>Luax</h4>
                                <p>3 personnages</p>
                            </div>
                        </div>
                    </a>
                </div>

                @endfor

                @for ($i = 0; $i < 2; $i++)

                <div class="col-md-3 col-xs-12">
                    <a href="">
                        <div class="thumbnail free">
                            <h1><i class="fa fa-user-plus"></i></h1>
                            <div class="caption">
                                <h5>Compte libre</h5>
                                <p>Créer un compte <i class="fa fa-plus" aria-hidden="true"></i></p>
                            </div>
                        </div>
                    </a>
                </div>

                @endfor

            </div>

        </div>
    </div>

</div>

<div class="col-md-3">

    <div class="panel panel-default about">
        <div class="panel-body">
            <div class="row">

                <div class="col-xs-4">
                    <img src="{{ Gravatar::src(Auth::user()->email, 262) }}">
                </div>

                <div class="col-xs-8">
                    <p>Bonjour <b>{{ Auth::user()->firstname }}</b></p>
                    <p><a href="{{ route('logout') }}"><i class="fa fa-power-off" aria-hidden="true"></i> Se déconnecter</a></p>
                </div>
            </div>

        </div>
    </div>

    <div class="panel panel-primary money">
        <div class="panel-body">
            <p>Mon solde de points</p>
            <h1>0</h1>
            <p><b><a href=""><i class="fa fa-cart-plus" aria-hidden="true"></i> En acheter d'avantage</a></b></p>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-body panel-menu">
            <ul class="nav" >
                <li><a href=""><i class="fa fa-user" aria-hidden="true"></i> Mon profile</a></li>
                <li><a href=""><i class="fa fa-users" aria-hidden="true"></i> Mes comptes de jeu</a></li>
                <li><a href=""><i class="fa fa-shopping-cart" aria-hidden="true"></i> Mes achats</a></li>
                <li><a href=""><i class="fa fa-life-ring" aria-hidden="true"></i> Mes tickets</a></li>
            </ul>
        </div>
    </div>

</div>
@stop
