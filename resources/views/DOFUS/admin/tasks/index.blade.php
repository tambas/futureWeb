@extends('layouts.admin.admin')
@section('title') Tasks @endsection
@section('page-title') Tasks @endsection
@section('header')
    {{ Html::style('css/vendor_admin_datatables.min.css') }}
    {{ Html::style('css/sweetalert.min.css') }}
    {{ Html::style('css/dragula.min.css') }}
    {{ Html::style('css/pick-a-color.min.css') }}
@endsection
@section('content')
<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-4">
                <div class="text-left m-b-30">
                    <a href="javascript:void(0)" class="btn btn-primary waves-effect waves-light btn-lg" data-toggle="modal" data-target="#task-add-modal">
                        <i class="zmdi zmdi-plus"></i> Add New
                    </a>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <div class="card-box taskboard-box">
                    <h4 class="header-title m-t-0 m-b-30 text-primary">To-do</h4>
                    <p class="small"><i class="fa fa-hand-o-up"></i> Drag task between lists</p>
                    <ul class="list-unstyled task-list sortable-list" id="to_do">
                        @foreach($tasks as $task)
                            @if($task->status == 'to_do')
                                <li id="{!! $task['id'] !!}" name="{!!$task['task']!!}" style="border-left: 3px solid {!!$task['color']!!};" color="{!!$task['color']!!}" order="{!! $task['status_order'] !!}">
                                    <div class="card-box kanban-box">
                                        <div class="kanban-detail">
                                            <span class="label label-danger pull-right">Urgent</span>
                                            <h4>
                                                <a @if($task['description']) href="javascript:void(0)" title="Description" class="description_button"@endif id="opendescri-{!! $task['id'] !!}" >{{ $task->task }}</a>
                                                <textarea id="description-{!! $task['id'] !!}" class="hidden">{!! $task['description'] !!}</textarea>
                                            </h4>
                                            <ul class="list-inline m-b-0">
                                                <li>
                                                    {{ Html::image(Auth::user()->avatar, 'avatar', ['class' => 'thumb-sm img-circle']) }}
                                                </li>
                                                <li>
                                                    <a href="javascript:void(0)" id="edit-{{ $task->id }}" class="edit_button" data-toggle="tooltip" data-placement="top" title="" data-original-title="Edit">
                                                        <i class="fa fa-pencil"></i>
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="javascript:void(0)" id="delete-{{ $task->id }}" class="delete_button" data-toggle="tooltip" data-placement="top" title="" data-original-title="Delete">
                                                        <i class="fa fa-trash"></i>
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </li>
                            @endif
                        @endforeach
                    </ul>
                </div>
            </div><!-- end col -->
            <div class="col-md-4">
                <div class="card-box taskboard-box">
                    <h4 class="header-title m-t-0 m-b-30 text-warning">In Progress</h4>
                    <p class="small"><i class="fa fa-hand-o-up"></i> Drag task between lists</p>
                    <ul class="list-unstyled task-list sortable-list connectedSortable agile-list ui-sortable" id="in_progress">
                        @foreach($tasks as $task)
                            @if($task->status == 'in_progress')
                                <li id="{!! $task['id'] !!}" name="{!!$task['task']!!}" style="border-left: 3px solid {!!$task['color']!!};" color="{!!$task['color']!!}" order="{!! $task['status_order'] !!}">
                                    <div class="card-box kanban-box">
                                        <div class="kanban-detail">
                                            <span class="label label-danger pull-right">Urgent</span>
                                            <h4>
                                                <a @if($task['description']) href="javascript:void(0)" title="Description" class="description_button"@endif id="opendescri-{!! $task['id'] !!}" >{{ $task->task }}</a>
                                                <textarea id="description-{!! $task['id'] !!}" class="hidden">{!! $task['description'] !!}</textarea>
                                            </h4>
                                            <ul class="list-inline m-b-0">
                                                <li>
                                                    {{ Html::image(Auth::user()->avatar, 'avatar', ['class' => 'thumb-sm img-circle']) }}
                                                </li>
                                                <li>
                                                    <a href="javascript:void(0)" id="edit-{{ $task->id }}" class="edit_button" data-toggle="tooltip" data-placement="top" title="" data-original-title="Edit">
                                                        <i class="fa fa-pencil"></i>
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="javascript:void(0)" id="delete-{{ $task->id }}" class="delete_button" data-toggle="tooltip" data-placement="top" title="" data-original-title="Delete">
                                                        <i class="fa fa-trash"></i>
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </li>
                            @endif
                        @endforeach
                    </ul>
                </div>
            </div><!-- end col -->
            <div class="col-md-4">
                <div class="card-box taskboard-box">
                    <h4 class="header-title m-t-0 m-b-30 text-success">Complete</h4>
                    <p class="small"><i class="fa fa-hand-o-up"></i> Drag task between lists</p>
                    <ul class="list-unstyled task-list sortable-list connectedSortable agile-list ui-sortable" id="complete">
                        @foreach($tasks as $task)
                            @if($task->status == 'complete')
                                <li id="{!! $task['id'] !!}" name="{!!$task['task']!!}" style="border-left: 3px solid {!!$task['color']!!};" color="{!!$task['color']!!}" order="{!! $task['status_order'] !!}">
                                    <div class="card-box kanban-box">
                                        <div class="kanban-detail">
                                            <span class="label label-danger pull-right">Urgent</span>
                                            <h4>
                                                <a @if($task['description']) href="javascript:void(0)" title="Description" class="description_button"@endif id="opendescri-{!! $task['id'] !!}" >{{ $task->task }}</a>
                                                <textarea id="description-{!! $task['id'] !!}" class="hidden">{!! $task['description'] !!}</textarea>
                                            </h4>
                                            <ul class="list-inline m-b-0">
                                                <li>
                                                    {{ Html::image(Auth::user()->avatar, 'avatar', ['class' => 'thumb-sm img-circle']) }}
                                                </li>
                                                <li>
                                                    <a href="javascript:void(0)" id="edit-{{ $task->id }}" class="edit_button" data-toggle="tooltip" data-placement="top" title="" data-original-title="Edit">
                                                        <i class="fa fa-pencil"></i>
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="javascript:void(0)" id="delete-{{ $task->id }}" class="delete_button" data-toggle="tooltip" data-placement="top" title="" data-original-title="Delete">
                                                        <i class="fa fa-trash"></i>
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </li>
                            @endif
                        @endforeach
                    </ul>
                </div>
            </div><!-- end col -->

        </div>
        <!-- end row -->
    </div> <!-- container -->
