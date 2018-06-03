<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Ignored Elements
    |--------------------------------------------------------------------------
    |
    */
    'ignored' => [
        'a',
        'code',
        'kbd',
        'pre',
        'script',
    ],

    /*
    |--------------------------------------------------------------------------
    | Filters
    |--------------------------------------------------------------------------
    |
    */
    'filters' => [
        \OsiemSiedem\Autolink\Filters\TrimFilter::class,
        \OsiemSiedem\Autolink\Filters\LimitLengthFilter::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Parsers
    |--------------------------------------------------------------------------
    |
    */
    'parsers' => [
        \OsiemSiedem\Autolink\Parsers\UrlParser::class,
        \OsiemSiedem\Autolink\Parsers\WwwParser::class,
        \OsiemSiedem\Autolink\Parsers\EmailParser::class,
    ],
];
