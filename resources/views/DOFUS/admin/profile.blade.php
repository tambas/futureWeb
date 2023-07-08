@extends('layouts.admin.admin')
@section('title') Profile @endsection
@section('page-title') Your profile @endsection
@section('header') {{ Html::style('css/dropify.min.css') }}
<style>
    .avatar img
    {
        width: 110px;
        height: 110px;
    }
</style>

@endsection
@section('content')
        <!-- Start content -->
        <div class="content">
            <div class="container">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="card-box">
                            <h4 class="header-title m-t-0 m-b-30">Update</h4>
                                <div class="avatar">
                                    {{ Html::image(Auth::user()->avatar, 'avatar',['class' => 'center-block img-responsive img-thumbnail', 'id' => 'avatar']) }}

                                    @if(Auth::user()->avatar != config('dofus.default_avatar'))
                                        <br/>
                                        <button id="reset-avatar" href="#" class="center-block btn btn-success waves-effect w-md waves-light">Reset avatar</button>
                                    @endif
                                </div>
                                <small class="pull-right">
                                    Member since: {{ Auth::user()->created_at->format('d M Y, g:i A') }}
                                </small>
                                {!! Form::open(['route' => 'admin.account.update', 'files' => true]) !!}
                                {{ method_field('PATCH') }}
                                <div class="form-group {{ $errors->has('firstname') ? ' has-error' : '' }}">
                                    <label for="username">Firstname:</label>
                                    {!! Form::text('firstname', Auth::user()->firstname, ['class' => 'form-control', 'id' => 'firstname']) !!}
                                    @if ($errors->has('firstname'))
                                        <span class="help-block">
                                                            <strong>{{ $errors->first('firstname') }}</strong>
                                                        </span>
                                    @endif
                                </div>
                                <div class="form-group {{ $errors->has('lastname') ? ' has-error' : '' }}">
                                    <label for="username">Lastname:</label>
                                    {!! Form::text('lastname', Auth::user()->lastname, ['class' => 'form-control', 'id' => 'lastname']) !!}
                                    @if ($errors->has('lastname'))
                                        <span class="help-block">
                                                                <strong>{{ $errors->first('lastname') }}</strong>
                                                            </span>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label for="email">E-mail:</label>
                                    {!! Form::text('email', Auth::user()->email, ['class' => 'form-control', 'disabled']) !!}
                                </div>
                                <div class="form-group {{ $errors->has('avatar') ? ' has-error' : '' }}">

                                    <label for="avatar">Upload Avatar:</label>

                                    {!! Form::file('avatar', ['class' => 'dropify', 'id' => 'avatar', 'data-show-remove' => 'false', 'data-max-file-size' => '3.5M', 'data-allowed-file-extensions' => 'jpg jpeg png']) !!}
                                    @if ($errors->has('avatar'))
                                        <span class="help-block">
                                                            <strong>{{ $errors->first('avatar') }}</strong>
                                                        </span>
                                    @endif
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
            {!! Html::script('js/admin/dropify.min.js') !!}
            <script>
                $('.dropify').dropify();
            </script>
            <script>
                // Some variables
                var token = '{{ Session::token() }}';
                var root = '{{ route('home') }}'
                var url_reset = '{{ route('admin.account.avatar.reset') }}';

                // Reset avatar (AJAX)
                $('#reset-avatar').on('click', function () {

                    var url_default_avatar = '{{ config('dofus.default_avatar') }}';
                    $.ajax({
                        method: 'PATCH',
                        url: url_reset,
                        data: { _token: token},

                        success: function (msg) {
                            toastr.success('Avatar updated');
                            $('.card-box .avatar button').hide();
                            $('#avatar').attr('src', root+'/'+url_default_avatar);
                            $('.user-img').find('img').attr('src', root+'/'+url_default_avatar);
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
                                errorsHtml = 'Unknow error';
                            }
                            toastr.error(errorsHtml);
                        }
                    });
                });
            </script>
    @endsection
