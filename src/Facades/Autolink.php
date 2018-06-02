<?php

declare(strict_types=1);

namespace OsiemSiedem\Autolink\Facades;

use Illuminate\Support\Facades\Facade;

class Autolink extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'osiemsiedem.autolink';
    }
}
