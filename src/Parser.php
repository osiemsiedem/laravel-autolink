<?php

declare(strict_types=1);

namespace OsiemSiedem\Autolink;

use Illuminate\Support\Arr;
use OsiemSiedem\Autolink\Contracts\Parser as ElementParser;

class Parser
{
    /**
     * @var array
     */
    protected $ignoredTags = ['a', 'pre', 'code', 'kbd', 'script'];

    /**
     * @var array
     */
    protected $elementParsers = [];

    /**
     * Add a new parser.
     *
     * @param  \OsiemSiedem\Autolink\Contracts\Parser  $parser
     * @return $this
     */
    public function addElementParser(ElementParser $parser): self
    {
        foreach ($parser->getCharacters() as $character) {
            $this->elementParsers[$character][] = $parser;
        }

        return $this;
    }

    /**
     * Set the ignored tags.
     *
     * @param  array  $ignored
     * @return $this
     */
    public function setIgnoredTags(array $ignored): self
    {
        $this->ignoredTags = $ignored;

        return $this;
    }

    /**
     * Parse the text.
     *
     * @param  string  $text
     * @return array
     */
    public function parse(string $text): array
    {
        $cursor = new Cursor($text);

        $elements = [];

        foreach ($cursor as $character) {
            if ($character === '<') {
                foreach ($this->ignoredTags as $ignoredTag) {
                    $length = strlen($ignoredTag) + 1;

                    $tag = $cursor->getText($cursor->getPosition(), $length);

                    if ($tag === "<{$ignoredTag}") {
                        $cursor->next($length);

                        while ($cursor->valid()) {
                            while ($cursor->valid() && $cursor->getCharacter() !== '<') {
                                $cursor->next();
                            }

                            if ($cursor->getPosition() === $cursor->getLength()) {
                                break 2;
                            }

                            $tag = $cursor->getText($cursor->getPosition(), strlen($ignoredTag) + 2);

                            if ($tag === "</{$ignoredTag}") {
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

            $parsers = Arr::get($this->elementParsers, $character);

            if (is_null($parsers)) {
                continue;
            }

            foreach ($parsers as $parser) {
                if ($element = $parser->parse($cursor)) {
                    $elements[] = $element;

                    break;
                }
            }
        }

        return $elements;
    }
}
