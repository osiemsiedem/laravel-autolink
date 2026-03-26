<?php

declare(strict_types=1);

namespace OsiemSiedem\Autolink;

use Illuminate\Support\Arr;
use OsiemSiedem\Autolink\Contracts\Element;
use OsiemSiedem\Autolink\Contracts\Parser as ElementParser;

class Parser
{
    /**
     * @var string[]
     */
    protected array $ignoredTags = ['a', 'pre', 'code', 'kbd', 'script'];

    /**
     * @var array<string, ElementParser[]>
     */
    protected array $elementParsers = [];

    /**
     * Add a new parser.
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
     * @param  string[]  $ignored
     */
    public function setIgnoredTags(array $ignored): self
    {
        $this->ignoredTags = $ignored;

        return $this;
    }

    /**
     * Parse the text.
     *
     * @return Element[]
     */
    public function parse(string $text): array
    {
        $cursor = new Cursor($text);

        $elements = [];

        foreach ($cursor as $character) {
            if ($character === '<') {
                foreach ($this->ignoredTags as $ignoredTag) {
                    $length = strlen($ignoredTag) + 1;

                    if ($this->matchesTag($cursor, $ignoredTag)) {
                        $cursor->next($length);

                        while ($cursor->valid()) {
                            while ($cursor->valid() && $cursor->getCharacter() !== '<') {
                                $cursor->next();
                            }

                            if ($cursor->getPosition() === $cursor->getLength()) {
                                break 2;
                            }

                            if ($this->matchesTag($cursor, $ignoredTag, true)) {
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

            /** @var ElementParser[]|null $parsers */
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

    /**
     * Check if the current position matches the ignored tag.
     */
    protected function matchesTag(Cursor $cursor, string $ignoredTag, bool $closing = false): bool
    {
        $position = $cursor->getPosition();
        $prefix = $closing ? '</' : '<';
        $length = strlen($prefix.$ignoredTag);

        if (strtolower($cursor->getText($position, $length)) !== strtolower($prefix.$ignoredTag)) {
            return false;
        }

        return $this->isTagBoundary($cursor->getCharacter($position + $length));
    }

    /**
     * Check if the character is a tag boundary.
     */
    protected function isTagBoundary(?string $character): bool
    {
        if ($character === null || $character === '>') {
            return true;
        }

        return ctype_space($character) || $character === '/';
    }
}
