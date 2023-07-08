@extends('layouts.contents.default')
@include('layouts.menus.base')

@section('header')
    {!! Html::style('css/lightbox.min.css') !!}
    {!! Html::script('js/lightbox.min.js') !!}
    {{ Html::script('tinymce/tinymce.min.js') }}
    {{ Html::script('tinymce/tinymce_simple_front.js') }}
@stop

@section('breadcrumbs')
{? $page_name = 'Ticket n°'.$request->id ?}
{!! Breadcrumbs::render('support.page', $page_name) !!}
@stop

@section('content')
<div class="ak-container ak-main-center">
    <div class="ak-title-container ak-backlink">
        <h1><span class="ak-icon-big ak-support"></span></a> Support - {{ $page_name }}</h1>
        <a href="{{ URL::route('support') }}" class="ak-backlink-button">Retour à mes tickets</a>
    </div>
 
    <div class="ak-container ak-banner-header ak-panel-stack">
            <img class="img-responsive" src="{{ URL::asset('imgs/support/new.png') }}">
        <div class="ak-container ak-panel ak-glue">
            <div class="ak-panel-content">
                <div class="panel-main">
                    <div class="row">
                        <div class="col-xs-9">
                             {!! $htmlReport !!}
                             <br>
                            <b>Ouvert le</b>: {{ $request->created_at->format('d/m/Y à H\hi') }}<br>
                            @if(!$request->isOpen())
                            <b>Fermé le</b>: {{ $request->updated_at->format('d/m/Y à H\hi') }}<br>
                            @endif
                            <br>
                        {!! Form::open(['route' => ['support.switch', $request->id], 'style' => 'display: inline;', 'method' => 'patch']) !!}
                        @if($request->isOpen())
                            <button typ="submit" class="btn btn-danger btn-lg"><i class="fa fa-lock m-r-5"></i> Cloturer le ticket</button>
                        @else
                            <button typ="submit" class="btn btn-primary btn-lg"><i class="fa fa-unlock m-r-5"></i> Réouvrir le ticket</button>
                        @endif
                        {!! Form::close() !!}
                        </div>
                        <div class="col-xs-3">
                            <img class="img-responsive" src="{{ URL::asset('imgs/support/ticket.png') }}">
                            <b>Statut</b>: {{ Utils::support_request_status($request->state) }}<br>
                            @if($request->userAssigned())
                                <b>Admin en charge</b>: 
                                <span @if($request->userAssigned()->can('manage-support')) style="color:#2baaff" @endif>{{$request->userAssigned()->pseudo}}</span>
                            @else
                                <i>Aucun admin en charge pour le moment</i>
                            @endif
                        </div>
                    </div>
                    <br>

                    <div class="clearfix"></div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="ak-container ak-panel-stack ak-glue">
<div class="ak-forum-post-panel ak-container ak-panel">
        <div class="ak-panel-title">
            <span class="ak-panel-title-icon"></span> Messages (<span class="counter">{{ $messages->count() }}</span>)
        </div>
        <div class="ak-panel-content">
            <div class="ak-forum-post-list">
                @foreach($messages as $message)
                <div class="ak-comments-row @if ($message->author->can('manage-support')) ak-avatar-admin @endif" id="message-{{$message->id}}">
                        <div class="ak-avatar">
                            <div class="ak-avatar-img">
                                <img src="{{ URL::asset($message->author->avatar) }}" alt="" border="0" /> </div>
                            <div class="ak-avatar-tag">{{$message->author->role->label}}</div>
                        </div>
                        <div class="ak-comment">
                            <div class="ak-user">
                                <strong>{{ $message->author->firstname }}</strong>
                                <small class="ak-time">{{ date('d F Y à H:i', strtotime($message->created_at)) }}</small>
                                <span class="actions pull-right">
                                    
                                </span>
                            </div>
                            <div class="ak-text-content" @if($message->isInfo()) style="font-weight:bold;"@endif>@if($message->isInfo()) INFO: @endif {!! $message->data['message'] !!}</div>
                        </div>
                    </div>
                @endforeach
            </div>
            @if($request->isOpen())
                {!! Form::open(['route' => ['support.message.post', $request->id], 'class' => 'ak-forum-post-form']) !!}
                    <div class="row ak-comment-container">
                        <div class="ak-avatar">
                            <img src="{{ URL::asset(Auth::user()->avatar) }}" alt="" border="0">
                        </div>
                        <div class="ak-comment">
                            {!! Form::textarea('message', null, ['class' => 'ak-comment-textarea', 'rows' => '10', 'cols' => '0', 'id' => 'message']) !!}
                        </div>
                         @if ($errors->has('message')) <label class="error control-label">{{ $errors->first('message') }}</label> @endif
                    </div>
                {!! Form::submit('Envoyer', ['class' => 'ak-comment-support-submit btn btn-primary btn-lg']) !!}
                {!! Form::close() !!}
            @endif
        </div>
    </div>
</div>
@stop
@section('bottom')
<script>
    var $ = require('jquery');
if($("textarea").length > 0){
                        editor_simple_front_config.selector = "textarea";
                        $("textarea").removeClass('hidden');
                        tinymce.init(editor_simple_front_config);
                    };
</script>
@endsection
