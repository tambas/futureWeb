@extends('layouts.admin.admin')
@section('title') Password @endsection
@section('page-title') Password @endsection
@section('content')
        <!-- Start content -->
        <div class="content">
            <div class="container">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="card-box">
                            <h4 class="header-title m-t-0 m-b-30">Update</h4>

                            {!! Form::open(['route' => 'admin.password.update']) !!}
                            {{ method_field('PATCH') }}

                            <div class="form-group {{ $errors->has('password') ? ' has-error' : '' }}">
                                <label for="password">New password:</label>
                                {!! Form::password('password',['class' => 'form-control']) !!}
                                @if ($errors->has('password'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
                            </div>

                            <div class="form-group {{ $errors->has('passwordConfirmation') ? ' has-error' : '' }}">
                                <label for="password_confirmation">New password verify:</label>
                                {!! Form::password('passwordConfirmation',['class' => 'form-control']) !!}
                                @if ($errors->has('passwordConfirmation'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('passwordConfirmation') }}</strong>
                                    </span>
                                @endif
                            </div>

                            {!! Form::submit('Update', ['class' => 'btn btn-sm btn-primary']) !!}
                            {!! Form::close() !!}

                        </div>
                    </div>
                </div>
            </div>
        </div>

        @endsection

