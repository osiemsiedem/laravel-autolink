<?php

declare(strict_types=1);

namespace OsiemSiedem\Autolink;

use Illuminate\Support\HtmlString;
use OsiemSiedem\Autolink\Contracts\Filter;
use OsiemSiedem\Autolink\Contracts\Parser;

class Autolink
{
    /**
     * @var array
     */
    protected $filters = [];

    /**
     * @var array
     */
    protected $parsers = [];

    /**
     * @var array
     */
    protected $ignored = ['a', 'pre', 'code', 'kbd', 'script'];

    /**
     * Add a new extension.
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
     * Add a new parser.
     *
     * @param  \OsiemSiedem\Autolink\Contracts\Parser  $parser
     * @return $this
     */
    public function addParser(Parser $parser): self
    {
        foreach ($parser->getCharacters() as $character) {
            $this->parsers[$character] = $parser;
        }

        return $this;
    }

    /**
     * Set the ignored tags.
     *
     * @param  array  $ignored
     * @return $this
     */
    public function ignore(array $ignored): self
    {
        $this->ignored = $ignored;

        return $this;
    }

    /**
     * Convert the URLs into clickable links.
     *
     * @param  string  $text
     * @param  callable|null  $callback
     * @return \Illuminate\Support\HtmlString
     */
    public function convert(string $text, callable $callback = null): HtmlString
    {
        $cursor = new Cursor($text);

        $links = [];

        foreach ($cursor as $character) {
            if ($character === '<') {
                foreach ($this->ignored as $ignored) {
                    if ($cursor->match("#^<{$ignored}[\s>]#i")) {
                        $cursor->next(strlen($ignored) + 1);

                        while ($cursor->valid()) {
                            while ($cursor->valid() && $cursor->getCharacter() !== '<') {
                                $cursor->next();
                            }

                            if ($cursor->getPosition() === $cursor->getLength()) {
                                break 2;
                            }

                            if ($cursor->match("#^</{$ignored}[\s>]#i")) {
                                break 2;
                            }

                            $cursor->next();
                        }

                        break;
                    }
                }

                while ($cursor->valid() && $cursor->getCharacter() !== '>') {
                    $cursor->next();
                }

                continue;
            }

            $parser = array_get($this->parsers, $character);

            if (is_null($parser)) {
                continue;
            }

            if ($link = $parser->parse($cursor)) {
                $links[] = $link;
            }
        }

        for ($i = count($links) - 1; $i >= 0; $i--) {
            $start = $links[$i]->getStart();

            $end = $links[$i]->getEnd();

            foreach ($this->filters as $filter) {
                $links[$i] = $filter->filter($links[$i]);
            }

            if ( ! is_null($callback)) {
                $links[$i] = $callback($links[$i]);
            }

            $text = mb_substr($text, 0, $start)
                .$links[$i]->toHtml()
                .mb_substr($text, $end, mb_strlen($text) - $end);
        }

        return new HtmlString($text);
    }
}
