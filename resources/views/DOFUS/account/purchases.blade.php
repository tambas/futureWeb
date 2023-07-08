@extends('layouts.contents.default')
@include('layouts.menus.base')

@section('breadcrumbs')
{? $page_name = 'Mes Achats' ?}
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
                <span class="ak-panel-title-icon"></span> Mes achats
            </div>
        </div>
    </div>
    <table class="ak-container ak-table">
        <tr>
            <th class="ak-center">#</th>
            <th>Date d'achat</th>
            <th>Ogrines</th>
            <th>Code</th>
            <th>Statut</th>
        </tr>
        @foreach ($transactions as $transaction)
        <tr>
            <td class="ak-center">{{ $transaction->id }}</td>
            <td>{!! ucwords(utf8_encode($transaction->created_at->formatLocalized('%e %B %Y &agrave; %Hh%M'))) !!}</td>
            <td><span class="ak-icon-small ak-ogrines-icon"></span> &nbsp; {{ Utils::format_price($transaction->points) }}</td>
            <td>{{ $transaction->code }}</td>
            <td>{{ Utils::transaction_status($transaction->state) }}</td>
        </tr>
        @endforeach
    </table>
    @if($transactions->links())
        <div class="text-center ak-pagination">
            <nav>
                {{ $transactions->links('pagination.default', ['target' => '.ak-list-pagination', 'settings' => '{"scroll":true}']) }}
            </nav>
        </div>  
    @endif
    <div class="ak-container ak-panel-stack">
        <div class="ak-container ak-panel ak-glue">
            <div class="ak-panel-content">
                <div class="text-center">
                    <a href="{{route('shop.payment.country')}}" class="btn btn-primary btn-lg">Acheter des <span class="ak-icon-small ak-ogrines-icon"></span></a>
                </div>
            </div>
        </div>
    </div>
</div>
@stop
