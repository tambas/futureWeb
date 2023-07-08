@extends('layouts.admin.admin')
@section('title') List tickets @endsection
@section('page-title') Tickets: List @endsection
@section('header')
    {{ Html::style('css/vendor_admin_datatables.min.css') }}
@endsection
@section('content')
    <!-- Start content -->
    <div class="content">
        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    <div class="card-box table-responsive">
                        <table id="tickets-table" class="table table-striped table-bordered dataTable no-footer" role="grid" aria-describedby="datatable_info">
                            <thead>
                            <tr>
                                <th>Id</th>
                                <th>Ticket</th>
                                <th>User</th>
                                <th>Used</th>
                                <th>Item</th>
                                <th>Date</th>
                                <th>Server</th>
                                <th>Giver</th>
                            </tr>
                            </thead>
                            <tfoot>
                            <tr>
                                <th>Id</th>
                                <th>Ticket</th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th>Date</th>
                                <th>Server</th>
                                <th></th>
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
    <script>
        $(document).ready(function () {
            var token = '{{ Session::token() }}';
            var oTable;
                oTable = $('#tickets-table').DataTable({
                    "order": [[ 0, "desc" ]],
                    processing: true,
                    serverSide: true,
                    autoWidth: true,
                    responsive: true,
                    ajax: '{{ route('datatables.lotteryticketsdata') }}',
                    columns: [
                        {data: 'id', name: 'id'},
                        {data: 'description', name: 'description', type: 'text'},
                        {data: 'user_id', name: 'user_id', class: 'user_id', orderable: false, searchable: false},
                        {data: 'used', name: 'used', searchable: false, class: 'used', searchable: false},
                        {data: 'item_id', name: 'item_id', class: 'item_id', orderable: false, searchable: false},
                        {data: 'created_at', name: 'created_at'},
                        {data: 'server', name: 'server'},
                        {data: 'giver', name: 'giver', class: 'giver', orderable: false, searchable: false}
                    ],
                    rowId: 'id'
                });
                $('#tickets-table tfoot th').each( function () {
                        var classNamed = $(this)[0].className;
                            if(classNamed != "user_id" && classNamed != "used" && classNamed != "item_id" && classNamed != "giver")
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

                    $('#tickets-table tbody').on('click', 'tr td:not(.user_id,.used,.item_id,.giver)', function () {
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

            });
    </script>
@endsection
