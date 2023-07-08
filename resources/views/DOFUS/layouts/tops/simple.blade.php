@include('layouts.tops.breadcrumb')

@section('top')
<div class="ak-pagetop ak-container">
    <div class="ak-pagetop-child ">
        <div class="ak-container container">
            <div>
                <div class="row">
                    @yield('breadcrumb')
                </div>
            </div>
        </div>
    </div>
</div>
@stop
