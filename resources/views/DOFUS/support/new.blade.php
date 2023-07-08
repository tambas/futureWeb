@extends('layouts.contents.default')
@include('layouts.menus.base')

@section('breadcrumbs')
{? $page_name = 'Nouveau ticket' ?}
{!! Breadcrumbs::render('support.page', $page_name) !!}
@stop
@section('header')
    {{ Html::script('tinymce/tinymce.min.js') }}
    {{ Html::script('tinymce/tinymce_simple_front.js') }}
<style>
.alert-info a
{
    color: #337ab7;
}
.alert-info a.btn
{
    color: #fff;
}
#support .hint {
    display: block;
    font-size: 12px;
    color: #777777;
}

#support .character {
    margin: 5px;
    border: #b9ae9d 2px solid;
    border-radius: 5px;
}

#support .character:hover {
    border-color: #8f8065;
    background-color: #FFF;
}

#support .character input[type=radio] {
    margin-left: 10px;
}

#support .character img {
    vertical-align:middle;
    width: 50px;
    height: 50px;
}
</style>
@endsection
@section('content')
<div class="ak-container ak-main-center">
    <div class="ak-title-container ak-backlink">
        <h1><span class="ak-icon-big ak-support"></span></a> Support - {{ $page_name }}</h1>
        <a href="{{ URL::route('support') }}" class="ak-backlink-button">Retour à mes tickets</a>
    </div>
    <div class="ak-container ak-panel-stack" id="advert-ticket">
        <div class="ak-container ak-panel ak-glue">
             <div class="ak-panel-title">
                <span class="ak-panel-title-icon"></span> Avant toute chose
            </div>
            <div class="ak-panel-content">
                <div class="alert alert-info">
                    <p>Avant d'ouvrir un ticket, nous vous invitons à lire attentivement nos <a href="{{ config('dofus.social.forum') }}22-tutoriels">tutoriels</a> ainsi que notre <a href="{{ config('dofus.social.forum') }}faq">F.A.Q</a>.</p>
                    <p class="mt-10">Ceci afin d'éviter d'encombrer le support et d'essayer de diminuer le temps de réponse pour vos demandes.</p>
                    <br>
                    <a class="btn btn-danger btn-lg" href="javascript:openticket();">Ouvrir un ticket</a>
                </div>
            </div>
        </div>
    </div>
    <div class="ak-container ak-panel-stack" style="display:none;" id="ticket-open">
        <div class="ak-container ak-panel ak-glue">
            <div class="ak-panel-title">
                <span class="ak-panel-title-icon"></span> Ouvrir un nouveau ticket
            </div>
            <div class="ak-panel-content">
                <div class="panel-main">
                    <div id="ticket-success" style="display:none;">
                        <h4 class="text-center">Votre ticket a bien été envoyé. Il sera traité le plus rapidement possible.</h4>
                        <p style="text-align: center;">Une copie vous a été envoyé à: <span id="success-email" class="ak-bold"></span>. <br> Le délai de réponse peut varier entre 1 et 48 heures.</p>
                    </div>
                    {!! Form::open([ 'route' => 'support.store', 'method' => 'POST', 'id' => 'support', 'class' => 'ak-form',  'files' => true]) !!}
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('bottom')
<script>
var $ = require('jquery');
function launchTinymce()
{
    if($("textarea").length > 0){
        editor_simple_front_config.selector = "textarea";
        $("textarea").removeClass('hidden');
        tinymce.init(editor_simple_front_config);
    };
}
function openticket()
{
    $('#advert-ticket').fadeOut(1000);
    $('#ticket-open').fadeIn(1500);
    window.scrollTo(0, 0);
}

var nb_part = 0;

function get_params() {
    params = {};

    $('#support select.special, #support input.special').each(function() {
        paramValue = $(this).val().split('|')[1];
        paramName  = $(this).attr('name').split('|')[0];
        params[paramName] = paramValue;

    });

    return params;
}

function get_child(id, name, query) {
    remove_parts(id);
    params = get_params();

    var url_base = '{{ route('home') }}';
    var url = ''+url_base+'/support/child/' +name;

    if (query != null && query != '') url +=  '/' + query;

    $.get(url, params, function(data) {
        /*if (id == 0) {
            $('#support').append('<div class="part" part="0">' + data + '</div>');
        } else {
            $('#support .part[part='+id+']').after('<div class="part" part="' + id + '">' + data + '</div>');
            nb_part = id + "";
        }*/
        $('#support').append('<div class="part" part="' + id + '">' + data + '</div>');
        nb_part = id + "";
    });
}

function remove_parts(id) {
    if (nb_part >= id) {
        for (var i = id; i <= nb_part; i++) {
            $('#support [part='+i+']').remove();
        }
        nb_part = id;
    }
}

// On field change
$('#support').on('change', 'select, input[type=radio]', function() {
    data = $(this).val().split('|');
    id = parseInt($(this).closest('.part').attr('part'));
    tag = data[0];

    if (tag == 'child')
    {
        child = (data[2] != null ? data[2] : '');
        query = (data[1] != null ? data[1] : '');

        get_child(id + 1, child, query);
    }
    else if (tag == 'reset')
    {
        remove_parts(id + 1);
    }
});

// On submit ticket
/*$('#support').on('click', 'input[type=submit]', function() {
    params = get_params();

    $.post('/api/support/store', params, function(data) {
        $('#support').html(data);
    });

    return false;
});*/
            $("#support").on("submit", function (event) {
            tinymce.triggerSave();
            var button = $('#support input[type="submit"]');
            var token = '{{ Session::token() }}';

            button.prop('disabled', true);
            event.preventDefault();

            var $form = $(this);
            var formdata = (window.FormData) ? new FormData($form[0]) : null;
            var data = (formdata !== null) ? formdata : $form.serialize();

            $.ajax({
                contentType: false,
                processData: false,
                method: 'POST',
                url: $form.attr("action"),
                data: data,
                success: function (email) {
                    $('#support').fadeOut(1000);
                    window.scrollTo(0, 0);
                    $('#success-email').text(email);
                    $('#ticket-success').fadeIn(1500);
                },
                error: function (jqXhr, json, errorThrown) {
                    var errors = jqXhr.responseJSON;
                    var errorsHtml;
                    if (errors) {
                        errorsHtml = '';
                        $.each(errors, function (key, value) {
                            errorsHtml += '<li>' + value[0] + '</li>';
                        });
                    }
                    else {
                        errorsHtml = 'Unknown error';
                    }
                    toastr.error(errorsHtml);
                    button.prop('disabled', false);
                }
            });
        });


get_child(1, 'support');
//get_child(0, 'final');

</script>
@endsection
