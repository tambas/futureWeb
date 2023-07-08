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
<div class="ak-title-container">
    <h1><span class="ak-icon-big ak-shop"></span> Boutique</h1>
</div>
<div class="ak-page-menu ak-glue">
   <nav class="ak-nav-expand ak-expanded">
      <div class="ak-nav-expand-container">
         <ul class="ak-nav-links ak-nav-expand-links">
            <li class="on">
               <a href="{{URL::route('shop.index')}}">
               Accueil      </a>
            </li>
            <li>
               <a href="{{URL::route('shop.payment.country')}}">
               Ogrines      </a>
            </li>  
             <li>
               <a href="{{URL::route('shop.market')}}">
               Marché des personnages      </a>
            </li>
         </ul>
      </div>
      <div class="ak-nav-expand-more">
         <span class="ak-nav-expand-icon ak-picto-common ak-picto-open" style="display: none;">+</span>
      </div>
   </nav>
</div>
<div class="ak-carousel ak-carousel-theme-black">
   <div class="ak-carouseltouch"> 
     <div class="ak-carousel-hlitem-category">
                <a class="ak-link-zone" href="{{URL::route('shop.market')}}"></a>
                <div class="row">
                     <div class="col-md-8 ak-shop-article-background" style="background-image: url('{{URL::asset('imgs/shop/carousel_buy_char.jpg')}}')">
                     </div>
                     <div class="col-md-4 ak-carousel-hlitem-content ak-shop-article">
                        <div class="ak-carousel-hlitem-name">
                           DÉCOUVREZ LE MARCHE DES PERSONNAGES      
                        </div>
                        <div class="ak-carousel-hlitem-description">
                           Tu souhaites acheter le personnage d'un autre joueur ou simplement vendre le tien ? Alors viens découvrir le marché des personnages !
                        </div>
                        <div class="ak-price-actions">
                           <a href="{{URL::route('shop.market')}}" class="btn btn-primary btn-lg ak-btn-unlock ak-btn-discover">Découvrir</a>
                        </div>
                     </div>
                </div> 
        </div>
        <div class="ak-carousel-hlitem-category">
                  <a class="ak-link-zone" href="{{URL::route('shop.payment.country')}}"></a>
                  <div class="row">
                     <div class="col-md-8 ak-shop-article-background" style="background-image: url('{{URL::asset('imgs/shop/carousel_ogrines.jpg')}}')">
                     </div>
                     <div class="col-md-4 ak-carousel-hlitem-content ak-shop-article">
                        <div class="ak-carousel-hlitem-name">
                           ACHETER DES OGRINES      
                        </div>
                        <div class="ak-carousel-hlitem-description">
                           Découvrez nos moyens de paiements pour l'achat d'ogrines     
                        </div>
                        <div class="ak-price-actions">
                           <a href="{{URL::route('shop.payment.country')}}" class="btn btn-info btn-lg ak-btn-unlock ak-btn-discover">Acheter</a>
                        </div>
                     </div>
                </div> 
        </div>
   </div>
   <script type="application/json">{"circular":true,"snap":true,"autoroll":true,"paginationcontrol":"next"}</script>
