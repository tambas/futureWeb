<?php

return [

    'invalid_server' => [
        'status'  => 404,
        'title'   => 'Le serveur sélectionné est invalide',
        'details' => 'Le serveur sélectionné ne fait pas partie des serveurs disponible.',
    ],

    'not_account_owner' => [
        'status'  => 401,
        'title'   => 'Le compte sélectionné est invalide',
        'details' => 'Le compte sélectionné n\'est pas valide.',
    ],

    'owner_error' => [
        'status'  => 401,
        'title'   => 'Le compte ou personnage sélectionné est invalide',
        'details' => 'Le compte ou le personnage sélectionné n\'est pas valide.',
    ],

    'invalid_news_type' => [
        'status'  => 404,
        'title'   => 'Type de news invalide',
        'details' => 'Le type de news n\'éxiste pas.',
    ],

    'invalid_ticket' => [
        'status'  => 404,
        'title'   => 'Le ticket selectionné est invalide',
        'details' => 'Le ticket selectionné n\'existe pas ou ne vous appartient pas.',
    ],

    'ticket_no_objects' => [
        'status'  => 401,
        'title'   => 'Ticket indisponible',
        'details' => 'Le ticket selectionné n\'est pas encore disponible.',
    ],

    'email_token_invalid' => [
        'status'  => 401,
        'title'   => 'Clé de validation d\'email invalide',
        'details' => 'La clé de validation de la nouvelle adresse email est invalide.',
    ],

    'not_ticket_owner' => [
        'status'  => 401,
        'title'   => 'Le ticket sélectionné est invalide',
        'details' => 'Le ticket sélectionné n\'est pas valide.',
    ],

];
