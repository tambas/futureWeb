@if (request()->server->get('HTTP_HOST') == config('dofus.domain.main'))
@include('errors.403_main')
@else
@include('errors.403_fake')
@endif
