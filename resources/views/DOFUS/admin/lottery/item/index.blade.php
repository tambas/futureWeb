@extends('layouts.admin.admin')
@section('title') Lottery Items @endsection
@section('page-title') Lottery: Items @endsection
@section('header')
    {{ Html::style('css/sweetalert.min.css') }}
    {{ Html::style('css/jquery.bootstrap-touchspin.min.css') }}
    <style>
        .loader {
            display: inline-block;
            border: 10px solid #f3f3f3; /* Light grey */
            border-top: 10px solid #3498db; /* Blue */
            border-radius: 50%;
            width: 50px;
            height: 50px;
            animation: spin 2s linear infinite;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
@endsection
@section('content')
        <!-- Start content -->
<div class="content">
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <div class="m-b-30">
                    <a href="{{route('admin.lottery')}}" class="btn btn-primary waves-effect waves-light btn-lg m-b-5"><i class="zmdi zmdi-long-arrow-return"></i> Return to lottery</a>
                </div>
                <div class="card-box">
                    <h4 class="header-title m-b-30">{{$type->name}} - Items</h4>
                    <div class="m-b-30">
                        <button type="button" data-toggle="modal" data-target="#add-item-modal" class="btn btn-primary waves-effect waves-light btn-lg m-b-5" href="#"><i class="fa fa-plus"></i> Add item</button>
                    </div>
                    @foreach (config('dofus.servers') as $server)
                        <h2>{{ ucfirst($server) }}</h2>
                        @if (count($type->objects($server)) > 0)
                        <table class="table table-striped" id="tickets-table">
                            <thead>
                            <tr>
                                <th>Id</th>
                                <th>Icon</th>
                                <th>Name</th>
                                <th>Perfect</th>
                                <th>Percentage</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($type->objects($server) as $object)
                            @if ($object->item($server))
                                <tr id="{{ $object->id }}">
                                    <td>{{ $object->item_id }}</td>
                                    <td class="image"><img src="{{ $object->item($server)->image() }}" alt="{{ $object->item($server)->name($server) }}" width="70"></td>
                                    <td class="name">{{ $object->item($server)->name($server) }}</td>
                                    <td class="perfect" data-value="{{$object->max}}">@if ($object->max) <span class="label label-success">Yes</span> @else <span class="label label-danger">No</span> @endif</td>
                                    <td class="percentage">{{ $object->percentage }}</td>
                                    <td>
                                        <a href="javascript:void(0)" data-id="{{ $object->id }}" class="edit btn btn-xs btn-default" data-toggle="tooltip" title="Edit"><i class="fa fa-pencil"></i></a>
                                        <a href="javascript:void(0)" data-id="{{ $object->id }}" class="delete btn btn-xs btn-danger" data-toggle="tooltip" title="Delete"><i class="fa fa-trash"></i></a>
                                    </td>
                                </tr>
                            @endif
                            @endforeach
                            </tbody>
                        </table>
                        @else
                            <div class="alert alert-info">
                                This lottery doesn't have any objects.
                            </div>
                        @endif
                        <hr>
                    @endforeach
                </div>
            </div>
        </div>
        </div>
    </div>
        <div id="add-item-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h4 class="modal-title">{{ $type->name }}: Add item</h4>
                    </div>
                    <div class="modal-body">
                        {!! Form::open(['route' => ['admin.lottery.item.store', $type->id], 'id' => 'form-additem']) !!}
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group {{ $errors->has('server') ? ' has-error' : '' }}">
                                    <label for="server">Server:</label>
                                    {!! Form::select('server', $servers, null, ['class' => 'form-control', 'id' => 'server']) !!}
                                    @if ($errors->has('server'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('server') }}</strong>
                                        </span>
                                    @endif
                                </div>
                                <div class="form-group {{ $errors->has('item') ? ' has-error' : '' }}">
                                    <label for="item">Item Id:</label>
                                    {!! Form::text('item', null,['class' => 'form-control', 'id' => 'item']) !!}
                                    @if ($errors->has('item'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('item') }}</strong>
                                        </span>
                                    @endif
                                </div>
                                <div style="text-align:center;">
                                    <div id="item_infos" class="hidden" style="margin-bottom: 15px;"></div>
                                    <div class="hidden loader"></div>
                                </div>
                                <div class="form-group {{ $errors->has('percentage') ? ' has-error' : '' }}">
                                    <label for="percentage">Percentage:</label>
                                    {!! Form::text('percentage', null,['class' => 'form-control', 'id' => 'percentage']) !!}
                                    @if ($errors->has('percentage'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('percentage') }}</strong>
                                        </span>
                                    @endif
                                </div>
                                <div class="form-group {{ $errors->has('max') ? ' has-error' : '' }}">
                                    <div class="checkbox checkbox-inline checkbox-success">
                                        {!! Form::checkbox('max', '1', null, ['class' => '', 'id' => 'max']) !!}
                                        <label for="max">Perfect</label>
                                        @if ($errors->has('max'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('max') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-info waves-effect waves-light" id="item_submit_button">Add</button>
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
        <div id="edit-item-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h4 class="modal-title"></h4>
                    </div>
                    <div class="modal-body">
                        {!! Form::open(['id' => 'formulaire-edit-item']) !!}
                        <div class="row">
                            <div class="col-sm-12">
                                <div style="text-align:center;">
                                    <div id="item_edit_infos" style="margin-bottom: 15px;"></div>
                                </div>
                                <div class="form-group {{ $errors->has('percentage') ? ' has-error' : '' }}">
                                    <label for="percentage">Percentage:</label>
                                    {!! Form::text('percentage', null,['class' => 'form-control', 'id' => 'percentage_edit']) !!}
                                    @if ($errors->has('percentage'))
                                        <span class="help-block">
                                                <strong>{{ $errors->first('percentage') }}</strong>
                                            </span>
                                    @endif
                                </div>
                                <div class="form-group {{ $errors->has('max') ? ' has-error' : '' }}">
                                   <div class="checkbox checkbox-inline checkbox-success">
                                        {!! Form::checkbox('max', true, false, ['id' => 'max_edit']) !!}
                                        <label for="max_edit">Perfect</label>
                                        @if ($errors->has('max'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('max') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-info waves-effect waves-light" id="item_edit_submit_button">Edit</button>
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    @endsection
@section('bottom')
    {!! Html::script('js/admin/sweetalert.min.js') !!}
    {!! Html::script('js/admin/jquery.bootstrap-touchspin.min.js') !!}
    <script>
        $(document).ready(function () {
            var token = '{{ Session::token() }}';
            var typingTimer;
            var doneTypingInterval = 1000;
            // Touchspin initialize
            $("input[name='percentage']").TouchSpin({
                min: 1,
                max: 100,
                step: 1,
                decimals: 0,
                boostat: 5,
                maxboostedstep: 10,
                buttondown_class: "btn btn-success",
                buttonup_class: "btn btn-primary",
                postfix: 'Percent'
            });

            $('#item').keyup(function() {
                var self = $(this);
                clearTimeout(typingTimer);
                if ($('#item').val()) {
                    typingTimer = setTimeout(function() {
                        loadItem(self);
                    }, doneTypingInterval);
                }
            });

            loadItem = function(itemInput) {
                $this = itemInput;
                var validItemId = new RegExp('^[0-9]+$');
                var item_id = $this.val();
                var item_infos = $('#item_infos');
                if(validItemId.test(item_id))
                {
                    var server = $('#server').val();
                    var route_base = '{{ route('admin.lottery.items', $type->id) }}';
                    var route = ''+route_base+'/'+server+'/'+item_id+'/search';
                    $.ajax({
                        url: route,
                        method: "GET",
                        beforeSend: function() {
                            item_infos.addClass('hidden');
                            $('.loader').removeClass('hidden');
                        },
                        complete: function(){
                            $('.loader').addClass('hidden');
                        },
                        success: function(data)
                        {
                            var parse = JSON.parse(data);
                            var image = '<img src="'+parse.image+'" alt="'+parse.name+'" width="70"><br>'+parse.name;
                            item_infos.removeClass('hidden').html(image);
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
                                errorsHtml = 'Unknown error';
                            }
                            var app = '<span class="help-block" style="color:red;"><strong>'+errorsHtml+'</strong></span>';
                            item_infos.removeClass('hidden').html(app);
                        }
                    });
                }
                else if(item_id == '')
                {
                    item_infos.addClass('hidden').html('');
                }
                else
                {
                    var app = '<span class="help-block" style="color:red;"><strong><li>L\'Id est invalide</li></strong></span>';
                    item_infos.removeClass('hidden').html(app);
                }
            }

            // Add ticket to user
            $( "#form-additem" ).on( "submit", function( event ) {
                $('#item_submit_button').prop('disabled', true);
                event.preventDefault();
                var $this = $(this);
                var datas = $this.serialize();

                $.ajax({
                    method: 'POST',
                    url: $this.attr("action"),
                    data: datas,

                    success: function (msg) {
                        $('#add-item-modal').modal('hide');
                        toastr.success('Item added!');
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
                            errorsHtml = 'Unknown error';
                        }
                        toastr.error(errorsHtml);
                        $('#item_submit_button').prop('disabled', false);
                    }
                });
            });

            $('#tickets-table tbody').on('click', 'tr .delete', function () {
                // Find ID of the item
                var id = $(this).data('id');

                // Some variables
                var route_base = '{{ route('admin.lottery.items', $type->id) }}';
                swal({
                    title: "Are you sure?",
                    text: "You will not be able to recover this item!",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Yes, delete it!",
                    closeOnConfirm: false }, function(){

                    $.ajax({
                        method: 'DELETE',
                        url: ''+route_base+'/'+id+'',
                        data: { _token: token },

                        success: function (msg) {
                            swal("Deleted!", "This item has been deleted.", "success");
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

            $('#tickets-table tbody').on('click', 'tr .edit', function () {
                $('#edit-item-modal').modal();
                var id = $(this).data('id');
                var percentage = $('tr#'+id+' td.percentage').html();
                var image = $('tr#'+id+' td.image').html();
                var name = $('tr#'+id+' td.name').html();
                var max = $('tr#'+id+' td.perfect').data('value');
                $('#edit-item-modal .modal-title').html(''+name+': Edit item');
                $('#edit-item-modal #item_edit_infos').html(image);
                $('#edit-item-modal input#percentage_edit').val(percentage);
                document.getElementById("max_edit").checked = max;
                $('#edit-item-modal').data('id', id);
            });

            $( "#formulaire-edit-item" ).on( "submit", function( event ) {
                $('#item_edit_submit_button').prop('disabled', true);
                event.preventDefault();
                var $this = $(this);
                var datas = $this.serialize();
                var route_base = '{{ route('admin.lottery.items', $type->id) }}';
                var id = $('#edit-item-modal').data('id');

                $.ajax({
                    method: 'PATCH',
                    url: ''+route_base+'/'+id+'',
                    data: datas,

                    success: function (msg) {
                        $('#edit-item-modal').modal('hide');
                        toastr.success('Item Edited!');
                        $('#item_edit_submit_button').prop('disabled', false);
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
                            errorsHtml = 'Unknown error';
                        }
                        toastr.error(errorsHtml);
                        $('#item_edit_submit_button').prop('disabled', false);
                    }
                });
            });
        });
    </script>
@endsection
