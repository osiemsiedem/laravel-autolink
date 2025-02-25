<?php

declare(strict_types=1);

namespace OsiemSiedem\Autolink\Facades;

use Illuminate\Support\Facades\Facade;

class Autolink extends Facade
{
    /**
     * {@inheritdoc}
     */
    protected static function getFacadeAccessor()
    {
        return 'osiemsiedem.autolink';
    }
}
