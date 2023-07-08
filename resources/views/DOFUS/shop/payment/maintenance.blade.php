@extends('layouts.contents.default')
@include('layouts.menus.base')

@section('breadcrumbs')
{? $page_name = 'Maintenance' ?}
{!! Breadcrumbs::render('shop.page', $page_name) !!}
@stop

@section('content')
<div class="ak-container ak-main-center">
    <div class="ak-title-container ak-backlink">
        <h1><span class="ak-icon-big ak-shop"></span></a> Maintenance</h1>
    </div>

    <div class="ak-container ak-panel panel-vote-link container-padding">
        <div class="ak-panel-content panel-main">
            <div class="row">
                <div class="col-sm-12">
                    <center>
                        <img src="{{ URL::asset('imgs/assets/maintenance.png') }}" alt="Maintenance">
                        <br><br>
                        <h3>Boutique en maintenance</h3>
                        L'achat d'Ogrines est actuellement indisponible.<br>
                        Merci de ressayer ulterieurement.<br><br>
                        <i>L'équipe {{ config('dofus.title') }}</i>.
                    </center>
                </div>
            </div>
        </div>
    </div>
</div>
@stop
