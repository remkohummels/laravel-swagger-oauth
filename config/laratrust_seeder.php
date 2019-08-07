<?php

use App\Providers\AuthServiceProvider;

return [
    'role_structure' => [
        AuthServiceProvider::ROLE_ADMINISTRATOR => [
            'users' => 'c,r,u,d',
            'companies' => 'c,r,u,d',
            'teams' => 'c,r,u,d',
            'acl' => 'c,r,u,d',
            'oauth' => 'c,r,u,d',
            'meta_types' => 'c,r,u,d',
        ],
        AuthServiceProvider::ROLE_CLIENT_APP => [
            'oauth' => 'c,r,u,d',
        ],
        AuthServiceProvider::ROLE_COMPANY => [
            'companies' => 'c,r,u,d',
            'users' => 'a,s',
            'profile' => 'r,u',
        ],
        AuthServiceProvider::ROLE_STAFF => [
            'companies' => 'r,u',
            'users' => 's',
        ],
        AuthServiceProvider::ROLE_USER => [
            'users' => 's',
        ],
    ],
    'permission_structure' => [],
    'permissions_map' => [
        'c' => 'create',
        'r' => 'read',
        'u' => 'update',
        'd' => 'delete',
        'a' => 'assign',
        's' => 'security'
    ]
];
