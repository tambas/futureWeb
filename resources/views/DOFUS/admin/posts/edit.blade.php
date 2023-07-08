@extends('layouts.admin.admin')
@section('title') Post {{ $post->title }} @endsection
@section('page-title') Post: Edit @endsection
@section('header')
    {{ Html::style('css/jquery.datetimepicker.min.css') }}
    {{ Html::script('tinymce/tinymce.min.js') }}
    {{ Html::script('tinymce/tinymce_editor.js') }}
    {{ Html::script('js/admin/browseserver.min.js') }}
@endsection
@section('content')
        <!-- Start content -->
        <div class="content">
            <div class="container">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="m-b-30">
                            <a href="{{ route('admin.posts') }}" class="btn btn-primary waves-effect waves-light btn-lg m-b-5"><i class="zmdi zmdi-long-arrow-return"></i> Return to list</a>
                        </div>
                        <div class="card-box">
                            <div class="m-b-30">
                                @if($post->isProgrammed())
                                <div class="text-center alert alert-info">
                                    <strong>Programmed!</strong> This post is programmed to be published on {{ $post->published_at }}
                                </div>
                                @endif
                                @if($post->isDraft())
                                    <div class="text-center alert alert-danger">
                                        <strong>Draft!</strong> This post is a draft and will not be published at the moment
                                    </div>
                                @endif
                                @if($post->isPublished())
                                    <div class="text-center alert alert-warning">
                                        <strong>Info!</strong> This post is public and already published
                                    </div>
                                @endif

                            </div>
                                {!! Form::model($post, ['route' => ['admin.post.update', $post->id], 'files' => true]) !!}
                                {{ method_field('PATCH') }}
                            <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group {{ $errors->has('title') ? ' has-error' : '' }}">
                                            <label for="title">Title:</label>
                                            {!! Form::text('title', null, ['class' => 'form-control', 'id' => 'title']) !!}
                                            <small>Slug will not be updated</small>
                                            @if ($errors->has('title'))
                                                <span class="help-block">
                                                                    <strong>{{ $errors->first('title') }}</strong>
                                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group {{ $errors->has('type') ? ' has-error' : '' }}">
                                            <label for="type">Type:</label>
                                            {!! Form::select('type', $typeArray, null, ['class' => 'form-control', 'id' => 'type']) !!}
                                            @if ($errors->has('type'))
                                                <span class="help-block">
                                                                            <strong>{{ $errors->first('type') }}</strong>
                                                                        </span>
                                            @endif
                                        </div>
                                    </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group {{ $errors->has('preview') ? ' has-error' : '' }}">
                                            <label for="preview">Preview:</label>
                                            {!! Form::textarea('preview',null, ['class' => 'form-control', 'id' => 'preview']) !!}
                                            @if ($errors->has('preview'))
                                                <span class="help-block">
                                                                            <strong>{{ $errors->first('preview') }}</strong>
                                                                        </span>
                                            @endif
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group {{ $errors->has('content') ? ' has-error' : '' }}">
                                        <label for="content">Content:</label>
                                        {!! Form::textarea('content',null, ['class' => 'form-control', 'id' => 'content']) !!}
                                        @if ($errors->has('content'))
                                            <span class="help-block">
                                                                        <strong>{{ $errors->first('content') }}</strong>
                                                                    </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12">
                                <label for="">Main Image:</label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12 image-wrapper {{ $errors->has('url_main_image') ? ' image-has-error' : '' }}">
                                            <input name="url_main_image" class="hidden" type="text" id="main_image_input" value="{{ url('/').$post->image }}">

                                            {{ Html::image($post->image,'main_image', ['class' => 'img-responsive image-image', 'id' => 'main_image']) }}

                                            @if ($errors->has('url_main_image'))
                                                <span class="help-block help">
                                                  <strong>{{ $errors->first('url_main_image') }}</strong>
                                                                                </span>
                                            @endif
                                </div>
                            </div>
                                <div class="row">
                                    <div class="form-group {{ $errors->has('published') ? ' has-error' : '' }}">
                                        <div class="checkbox checkbox-primary">
                                            {!! Form::checkbox('published',1,null, ['id' => 'published']) !!}
                                            <label for="published">
                                               Published
                                            </label>
                                            <strong class="post-date @if($post->isDraft()) hidden @endif">on: {!! Form::datetime('published_at',date('Y-m-d H:i:s', strtotime($post->published_at)),['class' => 'form-control', 'id' => 'dtpicker']) !!}
                                                </strong>

                                        </div>
                                    </div>
                                </div>
                                {!! Form::submit('Update', ['class' => 'btn btn-info']) !!}
                                {!! Form::close() !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @endsection

        @section('bottom')
            {{ Html::script('js/admin/jquery.datetimepicker.full.min.js') }}
            <script>
               $(document).ready(function () {
                    $('#dtpicker').datetimepicker({
                        format:'Y-m-d H:i:s'
                    });
                   $('.image-wrapper').on('click', function () {
                       BrowseServer('main_image_input', '{{ url('/filemanager/show?type=images') }}');
                   });
                   $("#published").change(function() {
                       if(this.checked) {
                           $('.post-date').removeClass('hidden');
                           $('.post-date').show();
                       }
                       else{
                           $('.post-date').hide();
                       }
                   });

                   if($("#preview").length > 0){
                       tinymce.init({
                           selector: 'textarea#preview',
                           theme: "modern",
                           plugins: [
                               "advlist autolink lists link charmap preview hr anchor pagebreak",
                               "searchreplace wordcount visualblocks visualchars code fullscreen",
                               "insertdatetime nonbreaking save contextmenu directionality",
                               "emoticons template paste textcolor colorpicker textpattern"
                           ],
                           toolbar1: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link | preview | forecolor backcolor emoticons",
                       });
                   }
                    if($("#content").length > 0){
                        editor_config.selector = "textarea#content";
                        editor_config.path_absolute = '{{ url('') }}';
                        tinymce.init(editor_config);
                    }
                });
            </script>
@endsection
