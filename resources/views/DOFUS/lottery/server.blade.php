@extends('layouts.contents.default')
@include('layouts.menus.base')

@section('header')
    {!! Html::style('css/codes.css') !!}
    {!! Html::style('css/set.css') !!}
@stop

@section('breadcrumbs')
{? $page_name = 'Loterie' ?}
{!! Breadcrumbs::render('page', $page_name) !!}
@stop

@section('content')
<div class="ak-page-header"></div>
<div class="ak-container ak-main-center">
    <div class="ak-title-container">
        <h1 class="ak-return-link">
            <span class="ak-icon-big ak-codes"></span> <span id="title">Choissisez un serveur !</span>
        </h1>
    </div>
    <div class="ak-container ak-panel-stack ak-glue">
        <div class="ak-container ak-panel ak-main-form-code">
            <div class="ak-panel-content">
                <div class="ak-container ak-panel ak-nocontentpadding">
                    <div class="ak-panel-content">
                        <div class="ak-container ak-panel ak-code-banner-form">
                            <div class="ak-panel-content">
                                <div class="row ak-container">
                                    <div class="ak-column ak-container col-md-4">
                                        <div class="ak-code-banner ak-banner-dofus"></div>
                                    </div>
                                    <div class="ak-column ak-container col-md-8">
                                        <!--<div class="ak-code-form-intro">
                                            <strong>Les tickets de loterie vous permettent de ganger des cadeaux !</strong>
                                            <br><br>
                                            Les ticket ce gagne lorsque vous atteignez un palier de vote ou lors d'évènement.
                                        </div>-->
                                        <div class="ak-container ak-panel">
                                            <div class="ak-panel-title">
                                                <span class="ak-panel-title-icon"></span> Liste des serveurs disponible
                                            </div>
                                            <div class="ak-panel-content">
                                                <div class="row ak-container ak-content-list ak-encyclo-item-crafts clearfix ak-displaymode-image-col">

                                                    @foreach (config('dofus.details') as $server)
                                                    <div class="ak-column ak-container col-xs-12 col-md-6">
                                                        <div class="ak-list-element">
                                                            <div class="ak-main">
                                                                <div class="ak-main-content">
                                                                    <div class="ak-image">
                                                                        <a href="{{ URL::route('lottery.draw', [$server->name, $ticketId]) }}"><span class="ak-linker"><img src="{{ URL::asset('imgs/admin/' . $server->name . '.png') }}" style="background-color: #d4f3ff;"></span></a>
                                                                    </div>
                                                                    <div class="ak-content">
                                                                        <div class="ak-title">
                                                                            <a href="{{ URL::route('lottery.draw', [$server->name, $ticketId]) }}"><span class="ak-linker">{{ ucfirst($server->name) }}</span></a>
                                                                        </div>
                                                                        <div class="ak-text" style="padding-left: 12px;">{{ $server->desc }} {{ $server->version }}</div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="clearfix  visible-xs visible-sm"></div>
                                                    @endforeach

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop
