@extends('layouts.contents.default')
@include('layouts.menus.base')

@section('breadcrumbs')
{? $page_name = 'Mes tickets' ?}
{!! Breadcrumbs::render('support') !!}
@stop

@section('content')
<div class="ak-container ak-main-center">
    <div class="ak-title-container">
        <h1><span class="ak-icon-big ak-support"></span></a> Support - {{ $page_name }}</h1>
    </div>

    @include('support.menu')
 
    <div class="ak-container ak-banner-header ak-panel-stack">
            <img class="img-responsive" src="{{ URL::asset('imgs/support/new.png') }}">
        <div class="ak-container ak-panel ak-glue">
            <div class="ak-panel-content">
                <div class="">
                    <a href="{{route('support.create')}}"><button class="btn btn-info btn-lg">Ouvrir un ticket</button></a>
                </div>
            </div>

            <div class="ak-panel-title">
                <span class="ak-panel-title-icon"></span> Mes tickets ouverts
            </div>
        </div>
    </div>
        @if(count($requests) == 0)
             <div class="ak-container ak-panel-stack">
                <div class="ak-container ak-panel ak-glue">
                    <div class="ak-panel-content">
                        <div class="panel-main">
                           Vous n'avez pas de tickets ouverts.
                        </div>
                    </div>
                </div>
            </div>
        @else
            <table class="ak-container ak-table">
                <tr>
                    <th class="ak-center">Numéro</th>
                    <th>Catégorie</th>
                    <th>Sujet</th>
                    <th>Date d'ouverture</th>
                    <th>Statut</th>
                    <th>Dernier message</th>
                    <th class="ak-center">Actions</th>
                </tr>
                @foreach ($requests as $request)
                <tr>
                    <td class="ak-center">{{ $request->id }}</td>
                    <td>{{ $request->category }}</td>
                    <td>{{ $request->subject }}</td>
                    <td>{{ $request->updated_at->format('d/m/Y H:i:s') }}</td>
                    <td class="ak-bold" @if($request->state == 1) style="color:green;" @endif>{{ Utils::support_request_status($request->state) }}</td>
                    <td @if($request->lastTicketAuthor()->can('manage-support')) style="color:#2baaff" @endif>{{ $request->lastTicketAuthor()->pseudo }}</td>
                    <td class="ak-center"><a href="{{route('support.show', $request->id)}}"><span class="ak-icon-small ak-filter"></span></a></td>
                </tr>
                @endforeach
            </table>
        @endif
</div>
@stop
