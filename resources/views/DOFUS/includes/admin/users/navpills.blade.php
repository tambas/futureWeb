<div class="m-b-30">
    <a href="{{ route('admin.users') }}" class="btn btn-primary waves-effect waves-light btn-lg m-b-5"><i class="zmdi zmdi-long-arrow-return"></i> Return to users</a>
</div>
<div class="m-b-30" style="background-color: white;border-radius: 5px;">
    <ul class="nav nav-pills nav-justified">
        <li role="presentation" class="{{ active_class(if_route('admin.user.edit',$user->id))}}"><a href="{{ route('admin.user.edit', $user->id) }}">Web Account</a></li>

        @foreach(config('dofus.servers') as $server)
            <li role="presentation" class="{{ active_class(if_route_param('server', $server))}}"><a href="{{ route('admin.user.game.accounts',[$user->id, $server]) }}">{{ ucfirst($server) }} Accounts</a></li>
        @endforeach
    </ul>
</div>