</div>
<div class="ak-catalog-article-list ak-container ak-list-paginated ak-catalog-article-list-list">
   <div class="ak-shop-mosaic">
      <div class="ak-responsivemosaic">
         <div class="row">
            <div class="col-sm-6 ">
               <div class="ak-mosaic-item-article ak-mosaic-item">
                  <div class="item">
                     <div class="ak-mosaic-item-illu illu">
                        <a href="{{URL::route('shop.payment.country')}}">
                        <img class="img-maxresponsive" data-max="200" onerror="this.src='{{URL::asset('imgs/shop/default_200_200.png')}}" src="{{URL::asset('imgs/shop/ogrines_200_200.png')}}">
                        </a>
                        <a href="{{URL::route('shop.payment.country')}}">
                        </a>
                     </div>
                     <div class="ak-mosaic-item-detail">
                        <div class="ak-mosaic-item-name name">
                           <a href="{{URL::route('shop.payment.country')}}">
                           <span>Acheter des ogrines</span>
                           </a>
                        </div>
                        <div class="ak-mosaic-item-info infos">
                           <a href="{{URL::route('shop.payment.country')}}"><span></span></a>
                        </div>
                     </div>
                     <div class="ak-item-bottom">
                        <div class="ak-price">
                           <span class="ak-text-before-price">minimum </span><span class="ak-display-price"<span class="ak-nobreak">3,00 €</span></span>
                        </div>
                        <div class="ak-item-actions ak-0-actions">
                        </div>
                     </div>
                  </div>
               </div>
            </div>
            <div class="ak-row-break clearfix visible-xs "></div>
            <div class="col-sm-3 ">
               <div class="ak-mosaic-item-article ak-mosaic-item">
                  <div class="item">
                     <div class="ak-mosaic-item-illu illu">
                        <a href="{{URL::route('profile')}}">
                        <img class="img-maxresponsive" data-max="200" onerror="this.src='{{URL::asset('imgs/shop/default_200_200.png')}}" src="{{URL::asset('imgs/shop/restore_char_200_200.png')}}">
                        </a>
                        <a href="{{URL::route('profile')}}">
                        </a>
                     </div>
                     <div class="ak-mosaic-item-detail">
                        <div class="ak-mosaic-item-name name">
                           <a href="{{URL::route('profile')}}">
                           <span>Restaurer un personnage</span>
                           </a>
                        </div>
                        <div class="ak-mosaic-item-info infos">
                           <a href="{{URL::route('profile')}}"><span></span></a>
                        </div>
                     </div>
                     <div class="ak-item-bottom">
                        <div class="ak-price">
                           <a href="{{URL::route('profile')}}"><span class="ak-display-price ak-currency-ogr"><span class="ak-nobreak">5 <span class="ak-icon-small ak-ogrines-icon"></span> par niveau</span> <sup>*</sup></span></a>
                        </div>
                        <div class="ak-item-actions ak-0-actions">
                        </div>
                     </div>
                  </div>
               </div>
            </div>
            <div class="ak-row-break clearfix visible-xs "></div>
            <div class="col-sm-3 ">
               <div class="ak-mosaic-item-article ak-mosaic-item">
                  <div class="item">
                     <div class="ak-mosaic-item-illu illu">
                        <a href="{{URL::route('shop.market')}}">
                        <img class="img-maxresponsive" data-max="200" onerror="this.src='{{URL::asset('imgs/shop/default_200_200.png')}}'" src="{{URL::asset('imgs/shop/buy_char_200_200.png')}}">
                        </a>
                        <a href="{{URL::route('shop.market')}}">
                        </a>
                     </div>
                     <div class="ak-mosaic-item-detail">
                        <div class="ak-mosaic-item-name name">
                           <a href="{{URL::route('shop.market')}}">
                           <span>Acheter un personnage</span>
                           </a>
                        </div>
                        <div class="ak-mosaic-item-info infos">
                           <a href="{{URL::route('shop.market')}}"><span></span></a>
                        </div>
                     </div>
                     <div class="ak-item-bottom">
                        <div class="ak-price">
                           <a href="{{URL::route('shop.market')}}">minimum <span class="ak-display-price ak-currency-ogr"><span class="ak-nobreak">{{config('dofus.characters_market.minimal_price')}} <span class="ak-icon-small ak-ogrines-icon"></span></span></span></a>
                        </div>
                        <div class="ak-item-actions ak-0-actions">
                        </div>
                     </div>
                  </div>
               </div>
            </div>
            <div class="ak-row-break clearfix visible-xs visible-sm visible-md visible-lg "></div>
         </div>
      </div>
   </div>
   <div class="clearfix"></div>
</div>
@stop
@section('bottom')
     {!! Html::script('js/shopcommon.js') !!}
@endsection
