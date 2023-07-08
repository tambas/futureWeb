<div class="m-b-30" style="background-color: white;border-radius: 5px;">
    <ul class="nav nav-pills nav-justified">
        <li role="presentation" class="{{ active_class(if_route('admin.support')) }}"><a href="{{ route('admin.support') }}">Open tickets</a></li>
        <li role="presentation" class="{{ active_class(if_route('admin.support.closed')) }}"><a href="{{ route('admin.support.closed') }}">Closed tickets</a></li>

    </ul>
</div>