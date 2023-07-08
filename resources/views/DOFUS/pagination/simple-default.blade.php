@if ($paginator->hasPages())
    <ul class="ak-pagination pagination">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <li class="disabled"><a href="javascript:void(0);" data-action="prev">@lang('pagination.previous')</a></li>
        @else
            <li><a href="{{ $paginator->previousPageUrl() }}" data-action="prev">@lang('pagination.previous')</a></li>
        @endif

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <li><a href="{{ $paginator->nextPageUrl() }}" data-action="next">@lang('pagination.next')</a></li>
        @else
            <li class="disabled"><a data-action="next">@lang('pagination.next')</a></li>
        @endif
    </ul>
    <script type="application/json">{!!$settings!!}</script>
@endif
