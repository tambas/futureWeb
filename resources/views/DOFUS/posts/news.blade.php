@extends('layouts.contents.default')
@include('layouts.menus.base')

@section('header')
    {!! Html::style('css/homepage.css') !!}
    <style>
    .ak-pagetop {
        margin-top: -85px;
    }
    </style>
@stop

<?php $carousel = true; ?>

@section('background') ak-background-type-homepage @stop

@section('breadcrumbs')
{? $page_name = config('dofus.subtitle') ?}
{!! Breadcrumbs::render('actuality') !!}
@stop

@section('content')
    <div class="ak-title-container">
        <h1><span class="ak-icon-big ak-news"></span> Toute l'actualité</h1>
    </div>
    <div class="ak-list-paginated">
        <div class="ak-item-list ak-grid-padding">
            <div class="row">
            @foreach ($posts as $post)
                <div class="col-sm-6">
                    <div class="ak-item-elt ak-universe-key-mmorpg">
                        <div class="ak-item-elt-content">
                            <a class="ak-link-img" href="{{ URL::route('posts.show', [$post->id, $post->slug]) }}">
                                <img class="img-responsive" alt="{{ $post->title }}" src="{{ URL::asset($post->image) }}">
                            </a>

                            <div class="ak-item-elt-inner">
                                <div class="ak-item-elt-title">
                                    <a href="{{ URL::route('posts.show', [$post->id, $post->slug]) }}">
                                        <img src="{{ URL::asset($post->author->avatar) }}" alt="Auteur" title="Auteur">
                                    </a>

                                        <span class="ak-text"><a href="{{ URL::route('posts.show', [$post->id, $post->slug]) }}">{{ $post->title }}</a>
                                            <span class="ak-publication"><span><a href="{{ route('posts.type', $post->type) }}">{{  config('dofus.news_type.'.$post->type.'.name') }}</a> -</span>  {!! ucwords(utf8_encode($post->published_at->formatLocalized('%e %B %Y'))) !!}</span>
                                        </span>
                                </div>
                                <div class="ak-item-elt-desc">
                                    {!! $post->preview !!}
                                </div>
                            </div>
                            <div class="ak-ellipsis-text"></div>
                            <div class="ak-bottom">
                                <div class="ak-comments pull-left">
                                    <a class="comment" href="{{ URL::route('posts.show', [$post->id, $post->slug]) }}#ak-block-posts">
                                        <svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="866.001px" height="866.013px" viewBox="930.736 591.271 866.001 866.013" enable-background="new 930.736 591.271 866.001 866.013" xml:space="preserve">
                                                <path fill="#010202" d="M930.736,1186.67v54.084c0,29.905,24.229,54.148,54.124,54.148h54.115l0,0h54.134v162.382l243.555-162.382h351.804c0.021,0,0.03,0,0.04,0h54.104c29.885,0,54.125-24.243,54.125-54.148v-595.37c0-29.897-24.24-54.112-54.125-54.112h-108.26h-108.248l0,0h-432.976c-0.01,0-0.01,0-0.01,0H984.86c-29.895,0-54.124,24.214-54.124,54.112v54.127l0,0V1186.67L930.736,1186.67zM1038.975,699.511h108.27l0,0h432.985l0,0h108.238v378.861v108.298h-108.238l0,0h-243.565l-135.306,81.165v-81.165h-54.114l0,0h-108.27V699.511z M1526.104,1078.372h54.114V970.125h-432.975v108.298h378.86V1078.372z M1580.219,807.766h-432.975v108.24h432.975V807.766z"></path>
                                            </svg>
                                        <span>{{ $post->comments->count() }}</span>
                                    </a>
                                </div>
                                <div class="pull-right">
                                    <a class="ak-more" href="{{ URL::route('posts.show', [$post->id, $post->slug]) }}">En savoir<span>+</span>        </a>
                                </div>
                                <div class="ak-bottom-infos pull-left">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
            </div>
            @if($posts->links())
                <div class="text-center ak-pagination">
                    <nav>
                        {{ $posts->links('pagination.default', ['target' => '.ak-list-paginated', 'settings' => '{"scroll":true}']) }}
                    </nav>
                </div>
            @endif
    </div>
</div>
@stop
