<?php

declare(strict_types=1);

namespace OsiemSiedem\Autolink\Filters;

use OsiemSiedem\Autolink\Contracts\Filter;
use OsiemSiedem\Autolink\Contracts\Element;

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
     * Filter the element.
     *
     * @param  \OsiemSiedem\Autolink\Contracts\Element  $element
     * @return \OsiemSiedem\Autolink\Contracts\Element
     */
    public function filter(Element $element): Element
    {
        $title = $element->getTitle();

        $title = str_limit($title, $this->limit, $this->end);

        $element->setTitle($title);

        return $element;
    }
}
