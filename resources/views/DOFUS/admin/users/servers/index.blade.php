@extends('layouts.admin.admin')
@section('title') User {{ $user->firstname }} @endsection
@section('page-title') User: {{ $user->firstname }} - Details @endsection
@section('header')
    {{ Html::style('css/jquery.datetimepicker.min.css') }}
    {{ Html::style('css/sweetalert.min.css') }}
@endsection
@section('content')
     <!-- Start content -->
    <div class="content">
        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    @include('includes.admin.users.navpills')
                    <div class="card-box">
                        @if(count($accounts) < config('dofus.accounts_limit'))
                        <div class="pull-right">
                                <button href="javascript:void(0)" class="btn btn-primary btn-lg waves-effect waves-light" data-toggle="modal" data-target="#game-account-add-modal"><i class="fa fa-plus"></i> Create account</button></li>
                        </div>
                        @endif
                        <h4 class="header-title m-b-30">{{ ucfirst($server) }} Accounts: Details</h4>
                        {{ Html::image('imgs/admin/'.$server.'.png', $server, ['class' => 'center-block m-b-30']) }}

                        @if(count($accounts) == 0)
                            <div class="alert alert-info">
                                <strong>Info!</strong> User doesn't have any game accounts on this server
                            </div>
                        @else
                                <table class="table table-striped" id="accounts-table">
                                    <thead>
                                    <tr>
                                        <th>Id</th>
                                        <th>Login</th>
                                        <th>Nickname</th>
                                        <th>User Group</th>
                                        <th>Tokens</th>
                                        <th>Last connection</th>
                                        <th>Last IP</th>
                                        <th>Last KEY</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($accounts as $account)
                                        <tr>
                                            <td>{{ $account->Id }}</td>
                                            <td>{{ $account->Login }}</td>
                                            <td>{{ $account->Nickname }}</td>
                                            <td>{{ config('dofus.ranks')[$account->UserGroupId] }}</td>
                                            <td>{{ $account->points() }}</td>
                                            <td>
                                                @if($account->LastConnection)
                                                {{ $account->LastConnection->diffForHumans() }}
                                                @endif
                                            </td>
                                            <td>{{ $account->LastConnectedIp }}</td>
                                            <td>{{ $account->LastClientKey }}</td>
                                            <td>
                                                {!! $account->htmlStatus() !!}
                                            </td>
                                            <td>
                                                <a href="{{ route('admin.user.game.account.edit', [$user->id, $server, $account->Id]) }}" class="edit btn btn-xs btn-default" data-toggle="tooltip" title="Edit"><i class="fa fa-pencil"></i></a>
                                                @if(!$account->IsBanned)
                                                <a href="javascript:void(0)" id="ban-{{$account->Id}}" class="ban pull-right btn btn-xs btn-danger m-l-5" data-toggle="tooltip" title="Ban"> <i class="fa fa-ban"></i> </a>
                                                @else
                                                    <a href="javascript:void(0)" id="unban-{{$account->Id}}" class="unban pull-right btn btn-xs btn-info m-l-5" data-toggle="tooltip" title="Unban"> <i class="fa fa-ban"></i> </a>
                                                @endif

                                                @if(!$account->IsJailed)
                                                    <a href="javascript:void(0)" id="jail-{{$account->Id}}" class="jail pull-right btn btn-xs btn-danger m-l-5" data-toggle="tooltip" title="Jail"> <i class="fa fa-lock"></i> </a>
                                                @else
                                                    <a href="javascript:void(0)" id="unjail-{{$account->Id}}" class="unjail pull-right btn btn-xs btn-info m-l-5" data-toggle="tooltip" title="Unjail"> <i class="fa fa-unlock"></i> </a>
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
    </div>
        <div id="game-account-add-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h4 class="modal-title">Create: {{ ucfirst($server) }} Account</h4>
                    </div>
                    <div class="modal-body">

                        {!! Form::open([ 'route' => ['admin.user.game.account.store','server' => $server, 'id' => $user->id], 'id' => 'form-game-account-add']) !!}
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="login" class="control-label">Login</label>
                                    <input type="text" class="form-control" id="login" name="login">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="nickname" class="control-label">Nickname</label>
                                    <input type="text" class="form-control" id="nickname" name="nickname">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="password" class="control-label">Password</label>
                                    <input type="password" class="form-control" name="password" id="password">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="passwordConfirmation" class="control-label">Confirmation</label>
                                    <input type="password" class="form-control" name="passwordConfirmation" id="passwordConfirmation">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                        <button id="button-task-add" type="submit" class="btn btn-info waves-effect waves-light">Create Game Account</button>
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
     <div id="account-sanction-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
         <div class="modal-dialog modal-sm">
             <div class="modal-content">
                 <div class="modal-header">
                     <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                     <h4 class="modal-title"></h4>
                 </div>
                 <div class="modal-body">
                     <div class="row">
                         <div class="col-md-12">
                             <div class="form-group">
                                 <label for="BanEndDate" class="control-label">End Date:</label>
                                 {!! Form::datetime('BanEndDate', \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', \Carbon\Carbon::now('Europe/Brussels'))->addWeek()->toDateTimeString(),['class' => 'form-control', 'id' => 'dtpicker']) !!}
                                 <small id="ban_default">Default value = 1 week</small>
                                 <div class="checkbox checkbox-success">
                                 {!! Form::checkbox('life', 1, false, ['id' => 'life']) !!}
                                     <label for="banlife">Life</label>
                                 </div>
                             </div>
                         </div>
                     </div>
                     <div class="row">
                         <div class="col-md-12">
                             <div class="form-group">
                                 <label for="BanReason" class="control-label">Reason:</label>
                                 {!! Form::textarea('BanReason',null, ['class' => 'form-control', 'id' => 'BanReason', 'rows' => '3']) !!}
                             </div>
                         </div>
                     </div>
                     <div class="row">
                         <div class="col-md-12">
                             <div class="form-group">
                                 <div class="checkbox checkbox-danger">
                                     {!! Form::checkbox('allaccounts',1,null, ['class' => 'form-control', 'id' => 'allaccounts']) !!}
                                     <label for="allaccounts" style="color: red;">/!\ Apply for all accounts: /!\</label>
                                 </div>
                             </div>
                         </div>
                     </div>
                 </div>
                 <div class="modal-footer">
                     <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                     <button id="button-sanction-add" type="submit" class="btn btn-danger waves-effect waves-light"></button>
                 </div>
             </div>
         </div>
     </div>
@endsection

@section('bottom')
    {!! Html::script('js/admin/sweetalert.min.js') !!}
    {{ Html::script('js/admin/jquery.datetimepicker.full.min.js') }}
<script>
    var token = '{{ Session::token() }}';
    $("#life").change(function() {
        if(this.checked) {
            $('#dtpicker').fadeOut(800);
            $('#ban_default').fadeOut(800);
        }
        else{
            $('#dtpicker').fadeIn(1000);
            $('#ban_default').fadeIn(1000);
        }
    });
    $( "#form-game-account-add" ).on( "submit", function( event ) {
        event.preventDefault();
        var $this = $(this);
        $.ajax({
            method: 'POST',
            url: $this.attr("action"),
            data: $this.serialize(),

            success: function (msg) {
                toastr.success('Adding account..');
                setTimeout(function(){ location.reload(); }, 500);
            },

            error: function (jqXhr, json, errorThrown) {
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

    $('#accounts-table tbody').on('click', 'tr .ban', function () {
        $('#dtpicker').datetimepicker({
            format:'Y-m-d H:i:s'
        });
        var clickedId = $(this).attr('id');
        var accountId = clickedId.replace("ban-", "");
        var element = $(this);
        var banReason = $('#pop-'+accountId).data('content');
        $('#account-sanction-modal').find('.modal-title').text('Ban: Account #'+accountId);
        $('#account-sanction-modal').find('.modal-title').attr('id', accountId);
        $('#account-sanction-modal').find('.modal-title').attr('type', 'ban');
        $('#account-sanction-modal').find('textarea#BanReason').text(banReason);
        $('#button-sanction-add').text('Ban');
        $('#account-sanction-modal').modal();
    });
    $('#accounts-table tbody').on('click', 'tr .jail', function () {
        $('#dtpicker').datetimepicker({
            format:'Y-m-d H:i:s'
        });
        var clickedId = $(this).attr('id');
        var accountId = clickedId.replace("jail-", "");
        var element = $(this);
        var banReason = $('#pop-'+accountId).data('content');
        $('#account-sanction-modal').find('.modal-title').text('Jail: Account #'+accountId);
        $('#account-sanction-modal').find('.modal-title').attr('id', accountId);
        $('#account-sanction-modal').find('.modal-title').attr('type', 'jail');
        $('#account-sanction-modal').find('textarea#BanReason').text(banReason);
        $('#button-sanction-add').text('Jail');
        $('#account-sanction-modal').modal();
    });
    $('#button-sanction-add').on('click', function(e){
        e.preventDefault();
        var accountId = $('#account-sanction-modal').find('.modal-title').attr('id');
        var type = $('#account-sanction-modal').find('.modal-title').attr('type');
        var banReason = $('#account-sanction-modal').find('textarea#BanReason').val();
        var banEndDate = $('#dtpicker').val();
        var url_accounts_base = '{{ route('admin.user.game.accounts', [$user->id, $server])}}';
        var allaccounts = '0';
        var life = '0';
        if(document.getElementById('life').checked == true){
            life = '1';
        }
        if(document.getElementById('allaccounts').checked == true){
            allaccounts = '1';
        }
        console.log(life);
        $.ajax({
            method: 'PATCH',
            url: ''+url_accounts_base+'/'+accountId+'/'+type,
            data: { _token: token, BanReason: banReason, BanEndDate: banEndDate, allaccounts: allaccounts, life: life},

            success: function (msg) {
                toastr.success('Account(s) sanctioned');
                setTimeout(function(){
                    swal.close();
                    location.reload(); }, 1000);
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
    $('#accounts-table tbody').on('click', 'tr .unban', function () {
        // Find ID of the user
        var clickedId = $(this).attr('id');
        var accountId = clickedId.replace("unban-", "");
        var element = $(this);
        var banReason = $('#pop-'+accountId).data('content');
        var url_accounts_base = '{{ route('admin.user.game.accounts', [$user->id, $server])}}';
        swal({
            title: "Are you sure?",
            text: "This account will be unbanned!<br/>Ban reason:<br/><strong> "+banReason+"</strong>",
            html: true,
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Yes, unban him!",
            closeOnConfirm: false }, function(){

            $.ajax({
                method: 'PATCH',
                url: ''+url_accounts_base+'/'+accountId+'/unban',
                data: { _token: token },

                success: function (msg) {
                    toastr.success('Account unbanned');
                    setTimeout(function(){
                        swal.close();
                        location.reload(); }, 1000);
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
    });
    $('#accounts-table tbody').on('click', 'tr .unjail', function () {
        // Find ID of the user
        var clickedId = $(this).attr('id');
        var accountId = clickedId.replace("unjail-", "");
        var element = $(this);
        var banReason = $('#pop-'+accountId).data('content');
        var url_accounts_base = '{{ route('admin.user.game.accounts', [$user->id, $server])}}';
        swal({
            title: "Are you sure?",
            text: "This account will be unjailed!<br/> Reason:<br/><strong> "+banReason+"</strong>",
            html: true,
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Yes, unjail!",
            closeOnConfirm: false }, function(){

            $.ajax({
                method: 'PATCH',
                url: ''+url_accounts_base+'/'+accountId+'/unjail',
                data: { _token: token },

                success: function (msg) {
                    toastr.success('Account unjailed');
                    setTimeout(function(){
                        swal.close();
                        location.reload(); }, 1000);
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
    });

</script>
@endsection
