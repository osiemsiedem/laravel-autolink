<?php

declare(strict_types=1);

namespace OsiemSiedem\Autolink\Parsers;

use OsiemSiedem\Autolink\Cursor;
use OsiemSiedem\Autolink\Contracts\Element;
use OsiemSiedem\Autolink\Elements\EmailElement;

class EmailParser extends AbstractParser
{
    /**
     * Get the characters.
     *
     * @return array
     */
    public function getCharacters(): array
    {
        return ['@'];
    }

    /**
     * Parse the text.
     *
     * @param  \OsiemSiedem\Autolink\Cursor  $cursor
     * @return \OsiemSiedem\Autolink\Contracts\Element|null
     */
    public function parse(Cursor $cursor): ?Element
    {
        $state = $cursor->getState();

        //
        // Local-part
        //
        $start = $cursor->getPosition();

        $cursor->prev();

        while ($cursor->valid()) {
            $character = $cursor->getCharacter();

            if (ctype_alnum($character) || strpos('.+-_%', $character) !== false) {
                $start = $cursor->getPosition();

                $cursor->prev();

                continue;
            }

            break;
        }

        $cursor->setState($state);

        if ($start === $cursor->getPosition()) {
            return null;
        }

        //
        // Domain
        //
        $end = $cursor->getPosition();

        $at = $dot = 0;

        while ($cursor->valid()) {
            $character = $cursor->getCharacter();

            if (ctype_alnum($character)) {
                $cursor->next();

                $end = $cursor->getPosition();

                continue;
            }

            if ($character === '@') {
                $at++;
            } elseif ($character === '.' && $end < $cursor->getLength() - 1) {
                $dot++;
            } elseif ($character !== '-' && $character !== '_') {
                break;
            }

            $cursor->next();

            $end = $cursor->getPosition();
        }

        if (($end - $start) < 5 || $at !== 1 || $dot === 0 || ($dot === 1 && $cursor->getCharacter($end - 1) === '.')) {
            $cursor->setState($state);

            return null;
        }

        $position = $this->trimDelimeters($cursor, $start, $end);

        $title = $cursor->getText($position['start'], $position['end'] - $position['start']);

        $url = "mailto:{$title}";

        return new EmailElement($title, $url, $position['start'], $position['end']);
    }
}
