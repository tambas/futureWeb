@extends('layouts.master')
@include('layouts.tops.play')

@section('page')
@if (@$carousel)
<div class="ak-container ak-carrousel-bg">
    @if (config('dofus.theme.animated'))
    <div class="ak-video-home">
        <video class="ak-widget-video" autoplay loop="loop" poster="{{ URL::asset('imgs/carousel/'.config('dofus.theme.background').'/preview.png') }}">
            <source type="video/mp4"  src="{{ URL::asset('imgs/carousel/'.config('dofus.theme.background').'/video.mp4') }}" />
            <source type="video/webm" src="{{ URL::asset('imgs/carousel/'.config('dofus.theme.background').'/video.webm') }}" />
            <source type="video/ogv"  src="{{ URL::asset('imgs/carousel/'.config('dofus.theme.background').'/video.ogv') }}" />
        </video>
    </div>
    @else
    <div class="ak-video-home ak-no-video"></div>
    @endif
    <a href="">
        <div class="img_sm"></div>
    </a>
    <div class="ak-carrousel-bg-title ak-carrousel-bg-title-left hidden-xs">
        @if (config('dofus.motd'))
        <div class="ak-block-title">
            <a href="{{ route('posts.show', config('dofus.motd.post_id')) }}" class="ak-title-link">
                <div class="ak-layer">
                    <span class="ak-title">{{ config('dofus.motd.title') }}</span>
                    <br> <span class="ak-subtitle">{{ config('dofus.motd.subtitle') }}</span> <span class="ak-banner-more">+</span>
                    <br>
                </div>
            </a>
        </div>
        @endif
    </div>
</div>
@endif

@yield('top')

<div class="container ak-main-container">
    <div class="ak-main-content">
        <div class="ak-main-page">
            <div class="row">
                <div class="main col-md-9 col-md-push-3">
                    <div class="ak-container ak-main-center">
                        @yield('content')
                    </div>
                </div>
                @yield('menu')
            </div>
        </div>
    </div>
</div>
@stop
