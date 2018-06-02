<?php

declare(strict_types=1);

namespace OsiemSiedem\Autolink\Filters;

use OsiemSiedem\Autolink\Link;
use OsiemSiedem\Autolink\Contracts\Filter;

class LimitLengthFilter implements Filter
{
    /**
     * @var int
     */
    protected $limit;

    /**
     * @var string
     */
    protected $end;

    /**
     * Create a new instance.
     *
     * @param  int  $limit
     * @param  string  $end
     * @return void
     */
    public function __construct(int $limit = 30, string $end = '...')
    {
        $this->limit = $limit;
        $this->end = $end;
    }

    /**
     * Filter the link.
     *
     * @param  \OsiemSiedem\Autolink\Link  $link
     * @return \OsiemSiedem\Autolink\Link
     */
    public function filter(Link $link): Link
    {
        $title = $link->getTitle();

        $title = str_limit($title, $this->limit, $this->end);

        $link->setTitle($title);

        return $link;
    }
}
