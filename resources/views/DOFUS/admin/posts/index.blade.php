@extends('layouts.admin.admin')
@section('title') Posts @endsection
@section('page-title') Posts: List @endsection
@section('header')
    {{ Html::style('css/vendor_admin_datatables.min.css') }}
    {{ Html::style('css/sweetalert.min.css') }}
    {{ Html::script('js/admin/browseserver.min.js') }}
@endsection
@section('content')
        <!-- Start content -->
        <div class="content">
            <div class="container">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="card-box table-responsive">
                            <div class="m-b-30">
                                <a href="{{ route('admin.post.create') }}" class="btn btn-primary waves-effect waves-light btn-lg m-b-5"><i class="zmdi zmdi-plus"></i> Create Post</a>
                            </div>
                            <table id="posts-table" class="table table-striped table-bordered dataTable no-footer" role="grid" aria-describedby="datatable_info">
                                <thead>
                                <tr>
                                    <th>Id</th>
                                    <th>Title</th>
                                    <th>Slug</th>
                                    <th>Type</th>
                                    <th>Author</th>
                                    <th>Published</th>
                                    <th>Actions</th>
                                </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @endsection

        @section('bottom')
            {!! Html::script('js/admin/vendor_admin_datatables.min.js') !!}
            {!! Html::script('js/admin/sweetalert.min.js') !!}
            <script>
                $(document).ready(function () {
                    var token = '{{ Session::token() }}';
                    var oTable;
                    oTable = $('#posts-table').on( 'init.dt', function () {
                        $('[data-toggle="tooltip"]').tooltip()
                    } ).DataTable({
                        "order": [[ 0, "desc" ]],
                        processing: false,
                        serverSide: false,
                        autoWidth: true,
                        responsive: true,
                        ajax: '{!! route('datatables.postdata') !!}',
                        columns: [
                            {data: 'id', name: 'posts.id'},
                            {data: 'title', name: 'posts.title'},
                            {data: 'slug', name: 'posts.slug'},
                            {data: 'type', name: 'posts.type'},
                            {data: 'firstname', name: 'users.firstname'},
                            {data: 'published', name: 'posts.published', type: 'html'},
                            {data: 'action', name: 'action',  orderable: false, searchable: false}
                        ],
                        rowId: 'id'
                    });

                    $('#posts-table tbody').on('click', 'tr .delete', function () {
                        // Find ID of the post
                        var id = $(this).attr('id').split('-');
                        var id = id[1];

                        // Some variables
                        var url_posts_base = '{{ route('admin.posts')}}';
                        swal({
                            title: "Are you sure?",
                            text: "You will not be able to recover this post!",
                            type: "warning",
                            showCancelButton: true,
                            confirmButtonColor: "#DD6B55",
                            confirmButtonText: "Yes, delete it!",
                            closeOnConfirm: false }, function(){

                            $.ajax({
                                method: 'DELETE',
                                url: ''+url_posts_base+'/'+id+'',
                                data: { _token: token, post: id},

                                success: function (msg) {
                                    swal("Deleted!", "This post has been deleted.", "success");
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

                    $('#posts-table tbody').on('click', 'tr .preview', function () {
                        // Find ID of the post
                        var id = $(this).attr('id').split('-');
                        var id = id[1];
                        // With the ID we can find the slug
                        var slug = $('#posts-table').DataTable().row('#'+ id +'').data().slug;
                        var url_post_base = '{{ route('home')}}';
                        var url = ''+url_post_base+'/article/'+id+'/'+slug+'';
                       BrowseServerUrl(url);
                    });
                });
            </script>
@endsection