</div>
<div id="task-add-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title">Add a new Task</h4>
            </div>
            <div class="modal-body">

                {!! Form::open([ 'route' => 'admin.task.store', 'id' => 'form-add-task']) !!}
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="task" class="control-label">Task</label>
                            <input type="text" class="form-control" id="task" name="task">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="status" class="control-label">Status</label>
                            <select class="form-control" id="status" name="status">
                                <option value="to_do">To-do</option>
                                <option value="in_progress">In Progress</option>
                                <option value="complete">Complete</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group no-margin">
                            <label for="description" class="control-label">Description</label>
                            <textarea class="form-control autogrow" name="description" id="description"></textarea>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group no-margin">
                            <label for="color" class="control-label">Color</label>
                            <input type="text" value="000" id="color" name="color" id="task-color" class="pick-a-color form-control">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                <button id="button-task-add" type="submit" class="btn btn-info waves-effect waves-light">Create Task</button>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>
<div id="task-edit-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            {!! Form::open([ 'route' => 'admin.task.update.modal', 'id' => 'form-edit-task']) !!}
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <label for="task-name-modal" class="control-label">Task</label>
                <input class="modal-title form-control" id="task-name" name="task-name">
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group no-margin">
                            <label for="description" class="control-label">Description</label>
                            <textarea class="form-control autogrow"  name="task-description" id="task-description"></textarea>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group no-margin">
                            <label for="task-color" class="control-label">Color</label>
                            <input type="text" value="000" name="task-color" id="task-color" id="task-color" class="pick-a-color form-control">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-info waves-effect waves-light">Update</button>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>
