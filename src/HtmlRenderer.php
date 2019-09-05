<?php

declare(strict_types=1);

namespace OsiemSiedem\Autolink;

use Spatie\Html\Elements\A;
use Illuminate\Support\HtmlString;
use OsiemSiedem\Autolink\Contracts\Filter;

class HtmlRenderer
{
    /**
     * @var array
     */
    protected $filters = [];

    /**
     * Add a new filter.
     *
     * @param  \OsiemSiedem\Autolink\Contracts\Filter  $filter
     * @return $this
     */
    public function addFilter(Filter $filter): self
    {
        $this->filters[] = $filter;

        return $this;
    }

    /**
     * Render the elements in the given text.
     *
     * @param  string  $text
     * @param  array  $elements
     * @param  callable|null  $callback
     * @return \Illuminate\Support\HtmlString
     */
    public function render(string $text, array $elements, callable $callback = null): HtmlString
    {
        for ($i = count($elements) - 1; $i >= 0; $i--) {
            $start = $elements[$i]->getStart();
            $end = $elements[$i]->getEnd();

            foreach ($this->filters as $filter) {
                $elements[$i] = $filter->filter($elements[$i]);
            }

            if (! is_null($callback)) {
                $elements[$i] = $callback($elements[$i]);
            }

            $link = A::create()
                ->href($elements[$i]->getUrl())
                ->text($elements[$i]->getTitle())
                ->attributes($elements[$i]->getAttributes()->toArray())
                ->toHtml();

            $text = mb_substr($text, 0, $start)
                .$link
                .mb_substr($text, $end, mb_strlen($text) - $end);
        }

        return new HtmlString($text);
    }
}
