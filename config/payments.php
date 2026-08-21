<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Default Payment Provider
    |--------------------------------------------------------------------------
    |
    | This value determines the default payment gateway used when no specific
    | provider is selected by the user or client application.
    |
    */

    'default' => env('PAYMENT_DEFAULT_PROVIDER', 'stripe'),

    /*
    |--------------------------------------------------------------------------
    | Default Currency
    |--------------------------------------------------------------------------
    |
    | The default ISO 4217 three-letter currency code used for school billing.
    |
    */

    'currency' => env('PAYMENT_DEFAULT_CURRENCY', 'USD'),

];
