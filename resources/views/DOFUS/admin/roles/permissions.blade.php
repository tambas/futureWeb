@extends('layouts.admin.admin')
@section('title') Roles @endsection
@section('page-title') Role: Permissions @endsection
@section('header')
    {{ Html::style('css/sweetalert.min.css') }}
@endsection
@section('content')
        <!-- Start content -->
        <div class="content">
            <div class="container">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="m-b-30">
                                <a href="{{ route('admin.roles') }}" class="btn btn-primary waves-effect waves-light btn-lg m-b-5"><i class="zmdi zmdi-long-arrow-return"></i> Return to list</a>
                        </div>
                        <div class="card-box">
                            <h4 class="header-title m-b-30">{{ $role->label }}: Permissions</h4>
                        <div class="m-b-30">
                            <button type="button" class="btn btn-success waves-effect waves-light btn-lg m-b-5" data-toggle="modal" data-target="#add-permission"><i class="fa fa-plus m-r-5"></i> Add permission</button>
                        </div>
                        @if(count($permissions) == 0)
                            <div class="alert alert-info">
                                <strong>Info!</strong> Role: {{ $role->label }} doesn't have any permissions
                            </div>
                        @else
                            <table class="table table-striped" id="permissions-table">
                                <thead>
                                <tr>
                                    <th>Id</th>
                                    <th>Name</th>
                                    <th>Actions</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($permissions as $permission)
                                    <tr id="{{$permission->id}}">
                                        <td>{{ $permission->id }}</td>
                                        <td>{{ $permission->label }}</td>
                                        <td>
                                            <a data-roleid="{{$role->id}}" data-permissionid="{{$permission->id}}" class="remove btn btn-xs btn-danger" data-toggle="tooltip" title="Remove"><i class="fa fa-remove"></i></a>
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
<div id="add-permission" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h4 class="modal-title">{{$role->label}}: Add permission</h4>
                    </div>
                     {!! Form::open(['route' => ['admin.role.permission.add', $role->id], 'id' => 'form-add-permission']) !!}
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group {{ $errors->has('permission') ? ' has-error' : '' }}">
                                    <label for="permission">Permission:</label>
                                    {!! Form::select('permission', $permissionsdata, null,['class' => 'form-control', 'id' => 'permission', empty($permissionsdata) ? 'disabled' : '']) !!}
                                    @if ($errors->has('permission'))
                                        <span class="help-block">
                                                <strong>{{ $errors->first('permission') }}</strong>
                                            </span>
                                    @endif
                                    @if (empty($permissionsdata))
                                        <div class="m-t-15">
                                            <div class="alert alert-warning">
                                                    {{ $role->label }} have all permissions
                                            </div>
                                        </div>
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
            $('#permissions-table tbody').on('click', 'tr .remove', function () {
                // Find ID of the permission
                var roleid = $(this).data("roleid");
                var permissionid = $(this).data("permissionid");

                // Some variables
                var url_roles_base = '{{ route('admin.roles')}}';
                swal({
                    title: "Are you sure?",
                    text: "This action will remove the permission!",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Yes, remove it!",
                    closeOnConfirm: false }, function(){

                    $.ajax({
                        method: 'DELETE',
                        url: ''+url_roles_base+'/'+roleid+'/permissions/remove',
                        data: { _token: token, id: roleid, permission: permissionid},

                        success: function (msg) {
                            swal("Removed!", "This permission has been removed.", "success");
                            $('#'+ permissionid +'').fadeOut();
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
            $( "#form-add-permission" ).on( "submit", function( event ) {
                        $("#form-add-permission button[type='submit']").prop("disabled", true);
                       event.preventDefault();
                       var $this = $(this);
                       var datas = $this.serialize();

                       $.ajax({
                           method: 'POST',
                           url: $this.attr("action"),
                           data: datas,

                           success: function (user) {
                               $('#add-permission').modal('hide');
                               toastr.success('Permission added');
                               setTimeout(function(){ location.reload(); }, 700);
                           },

                           error: function (jqXhr, json, errorThrown) {
                               $("#form-add-permission button[type='submit']").prop("disabled", false);
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
</script>
@endsection