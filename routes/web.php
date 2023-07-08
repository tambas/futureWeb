<?php

Route::group(['domain' => Config::get('dofus.domain.main')], function () {

    Route::any('/', [
        'uses' => 'PostController@index',
        'as'   => 'home'
    ]);

    /* ============ NEWS ============ */

    Route::get(Lang::get('routes.posts.index'), [
        'uses' => 'PostController@index',
        'as'   => 'posts'
    ]);

    Route::get(Lang::get('routes.posts.news'), [
        'uses' => 'PostController@news',
        'as'   => 'posts.news'
    ]);

    Route::get(Lang::get('routes.posts.type'), [
        'uses' => 'PostController@newsType',
        'as'   => 'posts.type'
    ]);

    Route::get(Lang::get('routes.posts.show'), [
        'uses' => 'PostController@show',
        'as'   => 'posts.show'
    ]);

    Route::get(Lang::get('routes.posts.show.old'), [
        'uses' => 'PostController@redirect'
    ]);

    Route::post(Lang::get('routes.posts.show'), [
        'middleware' => 'auth',
        'uses' => 'PostController@show',
        'as'   => 'posts.comment.store'
    ]);

    Route::delete(Lang::get('routes.posts.comment.destroy'), [
        'middleware' => ['auth','can:delete-comments'],
        'uses' => 'PostController@commentDestroy',
        'as'   => 'posts.comment.destroy'
    ]);

    /* ============ ACCOUNT ============ */

    Route::get(Lang::get('routes.account.register'), [
        'uses'       => 'AccountController@register',
        'as'         => 'register'
    ]);

    Route::post(Lang::get('routes.account.register'), [
        'middleware' => 'guest',
        'uses'       => 'AccountController@store',
        'as'         => 'register'
    ]);

    Route::get(Lang::get('routes.account.profile'), [
        'middleware' => 'auth',
        'uses'       => 'AccountController@profile',
        'as'         => 'profile'
    ]);

    Route::get(Lang::get('routes.account.purchases'), [
        'middleware' => 'auth',
        'uses'       => 'AccountController@purchases',
        'as'         => 'history.purchases'
    ]);

    Route::get(Lang::get('routes.account.votes'), [
        'middleware' => 'auth',
        'uses'       => 'AccountController@votes',
        'as'         => 'history.votes'
    ]);

    Route::get(Lang::get('routes.account.market'), [
        'middleware' => 'auth',
        'uses'       => 'AccountController@market',
        'as'         => 'history.market'
    ]);

    Route::get(Lang::get('routes.account.activation'), [
        'middleware' => 'guest',
        'uses'       => 'AccountController@activation',
        'as'         => 'activation'
    ]);

    Route::post(Lang::get('routes.account.re_send_email'), [
        'uses'       => 'AccountController@re_send_email',
        'as'         => 're-send-email'
    ]);

    Route::get(Lang::get('routes.account.password_lost'), [
        'middleware' => 'guest',
        'uses'       => 'AccountController@password_lost',
        'as'         => 'password-lost'
    ]);

    Route::post(Lang::get('routes.account.password_lost'), [
        'middleware' => 'guest',
        'uses'       => 'AccountController@passord_lost_email',
        'as'         => 'password-lost'
    ]);

    Route::get(Lang::get('routes.account.reset'), [
        'uses'       => 'AccountController@reset_form',
        'as'         => 'reset'
    ]);

    Route::post(Lang::get('routes.account.reset'), [
        'middleware' => 'auth',
        'uses'       => 'AccountController@reset_password',
        'as'         => 'reset'
    ]);

    Route::any(Lang::get('routes.account.change_email'), [
        'middleware' => 'auth',
        'uses'       => 'AccountController@change_email',
        'as'         => 'account.change_email'
    ]);

    Route::any(Lang::get('routes.account.change_password'), [
        'middleware' => 'auth',
        'uses'       => 'AccountController@change_password',
        'as'         => 'account.change_password'
    ]);

    Route::any(Lang::get('routes.account.change_profile'), [
        'middleware' => 'auth',
        'uses'       => 'AccountController@change_profile',
        'as'         => 'account.change_profile'
    ]);

    Route::any(Lang::get('routes.account.certify'), [
        'middleware' => 'auth',
        'uses'       => 'AccountController@certify',
        'as'         => 'account.certify'
    ]);

    Route::get(Lang::get('routes.account.valid_email'), [
        'middleware' => 'auth',
        'uses'       => 'AccountController@valid_email',
        'as'         => 'account.valid-email'
    ]);

    /* ============ GAME ACCOUNT ============ */

    Route::get(Lang::get('routes.gameaccount.create'), [
        'middleware' => 'auth',
        'uses'       => 'GameAccountController@create',
        'as'         => 'gameaccount.create'
    ]);

    Route::post(Lang::get('routes.gameaccount.create'), [
        'middleware' => 'auth',
        'uses'       => 'GameAccountController@store',
        'as'         => 'gameaccount.create'
    ]);

    Route::get(Lang::get('routes.gameaccount.view'), [
        'middleware' => 'auth',
        'uses'       => 'GameAccountController@view',
        'as'         => 'gameaccount.view'
    ]);

    Route::any(Lang::get('routes.gameaccount.edit'), [
        'middleware' => 'auth',
        'uses'       => 'GameAccountController@edit',
        'as'         => 'gameaccount.edit'
    ]);

    Route::any(Lang::get('routes.gameaccount.transfert'), [
        'middleware' => 'auth',
        'uses'       => 'GameAccountController@transfert',
        'as'         => 'gameaccount.transfert'
    ]);

    Route::any(Lang::get('routes.gameaccount.jetons'), [
        'middleware' => 'auth',
        'uses'       => 'GameAccountController@jetons',
        'as'         => 'gameaccount.jetons'
    ]);

    Route::any(Lang::get('routes.gameaccount.gifts'), [
        'middleware' => 'auth',
        'uses'       => 'GameAccountController@gifts',
        'as'         => 'gameaccount.gifts'
    ]);

    /* ============ CHARACTERS ============ */

    Route::get(Lang::get('routes.characters.view'), [
        'middleware' => 'auth',
        'uses'       => 'CharactersController@view',
        'as'         => 'characters.view'
    ]);

    Route::get(Lang::get('routes.characters.caracteristics'), [
        'middleware' => 'auth',
        'uses'       => 'CharactersController@caracteristics',
        'as'         => 'characters.caracteristics'
    ]);

    Route::get(Lang::get('routes.characters.inventory'), [
        'middleware' => 'auth',
        'uses'       => 'CharactersController@inventory',
        'as'         => 'characters.inventory'
    ]);

    Route::get(Lang::get('routes.characters.settings'), [
        'middleware' => 'auth',
        'uses'       => 'CharactersController@settings',
        'as'         => 'characters.settings'
    ]);

    Route::post(Lang::get('routes.characters.settings'), [
        'middleware' => 'auth',
        'uses'       => 'CharactersController@settings',
        'as'         => 'characters.settings'
    ]);

    Route::any(Lang::get('routes.characters.recover'), [
        'middleware' => 'auth',
        'uses'       => 'CharactersController@recover',
        'as'         => 'characters.recover'
    ]);

    /* ============ GUILDS ============ */

    Route::get(Lang::get('routes.guilds.view'), [
        'middleware' => 'auth',
        'uses'       => 'GuildController@view',
        'as'         => 'guild.view'
    ]);

    Route::get(Lang::get('routes.guilds.members'), [
        'middleware' => 'auth',
        'uses'       => 'GuildController@members',
        'as'         => 'guild.members'
    ]);

    /* ============ AUTH ============ */

    Route::get(Lang::get('routes.account.login'), [
        'middleware' => 'guest',
        'uses'       => 'AuthController@login',
        'as'         => 'login'
    ]);

    Route::post(Lang::get('routes.account.login'), [
        'middleware' => 'guest',
        'uses'       => 'AuthController@auth',
        'as'         => 'login'
    ]);

    Route::get(Lang::get('routes.account.logout'), [
        'middleware' => 'auth',
        'uses'       => 'AuthController@logout',
        'as'         => 'logout'
    ]);

    /* ============ SHOP ============ */

    Route::get(Lang::get('routes.shop.index'), [
        'middleware' => ['auth'],
        'uses'       => 'ShopController@index',
        'as'         => 'shop.index'
    ]);
    
    Route::get(Lang::get('routes.shop.market'), [
        'middleware' => ['auth', 'marketMaintenance'],
        'uses'       => 'ShopController@market',
        'as'         => 'shop.market'
    ]);

    Route::get(Lang::get('routes.shop.market.sell'), [
        'middleware' => ['auth', 'marketMaintenance'],
        'uses'       => 'ShopController@marketSell',
        'as'         => 'shop.market.sell'
    ]);

    Route::post(Lang::get('routes.shop.market.sell'), [
        'middleware' => ['auth', 'marketMaintenance'],
        'uses'       => 'ShopController@marketSell',
        'as'         => 'shop.market.sell'
    ]);

    Route::delete(Lang::get('routes.shop.market.remove'), [
        'middleware' => ['auth', 'marketMaintenance'],
        'uses'       => 'ShopController@marketRemove',
        'as'         => 'shop.market.remove'
    ]);

    Route::get(Lang::get('routes.shop.market.buy'), [
        'middleware' => ['auth', 'marketMaintenance'],
        'uses'       => 'ShopController@marketBuy',
        'as'         => 'shop.market.buy'
    ]);

    Route::post(Lang::get('routes.shop.market.buy'), [
        'middleware' => ['auth', 'marketMaintenance'],
        'uses'       => 'ShopController@marketBuy',
        'as'         => 'shop.market.buy'
    ]);

    Route::get(Lang::get('routes.shop.payment.choose-country'), [
        'middleware' => ['auth', 'ShopMaintenance'],
        'uses'       => 'PaymentController@country',
        'as'         => 'shop.payment.country'
    ]);

    Route::get(Lang::get('routes.shop.payment.choose-method'), [
        'middleware' => ['auth', 'ShopMaintenance'],
        'uses'       => 'PaymentController@method',
        'as'         => 'shop.payment.method'
    ]);

    Route::get(Lang::get('routes.shop.payment.choose-palier'), [
        'middleware' => ['auth', 'ShopMaintenance'],
        'uses'       => 'PaymentController@palier',
        'as'         => 'shop.payment.palier'
    ]);

    Route::any(Lang::get('routes.shop.payment.get-code'), [
        'middleware' => ['auth', 'ShopMaintenance'],
        'uses'       => 'PaymentController@code',
        'as'         => 'shop.payment.code'
    ]);

    Route::post(Lang::get('routes.shop.payment.process'), [
        'middleware' => ['auth', 'ShopMaintenance'],
        'uses'       => 'PaymentController@process',
        'as'         => 'shop.payment.process'
    ]);

    Route::get('shop/maintenance', function () {
        return view('shop.payment.maintenance');
    });

    /* ============ VOTE ============ */

    Route::get(Lang::get('routes.vote.index'), [
        'middleware' => 'auth',
        'uses'       => 'VoteController@index',
        'as'         => 'vote.index'
    ]);

    Route::get(Lang::get('routes.vote.confirm'), [
        'middleware' => 'auth',
        'uses'       => 'VoteController@confirm',
        'as'         => 'vote.confirm'
    ]);

    Route::post(Lang::get('routes.vote.process'), [
        'middleware' => 'auth',
        'uses'       => 'VoteController@process',
        'as'         => 'vote.process'
    ]);

    Route::get(Lang::get('routes.vote.palier'), [
        'middleware' => 'auth',
        'uses'       => 'VoteController@palier',
        'as'         => 'vote.palier'
    ]);

    Route::get(Lang::get('routes.vote.object'), [
        'middleware' => 'auth',
        'uses'       => 'VoteController@object',
        'as'         => 'vote.object'
    ]);

    Route::get(Lang::get('routes.vote.go'), [
        'uses'       => 'VoteController@go',
        'as'         => 'vote.go'
    ]);

    Route::get(Lang::get('routes.vote.callback'), [
        'uses'       => 'VoteController@callback',
        'as'         => 'vote.callback'
    ]);

    /* ============ LOTTERY ============ */

    Route::get(Lang::get('routes.lottery.index'), [
        'middleware' => ['auth', 'lottery'],
        'uses'       => 'LotteryController@index',
        'as'         => 'lottery.index'
    ]);

    Route::get(Lang::get('routes.lottery.servers'), [
        'middleware' => ['auth', 'lottery'],
        'uses'       => 'LotteryController@servers',
        'as'         => 'lottery.servers'
    ]);

    Route::get(Lang::get('routes.lottery.draw'), [
        'middleware' => ['auth', 'lottery'],
        'uses'       => 'LotteryController@draw',
        'as'         => 'lottery.draw'
    ]);

    Route::get(Lang::get('routes.lottery.process'), [
        'middleware' => ['auth', 'lottery'],
        'uses'       => 'LotteryController@process',
        'as'         => 'lottery.process'
    ]);

    /* ============ LADDER ============ */

    Route::get(Lang::get('routes.ladder.general'), [
        'uses' => 'LadderController@general',
        'as'   => 'ladder.general'
    ]);

    Route::get(Lang::get('routes.ladder.pvp'), [
        'uses' => 'LadderController@pvp',
        'as'   => 'ladder.pvp'
    ]);

    Route::get(Lang::get('routes.ladder.guild'), [
        'uses' => 'LadderController@guild',
        'as'   => 'ladder.guild'
    ]);

    Route::get(Lang::get('routes.ladder.kolizeum'), [
        'uses' => 'LadderController@kolizeum',
        'as'   => 'ladder.kolizeum'
    ]);

    Route::get(Lang::get('routes.ladder.kolizeum1v1'), [
        'uses' => 'LadderController@kolizeum1v1',
        'as'   => 'ladder.kolizeum1v1'
    ]);

    Route::get(Lang::get('routes.ladder.kolizeum.seasons'), [
        'uses' => 'LadderController@kolizeumSeasons',
        'as'   => 'ladder.kolizeum.seasons'
    ]);

    Route::get(Lang::get('routes.ladder.kolizeum1v1.seasons'), [
        'uses' => 'LadderController@kolizeum1v1Seasons',
        'as'   => 'ladder.kolizeum1v1.seasons'
    ]);

    Route::get(Lang::get('routes.ladder.kolizeum.season'), [
        'uses' => 'LadderController@kolizeumSeason',
        'as'   => 'ladder.kolizeum.season'
    ]);

    Route::get(Lang::get('routes.ladder.kolizeum1v1.season'), [
        'uses' => 'LadderController@kolizeum1v1Season',
        'as'   => 'ladder.kolizeum1v1.season'
    ]);

    /* ============ OTHERS ============ */

    Route::get(Lang::get('routes.download'), [
        'uses' => 'PageController@download',
        'as'   => 'download'
    ]);

    Route::get(Lang::get('routes.servers'), [
        'uses' => 'PageController@servers',
        'as'   => 'servers'
    ]);
    
    Route::get('news.rss', [
        'uses' => 'RssController@news'
    ]);

    /* ============ SUPPORT ============ */

    Route::group(['middleware' => ['auth'], 'prefix' => '/support'], function () {

        Route::get('/', [
            'uses' => 'SupportController@index',
            'as'   => 'support'
        ]);
        
        Route::get('child/{child}/{params?}', [
            'uses'       => 'SupportController@child'
        ]);
            
        Route::post('store', [
            'uses' => 'SupportController@store',
            'as' => 'support.store'
        ]);

        Route::get('closed', [
            'uses' => 'SupportController@closed',
            'as'   => 'support.closed'
        ]);

        Route::get('create', [
            'uses' => 'SupportController@create',
            'as' => 'support.create'
        ]);
        
        Route::group(['prefix' => '/ticket/{id}'], function () {
            
            Route::get('/', [
                'uses' => 'SupportController@show',
                'as'   => 'support.show'
            ])->where('id', '[0-9]+');

            Route::patch('switch', [
                'uses' => 'SupportController@switchStatus',
                'as'   => 'support.switch'
            ])->where('id', '[0-9]+');

            Route::post('post', [
                'uses' => 'SupportController@postMessage',
                'as'   => 'support.message.post'
            ])->where('id', '[0-9]+');
        });
    });

    /* ============ SITEMAP ============ */

    Route::get('sitemap.xml', function () {
        $sitemap = App::make("sitemap");
        $sitemap->setCache('laravel.sitemap', 60);

        if (!$sitemap->isCached()) {
            $sitemap->add(URL::route('home'), date('c', time()), '1.0', 'daily');
            $sitemap->add(URL::route('register'), date('c', time()), '0.9', 'daily');
            $sitemap->add(URL::route('download'), date('c', time()), '0.9', 'daily');
            $sitemap->add(URL::route('login'), date('c', time()), '0.9', 'daily');
            $sitemap->add(URL::route('posts'), date('c', time()), '0.8', 'daily');
            $sitemap->add(URL::route('ladder.general', ['sigma']), date('c', time()), '0.5', 'daily');
            $sitemap->add(URL::route('ladder.pvp', ['sigma']), date('c', time()), '0.5', 'daily');
            $sitemap->add(URL::route('ladder.guild', ['sigma']), date('c', time()), '0.5', 'daily');
            $sitemap->add(URL::route('ladder.general', ['epsilon']), date('c', time()), '0.5', 'daily');
            $sitemap->add(URL::route('ladder.pvp', ['epsilon']), date('c', time()), '0.5', 'daily');
            $sitemap->add(URL::route('ladder.guild', ['epsilon']), date('c', time()), '0.5', 'daily');
            $sitemap->add(URL::route('ladder.kolizeum', ['sigma']), date('c', time()), '0.5', 'daily');
            $sitemap->add(URL::route('ladder.kolizeum', ['epsilon']), date('c', time()), '0.5', 'daily');
            $sitemap->add(URL::route('ladder.kolizeum1v1', ['epsilon']), date('c', time()), '0.5', 'daily');
            $sitemap->add(URL::route('servers'), date('c', time()), '0.3', 'weekly');

            $posts = \DB::table('posts')->where('published', 1)->where('published_at', '<=', Carbon\Carbon::now())->orderBy('updated_at', 'desc')->get();

            foreach ($posts as $post) {
                $images = [];

                $images[] = [
                    'url'     => URL::asset($post->image),
                    'title'   => $post->title,
                    'caption' => html_entity_decode(strip_tags($post->preview)),
                ];

                $sitemap->add(URL::route('posts.show', [$post->id, $post->slug]), $post->updated_at, '0.8', 'daily', $images);
            }
        }

        return $sitemap->render('xml');
    });
});

