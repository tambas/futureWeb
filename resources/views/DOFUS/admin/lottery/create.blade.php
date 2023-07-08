@extends('layouts.admin.admin')
@section('title') Lottery Create @endsection
@section('page-title') Lottery: Create @endsection
@section('header')
    {{ Html::style('css/dropify.min.css') }}
@endsection
@section('content')
        <!-- Start content -->
<div class="content">
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <div class="m-b-30">
                    <a href="{{route('admin.lottery')}}" class="btn btn-primary waves-effect waves-light btn-lg m-b-5"><i class="zmdi zmdi-long-arrow-return"></i> Return to lottery</a>
                </div>
                <div class="card-box">
                    <h4 class="header-title m-b-30">Lottery</h4>
                    {{ Form::open(['route' => 'admin.lottery.store', 'id' => 'form-storelottery', 'files' => 'true']) }}

                    <div class="form-group {{ $errors->has('name') ? ' has-error' : '' }}">
                        <label for="name">Name:</label>

                        {!! Form::text('name', null, ['class' => 'form-control', 'id' => 'name']) !!}
                        @if ($errors->has('name'))
                            <span class="help-block">
                                                            <strong>{{ $errors->first('name') }}</strong>
                                                        </span>
                        @endif
                    </div>

                    <div class="form-group {{ $errors->has('icon_path') ? ' has-error' : '' }}">

                        <label for="icon_path">Icon:</label>

                        {!! Form::file('icon_path', ['class' => 'dropify', 'id' => 'icon_path', 'data-show-remove' => 'false', 'data-max-file-size' => '3.0M', 'data-allowed-file-extensions' => 'png']) !!}
                        @if ($errors->has('icon_path'))
                            <span class="help-block">
                                                            <strong>{{ $errors->first('icon_path') }}</strong>
                                                        </span>
                        @endif
                    </div>
                    <div class="form-group {{ $errors->has('image_path') ? ' has-error' : '' }}">

                        <label for="image_path">Image (Box):</label>

                        {!! Form::file('image_path', ['class' => 'dropify2', 'id' => 'image_path', 'data-show-remove' => 'false', 'data-max-file-size' => '3.0M', 'data-allowed-file-extensions' => 'png']) !!}
                        @if ($errors->has('image_path'))
                            <span class="help-block">
                                                            <strong>{{ $errors->first('image_path') }}</strong>
                                                        </span>
                        @endif
                    </div>

                    {!! Form::submit('Create', ['class' => 'btn btn-info']) !!}
                    {{ Form::close() }}
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
        $('.dropify2').dropify();
    </script>
@endsection
