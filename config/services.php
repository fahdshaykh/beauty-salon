<?php



return [



    /*

    |--------------------------------------------------------------------------

    | Third Party Services

    |--------------------------------------------------------------------------

    |

    | This file is for storing the credentials for third party services such

    | as Mailgun, SparkPost and others. This file provides a sane default

    | location for this type of information, allowing packages to have

    | a conventional file to locate the various service credentials.

    |

    */

    'twilio' => [
        'sid' => 'AC5e3db42288977900c5f01bcfc873845c',
        'token' => '83e48092add20a15c6bd5e59c09648f5',
        'whatsapp_from' => '+14406821069'
      ],

    'mailgun' => [

        'domain' => env('MAILGUN_DOMAIN'),

        'secret' => env('MAILGUN_SECRET'),

        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),

    ],



    'postmark' => [

        'token' => env('POSTMARK_TOKEN'),

    ],



    'ses' => [

        'key' => env('AWS_ACCESS_KEY_ID'),

        'secret' => env('AWS_SECRET_ACCESS_KEY'),

        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),

    ],



    'sparkpost' => [

        'secret' => env('SPARKPOST_SECRET'),

    ],



];

