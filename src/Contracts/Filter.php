<?php

declare(strict_types=1);

namespace OsiemSiedem\Autolink\Contracts;

interface Filter
{
    /**
     * Filter the element.
     *
     * @param  \OsiemSiedem\Autolink\Contracts\Element  $element
     * @return \OsiemSiedem\Autolink\Contracts\Element
     */
    public function filter(Element $element): Element;
}
