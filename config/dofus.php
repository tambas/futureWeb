<?php

return [

    'title'        => env('TITLE', 'Dofus'),

    'subtitle'     => env('SUBTITLE', 'Serveur privé'),

    'description'  => "★ Découvrez Azote, le leader des serveurs privés Dofus et profitez d'une expérience de jeu incroyable. Jouez à Dofus gratuitement et sans abonnement !",

    'servers'      => [], // filled in FillServers middleware

    'details'      => [], // filled in FillServers middleware

    'motd'         => [], // filled in FillSettings middleware

    'theme'        => [ // modified in FillSettings middleware
        'background' => 'kolizeum',
        'color'      => '#3d301e',
        'animated'   => false,
    ],
    
    'welcome'         => [], // filled in FillSettings middleware

    'domain' => [
        'main' => env('MAIN_DOMAIN'),
        'fake' => env('FAKE_DOMAIN')
    ],

    'default_server_ladder' => env('DEFAULT_SERVER_LADDER', 'sigma'),

    'default_avatar' => 'imgs/avatar/default.jpg',

    'accounts_limit' => 8,

    'characters_limit' => 5,

    'recover_level_multiplicator_price' => 5,

    'recover_minimal_price' => 150,

    'email'        => env('SUPPORT_EMAIL', 'support@azote.us'),

    'mailgun_key'  => env('MAILGUN_KEY'),

    'api_key'      => env('API_KEY'),

    'lottery_maintenance' => env('LOTTERY_MAINTENANCE', true),

    'shop_maintenance' => env('SHOP_MAINTENANCE', true),

    'market_maintenance' => env('MARKET_MAINTENANCE', true),

    'download'     => [
        'mac' => 'http://dl.azote.us/Azote_Setup.dmg',
        'win' => 'http://dl.azote.us/Azote_Setup.exe',
    ],

    'certify'     => [
        'min_age'  => '10',
        'max_age'  => '100',
        'article'  => 'http://forum.azote.us/topic/26-compte-web-certifi%C3%A9-quest-ce/'
    ],

    'forum'       => [
        'domain'     => env('FORUM_DOMAIN', ''),
        'user_group' => env('FORUM_USER_GROUP', 3)
    ],

    'forum'       => [
        'domain'     => env('FORUM_DOMAIN', ''),
        'user_group' => env('FORUM_USER_GROUP', 3)
    ],

    'social'       => [
        'forum'    => env('SOCIAL_FORUM'),
        'facebook' => env('SOCIAL_FACEBOOK'),
        'twitter'  => env('SOCIAL_TWITTER'),
        'youtube'  => env('SOCIAL_YOUTUBE'),
        'discord'  => env('SOCIAL_DISCORD'),
    ],

    'points_by_vote'   => env('POINTS_BY_VOTES', 10),

    'votes_for_ticket' => env('VOTES_FOR_TICKET', 10),

    'rpg-paradize' => [
        'delay'        => 10810, // 3h + 5s
        'ip'           => env('RPG_IP', "0.0.0.0"),
        'check_ip'     => env('RPG_CHECK_IP', true),
        'use_callback' => env('RPG_USE_CALLBACK', false),
    ],

    'promos' => [],

    'payment' => [

        'used'       => env('PAYMENT', 'dedipass'),

        'used_first' => env('PAYMENT_FIRST', 'starpass'),

        'check_min_transactions' => env('PAYMENT_CHECK_MIN_TRANSACTIONS', true),
        'minimum_for_real' => env('PAYMENT_MINIMUM', 3),
        'check_level' => env('PAYMENT_CHECK_LEVEL', true),
        'level_for_real'   => env('PAYMENT_LEVEL', 50),

        'dedipass' => [
            'name'       => 'Dedipass',
            'url'        => 'https://api.dedipass.com/v1/pay/rates?key={PUBLIC_KEY}',
            'validation' => 'http://api.dedipass.com/v1/pay/?public_key={PUBLIC_KEY}&private_key={PRIVATE_KEY}&code={CODE}',
            'public'     => env('PAYMENT_PUBLIC', 0),
            'private'    => env('PAYMENT_PRIVATE', 0),
        ],

        'starpass' => [
            'name'       => 'Starpass',
            'url'        => 'https://script.starpass.fr/script.php?idd={IDD}',
            'validation' => 'http://script.starpass.fr/check_php.php?ident={KEY}&codes={CODE}',
            'config'     => env('STARPASS_CONFIG', null),
        ],

        'recursos' => [
            'name'       => 'Recursos',
            'c'          => env('RECURSOS_C', null),
            'c_sms'      => env('RECURSOS_C_SMS', null),
            'w'          => env('RECURSOS_W', null),
            'pricesCB'   => env('RECURSOS_PRICES_CB', null),
            'pricesPS'   => env('RECURSOS_PRICES_PS', null),
            'coeffCB'    => env('RECURSOS_COEFF_CB', null),
            'coeffPS'    => env('RECURSOS_COEFF_PS', null),
        ],
		
        'payfee' => [
            'name'       => 'PayFee',
            'private'    => env('PAYFEE_PRIVATE', null),
            'user'    	 => env('PAYFEE_USER', null),
            'rate'    	 => env('PAYFEE_RATE', null),
        ],
    ],

    'ranks'       => [
        '1'  => 'Player',
        '2'  => 'Moderator',
        '3'  => 'GameMaster_Padawan',
        '4'  => 'GameMaster',
        '5'  => 'Administrator',
    ],

    'news_type' => [
        'infos' => [
            'name' => 'Information',
            'title' => 'Toutes les infos',
            'db' => 'infos',
            'icon-big' => 'calendar'
        ],
        'patchs' => [
            'name' => 'Patch',
            'title' => 'Tous les patchs',
            'db' => 'patchs',
            'icon-big' => 'patchnote'
        ]
    ],

    'support'     => [
        'minutes_between_actions'  => '10',
    ],

    'characters_market'     => [
        'minimal_price'  => '1000',
        'procent_taxe'   => '5',
        'maximum_per_user' => '4'
    ]

];