/* ============ FAKE CODE PAYMENT ============ */

Route::group(['domain' => Config::get('dofus.domain.fake')], function () {

    Route::get('/', function () {
        return "Coming Soon";
    });

    Route::get('code', [
        'middleware' => 'AuthPayment',
        'uses'       => 'PaymentController@fake',
        'as'         => 'code'
    ]);

    Route::post('code', [
        'middleware' => 'AuthPayment',
        'uses'       => 'PaymentController@fake_process',
        'as'         => 'code'
    ]);

    Route::get('code/cb', [
        'middleware' => 'AuthPayment',
        'uses'       => 'PaymentController@fake_starpass_cb',
        'as'         => 'code_starpass_cb'
    ]);

    Route::get('error/{code?}', [
        'uses'       => 'PageController@error_fake',
        'as'         => 'error.fake'
    ])->where('code', '[0-9]+');

    Route::get('code/cb_re/{key?}/{palier?}', [
        'middleware' => 'AuthPayment',
        'uses'       => 'PaymentController@redirect_recursos_cb',
        'as'         => 'redirect_recursos_cb'
    ]);

    Route::get('code/code_re/{key}', [
        'middleware' => 'AuthPayment',
        'uses'       => 'PaymentController@check_recursos_code',
        'as'         => 'check_recursos_code'
    ]);

    Route::get('code/code_re_fallback', [
        'middleware' => 'AuthPayment',
        'uses'       => 'PaymentController@code_re_fallback',
        'as'         => 'code_re_fallback'
    ]);

    Route::any('code/code_re_fallback_process', [
        'middleware' => 'AuthPayment',
        'uses'       => 'PaymentController@code_re_fallback_process',
        'as'         => 'code_re_fallback_process'
    ]);
	
	Route::any('code/code_re_callback', [
        'uses'       => 'PaymentController@code_re_callback',
        'as'         => 'code_re_callback'
    ]);
});

