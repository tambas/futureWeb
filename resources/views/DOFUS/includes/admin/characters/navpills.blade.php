<div class="m-b-30" style="background-color: white;border-radius: 5px;">
    <ul class="nav nav-pills nav-justified">
    @foreach(config('dofus.servers') as $k => $server)
        <li role="presentation" class="{{ active_class(if_route_param('server', $server))}}"><a href="{{ route('admin.characters', $server) }}">Characters {{ucfirst($server)}}</a></li>
    @endforeach
    </ul>
</div>