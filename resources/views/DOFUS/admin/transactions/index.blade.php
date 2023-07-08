@extends('layouts.admin.admin')
@section('title') List transactions @endsection
@section('page-title') Transactions: List @endsection
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
                    <div class="card-box table-responsive">
                        <table id="transactions-table" class="table table-striped table-bordered dataTable" role="grid" aria-describedby="datatable_info">
                            <thead>
                            <tr>
                                <th>Id</th>
                                <th>User</th>
                                <th>Provider</th>
                                <th>State</th>
                                <th>Code</th>
                                <th>Points</th>
                                <th>Date</th>
                            </tr>
                            </thead>
                            <tfoot>
                            <tr>
                                <th>Id</th>
                                <th></th>
                                <th>Provider</th>
                                <th></th>
                                <th>Code</th>
                                <th>Points</th>
                                <th>Date</th>
                            </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <div class="card-box">
                    <h4 class="header-title m-t-0">Last 30 days: Earnings</h4>
                        <canvas id="earningGraph"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('bottom')
    {!! Html::script('js/admin/vendor_admin_datatables.min.js') !!}
    {!! Html::script('https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.3.0/Chart.bundle.min.js') !!}
    {!! Html::script('https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.3.0/Chart.min.js') !!}
    <script>
        $(document).ready(function () {
            Chart.defaults.global.scaleLabel = true;

            var token = '{{ Session::token() }}';
            var ctx = document.getElementById("myChart");
            $.ajax({
                url: '{{ route('admin.transactions.getdata') }}',
                method: "GET",
                success: function(data) {
                    var days = [];
                    var earnings = [];
                    var json_data = JSON.parse(data);
                    for(var i in json_data) {
                        days.push(json_data[i].day);
                        earnings.push(json_data[i].earn);
                    }
                    var chartdata = {
                        labels: days,
                        datasets : [
                            {
                                fillColor: "rgba(220,220,220,0)",
                                strokeColor: "rgba(220,180,0,1)",
                                pointColor: "rgba(220,180,0,1)",
                                label: 'Earnings',
                                fill: false,
                                lineTension: 0.1,
                                backgroundColor: "rgba(255, 255, 255, 0)",
                                borderColor: "rgba(113, 182, 249, 1)",
                                borderCapStyle: 'butt',
                                borderDash: [],
                                borderDashOffset: 0.0,
                                borderJoinStyle: 'miter',
                                pointBorderColor: "rgba(113, 182, 249, 1)",
                                pointBackgroundColor: "#fff",
                                pointBorderWidth: 1,
                                pointHoverRadius: 5,
                                pointHoverBackgroundColor: "rgba(113, 182, 249, 1)",
                                pointHoverBorderColor: "rgba(0, 0, 0, 1)",
                                pointHoverBorderWidth: 2,
                                pointRadius: 1,
                                pointHitRadius: 10,
                                spanGaps: false,
                                data: earnings
                            }
                        ]
                    };

                    var ctx = $("#earningGraph");

                    var earnGraph = new Chart(ctx, {
                        type: 'line',
                        data: chartdata,
                        options: {
                            tooltips: {
                                enabled: true,
                                mode: 'single',
                                callbacks: {
                                    label: function(tooltipItems, data) {
                                        return tooltipItems.yLabel + ' €';
                                    }
                                }
                            },
                        }
                    });

                },
                error: function(data) {
                    console.log(data);
                }
            });

            var oTable;
                oTable = $('#transactions-table').DataTable({
                    "order": [[ 0, "desc" ]],
                    processing: true,
                    serverSide: true,
                    autoWidth: true,
                    responsive: true,
                    ajax: '{{ route('datatables.transactionsdata') }}',
                    columns: [
                        {data: 'id', name: 'id'},
                        {data: 'user_id', name: 'user_id', class: 'user_id', orderable: false, searchable: false},
                        {data: 'provider', name: 'provider'},
                        {data: 'state', name: 'state', class: 'state text-center', searchable: false},
                        {data: 'code', name: 'code'},
                        {data: 'points', name: 'points'},
                        {data: 'created_at', name: 'created_at'}
                    ],
                    rowId: 'id'
                });

                 $('#transactions-table tfoot th').each( function () {
                        var classNamed = $(this)[0].className;
                            if(classNamed != "user_id" && classNamed != "state text-center")
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

                    $('#transactions-table tbody').on('click', 'tr td:not(.user_id,.state)', function () {
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
