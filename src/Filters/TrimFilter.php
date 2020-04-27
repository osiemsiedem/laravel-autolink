<?php

declare(strict_types=1);

namespace OsiemSiedem\Autolink\Filters;

use OsiemSiedem\Autolink\Contracts\Element;
use OsiemSiedem\Autolink\Contracts\Filter;
use OsiemSiedem\Autolink\Elements\EmailElement;

class TrimFilter implements Filter
{
    /**
     * Filter the element.
     *
     * @param  \OsiemSiedem\Autolink\Contracts\Element  $element
     * @return \OsiemSiedem\Autolink\Contracts\Element
     */
    public function filter(Element $element): Element
    {
        if ($element instanceof EmailElement) {
            return $element;
        }

        $title = $element->getTitle();

        $title = preg_replace('#^\w+://#i', '', $title);

        $title = preg_replace('#^(?:www[0-9]*\.)?#i', '', $title);

        $title = rtrim($title, '/');

        $element->setTitle($title);

        return $element;
    }
}
