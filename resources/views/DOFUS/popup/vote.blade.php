<div class="ui-dialog ui-widget ui-widget-content ui-corner-all ui-front ak-modal-wrapper" tabindex="-1" role="dialog" style="height: auto; width: 90%; display: block; top: 200px; left: 336px; z-index: 101;">
    <div class="ui-dialog-titlebar ui-widget-header ui-corner-all ui-helper-clearfix">
        <span class="ui-dialog-title">Comment voter ?</span>
        <button type="button" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-icon-only ui-dialog-titlebar-close" role="button" title="Close">
            <span class="ui-button-icon-primary ui-icon ui-icon-closethick"></span>
            <span class="ui-button-text">Close</span>
        </button>
    </div>
    <div class="js-modal-login ui-dialog-content ui-widget-content">
        <div class="ak-modal-content ak-panel-stack">
            <div class="ak-container ak-panel ak-nocontentpadding">
                <div class="ak-panel-content">
                    <div class="row">
                        <div class="col-sm-5">
                            <center><img src="{{ URL::asset('imgs/help/vote.png') }}" alt="Vote" width="300" style="float:left;" /></center>
                        </div>
                        <div class="col-sm-7 ak-panel">

                            <div class="ak-panel-title">
                                <span class="ak-panel-title-icon"></span> Pourquoi voter
                            </div>
                            <p>Sachez que voter pour {{ config('dofus.title') }} vous rapporte 1 jeton <span class="ak-icon-small ak-votes-icon"></span> ({{ config('dofus.points_by_vote') }} <span class="ak-icon-small ak-ogrines-icon"></span>) à chacun de vos votes, et qu'en contrepartie vous nous aider à maintenir notre place sur RPG-Paradize.</p>

                            <br>

                            <div class="ak-panel-title">
                                <span class="ak-panel-title-icon"></span> Comment voter
                            </div>
                            <p>Pour voter, vous devez d'abord cliquer sur le bouton de vote <a href="{{ URL::to('http://www.rpg-paradize.com/?page=vote&vote=' . config("dofus.details")['sigma']->rpg) }}" target="_blank" class="btn btn-ingo btn-xs vote-link" style="background-color:#f29c06">Voter</a> et ensuite sur RPG-Paradize, remplissez le captcha demandé. Une fois que vous avez rempli le captcha et que le site RPG-Paradize vous a redirigé sur l'accueil, cherchez le serveur {{ config('dofus.title') }} dans le classement et récupérez sa valeur OUT<p>
                            <p><img src="{{ URL::asset('imgs/help/out.jpg') }}" alt="Valeur OUT" width="300px"></p>

                            <br>

                            <div class="ak-panel-title">
                                <span class="ak-panel-title-icon"></span> Finir mon vote
                            </div>
                            <p>Fermez la page RPG-Paradize puis revenez sur Azote, nous vous demanderons de préciser le OUT de la page d'{{ config('dofus.title') }} qui confirmera que vous avez bien voté, puis validez votre vote.<p>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
