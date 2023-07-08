@extends('layouts.admin.admin')
@section('title') User {{ $user->firstname }} @endsection
@section('page-title') User: {{ $user->firstname }} - Details @endsection
@section('content')
    <!-- Start content -->
    <div class="content">
        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    @include('includes.admin.users.navpills')
                    <div class="card-box">
                        <div class="pull-right">
                            <a class="btn btn-info waves-effect waves-light btn-sm" href="{{route('admin.user.game.accounts', [$user->id, $server])}}">
                                <i class="fa fa-long-arrow-left"></i> Back
                            </a>
                        </div>

                        <h4 class="header-title m-b-30">#{{$account->Id}} Account: Edit</h4>
                        <div class="alert-ban">
                            @if($account->isBanned())
                                <div class="alert alert-danger fade in m-b-30">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                    <span class="center-block text-center"><strong>This game account is banned @if($account->BanEndDate)until {{ $account->BanEndDate->format('d/m/Y H\hi') }} @else permanently. @endif</strong></span>
                                    @if($account->BanReason)<br><strong><u>Reason:</u></strong> {{$account->BanReason}}@endif
                                </div>
                            @endif
                        </div>
                        <div class="col-lg-12">
                            <div class="card-box">
                                <h4 class="header-title m-t-0 m-b-30">Actions</h4>
                                <div class="text-center buttons">
                                    <button type="button" class="btn btn-success waves-effect w-md m-b-5" data-toggle="modal" data-target="#account-password-modal"><i class="fa fa-key m-r-5"></i> Change password</button>
                                </div>
                            </div>
                        </div>
                        {!! Form::model($account, ['route' => ['admin.user.game.account.update', $user->id, $server, $account->Id]]) !!}
                        {{ method_field('PATCH') }}
                        <div class="row">
                            <div class="col-sm-12">
                                <small class="pull-right">
                                    Account created {{ $account->CreationDate->diffForHumans() }} ({{ $account->CreationDate->format('d-m-Y H:i:s') }})
                                </small>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group {{ $errors->has('Login') ? ' has-error' : '' }}">
                                    <label for="title">Login:</label>
                                    {!! Form::text('Login', null, ['class' => 'form-control', 'id' => 'Login']) !!}
                                    @if ($errors->has('Login'))
                                        <span class="help-block">
                                                                    <strong>{{ $errors->first('Login') }}</strong>
                                                                </span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group {{ $errors->has('Nickname') ? ' has-error' : '' }}">
                                    <label for="Nickname">Nickname:</label>
                                    {!! Form::text('Nickname', null, ['class' => 'form-control', 'id' => 'Nickname', 'disabled', 'readonly']) !!}
                                    @if ($errors->has('Nickname'))
                                        <span class="help-block">
                                                                    <strong>{{ $errors->first('Nickname') }}</strong>
                                                                </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group {{ $errors->has('UserGroupId') ? ' has-error' : '' }}">
                                    <label for="UserGroupId">User Group:</label>
                                    {!! Form::select('UserGroupId', config('dofus.ranks'), null,['class' => 'form-control', 'id' => 'UserGroupId']) !!}
                                    @if ($errors->has('UserGroupId'))
                                        <span class="help-block">
                                                                    <strong>{{ $errors->first('UserGroupId') }}</strong>
                                                                </span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Tokens:</label>
                                    {!! Form::text('Tokens', $account->points(),['class' => 'form-control', 'id' => 'UserGroupId', 'disabled', 'readonly']) !!}
                                </div>
                            </div>
                        </div>
                        {!! Form::submit('Update', ['class' => 'btn btn-info']) !!}
                        {!! Form::close() !!}
                    </div>

                    <div class="card-box">
                        <h4 class="header-title m-b-30">#{{$account->Id}} Account: Characters</h4>

                        @if(count($characters) == 0)
                            <div class="alert alert-info">
                                <strong>Info!</strong> User doesn't have any characters on this account
                            </div>
                        @else
                            <table class="table table-striped" id="characters-table">
                                <thead>
                                <tr>
                                    <th>Id</th>
                                    <th>Name</th>
                                    <th>Class</th>
                                    <th>Level</th>
                                    <th>Creation date</th>
                                    <th>Last usage</th>
                                    <th>Status</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($characters as $character)
                                    <tr>
                                        <td>{{ $character->Id }}</td>
                                        <td>{{ $character->Name }}</td>
                                        <td>{{ $character->classe() }}</td>
                                        <td>{{ $character->level($server) }}</td>
                                        <td>{{ $character->CreationDate->format('d-m-Y H:i:s') }}</td>
                                        <td>{{ $character->LastUsage->format('d-m-Y H:i:s') }}</td>
                                        <td>@if ($character->isDeleted())
                                                <span class="label label-danger">Deleted</span>
                                                <span class="label label-danger">{{ $character->DeletedDate->format('d-m-Y H:i:s') }}</span>
                                            @else
                                                <span class="label label-success">Running</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        @endif
                    </div>

                </div>
            </div>
        </div>
        <div id="account-password-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h4 class="modal-title">{{ $account->Login }}: Edit password</h4>
                    </div>
                    <div class="modal-body">

                        {!! Form::open(['route' => ['admin.user.game.account.password', $user->id, $server, $account->Id], 'id' => 'form-password-account']) !!}
                        <div class="row">
                            <div class="col-sm-12">
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
                                    <label for="password_confirmation">Confirmation:</label>
                                    {!! Form::password('passwordConfirmation',['class' => 'form-control']) !!}
                                    @if ($errors->has('passwordConfirmation'))
                                        <span class="help-block">
                                                        <strong>{{ $errors->first('passwordConfirmation') }}</strong>
                                                    </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-info waves-effect waves-light">Update</button>
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
@endsection

@section('bottom')
    <script>
        $(document).ready(function () {

            // Edit Password (Save ajax)
            $("#form-password-account").on("submit", function (event) {
                event.preventDefault();
                var $this = $(this);
                var datas = $this.serialize();

                $.ajax({
                    method: 'PATCH',
                    url: $this.attr("action"),
                    data: datas,

                    success: function (msg) {

                        toastr.success('Password updated');
                        $('#account-password-modal').modal('hide');
                    },

                    error: function (jqXhr, json, errorThrown) {
                        var errors = jqXhr.responseJSON;
                        var errorsHtml;
                        if (errors) {
                            errorsHtml = '';
                            $.each(errors, function (key, value) {
                                errorsHtml += '<li>' + value[0] + '</li>';
                            });
                        }
                        else {
                            errorsHtml = 'Unknow error';
                        }
                        toastr.error(errorsHtml);
                    }
                });
            });
        });
    </script>
@endsection
