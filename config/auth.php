<?php

return [
    /**
     * ------------------------------------------------------------
     * Custom config
     * ------------------------------------------------------------
     */
    'customs' => [
        'expire_time_reset_password_token_in_minutes'  => env('AUTH_EXPIRE_TIME_RESET_PASSWORD_TOKEN_IN_MINUTES', 30),
        'expire_time_send_activation_token_in_minutes' => env('AUTH_EXPIRE_TIME_SEND_ACTIVATION_TOKEN_IN_MINUTES', 30),
        'only_one_session'                             => env('AUTH_ONLY_ONE_SESSION', true),
        'login_ldap_is_active'                         => env('AUTH_LOGIN_LDAP_IS_ACTIVE', true),
        'expire_session_time_in_minutes'               => env('AUTH_EXPIRE_SESSION_TIME_IN_MINUTES', 45),
        'renapo_user'                                  => env('RENAPO_USER', 'wsgestion'),
        'renapo_password'                              => env('RENAPO_PASSWORD', 'wsgestion2011'),
        'renapo_transaction_type'                      => env('RENAPO_TRANSACTION_TYPE', 5),
        'renapo_curlopt_url'                           => env('RENAPO_CURLOPT_URL', 'http://172.18.203.9/WebServicesGestion/services/ConsultaPorCurpService?wsdl'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Authentication Defaults
    |--------------------------------------------------------------------------
    |
    | This option defines the default authentication "guard" and password
    | reset "broker" for your application. You may change these values
    | as required, but they're a perfect start for most applications.
    |
    */

    'defaults' => [
        'guard' => env('AUTH_GUARD', 'web'),
        'passwords' => env('AUTH_PASSWORD_BROKER', 'users'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Authentication Guards
    |--------------------------------------------------------------------------
    |
    | Next, you may define every authentication guard for your application.
    | Of course, a great default configuration has been defined for you
    | which utilizes session storage plus the Eloquent user provider.
    |
    | All authentication guards have a user provider, which defines how the
    | users are actually retrieved out of your database or other storage
    | system used by the application. Typically, Eloquent is utilized.
    |
    | Supported: "session"
    |
    */

    'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'users',
        ],
        'api' => [
            'driver'   => 'passport',
            'provider' => 'users'
        ]

    ],

    /*
    |--------------------------------------------------------------------------
    | User Providers
    |--------------------------------------------------------------------------
    |
    | All authentication guards have a user provider, which defines how the
    | users are actually retrieved out of your database or other storage
    | system used by the application. Typically, Eloquent is utilized.
    |
    | If you have multiple user tables or models you may configure multiple
    | providers to represent the model / table. These providers may then
    | be assigned to any extra authentication guards you have defined.
    |
    | Supported: "database", "eloquent"
    |
    */

    'providers' => [
        'users' => [
            'driver' => 'eloquent',
            'model' => env('AUTH_MODEL', App\Models\User::class),
        ],

        // 'users' => [
        //     'driver' => 'database',
        //     'table' => 'users',
        // ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Resetting Passwords
    |--------------------------------------------------------------------------
    |
    | These configuration options specify the behavior of Laravel's password
    | reset functionality, including the table utilized for token storage
    | and the user provider that is invoked to actually retrieve users.
    |
    | The expiry time is the number of minutes that each reset token will be
    | considered valid. This security feature keeps tokens short-lived so
    | they have less time to be guessed. You may change this as needed.
    |
    | The throttle setting is the number of seconds a user must wait before
    | generating more password reset tokens. This prevents the user from
    | quickly generating a very large amount of password reset tokens.
    |
    */

    'passwords' => [
        'users' => [
            'provider' => 'users',
            'table' => env('AUTH_PASSWORD_RESET_TOKEN_TABLE', 'password_reset_tokens'),
            'expire' => 60,
            'throttle' => 60,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Password Confirmation Timeout
    |--------------------------------------------------------------------------
    |
    | Here you may define the amount of seconds before a password confirmation
    | window expires and users are asked to re-enter their password via the
    | confirmation screen. By default, the timeout lasts for three hours.
    |
    */

    'password_timeout' => env('AUTH_PASSWORD_TIMEOUT', 10800),

];
