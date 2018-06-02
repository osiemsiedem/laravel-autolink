<?php

declare(strict_types=1);

namespace OsiemSiedem\Autolink\Parsers;

use OsiemSiedem\Autolink\Link;
use OsiemSiedem\Autolink\Cursor;

class WwwParser extends AbstractUrlParser
{
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
     * @return \OsiemSiedem\Autolink\Link|null
     */
    public function parse(Cursor $cursor): ?Link
    {
        $start = $cursor->getPosition();

        if (($cursor->getLength() - $start) < 4 || ! $cursor->match('/www\./i')) {
            return null;
        }

        if ( ! $this->validateDomain($cursor, $start, false)) {
            return null;
        }

        $state = $cursor->getState();

        $boundary = $cursor->getCharacter($start - 1);

        if ( ! is_null($boundary) && ! $this->isWhitespace($boundary) && ! ctype_punct($boundary)) {
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

        if ($position = $this->trimMoreDelimeters($cursor, $start, $end)) {
            $title = $cursor->getText($position['start'], $position['end'] - $position['start']);

            $url = "http://{$title}";

            return new Link($title, $url, [], $position['start'], $position['end']);
        }

        $cursor->setState($state);

        return null;
    }
}
