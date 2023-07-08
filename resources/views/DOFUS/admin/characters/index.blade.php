@extends('layouts.admin.admin')
@section('title') List characters @endsection
@section('page-title') Characters: List @endsection
@section('header')
    {{ Html::style('css/vendor_admin_datatables.min.css') }}
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
                        @include('includes.admin.characters.navpills')
                    <div class="card-box card-tabs table-responsive">
                        <div class="tab-content">
                            {{ Html::image('imgs/admin/'.$server.'.png', $server, ['class' => 'center-block m-b-30']) }}
                                 <table id="characters-table" class="table table-striped table-bordered dataTable" role="grid" aria-describedby="datatable_info">
                                        <thead>
                                        <tr>
                                            <th>Id</th>
                                            <th>Name</th>
                                            <th>Class</th>
                                            <th>Level</th>
                                            <th>Status</th>
                                            <th>Game account</th>
                                        </tr>
                                        </thead>
                                        <tfoot>
                                        <tr>
                                            <th>Id</th>
                                            <th>Name</th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                        </tr>
                                        </tfoot>
                                </table>
                        </div>
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
            oTable = $('#characters-table').on( 'init.dt', function () {
                        $('[data-toggle="tooltip"]').tooltip()
                    } ).DataTable({
                        "order": [[ 0, "desc" ]],
                        processing: true,
                        serverSide: true,
                        autoWidth: true,
                        responsive: true,
                        ajax: '{{ route('datatables.charactersdata', $server) }}',
                        columns: [
                            {data: 'Id', name: 'Id'},
                            {data: 'Name', name: 'Name'},
                            {data: 'Breed', name: 'Breed', type: 'text', class: 'Breed', orderable: false, searchable: false},
                            {data: 'Experience', name: 'Experience', class: 'Experience', orderable: false, searchable: false},
                            {data: 'Status', name: 'Status', class: 'Status', orderable: false, searchable: false},
                            {data: 'GameAccount', name: 'GameAccount', class: 'GameAccount', orderable: false, searchable: false}
                        ],
                        rowId: 'Id'
                    });

                    $('#characters-table tfoot th').each( function () {
                        var classNamed = $(this)[0].className;
                            if(classNamed != "Breed" && classNamed != "Experience" && classNamed != "Status" && classNamed != "GameAccount")
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

                    $('#characters-table tbody').on('click', 'tr td:not(.Breed,.Experience,.Status,.GameAccount)', function () {
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
