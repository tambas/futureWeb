<!-- ========== Left Sidebar Start ========== -->
<div class="left side-menu">
    <div class="sidebar-inner slimscrollleft">

        <!-- User -->
        <div class="user-box">
            <div class="user-img">
                <img src="{{ URL::asset(Auth::user()->avatar) }}" alt="avatar" title="{{ Auth::user()->pseudo }}"
                     class="img-thumbnail img-responsive">
            </div>
            <h4><a href="javascript:void(0)">{{ Auth::user()->pseudo }}</a></h4>
            <h5><a href="javascript:void(0)">{{ Auth::user()->role->label }}</a></h5>
            <ul class="list-inline">
                <li>
                    <a href="{{ route('admin.account') }}" alt="Profile" title="Profile">
                        <i class="zmdi zmdi-settings"></i>
                    </a>
                </li>

                <li>
                    <a href="{{ route('logout') }}" class="text-custom" alt="Logout" title="Logout">
                        <i class="zmdi zmdi-power"></i>
                    </a>
                </li>
            </ul>
        </div>
        <!-- End User -->

        <!--- Sidemenu -->
        <div id="sidebar-menu">
            <ul>
                <li class="text-muted menu-title">Navigation</li>

                <li>
                    <a href="{{ route('admin.dashboard') }}"
                       class="waves-effect {{ active_class(if_route('admin.dashboard'))}}"><i
                                class="fa fa-dashboard"></i> <span> Dashboard </span> </a>
                </li>

                 @if (Auth::user()->can('manage-account') || Auth::user()->can('manage-support'))
                    <li class="has_sub">
                        <a href="javascript:void(0);"
                        class="waves-effect {{ active_class(if_controller('App\Http\Controllers\Admin\AccountController'))}}"><i
                                    class="zmdi zmdi-account-o"></i> <span> Account </span> <span class="menu-arrow"></span></a>
                        <ul class="list-unstyled">
                            @can('manage-account')
                            <li class="{{ active_class(if_route('admin.account'))}}"><a href="{{ route('admin.account') }}">Profile</a>
                            </li>
                            @endcan
                            @can('manage-account')
                            <li class="{{ active_class(if_route('admin.password'))}}"><a href="{{ route('admin.password') }}">Password</a>
                            </li>
                            @endcan
                            @can('manage-support')
                            <li class="{{ active_class(if_route('admin.account.settings'))}}"><a href="{{ route('admin.account.settings') }}">Settings</a>
                            </li>
                            @endcan
                        </ul>
                    </li>
                @endif

                @can('manage-posts')
                    <li>
                    <a class="waves-effect {{ active_class(if_route('admin.posts'))}}" href="{{ route('admin.posts') }}"><i
                                    class="fa fa-pencil"></i> Posts</a></li>
                    </li>
                @endcan

                @can('manage-users')
                    <li>
                    <a class="waves-effect {{ active_class(if_route('admin.users'))}}" href="{{ route('admin.users') }}"><i
                                    class="fa fa-users"></i> Users</a></li>
                    </li>
                @endcan

                @can('manage-roles')
                    <li>
                    <a class="waves-effect {{ active_class(if_controller('App\Http\Controllers\Admin\RoleController'))}}" href="{{ route('admin.roles') }}"><i
                                    class="fa fa-key"></i> Roles</a></li>
                    </li>
                @endcan

                @if (Auth::user()->can('manage-characters') || Auth::user()->can('manage-announces'))
                    <li class="has_sub">
                        <a href="javascript:void(0);"
                        class="waves-effect {{ active_class(if_controller('App\Http\Controllers\Admin\CharacterController') ||  if_controller('App\Http\Controllers\Admin\AnnounceController')) }}"><i
                                    class="fa fa-gamepad"></i> <span> World </span> <span class="menu-arrow"></span></a>
                        <ul class="list-unstyled">
                            @can('manage-characters')
                            <li class="{{ active_class(if_route('admin.characters'))}}"><a
                                        href="{{ route('admin.characters', config('dofus.servers')[0]) }}">Characters</a></li>
                            @endcan
                            @can('manage-announces')
                            <li class="{{ active_class(if_route('admin.announces'))}}"><a
                                        href="{{ route('admin.announces', config('dofus.servers')[0]) }}">Announces</a></li>
                            @endcan
                        </ul>
                    </li>
                @endif

                @can('manage-transactions')
                    <li>
                        <a href="{{ route('admin.transactions') }}"
                        class="waves-effect {{ active_class(if_route('admin.transactions'))}}"><i class="fa fa-money"></i>
                            <span> Transactions </span> </a>
                    </li>
                @endcan

                @can('manage-lottery')
                    <li class="has_sub">
                        <a href="javascript:void(0);"
                        class="waves-effect {{ active_class(if_controller('App\Http\Controllers\Admin\LotteryController'))}}"><i
                                    class="fa fa-ticket"></i> <span> Lottery </span> <span class="menu-arrow"></span></a>
                        <ul class="list-unstyled">
                            <li class="{{ active_class(if_route('admin.lottery.tickets'))}}"><a href="{{ route('admin.lottery.tickets') }}">Tickets</a>
                            </li>
                            <li class="{{ active_class(if_route('admin.lottery'))}}"><a
                                        href="{{ route('admin.lottery') }}">Types</a></li>
                        </ul>
                    </li>
                @endcan
                
                @can('manage-support')
                    <li class="has_sub">
                        <a href="javascript:void(0);"
                        class="waves-effect {{ active_class(if_controller('App\Http\Controllers\Admin\SupportController'))}}"><i
                                    class="fa fa-comments-o"></i> <span> Support </span> <span class="menu-arrow"></span></a>
                        <ul class="list-unstyled">
                            <li class="{{ active_class(if_route('admin.support'))}}{{ active_class(if_route('admin.support.closed'))}}"><a href="{{ route('admin.support') }}">All tickets</a>
                            </li>
                            <li class="{{ active_class(if_route('admin.support.mytickets'))}}"><a
                                        href="{{ route('admin.support.mytickets') }}">My tickets</a></li>
                        </ul>
                    </li>
                @endcan

                @can('manage-tasks')
                    <li>
                        <a href="{{ route('admin.tasks') }}"
                        class="waves-effect {{ active_class(if_route('admin.tasks'))}}"><i class="fa fa-tasks"></i>
                            <span> Tasks </span> </a>
                    </li>
                @endcan

                @can('manage-settings')
                    <li>
                        <a href="{{ route('admin.settings') }}"
                        class="waves-effect {{ active_class(if_route('admin.settings'))}}"><i class="fa fa-cogs"></i>
                            <span> Settings </span> </a>
                    </li>
                @endcan

                <li class="text-muted menu-title">Links</li>
                @if(Auth::user()->isAdmin())
                    <li>
                        <a href="http://vodka.azote.us" target="_blank" class="waves-effect"><i class="fa fa-database"></i>
                            <span> Logger </span> </a>
                    </li>
                @endif
                <li>
                    <a href="{{ route('home') }}" target="_blank" class="waves-effect"><i class="fa fa-globe"></i>
                        <span> Website </span> </a>
                </li>
                <li>
                    <a href="{{ config('dofus.social.forum') }}" target="_blank" class="waves-effect"><i
                                class="fa fa-comments-o"></i> <span> Forum </span> </a>
                </li>

            </ul>
            <div class="clearfix"></div>
        </div>
        <!-- Sidebar -->
        <div class="clearfix"></div>

    </div>

</div>
<!-- Left Sidebar End -->
