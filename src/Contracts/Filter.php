<?php

declare(strict_types=1);

namespace OsiemSiedem\Autolink\Contracts;

interface Filter
{
    /**
     * Filter the element.
     */
    public function filter(Element $element): Element;
}
