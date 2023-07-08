@extends('layouts.admin.admin')
@section('title') User Settings @endsection
@section('page-title') User: Settings @endsection
@section('header')
    {{ Html::script('tinymce/tinymce.min.js') }}
    {{ Html::script('tinymce/tinymce_simple.js') }}
        {{ Html::style('css/sweetalert.min.css') }}
@endsection
@section('content')
        <!-- Start content -->
<div class="content">
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                 <div class="card-box">
                        <div class="m-b-30">
                             <button type="button" class="btn btn-success waves-effect waves-light btn-lg m-b-5" data-toggle="modal" data-target="#add-template"><i class="fa fa-plus m-r-5"></i> Add Template</button>
                        </div>
                        <h4 class="header-title m-b-30">Templates (Support)</h4>
                        @if(count(Auth::user()->personnal_templates()) == 0)
                            <div class="alert alert-info">
                                <strong>Info!</strong> You doesn't have any templates
                            </div>
                        @else
                            <table class="table table-striped" id="templates-table">
                                <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Description</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach(Auth::user()->personnal_templates() as $template)
                                    <tr id="{{ $template->title }}">
                                        <td>{{ $template->title }}</td>
                                        <td>{{ $template->description }}</td>
                                        <td>
                                            <a href="{{route('admin.account.settings.template.update', $template->title)}}" class="edit btn btn-xs btn-default pull-left" data-toggle="tooltip" title="Edit"><i class="fa fa-pencil"></i></a>
                                            <a data-title="{{ $template->title }}" class="delete pull-right btn btn-xs btn-danger" data-toggle="tooltip" title="Delete"><i class="fa fa-trash"></i></a>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        @endif
                    </div>
                <div class="card-box">
                    <h4 class="header-title m-b-30">Preload text (Support)</h4>
                        {!! Form::open(['route' => 'admin.account.settings.update']) !!}
                        {{ method_field('PATCH') }}
                        {!! Form::text('settings_type', 'preloadtext', ['class' => 'hidden disabled']) !!}
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group {{ $errors->has('preloadtext') ? ' has-error' : '' }}">
                                    <label for="preloadtext">Preloadtext:</label>
                                    {!! Form::textarea('preloadtext', Auth::user()->preloadtext(), ['class' => 'form-control hidden', 'id' => 'preloadtext']) !!}
                                    @if ($errors->has('preloadtext'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('preloadtext') }}</strong>
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
<div id="add-template" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h4 class="modal-title">Add Template</h4>
                    </div>
                     {!! Form::open(['route' => ['admin.account.settings.template.post'], 'id' => 'form-add-template']) !!}
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group {{ $errors->has('title') ? ' has-error' : '' }}">
                                    <label for="title">Title:</label>
                                    {!! Form::text('title', null,['class' => 'form-control', 'id' => 'title']) !!}
                                    @if ($errors->has('title'))
                                        <span class="help-block">
                                                <strong>{{ $errors->first('title') }}</strong>
                                            </span>
                                    @endif
                                </div> 
                                <div class="form-group {{ $errors->has('description') ? ' has-error' : '' }}">
                                    <label for="description">Description:</label>
                                    {!! Form::text('description', null,['class' => 'form-control', 'id' => 'description']) !!}
                                    @if ($errors->has('description'))
                                        <span class="help-block">
                                                <strong>{{ $errors->first('description') }}</strong>
                                            </span>
                                    @endif
                                </div> 
                                <div class="form-group {{ $errors->has('content') ? ' has-error' : '' }}">
                                    <label for="content">Content:</label>
                                    {!! Form::textarea('content', null,['class' => 'form-control', 'id' => 'content']) !!}
                                    @if ($errors->has('content'))
                                        <span class="help-block">
                                                <strong>{{ $errors->first('content') }}</strong>
                                            </span>
                                    @endif
                                </div> 
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-info waves-effect waves-light">Add</button>
                    </div>
                     {!! Form::close() !!}
                </div>
            </div>
</div>
@endsection
@section('bottom')
        {!! Html::script('js/admin/sweetalert.min.js') !!}
<script>
                $(document).ready(function () {
                    var token = '{{ Session::token() }}';
                    if($("textarea").length > 0){
                        editor_simple_config.selector = "textarea";
                        $("textarea").removeClass('hidden');
                        tinymce.init(editor_simple_config);
                    };
                     $( "#form-add-template" ).on( "submit", function( event ) {
                        $("#form-add-template button[type='submit']").prop("disabled", true);
                       event.preventDefault();
                       var $this = $(this);
                        var datas = $this.serialize() + tinymce.get('content').getContent();
                       $.ajax({
                           method: 'POST',
                           url: $this.attr("action"),
                           data: datas,

                           success: function (user) {
                               $('#form-add-template').modal('hide');
                               toastr.success('Template added');
                               setTimeout(function(){ location.reload(); }, 700);
                           },

                           error: function (jqXhr, json, errorThrown) {
                               $("#form-add-template button[type='submit']").prop("disabled", false);
                               var errors = jqXhr.responseJSON;
                               var errorsHtml;
                               if(errors)
                               {
                                   errorsHtml= '';
                                   $.each( errors, function( key, value ) {
                                       errorsHtml += '<li>' + value[0] + '</li>';
                                   });
                               }
                               else
                               {
                                   errorsHtml = 'Unknown error';
                               }
                               toastr.error(errorsHtml);
                           }
                       });
                   });
                    $('#templates-table tbody').on('click', 'tr .delete', function () {
                        // Find ID of the post
                        var Title = $(this).data("title");

                        // Some variables
                        var url_settings_base = '{{ route('admin.account.settings')}}';
                        swal({
                            title: "Are you sure?",
                            text: "You will not be able to recover this template!",
                            type: "warning",
                            showCancelButton: true,
                            confirmButtonColor: "#DD6B55",
                            confirmButtonText: "Yes, delete it!",
                            closeOnConfirm: false }, function(){

                            $.ajax({
                                method: 'DELETE',
                                url: ''+url_settings_base+'/template/delete/'+Title+'',
                                data: { _token: token, Title: Title},

                                success: function (msg) {
                                    swal("Deleted!", "This template has been deleted.", "success");
                                    $('#'+ Title +'').fadeOut();
                                },

                                error: function(jqXhr, json, errorThrown) {
                                    var errors = jqXhr.responseJSON;
                                    var errorsHtml;
                                    if(errors)
                                    {
                                        errorsHtml= '';
                                        $.each( errors, function( key, value ) {
                                            errorsHtml += '<li>' + value[0] + '</li>';
                                        });
                                    }
                                    else
                                    {
                                        errorsHtml = 'Unknown error';
                                    }
                                    toastr.error(errorsHtml);
                                }
                            });

                        });
                    });
                });
</script>
@endsection
