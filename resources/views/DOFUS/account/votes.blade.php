@extends('layouts.contents.default')
@include('layouts.menus.base')

@section('breadcrumbs')
{? $page_name = 'Mes votes' ?}
{!! Breadcrumbs::render('account') !!}
@stop

@section('content')
<div class="ak-container ak-main-center ak-list-pagination">
    <div class="ak-title-container">
        <h1><span class="ak-icon-big ak-bank"></span></a> Mon compte</h1>
    </div>
    @include('account.nav.topnav')
    <div class="ak-container ak-panel-stack">
        <div class="ak-container ak-panel ak-glue">
            <div class="ak-panel-title">
                <span class="ak-panel-title-icon"></span> Mes votes
            </div>
        </div>
    </div>

    <table class="ak-container ak-table">
        <tr>
            <th></th>
            <th>Date du vote</th>
            <th>Jetons</th>
        </tr>
        @foreach ($votes as $vote)
        <tr>
            <td></td>
            <td>{!! ucwords(utf8_encode($vote->created_at->formatLocalized('%e %B %Y &agrave; %Hh%M'))) !!}</td>
            <td>+1 <span class="ak-icon-small ak-votes-icon"></span></td>
        </tr>
        @endforeach
    </table>
    @if($votes->links())
        <div class="text-center ak-pagination">
            <nav>
                {{ $votes->links('pagination.default', ['target' => '.ak-list-pagination', 'settings' => '{"scroll":true}']) }}
            </nav>
        </div>  
    @endif
    <div class="ak-container ak-panel-stack">
        <div class="ak-container ak-panel ak-glue">
            <div class="ak-panel-content">
                <div class="text-center">
                    <a href="{{route('vote.index')}}" class="btn btn-primary btn-lg">Voter pour le serveur <span class="ak-icon-small ak-votes-icon"></span></a>
                </div>
            </div>
        </div>
    </div>
</div>
@stop
