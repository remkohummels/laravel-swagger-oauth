<?php /** @noinspection PhpCSFixerValidationInspection */

use App\Database\Eloquent\{
    CompanyQueryBuilder,
    MetaTypeQueryBuilder,
    TeamQueryBuilder,
    UserQueryBuilder};
use App\Providers\AuthServiceProvider;

return [
    'api' => [
        /*
        |--------------------------------------------------------------------------
        | Edit to set the api's title
        |--------------------------------------------------------------------------
        */

        'title' => env('APP_NAME'),
    ],

    'routes' => [
        /*
        |--------------------------------------------------------------------------
        | Route for accessing api documentation interface
        |--------------------------------------------------------------------------
        */

        'api' => 'api/documentation',

        /*
        |--------------------------------------------------------------------------
        | Route for accessing parsed swagger annotations.
        |--------------------------------------------------------------------------
        */

        'docs' => 'docs',

        /*
        |--------------------------------------------------------------------------
        | Route for Oauth2 authentication callback.
        |--------------------------------------------------------------------------
        */

        'oauth2_callback' => 'api/oauth2-callback',

        /*
        |--------------------------------------------------------------------------
        | Middleware allows to prevent unexpected access to API documentation
        |--------------------------------------------------------------------------
         */
        'middleware' => [
            'api' => [],
            'asset' => [],
            'docs' => [],
            'oauth2_callback' => [],
        ],
    ],

    'paths' => [
        /*
        |--------------------------------------------------------------------------
        | Absolute path to location where parsed swagger annotations will be stored
        |--------------------------------------------------------------------------
        */

        'docs' => storage_path('api-docs'),

        /*
        |--------------------------------------------------------------------------
        | File name of the generated json documentation file
        |--------------------------------------------------------------------------
        */

        'docs_json' => 'api-docs.json',

        /*
        |--------------------------------------------------------------------------
        | File name of the generated YAML documentation file
        |--------------------------------------------------------------------------
         */

        'docs_yaml' => 'api-docs.yaml',

        /*
        |--------------------------------------------------------------------------
        | Absolute path to directory containing the swagger annotations are stored.
        |--------------------------------------------------------------------------
        */

        'annotations' => base_path('app'),

        /*
        |--------------------------------------------------------------------------
        | Absolute path to directory where to export views
        |--------------------------------------------------------------------------
        */

        'views' => base_path('resources/views/vendor/l5-swagger'),

        /*
        |--------------------------------------------------------------------------
        | Edit to set the api's base path
        |--------------------------------------------------------------------------
        */

        'base' => env('L5_SWAGGER_BASE_PATH', null),

        /*
        |--------------------------------------------------------------------------
        | Absolute path to directories that you would like to exclude from swagger generation
        |--------------------------------------------------------------------------
        */

        'excludes' => [],
    ],

    /*
    |--------------------------------------------------------------------------
    | API security definitions. Will be generated into documentation file.
    |--------------------------------------------------------------------------
    */
    'security' => [
        /*
        |--------------------------------------------------------------------------
        | Examples of Security definitions
        |--------------------------------------------------------------------------
        */
        /*
        'api_key_security_example' => [ // Unique name of security
            'type' => 'apiKey', // The type of the security scheme. Valid values are "basic", "apiKey" or "oauth2".
            'description' => 'A short description for security scheme',
            'name' => 'api_key', // The name of the header or query parameter to be used.
            'in' => 'header', // The location of the API key. Valid values are "query" or "header".
        ],
        'oauth2_security_example' => [ // Unique name of security
            'type' => 'oauth2', // The type of the security scheme. Valid values are "basic", "apiKey" or "oauth2".
            'description' => 'A short description for oauth2 security scheme.',
            'flow' => 'implicit', // The flow used by the OAuth2 security scheme. Valid values are "implicit", "password", "application" or "accessCode".
            'authorizationUrl' => 'http://example.com/auth', // The authorization URL to be used for (implicit/accessCode)
            //'tokenUrl' => 'http://example.com/auth' // The authorization URL to be used for (password/application/accessCode)
            'scopes' => [
                'read:projects' => 'read your projects',
                'write:projects' => 'modify projects in your account',
            ]
        ],
        */

        /* Open API 3.0 support */
        'passport' => [ // Unique name of security
            'type' => 'oauth2', // The type of the security scheme. Valid values are "basic", "apiKey" or "oauth2".
            'description' => 'Password flow for mobile applications, authorizationCode flow for web applications',
            'scheme' => \Illuminate\Support\Str::before(env('L5_SWAGGER_CONST_HOST'), ':/'),
            'flows' => [
                'password' => [
                    'tokenUrl' => config('app.url') . '/oauth/token',
                    'refreshUrl' => config('app.url') . '/token/refresh',
                    'scopes' => AuthServiceProvider::getManageScopes(),
                ],
                'authorizationCode' => [
                    'authorizationUrl' => config('app.url') . '/oauth/authorize',
                    'tokenUrl' => config('app.url') . '/oauth/token',
                    'refreshUrl' => config('app.url') . '/token/refresh',
                    'scopes' => AuthServiceProvider::getManageScopes(),
                ],
                'clientCredentials' => [
                    'tokenUrl' => config('app.url') . '/oauth/token',
                    'refreshUrl' => config('app.url') . '/token/refresh',
                    'scopes' => AuthServiceProvider::getManageScopes(),
                ],
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Turn this off to remove swagger generation on production
    |--------------------------------------------------------------------------
    */

    'generate_always' => env('L5_SWAGGER_GENERATE_ALWAYS', false),

    /*
    |--------------------------------------------------------------------------
    | Turn this on to generate a copy of documentation in yaml format
    |--------------------------------------------------------------------------
     */

    'generate_yaml_copy' => env('L5_SWAGGER_GENERATE_YAML_COPY', false),

    /*
    |--------------------------------------------------------------------------
    | Edit to set the swagger version number
    |--------------------------------------------------------------------------
    */

    'swagger_version' => env('SWAGGER_VERSION', '3.0'),

    /*
    |--------------------------------------------------------------------------
    | Edit to trust the proxy's ip address - needed for AWS Load Balancer
    |--------------------------------------------------------------------------
    */

    'proxy' => false,

    /*
    |--------------------------------------------------------------------------
    | Configs plugin allows to fetch external configs instead of passing them to SwaggerUIBundle.
    | See more at: https://github.com/swagger-api/swagger-ui#configs-plugin
    |--------------------------------------------------------------------------
    */

    'additional_config_url' => null,

    /*
    |--------------------------------------------------------------------------
    | Apply a sort to the operation list of each API. It can be 'alpha' (sort by paths alphanumerically),
    | 'method' (sort by HTTP method).
    | Default is the order returned by the server unchanged.
    |--------------------------------------------------------------------------
    */

    'operations_sort' => env('L5_SWAGGER_OPERATIONS_SORT', null),

    /*
    |--------------------------------------------------------------------------
    | Uncomment to pass the validatorUrl parameter to SwaggerUi init on the JS
    | side.  A null value here disables validation.
    |--------------------------------------------------------------------------
    */

    'validator_url' => null,

    /*
    |--------------------------------------------------------------------------
    | Uncomment to add constants which can be used in anotations
    |--------------------------------------------------------------------------
     */
    'constants' => [
        'L5_SWAGGER_CONST_HOST'              => env('L5_SWAGGER_CONST_HOST', 'http://my-default-host.com'),
        'L5_SWAGGER_CONST_TERMS_OF_SERVICES' => env('L5_SWAGGER_CONST_TERMS_OF_SERVICES', 'http://my-default-host.com/terms'),
        'L5_SWAGGER_CONST_ENV'               => env('L5_SWAGGER_CONST_ENV', env('APP_NAME') . ' ' . env('APP_ENV')),
        'L5_SWAGGER_USER_INCLUDES' => UserQueryBuilder::getIncludesExample(),
        'L5_SWAGGER_USER_SORTS'    => UserQueryBuilder::getSortExample(),
        'L5_SWAGGER_USER_FILTERS'  => UserQueryBuilder::getFiltersExample(),
        'L5_SWAGGER_USER_FIELDS'   => UserQueryBuilder::getFieldsExample(),
        'L5_SWAGGER_COMPANY_INCLUDES' => CompanyQueryBuilder::getIncludesExample(),
        'L5_SWAGGER_COMPANY_SORTS'    => CompanyQueryBuilder::getSortExample(),
        'L5_SWAGGER_COMPANY_FILTERS'  => CompanyQueryBuilder::getFiltersExample(),
        'L5_SWAGGER_COMPANY_FIELDS'   => CompanyQueryBuilder::getFieldsExample(),
        'L5_SWAGGER_TEAM_INCLUDES'  => TeamQueryBuilder::getIncludesExample(),
        'L5_SWAGGER_TEAM_SORTS'     => TeamQueryBuilder::getSortExample(),
        'L5_SWAGGER_TEAM_FILTERS'   => TeamQueryBuilder::getFiltersExample(),
        'L5_SWAGGER_TEAM_FIELDS'    => TeamQueryBuilder::getFieldsExample(),
        'L5_SWAGGER_META_TYPE_INCLUDES'   => MetaTypeQueryBuilder::getIncludesExample(),
        'L5_SWAGGER_META_TYPE_SORTS'      => MetaTypeQueryBuilder::getSortExample(),
        'L5_SWAGGER_META_TYPE_FILTERS'    => MetaTypeQueryBuilder::getFiltersExample(),
        'L5_SWAGGER_META_TYPE_FIELDS'     => MetaTypeQueryBuilder::getFieldsExample(),
        'L5_SAGGER_READ_USERS'   => AuthServiceProvider::READ_USERS,
        'L5_SAGGER_MANAGE_USERS' => AuthServiceProvider::MANAGE_USERS,
        'L5_SAGGER_READ_COMPANIES'   => AuthServiceProvider::READ_COMPANIES,
        'L5_SAGGER_MANAGE_COMPANIES' => AuthServiceProvider::MANAGE_COMPANIES,
        'L5_SAGGER_READ_TEAMS'   => AuthServiceProvider::READ_TEAMS,
        'L5_SAGGER_MANAGE_TEAMS' => AuthServiceProvider::MANAGE_TEAMS,
        'L5_SAGGER_READ_META_TYPES'   => AuthServiceProvider::READ_META_TYPES,
        'L5_SAGGER_MANAGE_META_TYPES' => AuthServiceProvider::MANAGE_META_TYPES,

    ],
];
