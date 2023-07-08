@extends('layouts.contents.default')
@include('layouts.menus.base')

@section('header')
    {!! Html::style('css/vote.css') !!}
    {!! Html::style('css/encyclopedia.css') !!}
@stop

@section('breadcrumbs')
{? $page_name = 'Voter' ?}
{!! Breadcrumbs::render('page', $page_name) !!}
@stop

@section('content')
<div class="ak-container ak-main-center vote-rewards">
    <div class="ak-title-container">
        <h1 class="ak-return-link">
            <span class="ak-icon-big ak-gift"></span></a> Vote pour le serveur
        </h1>
    </div>
    <div class="ak-infos-general">
        <div class="row">
            <div class="col-sm-6">
                <div class="ak-infos">
                    <div class="ak-cumul">Vous avez déjà voté <span class="nb-steps">{{ $votesCount }}</span> fois !</div>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="row">
                    <div class="ak-block-gift-win">
                        <div class="ak-gift"><img src="{{ URL::asset('imgs/lottery/ticket_gold.png') }}" alt="gift"></div>
                        <div class="ak-gift-win"><span>Tickets gagnés</span> {{ $giftsCount }}</div>
                    </div>
                    <div class="ak-block-next-gift">
                        <div class="ak-gift"><img src="{{ URL::asset('imgs/lottery/ticket_gold.png') }}" alt="gift"></div>
                        <div class="ak-next-gift"><span>Prochain ticket</span> dans {{ $nextGifts }} votes</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="ak-container ak-panel panel-vote-link">
        <div class="ak-panel-content">
            <div class="row">
                <div class="col-sm-6">
                    @if ($delay->canVote)
                        @if (config('dofus.rpg-paradize.use_callback'))
                            <a href="{{ URL::route('vote.go') }}" target="_blank" class="btn btn-blok btn-lg btn-info vote-link" style="margin-top:30px">Voter</a>
                        @else
                            <a href="{{ URL::to('http://www.rpg-paradize.com/?page=vote&vote=' . $rpgId) }}" target="_blank" class="btn btn-blok btn-lg btn-info vote-link" style="margin-top:30px">Voter</a>
                        @endif
                    @else
                        <p style="margin-top:30px"><b>Vous devez attendre {{ $delay->hours }}h {{ $delay->minutes }}m {{ $delay->seconds }}s avant de pouvoir re-voter.</b></p>
                    @endif
                </div>
                <div class="col-sm-6">
                    <p>Tous les {{ $votesForTicket }} votes, vous gagnerez un nouveau ticket qui vous permettra de jouer à la loterie, et tous les {{ $votesForTicket * 5 }} votes vous gagnerez un ticket doré qui vous permettra de jouer à une autre loterie vous donnant accès à des récompenses plus importantes.</p>
                </div>
            </div>
        </div>
    </div>
    <div class="ak-panel-stack">
        <div class="ak-block-rewards">
            <div class="ak-menu-left ak-ajaxloader">
                <a data="1">1 <sup>èr</sup> Palier</a>
                @for ($i = 2; $i <= ceil(($votesCount+1) / ($votesForTicket * 5)); $i++)
                    <a data="{{ $i }}">{{ $i }} <sup>ème</sup> Palier</a>
                @endfor
            </div>
            <div class="ak-select-menu-left ak-ajaxloader">
                <select onchange="$('.ak-block-rewards .ak-menu-left a[data='+this.value+']').trigger('click');">
                    @for ($i = 1; $i <= ceil(($votesCount+1) / ($votesForTicket * 5)); $i++)
                        <option value="{{ $i }}">Palier {{ $i }}</option>
                    @endfor
                </select>
                <a class="ak-select-link hide"></a>
            </div>
            <div id="step-view">
                @include('vote.paliers')
            </div>
            <div class="loadmask"></div>
            <div class="ak-loading">
                <div class="spinner">
                    <div class="mask">
                        <div class="circle"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    var $ = require('jquery');

    $(document).ready(function() {

        $(".ak-block-rewards .ak-menu-left a[data={{ $palierId }}]").addClass("ak-selected");
        $(".ak-block-rewards .ak-select-menu-left option[value={{ $palierId }}]").attr('selected','selected');

        progress();
        showItem({{ $current }}, {{ ($votesForTicket * 5) * ($palierId - 1) + ($votesForTicket * 1) }});

        $(".ak-block-rewards .ak-menu-left a").on("click", function() {
            var self = $(this);
            var palierId = self.attr("data");

            loader('ak-block-rewards', true);

            $(".ak-block-rewards .ak-menu-left a.ak-selected").removeClass("ak-selected");

            $.ajax({
                type: "GET",
                url: "{{ URL::route('vote.palier') }}/" + palierId,
            })
            .done(function(res) {
                $("#step-view").html(res);

                $(".ak-block-rewards .ak-menu-left a[data="+palierId+"]").addClass("ak-selected");
                $(".ak-block-rewards .ak-select-menu-left option[value="+palierId+"]").attr('selected','selected');

                loader('ak-block-rewards', false);

                progress();

                var item = $("#load-item");
                showItem(item.attr("step"), item.attr("votes"));
            });
        });

        $("#step-view").on("click", ".ak-block-step a", function() {
            $(".ak-block-step a.ak-selected").removeClass("ak-selected");

            var parent = $(this).parent(".ak-block-step");
            var item = parent.attr("item");
            var step = parent.attr("step");
            var votes = parent.attr("votes");

            $(this).addClass("ak-selected");
            $(".ak-select-time-line option[value="+step+"]").attr('selected','selected');

            showItem(step, votes);
        });

        $(".vote-link").on("click", function() {
            setTimeout(function() {
                @if (config('dofus.rpg-paradize.use_callback'))
                    window.location.href = "{{ URL::route('vote.index') }}";
                @else
                    window.location.href = "{{ URL::route('vote.confirm') }}";
                @endif
            }, 5000);
        });

        function loader(seletor, state) {
            if (state) {
                $("."+seletor).addClass("mask-relative masked");
                $("."+seletor+" .loadmask").show();
                $("."+seletor+" .ak-loading").show();
            } else {
                $("."+seletor).removeClass("mask-relative masked");
                $("."+seletor+" .loadmask").hide();
                $("."+seletor+" .ak-loading").hide();
            }
        }

        function progress() {
            var percent = $(".progress-bar").attr("data");
            $(".progress-bar").animate({width: percent +'%'}, 0, "linear");
        }

        function showItem(step, votes) {
            // Show loader
            loader('ak-block-rewards', true);

            // Clear previous data
            $(".ak-name-gift").html("");
            $(".ak-encyclo-detail-right .ak-panel-content").html("");
            $(".ak-encyclo-detail-illu-l img").attr("src", "");
            $(".ak-step").removeClass("ak-step1 ak-step2 ak-step3 ak-step4 ak-step5");

            // Display new data
            $(".ak-nb-step span").html(votes);
            $(".ak-nb-step").addClass("ak-step" + step);

            // Get reward info
            $.ajax({
                type: "GET",
                url: "{{ URL::route('vote.object') }}/" + votes,
                dataType: "json",
            })
            .done(function(res) {
                // Dislay new reward
                $(".ak-name-gift").html(res.name);
                $(".ak-encyclo-detail-right .ak-panel-content").html(res.description);
                $(".ak-encyclo-detail-illu-l img").attr("src", res.image);

                // Hide loader
                loader('ak-block-rewards', false);
            });
        }
    });
</script>
@stop
