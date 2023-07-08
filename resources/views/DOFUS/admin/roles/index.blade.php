@extends('layouts.admin.admin')
@section('title') Roles @endsection
@section('page-title') Roles : List @endsection
@section('header')
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
                            <a href="{{ route('admin.role.create') }}" class="btn btn-primary waves-effect waves-light btn-lg m-b-5"><i class="zmdi zmdi-plus"></i> Create Role</a>
                        </div>
                        <h4 class="header-title m-b-30">Roles</h4>

                        @if(count($roles) == 0)
                            <div class="alert alert-info">
                                <strong>Info!</strong> {{ ucfirst($server) }} doesn't have any roles
                            </div>
                        @else
                            <table class="table table-striped" id="roles-table">
                                <thead>
                                <tr>
                                    <th>Id</th>
                                    <th>Name</th>
                                    <th>Nb.Users</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($roles as $role)
                                    <tr id="{{$role->id}}">
                                        <td>{{ $role->id }}</td>
                                        <td>{{ $role->label }}</td>
                                        <td>{{ $role->users()->count() }}</td>
                                        <td>{{ $role->created_at->format('d/m/Y H:i:s') }}</td>
                                        <td>
                                            <a href="{{route('admin.role.edit', $role->id)}}" class="edit btn btn-xs btn-default pull-left" data-toggle="tooltip" title="Edit"><i class="fa fa-pencil"></i></a>
                                            <a href="{{route('admin.role.permissions', $role->id)}}" class="permissions btn btn-xs btn-default pull-left" data-toggle="tooltip" title="Permissions"><i class="fa fa-key"></i></a>
                                            <a data-id="{{$role->id}}" class="delete pull-right btn btn-xs btn-danger" data-toggle="tooltip" title="Delete"><i class="fa fa-trash"></i></a>
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
@endsection

@section('bottom')
            {!! Html::script('js/admin/sweetalert.min.js') !!}
    <script>
        $(document).ready(function () {
            var token = '{{ Session::token() }}';
            $('#roles-table tbody').on('click', 'tr .delete', function () {
                // Find ID of the role
                var id = $(this).data("id");

                // Some variables
                var url_roles_base = '{{ route('admin.roles')}}';
                swal({
                    title: "Are you sure?",
                    text: "You will not be able to recover this role!",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Yes, delete it!",
                    closeOnConfirm: false }, function(){

                    $.ajax({
                        method: 'DELETE',
                        url: ''+url_roles_base+'/'+id+'',
                        data: { _token: token},

                        success: function (msg) {
                            swal("Deleted!", "This role has been deleted.", "success");
                            $('#'+ id +'').fadeOut();
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
