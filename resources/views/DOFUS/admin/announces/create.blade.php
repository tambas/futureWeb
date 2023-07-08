@extends('layouts.admin.admin')
@section('title') World Announces @endsection
@section('page-title') World Announces : Create @endsection
@section('content')
        <!-- Start content -->
        <div class="content">
            <div class="container">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="m-b-30">
                            <a href="{{ route('admin.announces', $server) }}" class="btn btn-primary waves-effect waves-light btn-lg m-b-5"><i class="zmdi zmdi-long-arrow-return"></i> Return to list</a>
                        </div>                    
                        <div class="card-box">
                            {!! Form::open(['route' => ['admin.announce.store',$server], 'files' => true]) !!}
                                {{ method_field('POST') }}
                            <div class="row">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="col-sm-12">
                                            <div class="form-group {{ $errors->has('Message') ? ' has-error' : '' }}">
                                                <label for="Message">Message:</label>
                                                {!! Form::textarea('Message', null, ['class' => 'form-control', 'id' => 'Message']) !!}
                                                @if ($errors->has('Message'))
                                                    <span class="help-block">
                                                                        <strong>{{ $errors->first('Message') }}</strong>
                                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                                {!! Form::submit('Create', ['class' => 'btn btn-info btn-block']) !!}
                                {!! Form::close() !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
@endsection