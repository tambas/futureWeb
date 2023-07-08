<?php

// Help: http://laravel-breadcrumbs.davejamesmiller.com/en/latest/start.html#install-laravel-breadcrumbs

// Accueil
Breadcrumbs::register('home', function ($breadcrumbs) {
    $breadcrumbs->push('Accueil', route('home'));
});

// Accueil > [page]
Breadcrumbs::register('page', function ($breadcrumbs, $page) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push($page);
});

// Accueil > Actualités
Breadcrumbs::register('actuality', function ($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push('Actualités', route('posts.news'));
});

// Accueil > Actualités > [page]
Breadcrumbs::register('actuality.page', function ($breadcrumbs, $page) {
    $breadcrumbs->parent('actuality');
    $breadcrumbs->push($page);
});

// Accueil > Mon Compte
Breadcrumbs::register('account', function ($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push('Mon Compte', route('profile'));
});

// Accueil > Mon Compte > [page]
Breadcrumbs::register('account.page', function ($breadcrumbs, $page) {
    $breadcrumbs->parent('account');
    $breadcrumbs->push($page);
});

// Accueil > Mon Compte > Compte de Jeu
Breadcrumbs::register('gameaccount', function ($breadcrumbs, $params) {
    $breadcrumbs->parent('account');
    $breadcrumbs->push('Compte de Jeu', route('gameaccount.view', $params));
});

// Accueil > Mon Compte > Compte de jeu > [page]
Breadcrumbs::register('gameaccount.page', function ($breadcrumbs, $page, $params) {
    $breadcrumbs->parent('gameaccount', $params);
    $breadcrumbs->push($page);
});

// Accueil > Boutique
Breadcrumbs::register('shop', function ($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push('Boutique', route('shop.index'));
});

// Accueil > Boutique > [page]
Breadcrumbs::register('shop.page', function ($breadcrumbs, $page) {
    $breadcrumbs->parent('shop');
    $breadcrumbs->push($page);
});

// Accueil > Classement
Breadcrumbs::register('ladder', function ($breadcrumbs, $server) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push('Classement', route('ladder.general', [$server]));
});

// Accueil > Classement > [page]
Breadcrumbs::register('ladder.page', function ($breadcrumbs, $page, $server) {
    $breadcrumbs->parent('ladder', $server);
    $breadcrumbs->push($page);
});

// Accueil > Support
Breadcrumbs::register('support', function ($breadcrumbs) {
    $breadcrumbs->parent('home');
     $breadcrumbs->push('Support', route('support'));
});

// Accueil > Support > [page]
Breadcrumbs::register('support.page', function ($breadcrumbs, $page) {
    $breadcrumbs->parent('support');
    $breadcrumbs->push($page);
});
