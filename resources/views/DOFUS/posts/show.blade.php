@extends('layouts.contents.default')
@include('layouts.menus.base')

@section('header')
    {!! Html::style('css/lightbox.min.css') !!}
    {!! Html::script('js/lightbox.min.js') !!}
@stop

@section('breadcrumbs')
{? $page_name = $post->title ?}
{!! Breadcrumbs::render('page', 'Actualités') !!}
@stop

@section('content')
<div class="ak-title-container ak-backlink">
    <h1 class="ak-return-link">
        <a href="">
            <span class="ak-icon-big ak-news"></span>
        </a>
        {{ $post->title }}
        <span class="ak-subtitle"><span>{{  config('dofus.news_type.'.$post->type.'.name') }}</span> -  {!! ucwords(utf8_encode($post->published_at->formatLocalized('%e %B %Y'))) !!}</span>
    </h1>

    <a href="{{ URL::route('posts.type', $post->type ) }}" class="ak-backlink-button">Retour à la liste</a>
</div>

<div class="ak-container ak-panel-stack ak-glue">
    <div class="ak-container ak-panel ak-nocontentpadding">
        <div class="ak-panel-content">
            <div class="ak-item ak-admin-text">
                <div class="ak-item-mid">
                    <div class="ak-intro">
                        <p>{!! $post->preview !!}</p>
                    </div>
                    <div class="ak-big-image template_block">
                        <a href="{{ URL::asset($post->image) }}" data-lightbox="image" data-title="{{ $post->title }}"><img alt="" class="img-responsive img-maxresponsive" src="{{ URL::asset($post->image) }}"></a>
                    </div>
                    <p>{!! $post->content !!}</p>
                </div>
            </div>
        </div>
        <div class="ak-container">
            <div class="ak-social">
                <div class="pull-left">
                    <div class="ak-social-block facebook">
                        <a href="https://www.facebook.com/sharer/sharer.php?u={{URL::current()}}" onclick="javascript:window.open(this.href,'','menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;" class="">Partager</a>        </div>
                    <div class="ak-social-block twitter">
                        <a href="http://twitter.com/home?status={{URL::current()}}" onclick="javascript:window.open(this.href,'','menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;" class="">Tweet</a>        </div>
                    <div class="ak-social-block google">
                        <a href="https://plus.google.com/share?url={{URL::current()}}" onclick="javascript:window.open(this.href,'','menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;" class="">Partager</a>        </div>
                </div>
            </div>
        </div>
    </div>
    <div class="ak-forum-post-panel ak-container ak-panel" id="ak-block-posts">
        <div class="ak-panel-title">
            <span class="ak-panel-title-icon"></span> Commentaires (<span class="counter">{{ $post->comments->count() }}</span>)
        </div>
        <div class="ak-panel-content">
            <div class="ak-forum-post-list">
                @foreach ($comments as $comment)
                    <div class="ak-comments-row @if ($comment->author->isStaff()) ak-avatar-admin @endif">
                        <div class="ak-avatar">
                            <div class="ak-avatar-img">
                                <img src="{{ URL::asset($comment->author->avatar) }}" alt="" border="0" /> </div>
                            <div class="ak-avatar-tag">{{$comment->author->role->label}}</div>
                        </div>
                        <div class="ak-comment">
                            <div class="ak-user">
                                <strong>{{ $comment->author->firstname }}</strong>
                                <small class="ak-time">{!! ucwords(utf8_encode($comment->created_at->formatLocalized('%e %B %Y &agrave; %Hh%M'))) !!}</small>
                                <span class="actions pull-right">
                                    @if(!Auth::guest() && Auth::user()->can('delete-comments'))
                                        {!! Form::open(['route' => ['posts.comment.destroy', $post->id, $post->slug, $comment->id], 'class' => 'form-inline']) !!}
                                        {{ method_field('DELETE') }}
                                        <button type="submit" class="btn btn-danger btn-xs"><i class="fa fa-trash-o"></i></button>
                                        {!! Form::close() !!}
                                    @endif
                                </span>
                            </div>
                            <div class="ak-text-content">{{ $comment->text }}</div>
                        </div>
                    </div>
                @endforeach
                <div class="text-center ak-pagination">
                    <nav>
                        {{ $comments->links('pagination.default', ['target' => '#ak-block-posts', 'settings' => '{"scroll":true}']) }}
                    </nav>
                </div>
            </div>
            @if(Auth::check() && $result == false)
                {!! Form::open(['url' => URL::full(), 'class' => 'ak-forum-post-form ak-ajaxloader', 'data-target' => '.ak-forum-post-panel']) !!}
                    <div class="row ak-comment-container">
                        <div class="ak-avatar">
                            <img src="{{ URL::asset(Auth::user()->avatar) }}" alt="" border="0">
                        </div>
                        <input type="hidden" name="postback" value="forum_add_comment">
                        <input type="hidden" name="topic_title" value="{{$post->title}}">
                        <input type="hidden" name="user" value="{{Auth::user()->id}}">
                        <div class="ak-comment">
                            {!! Form::textarea('comment', null, ['class' => 'ak-comment-textarea', 'placeholder' => 'Commenter...', 'rows' => '0', 'cols' => '0', 'style' => 'height:70px;']) !!}
                        </div>
                    </div>
                        @if ($errors->has('comment'))
                        <div class="ak-error" style="color: red; font-weight: bold;">{{ $errors->first('comment') }}</div>
                        @endif
                {!! Form::submit('Valider', ['class' => 'ak-comment-submit btn btn-primary btn-lg']) !!}
                {!! Form::close() !!}
            @endif
        </div>
    </div>
</div>
@stop
