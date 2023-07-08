@extends('layouts.admin.admin')
@section('title') Support @endsection
@section('page-title') Support: Ticket n°{{$request->id}} @endsection
@section('header')
    {{ Html::script('tinymce/tinymce.min.js') }}
    {{ Html::script('tinymce/tinymce_editor_support.js') }}
    {!! Html::style('css/lightbox.min.css') !!}
    {!! Html::script('js/lightbox.min.js') !!}
    {{ Html::style('css/sweetalert.min.css') }}
    <style>
    .ak-support-image img
    {
        border: 2px solid #c9c6bb;
        width: 200px;
    }
    </style>
@endsection
@section('content')
        <!-- Start content -->
        <div class="content">
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <div class="m-b-30">
                            <a href="{{ route('admin.support') }}" class="btn btn-primary waves-effect waves-light btn-lg m-b-5"><i class="zmdi zmdi-long-arrow-return"></i> Return to tickets</a>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="card-box task-detail">
                            <div class="col-md-12">
                                <div class="alert @if($request->isOpen())alert-warning @else alert-danger @endif">
                                    <strong><i class="fa fa-info-circle"></i> State:</strong> {{Utils::support_request_status($request->state, true)}} 
                                </div>
                                <div class="card-box">
                                    <h4 class="header-title m-t-0 m-b-30">Ticket actions</h4>
                                    <div class="text-center buttons">
                                        {!! Form::open(['route' => ['admin.support.ticket.switch.status', $request->id], 'style' => 'display: inline;', 'method' => 'patch']) !!}
                                        @if ($request->isOpen())
                                            <button typ="submit" class="btn btn-danger waves-effect w-md m-b-5"><i class="fa fa-lock m-r-5"></i>Close</button>
                                        @else
                                            <button typ="submit" class="btn btn-primary waves-effect w-md m-b-5"><i class="fa fa-unlock m-r-5"></i>Re-open</button>
                                        @endif
                                        {!! Form::close() !!}
                                        
                                        @if($request->isOpen() && ($request->assign_to != Auth::user()->id))
                                        {!! Form::open(['route' => ['admin.support.ticket.take', $request->id, Auth::user()->id], 'style' => 'display: inline;', 'method' => 'patch']) !!}
                                            <button type="submit" class="btn btn-info waves-effect w-md m-b-5"><i class="fa fa-check m-r-5"></i> 
                                            Take in charge @if($request->userAssigned() && ($request->assign_to != Auth::user()->id))instead of {{$request->userAssigned()->pseudo}} @endif
                                            </button>
                                        {!! Form::close() !!}
                                        @endif
                                        
                                        @if($request->isOpen())
                                        <button type="button" class="btn btn-success waves-effect w-md m-b-5" data-toggle="modal" data-target="#assign-to"><i class="fa fa-user m-r-5"></i> Assign to..</button>
                                        @endif
                                    </div>
                                </div>
                         </div>
                                    <div class="media m-b-20" style="overflow: visible;">
                                        <div class="media-left">
                                            <a data-toggle="tooltip" data-placement="top" title="" data-original-title="{{ $request->user ? $request->user->pseudo : '(User not found)' }}" href="{{route('admin.user.edit', $request->user ? $request->user->id : '0' )}}"> <img class="media-object img-circle" alt="64x64" src="{{URL::asset($request->user ? $request->user->avatar : '0')}}" style="width: 48px; height: 48px;"> </a>
                                        </div>
                                        <div class="media-body">
                                            <h4 class="media-heading m-b-0">{{ $request->user ? $request->user->pseudo : '(User not found)' }}</h4>
                                            <span class="label label-success">{{$request->category}}</span>
                                        </div>
                                    </div>

                                    <h4 class="font-600 m-b-20">{{$request->subject}}</h4>

                                    <p>
                                        {!! $request->message !!}
                                    </p>

                                    <ul class="list-inline task-dates m-b-0 m-t-20">
                                        <li>
                                            <h5 class="font-600 m-b-5">Start Date</h5>
                                            <p> {{ $request->created_at->format('d F Y') }} <small class="text-muted">{{ $request->created_at->format('H\:i') }}</small></p>
                                        </li>
                                        @if(!$request->isOpen())
                                         <li>
                                            <h5 class="font-600 m-b-5">Close Date</h5>
                                            <p> {{ $request->updated_at->format('d F Y') }} <small class="text-muted">{{ $request->updated_at->format('H\:i') }}</small></p>
                                        </li>
                                        @endif
                                    </ul>
                                    <div class="clearfix"></div>
                                    <div class="assign-team m-t-30">
                                        <h5 class="font-600 m-b-5">Admin in charge:</h5>
                                        <div>
                                        @if($request->userAssigned())
                                            <a data-toggle="tooltip" data-placement="top" title="" data-original-title="{{$request->userAssigned()->pseudo}}" href="{{route('admin.user.edit', $request->userAssigned()->id)}}"> <img class="img-circle thumb-sm" alt="assign_to_avatar" src="{{URL::asset($request->userAssigned()->avatar)}}"></a>{{$request->userAssigned()->pseudo}} 
                                        @else
                                            No one in charge at the moment
                                        @endif
                                        </div>
                                    </div>
                                </div>
                     <div class="row">
                    <div class="col-md-12">
                        <div class="card-box">
                        <div class="m-b-30">
                               <h4 class="header-title m-b-30">More informations</h4>
                               <p>{!! $htmlReport !!}</p>
                            </div>
                        </div>
                    </div>
                </div>
                    </div>
                    <div class="col-md-6">
                                <div class="card-box">
                                 

                        			<h4 class="header-title m-t-0 m-b-30">Messages ({{count($messages)}})</h4>

                                    <div>
                                        @foreach($messages as $message)
                                            <div class="media m-b-10">
                                                <div class="media-left">
                                                    <img class="media-object img-circle thumb-sm" alt="avatar" src="{{URL::asset($message->author ? $message->author->avatar : '')}}">
                                                </div>
                                                <div class="media-body">
                                                    <h4 class="media-heading">{{ $message->author ? $message->author->pseudo : '(User not found)' }}<small class="text-muted"> - {{ $message->created_at->diffForHumans() }}</small></h4>
                                                    <p class="m-b-0 @if($message->isInfo()) text-inverse @endif" @if($message->isInfo()) style="font-weight:bold; font-size:12px;" @endif">
                                                       @if($message->isInfo()) INFO: @endif {!! $message->data['message'] !!}
                                                    </p>
                                                </div>
                                            </div>
                                        @endforeach
                                        
                                        <div class="media">
											<div class="media-left">
												<img data-toggle="tooltip" data-placement="top" title="" data-original-title="{{Auth::user()->pseudo}}" class="media-object img-circle thumb-sm" alt="avatar" src="{{URL::asset(Auth::user()->avatar)}}">
											</div>
                                            <div class="media-body">
                                            {!! Form::open(['route' => ['admin.support.ticket.post.message', $request->id]]) !!}
                                                 <div class="form-group {{ $errors->has('message') ? ' has-error' : '' }}">
                                                    @if($request->isOpen())
                                                    {!! Form::textarea('message', Auth::user()->preloadtext(), ['class' => 'form-control hidden', 'id' => 'message']) !!}
                                                    @else
                                                    {!! Form::textarea('message', null, ['class' => 'form-control', 'id' => 'message', 'disabled']) !!}
                                                    @endif
                                                    @if ($errors->has('message'))
                                                        <span class="help-block">
                                                            <strong>{{ $errors->first('message') }}</strong>
                                                        </span>
                                                    @endif
                                                </div>
                                                <div class="form-group {{ $errors->has('close') ? ' has-error' : '' }}">
                                                    <div class="checkbox checkbox-inline checkbox-success">
                                                        @if($request->isOpen())
                                                        {!! Form::checkbox('close',true,false, ['id' => 'close']) !!}
                                                        @else
                                                        {!! Form::checkbox('close',true,false, ['id' => 'close', 'disabled']) !!}
                                                        @endif
                                                        <label for="close">Close ticket</label>
                                                    </div>
                                                    @if($request->isOpen() && (!$request->userAssigned() || ($request->assign_to != Auth::user()->id)))
                                                    <div class="checkbox checkbox-inline checkbox-info pull-right">
                                                        {!! Form::checkbox('take',true,true, ['id' => 'take']) !!}
                                                        <label for="take">Take in charge @if($request->userAssigned() && ($request->assign_to != Auth::user()->id)) instead of {{$request->userAssigned()->pseudo}} @endif</label>
                                                    </div>
                                                    @endif
                                                </div>
                                                <div class="form-group">
                                                    @if($request->isOpen())
                                                    {!! Form::submit('Send', ['class' => 'btn btn-primary btn-block']) !!}
                                                    @else
                                                    {!! Form::submit('Send', ['class' => 'btn btn-danger btn-block', 'disabled']) !!}
                                                    @endif
                                                </div> 
                                                {!! Form::close() !!}
                                            </div>
										</div>
                                      
                                    </div>
                                </div>
                            </div>
                </div>
            </div>
        </div>

