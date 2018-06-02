<?php

declare(strict_types=1);

namespace OsiemSiedem\Autolink\Filters;

use OsiemSiedem\Autolink\Link;
use OsiemSiedem\Autolink\Contracts\Filter;

class TrimFilter implements Filter
{
    /**
     * Filter the link.
     *
     * @param  \OsiemSiedem\Autolink\Link  $link
     * @return \OsiemSiedem\Autolink\Link
     */
    public function filter(Link $link): Link
    {
        $title = $link->getTitle();

        $title = rtrim(preg_replace('#^\w+://(?:www[0-9]*\.)?#i', '', $title), '/');

        $link->setTitle($title);

        return $link;
    }
}
