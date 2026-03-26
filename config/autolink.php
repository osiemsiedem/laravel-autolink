<?php

declare(strict_types=1);
use OsiemSiedem\Autolink\Filters\LimitLengthFilter;
use OsiemSiedem\Autolink\Filters\TrimFilter;
use OsiemSiedem\Autolink\Parsers\EmailParser;
use OsiemSiedem\Autolink\Parsers\UrlParser;
use OsiemSiedem\Autolink\Parsers\WwwParser;

return [
    /*
    |--------------------------------------------------------------------------
    | Ignored Tags
    |--------------------------------------------------------------------------
    |
    */
    'ignored_tags' => [
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
        TrimFilter::class,
        LimitLengthFilter::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Parsers
    |--------------------------------------------------------------------------
    |
    */
    'parsers' => [
        UrlParser::class,
        WwwParser::class,
        EmailParser::class,
    ],
];
