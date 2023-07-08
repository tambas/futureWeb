@extends('layouts.admin.admin')
@section('title')
    Dashboard
@endsection
@section('page-title')
    Dashboard
@endsection
@section('content')

        <!-- ============================================================== -->
    <!-- Start right Content here -->
    <!-- ============================================================== -->
        <!-- Start content -->
        <div class="content">
            <div class="container">
                <div class="row">
                    @can('view-number-accounts')
                        @foreach(config('dofus.servers') as $k => $server)
                            <div class="@if(count(config('dofus.servers')) == 1)col-lg-12 @else col-lg-6 @endif">
                                <div class="card-box">
                                    <h4 class="header-title m-t-0 m-b-30">{{ucfirst($server)}} Accounts</h4>

                                    <div class="widget-chart-1">
                                        <div class="widget-chart-box-1">
                                            <i class="fa fa-user"></i>
                                        </div>

                                        <div class="widget-detail-1">
                                            <h2 class="p-t-10 m-b-0">{{ $count['servers'][$server] }}</h2><p class="text-muted">Accounts</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endcan
                    @can('view-number-users')
                        <div class="col-lg-4">
                            <div class="card-box">
                                <h4 class="header-title m-t-0 m-b-30">Total Users</h4>

                                <div class="widget-chart-1">
                                    <div class="widget-chart-box-1">
                                    <i class="fa fa-users"></i>
                                    </div>

                                    <div class="widget-detail-1">
                                        <h2 class="p-t-10 m-b-0">{{ $count['users'] }}</h2><p class="text-muted">Users</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endcan
                    @can('view-number-news')
                        <div class="col-lg-4">
                            <div class="card-box">
                                <h4 class="header-title m-t-0 m-b-30">Total News</h4>

                                <div class="widget-chart-1">
                                    <div class="widget-chart-box-1">
                                        <i class="fa fa-file-text"></i>
                                    </div>

                                    <div class="widget-detail-1">
                                        <h2 class="p-t-10 m-b-0">{{ $count['posts'] }}</h2><p class="text-muted">News</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endcan
                    @can('view-number-tickets')
                        <div class="col-lg-4">
                            <div class="card-box">
                                <h4 class="header-title m-t-0 m-b-30">Total Support tickets</h4>

                                <div class="widget-chart-1">
                                    <div class="widget-chart-box-1">
                                        <i class="fa fa-comments-o"></i>
                                    </div>

                                    <div class="widget-detail-1">
                                        <h2 class="p-t-10 m-b-0">{{ $count['tickets'] }}</h2><p class="text-muted">Tickets</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endcan
                </div>
                <div class="row">
                    @can('view-today-transactions')
                        <div class="col-lg-4">
                            <div class="card-box">
                                <h4 class="header-title m-t-0 m-b-30">Today Eearnings</h4>

                                <div class="widget-chart-1">
                                    <div class="widget-chart-box-1">
                                        <i class="fa fa-money"></i>
                                    </div>

                                    <div class="widget-detail-1">
                                        <h2 class="p-t-10 m-b-0">{{ $count['todayEarnings'] }}</h2><p class="text-muted">Euros</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endcan
                    @can('view-servers-status')
                        @foreach(config('dofus.servers') as $k => $server)
                            <div class="@if(count(config('dofus.servers')) == 1)col-lg-8 @else col-lg-4 @endif">
                                <div class="card-box">
                                    <h4 class="header-title m-t-0 m-b-30">{{ucfirst($server)}} Status</h4>

                                    <div class="widget-chart-1">
                                        <div class="widget-chart-box-1">
                                            <i class="fa fa-gamepad"></i>
                                        </div>

                                        <div class="widget-detail-1">
                                            <h2 class="p-t-10 m-b-0">@if($count['connectedUsers'][$server] != null){{ $count['connectedUsers'][$server] }} @else Server Offline @endif</h2><p class="text-muted">@if($count['connectedUsers'][$server] != null)Online users @endif</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endcan
                </div>
                @can('view-tickets-table')
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card-box">
                                <div class="dropdown pull-right">
                                    <a href="#" class="dropdown-toggle card-drop" data-toggle="dropdown" aria-expanded="false">
                                        <i class="zmdi zmdi-more-vert"></i>
                                    </a>
                                    <ul class="dropdown-menu" role="menu">
                                        <li><a href="{{route('admin.support')}}">See all tickets</a></li>
                                        <li><a href="{{route('admin.support.mytickets')}}">See my tickets</a></li>
                                    </ul>
                                </div>

                                <h4 class="header-title m-t-0 m-b-30">Latest Support tickets <i class="fa fa-comments-o"></i></h4>

                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Category</th>
                                            <th>User</th>
                                            <th>State</th>
                                            <th>In charge</th>
                                            <th>Open</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($newtickets as $newticket)
                                            <tr>
                                                <td><a href="{{ URL::route('admin.support.ticket.show', $newticket->id) }}">{{ $newticket->id }}</a></td>
                                                <td>{{ $newticket->category }}</td>
                                                <td>{{ $newticket->user ? $newticket->user->pseudo : '(User not found)' }}</td>
                                                <td>
                                                @if($newticket->state == \App\SupportRequest::OPEN)
                                                    <span class="label label-success">{{ Utils::support_request_status($newticket->state, 1) }}</span>
                                                @elseif($newticket->state == \App\SupportRequest::WAIT)
                                                    <span class="label label-primary">{{ Utils::support_request_status($newticket->state, 1) }}</span>
                                                @else
                                                    <span class="label label-danger">{{ Utils::support_request_status($newticket->state, 1) }}</span>
                                                @endif
                                                </td>
                                                <td>{{ $newticket->userAssigned() ? $newticket->userAssigned()->pseudo : "Not assigned" }}</td>
                                                <td>{{ $newticket->created_at->diffForHumans() }}</td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                @endcan
                @can('manage-users')
                    <div class="row">
                        <div class="col-lg-12">
                                <div class="card-box">
                                    <div class="dropdown pull-right">
                                        <a href="#" class="dropdown-toggle card-drop" data-toggle="dropdown" aria-expanded="false">
                                            <i class="zmdi zmdi-more-vert"></i>
                                        </a>
                                        <ul class="dropdown-menu" role="menu">
                                            <li><a href="{{route('admin.users')}}">See all users</a></li>
                                            <li><a href="{{route('admin.user.create')}}">Add new user</a></li>
                                        </ul>
                                    </div>

                                    <h4 class="header-title m-t-0 m-b-30">Latest Users <i class="fa fa-users"></i></h4>

                                    <div class="table-responsive">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Pseudo</th>
                                                    <th>E-mail</th>
                                                    <th>Firstname</th>
                                                    <th>Lastname</th>
                                                    <th>Status</th>
                                                    <th>Joined</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($newusers as $newuser)
                                                <tr>
                                                    <td>{{ $newuser->id }}</td>
                                                    <td><a href="{{ URL::route('admin.user.edit', $newuser->id) }}">{{ $newuser->pseudo }}</a></td>
                                                    <td>{{ $newuser->email }}</td>
                                                    <td>{{ $newuser->firstname }}</td>
                                                    <td>{{ $newuser->lastname }}</td>
                                                    <td>
                                                        @if($newuser->isActive())
                                                            <span class="label label-success">Actif</span>
                                                        @else
                                                            <span class="label label-danger">Inactif</span>
                                                        @endif
                                                    </td>
                                                    <td>{{ $newuser->created_at->diffForHumans() }}</td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                        </div>
                    </div>
                @endcan
                @can('manage-posts')
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card-box">
                                <div class="dropdown pull-right">
                                    <a href="#" class="dropdown-toggle card-drop" data-toggle="dropdown" aria-expanded="false">
                                        <i class="zmdi zmdi-more-vert"></i>
                                    </a>
                                    <ul class="dropdown-menu" role="menu">
                                        <li><a href="{{route('admin.posts')}}">See all news</a></li>
                                        <li><a href="{{route('admin.post.create')}}">Add new news</a></li>
                                    </ul>
                                </div>

                                <h4 class="header-title m-t-0 m-b-30">Latest News <i class="fa fa-pencil"></i></h4>

                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Title</th>
                                            <th>Type</th>
                                            <th>Author</th>
                                            <th>Status</th>
                                            <th>Updated</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($newposts as $newpost)
                                            <tr>
                                                <td>{{ $newpost->id }}</td>
                                                <td><a href="{{ URL::route('admin.post.edit', $newpost->id) }}">{{ $newpost->title }}</a></td>
                                                <td>{{ $newpost->type }}</td>
                                                <td>{{ $newpost->author->firstname }}</td>
                                                <td>
                                                    @if($newpost->isProgrammed())
                                                    <span class="label label-info">Programmed</span>
                                                        @endif
                                                    @if($newpost->isDraft())
                                                    <span class="label label-danger">Draft</span>
                                                        @endif
                                                    @if($newpost->isPublished())
                                                    <span class="label label-success">Published</span>
                                                        @endif
                                                </td>
                                                <td>{{ $newpost->updated_at->diffForHumans() }}</td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                @endcan
             </div> <!-- container -->
        </div> <!-- content -->
@endsection
