<?php

declare(strict_types=1);

namespace OsiemSiedem\Autolink;

use Illuminate\Support\HtmlString;

class Autolink
{
    /**
     * @var \OsiemSiedem\Autolink\Parser
     */
    protected $parser;

    /**
     * @var \OsiemSiedem\Autolink\HtmlRenderer
     */
    protected $renderer;

    /**
     * Create a new instance.
     *
     * @param  \OsiemSiedem\Autolink\Parser  $parser
     * @param  \OsiemSiedem\Autolink\HtmlRenderer  $renderer
     * @return void
     */
    public function __construct(Parser $parser, HtmlRenderer $renderer)
    {
        $this->parser = $parser;
        $this->renderer = $renderer;
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
        $elements = $this->parse($text);

        return $this->render($text, $elements, $callback);
    }

    /**
     * Parse the text.
     *
     * @param  string  $text
     * @return array
     */
    public function parse(string $text): array
    {
        return $this->parser->parse($text);
    }

    /**
     * Render the elements.
     *
     * @param  string  $text
     * @param  array  $elements
     * @param  callable|null  $callback
     * @return \Illuminate\Support\HtmlString
     */
    public function render(string $text, array $elements, callable $callback = null): HtmlString
    {
        return $this->renderer->render($text, $elements, $callback);
    }
}
