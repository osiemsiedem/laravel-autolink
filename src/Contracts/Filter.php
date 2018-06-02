<?php

declare(strict_types=1);

namespace OsiemSiedem\Autolink\Contracts;

use OsiemSiedem\Autolink\Link;

interface Filter
{
    /**
     * Filter the link.
     *
     * @param  \OsiemSiedem\Autolink\Link  $link
     * @return \OsiemSiedem\Autolink\Link
     */
    public function filter(Link $link): Link;
}