/* ============ FORGE ============ */

Route::group(['prefix' => 'forge', 'domain' => Config::get('dofus.domain.main')], function () {
    Route::get('image/{request}', 'ForgeController@image')->where('request', '(.*)');
    
    Route::get('text/{id}', 'ForgeController@text')->where('id', '[0-9]+');
});

/* ============ LINKER ============ */

Route::group(['prefix' => 'linker', 'domain' => Config::get('dofus.domain.main')], function () {
    Route::get('/{request}', 'LinkerController@get')->where('request', '(.*)')->name('linker.get');
});

/* ============ Utils ============ */

Route::group(['prefix' => 'utils', 'domain' => Config::get('dofus.domain.main')], function () {
    Route::get('/{server}/{name}', 'UtilsController@checkNameAvailability')->where('name', '^[A-Z][a-z]{2,9}(?:-[A-Za-z][a-z]{2,9}|[a-z]{1,10})$')->name('utils.checknameavailability');
});

/* ============ ADMIN PANEL ============ */

Route::group(['middleware' => ['auth', 'staff']], function () {

    Route::group(['prefix' => 'filemanager', 'middleware' => 'can:manage-filemanager'], function () {
        Route::get('show', 'FilemanagerLaravelController@getShow');
        Route::get('connectors', 'FilemanagerLaravelController@getConnectors');
        Route::post('connectors', 'FilemanagerLaravelController@postConnectors');
    });

    /* ============ ADMIN PREFIX ============ */
    Route::group(['namespace' => 'Admin', 'prefix' => 'admin', 'domain' => Config::get('dofus.domain.main')], function () {

        Route::any('/', [
            'uses' => 'AdminController@index',
            'as'   => 'admin.dashboard'
        ]);

        // ACCOUNT //
        Route::group(['prefix' => 'account'], function () {
            Route::group(['middleware' => 'can:manage-account'], function () {
                Route::get('/', [
                    'uses' => 'AccountController@index',
                    'as'   => 'admin.account'
                ]);

                Route::patch('/update', [
                    'uses' => 'AccountController@accountUpdate',
                    'as'   => 'admin.account.update'
                ]);

                route::patch('/avatar/reset', [
                    'uses' => 'AccountController@resetAvatar',
                    'as'   => 'admin.account.avatar.reset'
                ]);

                Route::get('/password', [
                    'uses' => 'AccountController@password',
                    'as'   => 'admin.password'
                ]);

                Route::patch('/password/update', [
                    'uses' => 'AccountController@passwordUpdate',
                    'as'   => 'admin.password.update'
                ]);
            });
            Route::group(['middleware' => 'can:manage-support'], function () {
                Route::get('/settings', [
                    'uses' => 'AccountController@settings',
                    'as'   => 'admin.account.settings'
                ]);            
                Route::patch('/settings', [
                    'uses' => 'AccountController@settingsUpdate',
                    'as'   => 'admin.account.settings.update'
                ]);

                Route::post('/settings/template/add', [
                    'uses' => 'AccountController@templateAdd',
                    'as'   => 'admin.account.settings.template.post'
                ]);

                Route::get('/settings/template/edit/{templateTitle}', [
                    'uses' => 'AccountController@templateEdit',
                    'as'   => 'admin.account.settings.template.edit'
                ]);

                Route::patch('/settings/template/edit/{templateTitle}', [
                    'uses' => 'AccountController@templateUpdate',
                    'as'   => 'admin.account.settings.template.update'
                ]);
                Route::delete('/settings/template/delete/{templateTitle}', [
                    'uses' => 'AccountController@templateDestroy',
                    'as'   => 'admin.account.settings.template.destroy'
                ]);
            });
        });

        // POSTS //
        Route::group(['prefix' => 'posts', 'middleware' => 'can:manage-posts'], function () {

            Route::get('data', [
                'uses' => 'PostDatatablesController@anyData',
                'as'  => 'datatables.postdata'
            ]);

        });

        Route::resource('post', 'PostController', ['middleware' => 'can:manage-posts', 'names' => [
            'index'   => 'admin.posts', // GET Index
            'create'  => 'admin.post.create', // GET Create
            'store'   => 'admin.post.store', // POST Store (create POST)
            'destroy' => 'admin.post.destroy', // DELETE
            'edit'    => 'admin.post.edit', // GET Edit (view) /post/ID/edit
            'update'  => 'admin.post.update' // PUT OU PATCH for update the edit
        ]]);

        // TASKS //
        Route::group(['prefix' => 'task', 'middleware' => 'can:manage-tasks'], function () {
            Route::patch('updatePositions', [
                'uses' => 'TaskController@updatePositions',
                'as'   => 'admin.task.update.positions'
            ]);
            Route::patch('updateModal', [
                'uses' => 'TaskController@updateModal',
                'as'   => 'admin.task.update.modal'
            ]);
        });

        Route::resource('task', 'TaskController', ['middleware' => 'can:manage-tasks', 'names' => [
            'index'   => 'admin.tasks', // GET Index
            'create'  => 'admin.task.create', // GET Create
            'store'   => 'admin.task.store', // POST Store (create TASK)
            'destroy' => 'admin.task.destroy', // DELETE
            'edit'    => 'admin.task.edit', // GET Edit (view) /task/ID/edit
            'update'  => 'admin.task.update' // PUT OU PATCH for update the edit
        ]]);

        // USERS //
        Route::group(['middleware' => 'can:manage-users'], function () {
            Route::get('/users/data', [
                'uses' => 'UserDatatablesController@anyData',
                'as'  => 'datatables.userdata'
            ]);
            Route::group(['prefix' => 'user/{user}'], function () {

                // Users actions
                Route::patch('ban', [
                    'uses' => 'UserController@ban',
                    'as'   => 'admin.user.ban'
                ])->where('user', '[0-9]+');

                Route::patch('unban', [
                    'uses' => 'UserController@unban',
                    'as'   => 'admin.user.unban'
                ])->where('user', '[0-9]+');

                Route::patch('activate', [
                    'uses' => 'UserController@activate',
                    'as'   => 'admin.user.activate'
                ])->where('user', '[0-9]+');

                Route::patch('decertify', [
                    'uses' => 'UserController@decertify',
                    'as'   => 'admin.user.decertify'
                ])->where('user', '[0-9]+');

                Route::patch('certify', [
                    'uses' => 'UserController@certify',
                    'as'   => 'admin.user.certify'
                ])->where('user', '[0-9]+');

                Route::patch('password', [
                    'uses' => 'UserController@password',
                    'as'   => 'admin.user.password'
                ])->where('user', '[0-9]+');

                Route::patch('avatar/reset', [
                    'uses' => 'UserController@resetAvatar',
                    'as'   => 'admin.user.reset.avatar'
                ])->where('user', '[0-9]+');

                Route::patch('avatar/reset', [
                    'uses' => 'UserController@resetAvatar',
                    'as'   => 'admin.user.reset.avatar'
                ])->where('user', '[0-9]+');

                Route::post('re-send-email', [
                    'uses'       => 'UserController@re_send_email',
                    'as'         => 're-send-email-admin'
                ]);

                Route::post('addticket', [
                    'uses'       => 'UserController@addTicket',
                    'as'         => 'admin.user.addticket'
                ]);

                // Game Accounts
                Route::group(['prefix' => 'server/{server}'], function () {

                    // Index
                    Route::get('/', [
                        'uses' => 'GameAccountController@index',
                        'as'   => 'admin.user.game.accounts'
                    ])->where('user', '[0-9]+');
                    // Store
                    Route::post('/store', [
                        'uses' => 'GameAccountController@store',
                        'as'   => 'admin.user.game.account.store'
                    ])->where('user', '[0-9]+');
                    // Edit (view)
                    Route::get('/{id}/edit', [
                        'uses' => 'GameAccountController@edit',
                        'as'   => 'admin.user.game.account.edit'
                    ])->where('user', '[0-9]+')->where('id', '[0-9]+');
                    // Ban
                    Route::patch('/{id}/ban', [
                        'uses' => 'GameAccountController@ban',
                        'as'   => 'admin.user.game.account.ban'
                    ])->where('user', '[0-9]+')->where('id', '[0-9]+');
                    // Unban
                    Route::patch('/{id}/unban', [
                        'uses' => 'GameAccountController@unban',
                        'as'   => 'admin.user.game.account.unban'
                    ])->where('user', '[0-9]+')->where('id', '[0-9]+');
                    // Jail
                    Route::patch('/{id}/jail', [
                        'uses' => 'GameAccountController@jail',
                        'as'   => 'admin.user.game.account.jail'
                    ])->where('user', '[0-9]+')->where('id', '[0-9]+');
                    // Unjail
                    Route::patch('/{id}/unjail', [
                        'uses' => 'GameAccountController@unjail',
                        'as'   => 'admin.user.game.account.unjail'
                    ])->where('user', '[0-9]+')->where('id', '[0-9]+');
                    // Unjail
                    Route::patch('/{id}/password', [
                        'uses' => 'GameAccountController@password',
                        'as'   => 'admin.user.game.account.password'
                    ])->where('user', '[0-9]+')->where('id', '[0-9]+');
                    // Update
                    Route::patch('/{id}', [
                        'uses' => 'GameAccountController@update',
                        'as'   => 'admin.user.game.account.update'
                    ])->where('user', '[0-9]+')->where('id', '[0-9]+');
                });
            });

            Route::resource('user', 'UserController', ['names' => [
                'index'   => 'admin.users', // GET Index
                'create'  => 'admin.user.create', // GET Create
                'store'   => 'admin.user.store', // POST Store (create TASK)
                'destroy' => 'admin.user.destroy', // DELETE
                'edit'    => 'admin.user.edit', // GET Edit (view) /user/ID/edit
                'update'  => 'admin.user.update' // PUT OU PATCH for update the edit
            ]]);
         });

        // CHARACTERS //
        Route::group(['prefix' => 'characters', 'middleware' => 'can:manage-characters'], function () {
            Route::get('data/{server}', [
                'uses' => 'CharacterDatatablesController@anyData',
                'as'  => 'datatables.charactersdata'
            ]);

            Route::get('{server}', [
                'uses' => 'CharacterController@index',
                'as'   => 'admin.characters'
            ]);
        });

        // SETTINGS //
        Route::group(['prefix' => 'settings', 'middleware' => 'can:manage-settings'], function () {
            Route::get('/', [
                'uses' => 'SettingsController@index',
                'as'   => 'admin.settings'
            ]);
            Route::patch('update', [
                'uses' => 'SettingsController@update',
                'as'   => 'admin.settings.update'
            ]);
        });
        // ANNOUNCES //
        Route::group(['prefix' => 'announces/{server}', 'middleware' => 'can:manage-announces'], function () {

            // Users actions
            Route::get('/', [
                'uses' => 'AnnounceController@index',
                'as'   => 'admin.announces'
            ]);

            Route::get('/create', [
                'uses' => 'AnnounceController@create',
                'as'   => 'admin.announce.create'
            ]);

            Route::post('/', [
                'uses' => 'AnnounceController@store',
                'as'   => 'admin.announce.store'
            ]);

            Route::delete('/{Id}', [
                'uses' => 'AnnounceController@destroy',
                'as'   => 'admin.announce.destroy'
            ])->where('Id', '[0-9]+');
            ;

            Route::get('/{Id}/edit', [
                'uses' => 'AnnounceController@edit',
                'as'   => 'admin.announce.edit'
            ])->where('Id', '[0-9]+');
            ;

            Route::patch('/{Id}', [
                'uses' => 'AnnounceController@update',
                'as'   => 'admin.announce.update'
            ])->where('Id', '[0-9]+');
            ;
        });

        // ROLES //
        Route::group(['prefix' => 'roles', 'middleware' => 'can:manage-roles'], function () {

            // Users actions
            Route::get('/', [
                'uses' => 'RoleController@index',
                'as'   => 'admin.roles'
            ]);

            Route::post('/', [
                'uses' => 'RoleController@store',
                'as'   => 'admin.role.store'
            ]);

            Route::get('/create', [
                'uses' => 'RoleController@create',
                'as'   => 'admin.role.create'
            ]);

            Route::delete('/{id}', [
                'uses' => 'RoleController@destroy',
                'as'   => 'admin.role.destroy'
            ])->where('id', '[0-9]+');

            Route::patch('/{id}', [
                'uses' => 'RoleController@update',
                'as'   => 'admin.role.update'
            ])->where('id', '[0-9]+');

            Route::get('/{id}/edit', [
                'uses' => 'RoleController@edit',
                'as'   => 'admin.role.edit'
            ])->where('id', '[0-9]+');

            Route::get('/{id}/permissions', [
                'uses' => 'RoleController@permissions',
                'as'   => 'admin.role.permissions'
            ])->where('id', '[0-9]+');

            Route::delete('/{id}/permissions/remove', [
                'uses' => 'RoleController@permissionRemove',
                'as'   => 'admin.role.permission.remove'
            ])->where('id', '[0-9]+');

            Route::post('/{id}/permissions/add', [
                'uses' => 'RoleController@permissionAdd',
                'as'   => 'admin.role.permission.add'
            ])->where('id', '[0-9]+');
        });

        // TRANSACTIONS //
        Route::group(['prefix' => 'transactions', 'middleware' => 'can:manage-transactions'], function () {

            Route::get('/', [
                'uses' => 'TransactionController@index',
                'as'   => 'admin.transactions'
            ]);

            Route::get('data', [
                'uses' => 'TransactionDatatablesController@anyData',
                'as'  => 'datatables.transactionsdata'
            ]);

            Route::get('getdata', [
                'uses' => 'TransactionController@getData',
                'as'   => 'admin.transactions.getdata'
            ]);
        });

        // TICKETS //
        Route::group(['middleware' => 'can:manage-lottery'], function () {
            Route::get('/lotterytickets/data', [
                'uses' => 'LotteryTicketDatatablesController@anyData',
                'as'  => 'datatables.lotteryticketsdata'
            ]);

            Route::get('lottery/tickets', [
                'uses' => 'LotteryController@tickets',
                'as'   => 'admin.lottery.tickets'
            ]);

            Route::get('lottery/{lottery}/items/{serverId}/{itemid}/search', [
                'uses' => 'LotteryItemController@getItemData',
                'as'   => 'admin.lottery.item.getdata'
            ])->where('itemid', '[0-9]+');

            Route::resource('lottery/{lottery}/items', 'LotteryItemController', ['only' => [
                'index', 'store', 'update', 'destroy'], 'names' => [
                'index'   => 'admin.lottery.items',
                'store'   => 'admin.lottery.item.store',
                'update'  => 'admin.lottery.item.update',
                'destroy' => 'admin.lottery.item.destroy'
                ]]);

            Route::resource('lottery', 'LotteryController', ['only' => [
                'index', 'create', 'store', 'edit', 'update'], 'names' => [
                'index'   => 'admin.lottery',
                'create'  => 'admin.lottery.create',
                'store'   => 'admin.lottery.store',
                'edit'    => 'admin.lottery.edit',
                'update'  => 'admin.lottery.update'
            ]]);
        });
        // SUPPORT //
        Route::group(['prefix' => 'support', 'middleware' => 'can:manage-support'], function () {

            //Support actions
            Route::get('/', [
                'uses' => 'SupportController@index',
                'as'   => 'admin.support'
            ]);

            Route::get('opendata', [
            'uses' => 'SupportDatatablesController@anyDataOpen',
            'as'  => 'datatables.supportopendata'
             ]);

            Route::get('closeddata', [
                'uses' => 'SupportDatatablesController@anyDataClosed',
                'as'  => 'datatables.supportcloseddata'
            ]);

            Route::get('minedata', [
                'uses' => 'SupportDatatablesController@anyDataMine',
                'as'  => 'datatables.supportminedata'
            ]);


            Route::get('/closed', [
                'uses' => 'SupportController@closedTickets',
                'as'   => 'admin.support.closed'
            ]);
            Route::get('/mytickets', [
            'uses' => 'SupportController@myTickets',
            'as'   => 'admin.support.mytickets'
            ]);

            //Specific ticket actions
            Route::group(['prefix' => '/ticket/{id}'], function () {
                Route::get('/', [
                'uses' => 'SupportController@show',
                'as'   => 'admin.support.ticket.show'
                ])->where('id', '[0-9]+');

                Route::post('/post', [
                'uses' => 'SupportController@postMessage',
                'as'   => 'admin.support.ticket.post.message'
                ])->where('id', '[0-9]+');
           
                Route::patch('/switch', [
                'uses' => 'SupportController@switchStatus',
                'as'   => 'admin.support.ticket.switch.status'
                ])->where('id', '[0-9]+');

                Route::patch('/take', [
                'uses' => 'SupportController@take',
                'as'   => 'admin.support.ticket.take'
                ])->where('id', '[0-9]+');
            
                Route::patch('/assignto', [
                'uses' => 'SupportController@assignTo',
                'as'   => 'admin.support.ticket.assignto'
                ])->where('id', '[0-9]+');
            });
        });
    });
});
