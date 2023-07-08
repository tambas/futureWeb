@extends('layouts.admin.admin')
@section('title') Create post @endsection
@section('page-title') Post: Create @endsection
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
                                {!! Form::open(['route' => 'admin.post.store', 'files' => true]) !!}
                                {{ method_field('POST') }}
                            <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group {{ $errors->has('title') ? ' has-error' : '' }}">
                                                <label for="title">Title:</label>
                                                {!! Form::text('title', null, ['class' => 'form-control', 'id' => 'title']) !!}
                                                <small>Slug will be generated automatically</small>
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
                                        <input name="url_main_image" class="hidden" type="text" id="main_image_input">
                                        <div class="image-message">
                                            <span class="zmdi zmdi-image"></span>
                                            <p>Click to choose / upload a file</p>
                                        </div>
                                        <img class="img-responsive hidden image-image" src=""  id="main_image">
                                        @if ($errors->has('url_main_image'))
                                            <span class="help-block help">
                                              <strong>{{ $errors->first('url_main_image') }}</strong>
                                                                            </span>
                                        @endif
                                </div>
                            </div>
                                    <div class="form-group {{ $errors->has('published') ? ' has-error' : '' }}">
                                        <div class="checkbox checkbox-success">
                                            {!! Form::checkbox('published',1,false, ['id' => 'published']) !!}
                                            <label for="published">Published</label>
                                            <strong class="post-date hidden">on: {!! Form::datetime('published_at',date('Y-m-d H:i:s', strtotime(Carbon\Carbon::now())),['class' => 'form-control', 'id' => 'dtpicker']) !!}
                                            </strong>
                                        </div>
                                    </div>
                                {!! Form::submit('Create', ['class' => 'btn btn-info']) !!}
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
                   $("#published").change(function() {
                       if(this.checked) {
                           $('.post-date').removeClass('hidden');
                           $('.post-date').show();
                       }
                       else{
                           $('.post-date').hide();
                       }
                   });


                   $('.image-wrapper').on('click', function () {
                       BrowseServer('main_image_input', '{{ url('/filemanager/show?type=images') }}');
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
