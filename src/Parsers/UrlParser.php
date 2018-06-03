<?php

declare(strict_types=1);

namespace OsiemSiedem\Autolink\Parsers;

use OsiemSiedem\Autolink\Link;
use OsiemSiedem\Autolink\Cursor;

class UrlParser extends AbstractUrlParser
{
    /**
     * Get the characters.
     *
     * @return array
     */
    public function getCharacters(): array
    {
        return [':'];
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

        if (($cursor->getLength() - $start) < 4 || ! $cursor->match('#://#i')) {
            return null;
        }

        if ( ! $this->validateDomain($cursor, $start + 3)) {
            return null;
        }

        $state = $cursor->getState();

        while ($cursor->valid()) {
            $character = $cursor->getCharacter();

            if ($this->isWhitespace($character)) {
                break;
            }

            $cursor->next();
        }

        $end = $cursor->getPosition();

        while ($start > 0) {
            if (ctype_alpha($cursor->getCharacter($start - 1))) {
                $start--;

                continue;
            }

            break;
        }

        if ( ! $this->validateProtocol($cursor, $start)) {
            return null;
        }

        if ($position = $this->trimMoreDelimeters($cursor, $start, $end)) {
            $url = $title = $cursor->getText($position['start'], $position['end'] - $position['start']);

            return new Link($title, $url, [], $position['start'], $position['end']);
        }

        $cursor->setState($state);

        return null;
    }

    /**
     * Validate the protocol.
     *
     * @param  \OsiemSiedem\Autolink\Cursor  $cursor
     * @param  int  $start
     * @return bool
     */
    protected function validateProtocol(Cursor $cursor, int $start): bool
    {
        $text = $cursor->getText($start, 8);

        return preg_match('#^https?://#i', $text) > 0;
    }
}
