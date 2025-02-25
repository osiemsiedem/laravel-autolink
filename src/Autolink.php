<?php

declare(strict_types=1);

namespace OsiemSiedem\Autolink;

use Illuminate\Support\HtmlString;

class Autolink
{
    protected Parser $parser;

    protected HtmlRenderer $renderer;

    /**
     * Create a new instance.
     */
    public function __construct(Parser $parser, HtmlRenderer $renderer)
    {
        $this->parser = $parser;
        $this->renderer = $renderer;
    }

    /**
     * Convert the URLs into clickable links.
     */
    public function convert(string $text, ?callable $callback = null): HtmlString
    {
        $elements = $this->parse($text);

        return $this->render($text, $elements, $callback);
    }

    /**
     * Parse the text.
     *
     * @return \OsiemSiedem\Autolink\Contracts\Element[]
     */
    public function parse(string $text): array
    {
        return $this->parser->parse($text);
    }

    /**
     * Render the elements.
     */
    public function render(string $text, array $elements, ?callable $callback = null): HtmlString
    {
        return $this->renderer->render($text, $elements, $callback);
    }
}
