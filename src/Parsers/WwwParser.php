<?php

declare(strict_types=1);

namespace OsiemSiedem\Autolink\Parsers;

use OsiemSiedem\Autolink\Cursor;
use OsiemSiedem\Autolink\Contracts\Element;
use OsiemSiedem\Autolink\Elements\UrlElement;

class WwwParser extends AbstractUrlParser
{
    /**
     * @var bool
     */
    protected $allowShort = false;

    /**
     * Get the characters.
     *
     * @return array
     */
    public function getCharacters(): array
    {
        return ['w', 'W'];
    }

    /**
     * Parse the text.
     *
     * @param  \OsiemSiedem\Autolink\Cursor  $cursor
     * @return \OsiemSiedem\Autolink\Contracts\Element|null
     */
    public function parse(Cursor $cursor): ?Element
    {
        $start = $cursor->getPosition();

        if ($cursor->getLength() - $start < 8) {
            return null;
        }

        if (strtolower($cursor->getText($start, 4)) !== 'www.') {
            return null;
        }

        if ( ! $this->validateDomain($cursor, $start, $this->allowShort)) {
            return null;
        }

        $boundary = $cursor->getCharacter($start - 1);

        if ( ! is_null($boundary) && ! ctype_space($boundary) && ! ctype_punct($boundary)) {
            return null;
        }

        while ($cursor->valid()) {
            $character = $cursor->getCharacter();

            if ($this->isWhitespace($character)) {
                break;
            }

            $cursor->next();
        }

        $end = $cursor->getPosition();

        $position = $this->trimMoreDelimeters($cursor, $start, $end);

        $title = $cursor->getText($position['start'], $position['end'] - $position['start']);

        return new UrlElement($title, "http://{$title}", $position['start'], $position['end']);
    }
}
