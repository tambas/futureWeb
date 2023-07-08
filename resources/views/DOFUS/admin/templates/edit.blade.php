@extends('layouts.admin.admin')
@section('title') Template Edit @endsection
@section('page-title') Template: Edit @endsection
@section('header')
    {{ Html::script('tinymce/tinymce.min.js') }}
    {{ Html::script('tinymce/tinymce_simple.js') }}
@endsection
@section('content')
        <!-- Start content -->
<div class="content">
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <div class="m-b-30">
                            <a href="{{ route('admin.account.settings') }}" class="btn btn-primary waves-effect waves-light btn-lg m-b-5"><i class="zmdi zmdi-long-arrow-return"></i> Return to list</a>
                </div>
                <div class="card-box">
                    <h4 class="header-title m-b-30">Template: Edit</h4>
                        {!! Form::open(['route' => ['admin.account.settings.template.update', $template->title]]) !!}
                        {{ method_field('PATCH') }}
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group {{ $errors->has('title') ? ' has-error' : '' }}">
                                    <label for="title">Title:</label>
                                    {!! Form::text('title', $template->title,['class' => 'form-control', 'id' => 'title']) !!}
                                    @if ($errors->has('title'))
                                        <span class="help-block">
                                                <strong>{{ $errors->first('title') }}</strong>
                                            </span>
                                    @endif
                                </div> 
                                <div class="form-group {{ $errors->has('description') ? ' has-error' : '' }}">
                                    <label for="description">Description:</label>
                                    {!! Form::text('description', $template->description,['class' => 'form-control', 'id' => 'description']) !!}
                                    @if ($errors->has('description'))
                                        <span class="help-block">
                                                <strong>{{ $errors->first('description') }}</strong>
                                            </span>
                                    @endif
                                </div> 
                                <div class="form-group {{ $errors->has('content') ? ' has-error' : '' }}">
                                    <label for="content">Content:</label>
                                    {!! Form::textarea('content', $template->content,['class' => 'form-control hidden', 'id' => 'content']) !!}
                                    @if ($errors->has('content'))
                                        <span class="help-block">
                                                <strong>{{ $errors->first('content') }}</strong>
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
@section('bottom')
<script>
                $(document).ready(function () {
                    if($("textarea").length > 0){
                        editor_simple_config.selector = "textarea";
                        $("textarea").removeClass('hidden');
                        tinymce.init(editor_simple_config);
                    };
                });
</script>
@endsection