<div id="assign-to" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h4 class="modal-title">Ticket n°{{$request->id}}: Assign to admin</h4>
                    </div>
                    {!! Form::open(['route' => ['admin.support.ticket.assignto', $request->id], 'id' => 'form-assign-to']) !!}
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group {{ $errors->has('assign-to') ? ' has-error' : '' }}">
                                    <label for="assign-to">Assign to:</label>
                                    {!! Form::select('adminid', $admins, null,['class' => 'form-control', 'id' => 'assign-to']) !!}
                                    @if ($errors->has('assign-to'))
                                        <span class="help-block">
                                                <strong>{{ $errors->first('assign-to') }}</strong>
                                            </span>
                                    @endif
                                </div> 
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-info waves-effect waves-light">Assign</button>
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
                    $('[data-toggle="tooltip"]').tooltip();

                    if($("#message").length > 0){
                        editor_support_config.selector = "textarea#message";
                        editor_support_config.path_absolute = '{{ url('') }}';
                        editor_support_config.templates = {!!json_encode(Auth::user()->personnal_templates())!!};
                        $("#message").removeClass('hidden');
                        tinymce.init(editor_support_config);
                    }

                   $( "#form-assign-to" ).on( "submit", function( event ) {
                        $("#form-assign-to button[type='submit']").prop("disabled", true);
                       event.preventDefault();
                       var $this = $(this);
                       var datas = $this.serialize();

                       $.ajax({
                           method: 'PATCH',
                           url: $this.attr("action"),
                           data: datas,

                           success: function (user) {
                               $('#assign-to').modal('hide');
                               toastr.success('Ticket assigned to '+user);
                               setTimeout(function(){ location.reload(); }, 700);
                           },

                           error: function (jqXhr, json, errorThrown) {
                               $("#form-assign-to button[type='submit']").prop("disabled", false);
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