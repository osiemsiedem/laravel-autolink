<?php

declare(strict_types=1);

namespace OsiemSiedem\Autolink\Filters;

use Illuminate\Support\Str;
use OsiemSiedem\Autolink\Contracts\Element;
use OsiemSiedem\Autolink\Contracts\Filter;

class LimitLengthFilter implements Filter
{
    protected int $limit;

    protected string $end;

    /**
     * Create a new instance.
     */
    public function __construct(int $limit = 30, string $end = '...')
    {
        $this->limit = $limit;
        $this->end = $end;
    }

    /**
     * {@inheritdoc}
     */
    public function filter(Element $element): Element
    {
        $title = $element->getTitle();

        $title = Str::limit($title, $this->limit, $this->end);

        $element->setTitle($title);

        return $element;
    }
}
