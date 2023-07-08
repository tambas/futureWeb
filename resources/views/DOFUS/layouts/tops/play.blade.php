@include('layouts.tops.breadcrumb')

@section('top')
<div class="ak-pagetop ak-container">
    <div class="ak-pagetop-child ">
        <div class="ak-container container">
            <div>
                <div class="row">
                    @yield('breadcrumb')
                    <div class="col-sm-3 text-center">
                        <span><a class="btn btn-info ak-btn-big ak-btn-play" href="{{ URL::route('register') }}">Jouer !</a></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop
