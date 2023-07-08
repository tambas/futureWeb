@extends('layouts.contents.default')
@include('layouts.menus.base')

@section('header')
    {!! Html::style('css/server.css') !!}
@stop

@section('breadcrumbs')
{? $page_name = 'Serveurs' ?}
{!! Breadcrumbs::render('page', $page_name) !!}
@stop

@section('content')
<div class="ak-container ak-main-center">
    <div class="ak-title-container">
        <h1><span class="ak-icon-big ak-servers"></span></a> Serveurs</h1>
    </div>

    <div class="ak-responsivetable-wrapper">
        <table border="1" class="ak-ladder ak-server-list ak-container ak-table ak-responsivetable" style="white-space: nowrap; visibility: visible;">
            <thead>
                <tr class="ak-ajaxloader">
                    <th>Etat</th>
                    <th>Nom du serveur</th>
                    <th class="text-center">Version</th>
                    <th class="text-center">Mode</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($servers as $server)
                <tr>
                    <td class="ak-server-online">
                        <span class="ak-icon-med @if($serverOnline[$server->name] == true)ak-server-online @else ak-server-offline @endif"></span>
                        {{ $serverOnline[$server->name] == false ? 'Hors ligne' : 'En ligne' }}
                    </td>
                    <td class="server server_{{ $server->name }}">
                        <span></span>
                        {{ ucfirst($server->name) }}
                    </td>
                    <td class="text-center">{{ $server->version }}</td>
                    <td class="text-center">{{ $server->desc }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@stop
