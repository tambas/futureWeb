@extends('layouts.admin.admin')
@section('title') Support @endsection
@section('page-title') Support: Tickets @endsection
@section('header')
    {{ Html::style('css/vendor_admin_datatables.min.css') }}
    {{ Html::style('css/sweetalert.min.css') }}
    {{ Html::script('js/admin/browseserver.min.js') }}
    <style>
    tfoot input {
        width: 100%;
        padding: 3px;
        box-sizing: border-box;
    }
    </style>
@endsection
@section('content')
        <!-- Start content -->
        <div class="content">
            <div class="container">
                <div class="row">
                    <div class="col-sm-12">
                    @if($type != "Mine")
                        @include('includes.admin.support.navpills')
                    @endif
                        <div class="card-box table-responsive">
                             <div class="m-b-30">
                               <h4 class="header-title m-b-30">Tickets: {{$type}}</h4>
                            </div>
                            <table id="support-table" class="table table-striped table-bordered dataTable" role="grid" aria-describedby="datatable_info">
                                <thead>
                                <tr>
                                    <th>Id</th>
                                    <th>Category</th>
                                    <th>Subject</th>
                                    <th>User</th>
                                    <th>Open</th>
                                    <th>In charge</th>
                                    <th>State</th>
                                    <th>Last message</th>
                                    <th>Actions</th>
                                </tr>
                                </thead>
                                <tfoot>
                                <tr>
                                    <th>Id</th>
                                    <th>Category</th>
                                    <th>Subject</th>
                                    <th>User</th>
                                    <th>Open</th>
                                    <th>In charge</th>
                                    <th>State</th>
                                    <th>Last message</th>
                                </tr>
                            </tfoot>
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
                    oTable = $('#support-table').on( 'init.dt', function () {
                        $('[data-toggle="tooltip"]').tooltip()
                    } ).DataTable({
                        "order": [[ 0, "desc" ]],
                        processing: true,
                        serverSide: false,
                        autoWidth: true,
                        responsive: true,
                        ajax: '{!! route($route) !!}',
                        columns: [
                            {data: 'id', name: 'id'},
                            {data: 'category', name: 'category'},
                            {data: 'subject', name: 'subject'},
                            {data: 'user_id', name: 'user_id', type: 'html'},
                            {data: 'created_at', name: 'created_at'},
                            {data: 'assign_to', name: 'assign_to'},
                            {data: 'state', name: 'state', type: 'html'},
                            {data: 'last_message', name: 'last_message'},
                            {data: 'action', name: 'action', class: 'actions', orderable: false, searchable: false},
                        ],
                        rowId: 'id'
                    });

                    $('#support-table tfoot th').each( function () {
                        var classNamed = $(this)[0].className;
                            if(classNamed != "actions")
                            {
                                var title = $(this).text();
                                $(this).html( '<input type="text" placeholder="Search '+title+'" />' );
                            }
                        } );

                    oTable.columns().every( function () {
                    var that = this;
                    if(this.footer())
                    {
                        $( 'input', this.footer() ).on( 'keyup change', function () {
                            if ( that.search() !== this.value ) {
                                that
                                    .search( this.value )
                                    .draw();
                            }
                        } );
                    }
                    } );

                    $('#support-table tbody').on('click', 'tr td:not(.actions)', function () {
                         var row_clicked = $(this).index();
                          $( 'input', oTable.column(row_clicked).footer() ).val(noHtml(oTable.cell( this ).data())).keyup();
                    } );

                    function noHtml(txt) {
                        if(Number.isInteger(txt)){
                            return (txt);
                        }
                            a = txt.indexOf('<');
                            b = txt.indexOf('>');
                            len = txt.length;
                            c = txt.substring(0, a);
                                if(b == -1) 
                                {
                                    b = a;
                                }
                            d = txt.substring((b + 1), len);
                            txt = c + d;
                            cont = txt.indexOf('<');
                            if (a != b) 
                            {
                                txt = noHtml(txt);
                            }
                            return(txt);
                    }

                    $('#support-table tbody').on('click', 'tr .toswitch', function () {
                        // Find ID of the ticket
                         var id = $(this).data("id");

                        // Some variables
                        var url_support_base = '{{ route('admin.support')}}';
                        swal({
                            title: "Are you sure?",
                            text: "You will close/open this ticket!",
                            type: "warning",
                            showCancelButton: true,
                            confirmButtonColor: "#DD6B55",
                            confirmButtonText: "Yes, close/open it!",
                            closeOnConfirm: false }, function(){
                            $.ajax({
                                method: 'PATCH',
                                url: ''+url_support_base+'/ticket/'+id+'/switch',
                                data: { _token: token},

                                success: function (msg) {
                                    swal("Updated!", "This ticket has been updated.", "success");
                                    $('#'+ id +'').fadeOut();
                                },

                                error: function(jqXhr, json, errorThrown) {
                                    console.log(url);
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