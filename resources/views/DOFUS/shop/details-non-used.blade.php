@extends('layouts.contents.default')
@include('layouts.menus.base')

@section('header')
      {!! Html::style('css/shop.css') !!}
@stop

@section('breadcrumbs')
{? $page_name = 'Boutique' ?}
{!! Breadcrumbs::render('shop.page', $page_name) !!}
@stop

@section('content')
            <div class="ak-mosaic-article-item-inline" style="clear: both; opacity: 1;">
                <div class="arrowup" style="left: 87.5px;"></div>
                <a class="ak-item-inline-close"></a>
                <div>
                    <div class="ak-container ak-panel-stack ak-glue ak-shop-article" itemscope="" itemtype="http://data-vocabulary.org/Product">
                        <div class="ak-container ak-panel ak-article-infos ak-display-inline">
                            <div class="ak-panel-content">
                            <div class="row">
                                <div class="col-sm-1">
                                    <div class="ak-article-illu">
                                        <img onerror="this.src='https://static.ankama.com/shops_ng/img/article/dofus/default_200_200.png'" src="https://static.ankama.com/shops/img/article/6480/gallery_all_200_200.png" itemprop="image" class="img-maxresponsive" data-max="200">
                                    </div>
                                </div>
                                <div class="col-sm-11">
                                    <div class="ak-container ak-panel ak-article-heading ak-nocontentpadding">
                                        <div class="ak-panel-title">
                                        <span class="ak-panel-title-icon"></span>
                                        <span itemprop="name">Changement de Classe</span>            
                                        </div>
                                        <div class="ak-panel-content">
                                        <meta itemprop="category" content="Services">
                                        <div class="price">
                                            <span>
                                            <span class="ak-display-price ak-currency-ogr"><span class="ak-nobreak">50 <span class="ak-icon-small ak-ogrines-icon"></span> par niveau</span> <sup>*</sup></span>    </span>
                                        </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <div class="ak-container ak-panel ak-infos-right ak-nocontentpadding">
                                    <div class="ak-panel-content">
                                        <div class="ak-hr"></div>
                                        <div>
                                        <div class="ak-container ak-panel ak-article-description ak-nocontentpadding">
                                            <div class="ak-panel-content">
                                                <div itemprop="description">
                                                    <p><strong>Assumez votre véritable personnalité et changez votre façon de jouer en changeant de classe et en conservant la même puissance !</strong><br>
                                                    &nbsp;
                                                    </p>
                                                    <ul>
                                                    <li>Ce service contient un changement de sexe, de visage et de couleur</li>
                                                    <li>En changeant de classe, les points de caractéristiques de votre personnage seront réinitialisés et vous conservez les points additionnels.</li>
                                                    <li>Les points de sorts de votre personnage seront remis à zéro, sauf les points additionnels.</li>
                                                    <li>Les métiers, l’ensemble des succès et les quêtes (excepté celles de classe) sont conservés.</li>
                                                    <li>Le changement de classe sera obligatoire lors de la prochaine connexion de votre personnage</li>
                                                    <li>Ce service n'est pas disponible sur le serveur Oto Mustam.</li>
                                                    <li>Bien sûr, vous ne pouvez pas reprendre la classe que vous incarnez déjà.</li>
                                                    <li>Le prix est adapté au niveau du personnage:</li>
                                                    </ul>
                                                    <p><strong>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;Niveau 1 à 100 = 50 OG par niveau</strong><br>
                                                    <strong><strong>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;Niveau 101 à 200 = 150 OG par niveau</strong></strong>
                                                    </p>
                                                    <p>&nbsp;<br>
                                                    <em>Attention ! Il ne suffit pas de changer de classe pour aussitôt la maîtriser ! Avant de vous lancer dans l’aventure, prenez le temps de tester vos sorts et de vous familiariser avec votre « nouveau vous »… </em><br>
                                                    <br>
                                                    Bonne chance&nbsp;!
                                                    </p>
                                                    <p><strong>ATTENTION : </strong></p>
                                                    <p><strong>- Les serveurs Ombre et Oto-Mustam n'ont pas accès à ce service !</strong></p>
                                                </div>
                                            </div>
                                        </div>
                                        </div>
                                        <div class="ak-actions">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            </div>
                        </div>
                        <div class="ak-container ak-panel ak-choice-transfer ">
                            <div class="ak-panel-content">
                            <div class="ak-form">
                                <form class="ak-container ak-simpleform ak-choiceform" role="form" method="POST" action="/fr/boutique/19-services/6480-changement-classe" data-target=".ak-choice-transfer" novalidate="novalidate">
                                    <div class="ak-container ak-panel ak-choice-char ak-nocontentpadding">
                                        <div class="ak-panel-title">
                                        <span class="ak-panel-title-icon"></span>
                                        Quel personnage souhaitez-vous changer de classe * ?            
                                        </div>
                                        <div class="ak-panel-content">
                                        <div class="ak-container ak-content-list ak-displaymode-image-col">
                                            <div class="row ak-container">
                                                <div class="ak-column ak-container col-md-6">
                                                    <div class="ak-list-element">
                                                    <div class="ak-main">
                                                        <div class="ak-main-content ">
                                                            <div class="ak-image">
                                                                <div class="ak-entitylook" alt="" style="background:url(https://static.ankama.com/dofus/renderer/look/7b317c333238352c333239362c313530392c313438372c35392c333335337c313d31363337383833322c323d31363735303834382c333d31363735303834382c343d3137353037342c353d31343237373038312c373d302c383d31363537373637342c393d31363531343739312c31303d307c3134307d/face/2/70_70-0.png) top left;width:70px;height:70px"></div>
                                                            </div>
                                                            <div class="ak-content">
                                                                <div class="ak-title">
                                                                Kaew <span class="ak-list-title-price">20 000 OGR</span>                            
                                                                </div>
                                                                <div class="ak-text">Niv. 200 - Djaul</div>
                                                            </div>
                                                            <div class="ak-aside">
                                                                <div class="form-group">
                                                                <div class="radio">
                                                                    <input type="radio" value="934341300003" name="choice_char[]" id="uid-59009e80e19a2">
                                                                </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    </div>
                                                </div>
                                                <div class="ak-column ak-container col-md-6">
                                                    <div class="ak-list-element">
                                                    <div class="ak-main">
                                                        <div class="ak-main-content ">
                                                            <div class="ak-image">
                                                                <div class="ak-entitylook" alt="" style="background:url(https://static.ankama.com/dofus/renderer/look/7b317c32302c323033352c3233392c3131392c3539307c313d31353937333437302c323d31313531333737372c333d333335353434332c343d373334373438372c353d31343934323436367c3133307d/face/2/70_70-0.png) top left;width:70px;height:70px"></div>
                                                            </div>
                                                            <div class="ak-content">
                                                                <div class="ak-title">
                                                                Madness-Overload <span class="ak-list-title-price">2 600 OGR</span>                            
                                                                </div>
                                                                <div class="ak-text">Niv. 52 - Djaul</div>
                                                            </div>
                                                            <div class="ak-aside">
                                                                <div class="form-group">
                                                                <div class="radio">
                                                                    <input type="radio" value="1154840900003" name="choice_char[]" id="uid-59009e80e1a5a">
                                                                </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    </div>
                                                </div>
                                                <div class="clearfix  visible-md visible-lg"></div>
                                                <div class="ak-column ak-container col-md-6">
                                                    <div class="ak-list-element">
                                                    <div class="ak-main">
                                                        <div class="ak-main-content ">
                                                            <div class="ak-image">
                                                                <div class="ak-entitylook" alt="" style="background:url(https://static.ankama.com/dofus/renderer/look/7b317c3130302c323135362c3233392c3131397c313d383533373838372c323d383136343039392c333d31343430323636392c343d31343430323636392c353d31313936303839327c3135307d/face/2/70_70-0.png) top left;width:70px;height:70px"></div>
                                                            </div>
                                                            <div class="ak-content">
                                                                <div class="ak-title">
                                                                Adelahd <span class="ak-list-title-price">1 100 OGR</span>                            
                                                                </div>
                                                                <div class="ak-text">Niv. 22 - Djaul</div>
                                                            </div>
                                                            <div class="ak-aside">
                                                                <div class="form-group">
                                                                <div class="radio">
                                                                    <input type="radio" value="1181413400003" name="choice_char[]" id="uid-59009e80e1ae1">
                                                                </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    </div>
                                                </div>
                                                <div class="ak-column ak-container col-md-6">
                                                    <div class="ak-list-element">
                                                    <div class="ak-main">
                                                        <div class="ak-main-content ">
                                                            <div class="ak-image">
                                                                <div class="ak-entitylook" alt="" style="background:url(https://static.ankama.com/dofus/renderer/look/7b317c3130312c323136357c313d383533373838372c323d373334333830362c333d3436343833372c343d31303432353031392c353d3539373639397c3134357d/face/2/70_70-0.png) top left;width:70px;height:70px"></div>
                                                            </div>
                                                            <div class="ak-content">
                                                                <div class="ak-title">
                                                                Captain-Neo <span class="ak-list-title-price">4 250 OGR</span>                            
                                                                </div>
                                                                <div class="ak-text">Niv. 85 - Maimane</div>
                                                            </div>
                                                            <div class="ak-aside">
                                                                <div class="form-group">
                                                                <div class="radio">
                                                                    <input type="radio" value="442317400009" name="choice_char[]" id="uid-59009e80e1b63">
                                                                </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    </div>
                                                </div>
                                                <div class="clearfix  visible-md visible-lg"></div>
                                                <div class="ak-column ak-container col-md-6">
                                                    <div class="ak-list-element">
                                                    <div class="ak-main">
                                                        <div class="ak-main-content ">
                                                            <div class="ak-image">
                                                                <div class="ak-entitylook" alt="" style="background:url(https://static.ankama.com/dofus/renderer/look/7b317c3130312c323136347c313d383533373838372c323d383535373832362c333d31363131343539362c343d31363131343539362c353d31353132383437387c3134357d/face/2/70_70-0.png) top left;width:70px;height:70px"></div>
                                                            </div>
                                                            <div class="ak-content">
                                                                <div class="ak-title">
                                                                Mario-its-me <span class="ak-list-title-price">50 OGR</span>                            
                                                                </div>
                                                                <div class="ak-text">Niv. 1 - Silouate</div>
                                                            </div>
                                                            <div class="ak-aside">
                                                                <div class="form-group">
                                                                <div class="radio">
                                                                    <input type="radio" value="861252200013" name="choice_char[]" id="uid-59009e80e1be2">
                                                                </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="text-center">
                                <button type="button" role="button" class="btn btn-lg btn-blue btn-primary ak-tooltip" data-hasqtip="14">Changer la classe de mon personnage</button><script type="application/json">{"manual":true,"tooltip":{"content":{"title":"","text":"Veuillez choisir un personnage."},"style":{"classes":"ak-tooltip-white-shop"},"position":{"my":"bottom center","at":"top center","adjust":{"scroll":false}},"show":{"event":"click"},"hide":{"event":"unfocus","fixed":true,"delay":400}},"hideOnScroll":true,"forceOnTouch":true}</script>          
                            </div>
                            <div class="more_info"></div>
                            </div>
                        </div>
                        <div class="ak-container ak-panel">
                            <div class="ak-panel-content">
                            * A partir de
                            </div>
                        </div>
                        <div class="ak-container">
                            <div class="ak-social">
                            <div class="pull-left">
                                <div class="ak-social-block facebook">
                                    <a href="https://www.facebook.com/sharer/sharer.php?u=https%3A%2F%2Fsecure.dofus.com%2Ffr%2Fboutique%2F19-services%2F6480-changement-classe" onclick="javascript:window.open(this.href,'','menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;" class="">Partager</a>        
                                </div>
                                <div class="ak-social-block twitter">
                                    <a href="http://twitter.com/home?status=https%3A%2F%2Fsecure.dofus.com%2Ffr%2Fboutique%2F19-services%2F6480-changement-classe" onclick="javascript:window.open(this.href,'','menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;" class="">Tweet</a>        
                                </div>
                                <div class="ak-social-block google">
                                    <a href="https://plus.google.com/share?url=https%3A%2F%2Fsecure.dofus.com%2Ffr%2Fboutique%2F19-services%2F6480-changement-classe" onclick="javascript:window.open(this.href,'','menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;" class="">Partager</a>        
                                </div>
                            </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
@stop
