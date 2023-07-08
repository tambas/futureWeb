<div class="ui-dialog ui-widget ui-widget-content ui-corner-all ui-front ak-modal-wrapper" tabindex="-1" role="dialog" style="height: auto; width: 90%; display: block; top: 200px; left: 336px; z-index: 101;">
    <div class="ui-dialog-titlebar ui-widget-header ui-corner-all ui-helper-clearfix">
        <span id="ui-id-7" class="ui-dialog-title">Comment jouer ?</span>
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
                            <center><img src="{{ URL::asset('imgs/help/social.png') }}" alt="Social" width="300" style="float:left;" /></center>
                        </div>
                        <div class="col-sm-7 ak-panel">

                            <div class="ak-panel-title">
                                <span class="ak-panel-title-icon"></span> Bienvenue !
                            </div>
                            <p>Vous voila maintenant inscrit, c'est le début d'une grande aventure, mais avant il vous reste quelques petites choses à faire avant de vous lancer.</p>

                            <br>

                            <div class="ak-panel-title">
                                <span class="ak-panel-title-icon"></span> Créer un compte de jeu
                            </div>
                            <p>Pour pouvoir rejoindre le serveur de jeu il vous faut créer un compte de jeu. Un compte web vous permet de créer jusqu'à 8 compte de jeu.<p>
                            <p><a href="{{ URL::route('gameaccount.create') }}"><button class="btn btn-primary btn-lg">Créer un compte de jeu</button></a></p>

                            <br>

                            <div class="ak-panel-title">
                                <span class="ak-panel-title-icon"></span> Sécurisez votre compte
                            </div>
                            <p>Afin de vous protéger contre le vol de compte, vous pouvez certifier votre compte avec vos informations personnelles. Celles-ci vous seront demandées en cas de problème afin de valider que vous en êtes bien l'auteur.<p>
                            <a href="{{ URL::route('account.certify') }}"><button class="btn btn-danger btn-lg">Certifier mon compte</button></a>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
