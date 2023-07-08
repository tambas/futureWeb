@if (request()->server->get('HTTP_HOST') == config('dofus.domain.main'))
@include('errors.404_main')
@else
@include('errors.404_fake')
@endif