<div id="task-description-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title" id="task_task"></h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <p id="description_task"></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
    @section('bottom')
        {{ Html::script('js/admin/dragula.min.js') }}
        {{ Html::script('js/admin/pick-a-color.min.js') }}
        <script>
            // Global variable
            var token = '{{ Session::token() }}';

            // SORTABLE INITIALIZE
            dragula([document.querySelector('#to_do'), document.querySelector('#in_progress'), document.querySelector('#complete')], {
                isContainer: function (el) {
                    return false; // only elements in drake.containers will be taken into account
                },
                moves: function (el, source, handle, sibling) {
                    return true; // elements are always draggable by default
                },
                accepts: function (el, target, source, sibling) {
                    return true; // elements can be dropped in any of the `containers` by default
                },
                invalid: function (el, handle) {
                    return false; // don't prevent any drags from initiating by default
                },
                direction: 'vertical',             // Y axis is considered when determining where an element would be dropped
                copy: false,                       // elements are moved by default, not copied
                copySortSource: false,             // elements in copy-source containers can be reordered
                revertOnSpill: false,              // spilling will put the element back where it was dragged from, if this is true
                removeOnSpill: false,              // spilling will `.remove` the element, if this is true
                mirrorContainer: document.body,    // set the element that gets mirror elements appended
                ignoreInputTextSelection: true     // allows users to select input text, see details below
            });
                $( "#to_do, #in_progress, #complete" ).sortable({
                    connectWith: ".sortable-list"
                });
            // Pick a color initialize
            $(".pick-a-color").pickAColor({
                showSpectrum            : true,
                showAdvanced            : false,
                showSavedColors         : true,
                saveColorsPerElement    : false,
                fadeMenuToggle          : true,
                showHexInput            : true,
                showBasicColors         : true,
                allowBlank              : false,
                inlineDropdown          : false,
                basicColors           : {
                    red       : 'f00',
                    orange    : 'f60',
                    yellow    : 'ff0',
                    green     : '008000',
                    blue      : '00f',
                    purple    : '800080',
                    black     : '000'
                }
            });

            // ONE ELEMENT HAS BEEN DROPPED INTO ANOTHER LIST
            $( ".sortable-list" ).on( "sortreceive", function( event, ui ) {
                var status = this.id;
                var id = ui.item.attr('id');
                var url_base = '{{ route('admin.tasks') }}';
                var url_update = ''+url_base+'/'+id+'';
                $.ajax( {
                    url: url_update,
                    type: 'PATCH',
                    dataType: "json",
                    data: {'status': status, '_token': token},
                    success:function(data, textStatus, jqXHR)
                    {
                        toastr.success('Task updated');
                    },
                    error: function(jqXhr, json, errorThrown)
                    {
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
            } );
            // A LIST ORDER CHANGED
            $( ".sortable-list" ).on( "sortupdate", function( event, ui ) {
                //var elementId = this.id;
                // Normalement on en a pas besoin
                // pas besoin normalement géré par sortreceive
                var order = $(this).sortable('toArray');
                var url_update_positions = '{{ route('admin.task.update.positions') }}';
                $.ajax( {
                    url: url_update_positions,
                    type: 'PATCH',
                    dataType: "json",
                    data: {'positions': order, '_token': token}
                });
            } );
            // Edit Task (Open Modal)
            $("body").on("click", ".edit_button", function(e) {
                e.preventDefault();
                var clickedID = this.id;
                var taskId = clickedID.replace("edit-", "");


                var taskName = $('#'+taskId).attr('name');
                var taskDescription = $('#description-'+taskId).val();

                var color_split = $('#'+taskId).attr('color').split('#');
                var color = color_split[1];

                // On importe le nom et la description
                $('#task-edit-modal').find('input[name="task-name"]').attr('value', taskName);
                $('#task-description').val(taskDescription);

                // On importe la couleur et ajoute la preview
                $('#task-edit-modal').find('input[name="task-color"]').val(color);
                $('#task-edit-modal').find(".input-group-btn").find(".current-color").css("background-color",
                        "#" + color);
                $('#task-edit-modal').attr('data-taskid', taskId);
                $('#task-edit-modal').modal();

            });
            // Edit Task (Save and update view: ajax)
            $( "#form-edit-task" ).on( "submit", function( event ) {
                event.preventDefault();
                var $this = $(this);
                var taskId = $('#task-edit-modal').attr('data-taskid');
                var datas = $this.serialize() + "&taskid=" + taskId;

                $.ajax({
                    method: 'PATCH',
                    url: $this.attr("action"),
                    data: datas,

                    success: function (msg) {

                        var new_description = msg['new_description'];
                        var new_color = msg['new_color'];
                        var old_description = msg['old_description']; // Booléan
                        var new_name = msg['new_name'];

                        $('#'+taskId).attr('color', new_color); // attribut color
                        $('#'+taskId).attr('style', 'border-left: 3px solid '+ new_color); // Style color dans le <li>

                        $('#'+taskId).find('h4 a').text(new_name);
                        $('#'+taskId).attr('name', new_name);

                        if(old_description === true && new_description != '')
                        {
                            $('#description-'+taskId).val(new_description);
                        }
                        if(old_description === true && new_description == '')
                        {
                            $('#'+taskId).find('h4 a').removeClass('description_button');
                            $('#'+taskId).find('h4 a').removeAttr('title');
                            $('#'+taskId).find('h4 a').removeAttr('href');
                            $('#'+taskId).find('h4 textarea').text('');
                            $('#description-'+taskId).val('');
                        }
                        if((old_description == false) && new_description != '')
                        {
                            $('#description-'+taskId).val(new_description);
                            $('#'+taskId).find('h4 a').addClass('description_button');
                            $('#'+taskId).find('h4 a').attr('title', 'Description');
                            $('#'+taskId).find('h4 a').attr('href', 'javascript:void(0)');
                        }
                        toastr.success('Task edited');
                        $('#task-edit-modal').modal('hide');
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
            // Open description (Open Modal)
            $("body").on("click", ".description_button", function(e) {
                e.preventDefault();
                var clickedID = this.id;
                var id = clickedID.replace("opendescri-", "");
                var description = $('#description-'+id).val();
                var name = $('#'+id).attr('name');

                $('#task-description-modal').find('#task_task').text(name);
                $('#task-description-modal').find('#description_task').text(description);
                $('#task-description-modal').modal();
            });
            // Delete Task (Ajax)
            $("body").on("click", ".delete_button", function(e) {
                e.preventDefault();
                var clickedID = this.id;
                var id = clickedID.replace("delete-", "");

                var url_base = '{{ route('admin.tasks') }}';
                var url_delete = ''+url_base+'/'+id+'';

                $(this).fadeOut();

                $.ajax({
                    type: "DELETE",
                    url: url_delete,
                    dataType: "json",
                    data: { _token: token},
                    success:function(msg)
                    {
                        $('#'+id).fadeOut();
                    },
                    error:function (jqXhr, json, errorThrown){
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
            // Create Task (Modal) and ajax html update
            $( "#form-add-task" ).on( "submit", function( event ) {
                event.preventDefault();
                var $this = $(this);
                $.ajax({
                    method: 'POST',
                    url: $this.attr("action"),
                    data: $this.serialize(),

                    success: function (msg) {
                        toastr.success('Adding task..');
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
                    }
                });


            });
        </script>
    @endsection
