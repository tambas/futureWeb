@extends('layouts.admin.admin')
@section('title') World Announces @endsection
@section('page-title') World Announces : List @endsection
@section('header')
    {{ Html::style('css/sweetalert.min.css') }}
@endsection
@section('content')
    <!-- Start content -->
    <div class="content">
        <div class="container">
            <div class="row">
                <div class="m-b-30" style="background-color: white;border-radius: 5px;">
                    <ul class="nav nav-pills nav-justified">
                        @foreach(config('dofus.servers') as $serverconfig)
                            <li role="presentation" class="{{ active_class(if_route_param('server', $serverconfig))}}"><a href="{{ route('admin.announces', $serverconfig) }}">{{ ucfirst($serverconfig) }}</a></li>
                        @endforeach
                    </ul>
                </div>
                <div class="col-sm-12">
                    <div class="card-box">
                        <div class="m-b-30">
                            <a href="{{ route('admin.announce.create', $server) }}" class="btn btn-primary waves-effect waves-light btn-lg m-b-5"><i class="zmdi zmdi-plus"></i> Create Announce</a>
                        </div>
                        <h4 class="header-title m-b-30">{{ ucfirst($server) }}: Announces</h4>

                        @if(count($announces) == 0)
                            <div class="alert alert-info">
                                <strong>Info!</strong> {{ ucfirst($server) }} doesn't have any announces
                            </div>
                        @else
                            <table class="table table-striped" id="announces-table">
                                <thead>
                                <tr>
                                    <th>Id</th>
                                    <th>Message</th>
                                    <th>Actions</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($announces as $announce)
                                    <tr id="{{$announce->Id}}">
                                        <td>{{ $announce->Id }}</td>
                                        <td>{{ $announce->Message }}</td>
                                        <td>
                                            <a href="{{ route('admin.announce.edit', [$server, $announce->Id]) }}" class="edit btn btn-xs btn-default pull-left" data-toggle="tooltip" title="Edit"><i class="fa fa-pencil"></i></a>
                                            <a data-id="{{$announce->Id}}" class="delete pull-right btn btn-xs btn-danger" data-toggle="tooltip" title="Delete"><i class="fa fa-trash"></i></a>
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
            $('#announces-table tbody').on('click', 'tr .delete', function () {
                // Find ID of the post
                var Id = $(this).data("id");

                // Some variables
                var url_announces_base = '{{ route('admin.announces', $server)}}';
                swal({
                    title: "Are you sure?",
                    text: "You will not be able to recover this announce!",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Yes, delete it!",
                    closeOnConfirm: false }, function(){

                    $.ajax({
                        method: 'DELETE',
                        url: ''+url_announces_base+'/'+Id+'',
                        data: { _token: token, Id: Id},

                        success: function (msg) {
                            swal("Deleted!", "This announce has been deleted.", "success");
                            $('#'+ Id +'').fadeOut();
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
