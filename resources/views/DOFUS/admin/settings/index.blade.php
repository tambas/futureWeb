@extends('layouts.admin.admin')
@section('title') Website Settings @endsection
@section('page-title') Website: Settings @endsection
@section('content')
        <!-- Start content -->
<div class="content">
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <div class="card-box">
                    <h4 class="header-title m-b-30">Message of the Day (MOTD)</h4>

                    @if(!empty($posts))
                        {!! Form::model($motd, ['route' => 'admin.settings.update']) !!}
                        {{ method_field('PATCH') }}
                        {!! Form::text('settings_type', 'motd', ['class' => 'hidden disabled']) !!}
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group {{ $errors->has('title') ? ' has-error' : '' }}">
                                    <label for="title">Title:</label>
                                    {!! Form::text('title', null, ['class' => 'form-control', 'id' => 'title']) !!}
                                    @if ($errors->has('title'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('title') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group {{ $errors->has('subtitle') ? ' has-error' : '' }}">
                                    <label for="subtitle">Subtitle:</label>
                                    {!! Form::text('subtitle', null, ['class' => 'form-control', 'id' => 'subtitle']) !!}
                                    @if ($errors->has('subtitle'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('subtitle') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group {{ $errors->has('post_id') ? ' has-error' : '' }}">
                                    <label for="post_id">Linked Post:</label>
                                    {!! Form::select('post_id', $posts, null,['class' => 'form-control', 'id' => 'post_id']) !!}
                                    @if ($errors->has('post_id'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('post_id') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        {!! Form::submit('Update', ['class' => 'btn btn-info']) !!}
                        {!! Form::close() !!}
                    @else
                        <div class="alert alert-warning">
                            <strong>Info!</strong> You can't edit MOTD if you don't have any posts.
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="card-box">
                    <h4 class="header-title m-b-30">Theme</h4>
                    {!! Form::model($theme, ['route' => 'admin.settings.update']) !!}
                    {{ method_field('PATCH') }}
                    {!! Form::text('settings_type', 'theme', ['class' => 'hidden disabled']) !!}
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group {{ $errors->has('background') ? ' has-error' : '' }}">
                                <label for="background">Background:</label>
                                {!! Form::text('background', null, ['class' => 'form-control', 'id' => 'background']) !!}
                                @if ($errors->has('background'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('background') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group {{ $errors->has('color') ? ' has-error' : '' }}">
                                <label for="color">Color:</label>
                                {!! Form::text('color', null, ['class' => 'form-control', 'id' => 'color']) !!}
                                @if ($errors->has('color'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('color') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group {{ $errors->has('animated') ? ' has-error' : '' }}">
                                <div class="checkbox checkbox-inline checkbox-success">
                                    {!! Form::checkbox('animated', 'true', $theme['animated'], ['class' => 'form-control', 'id' => 'animated']) !!}
                                    <label for="animated">Animated</label>
                                </div>
                                @if ($errors->has('animated'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('animated') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                    </div>
                    {!! Form::submit('Update', ['class' => 'btn btn-info']) !!}
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="card-box">
                    <h4 class="header-title m-b-30">Welcome</h4>
                    {!! Form::model($welcome, ['route' => 'admin.settings.update']) !!}
                    {{ method_field('PATCH') }}
                    {!! Form::text('settings_type', 'welcome', ['class' => 'hidden disabled']) !!}
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group {{ $errors->has('message') ? ' has-error' : '' }}">
                                <label for="backgromessageund">Message:</label>
                                {!! Form::text('message', null, ['class' => 'form-control', 'id' => 'message']) !!}
                                @if ($errors->has('message'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('message') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                    </div>
                    {!! Form::submit('Update', ['class' => 'btn btn-info']) !!}
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
    @endsection
