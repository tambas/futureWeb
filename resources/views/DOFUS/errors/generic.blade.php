@extends('layouts.contents.default')
@include('layouts.menus.base')

@section('breadcrumbs')
{? $page_name = 'Erreur' ?}
{!! Breadcrumbs::render('page', $page_name) !!}
@stop

@section('content')
<div class="ak-container ak-main-center">
    <div class="ak-title-container">
        <h1><span class="ak-icon-big ak-devblog"></span></a> {{ $title }}</h1>
    </div>

    <div class="ak-container ak-panel-stack">
        <div class="ak-container ak-panel ak-glue">
            <div class="ak-panel-content">
                <div class="panel-main">
                    {{ $detail }}
                </div>
            </div>
        </div>
    </div>
</div>
@stop
