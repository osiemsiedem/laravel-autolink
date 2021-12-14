<?php

declare(strict_types=1);

namespace OsiemSiedem\Autolink\Parsers;

use OsiemSiedem\Autolink\Cursor;

abstract class AbstractUrlParser extends AbstractParser
{
    /**
     * Validate the domain name.
     *
     * @param  \OsiemSiedem\Autolink\Cursor  $cursor
     * @param  int  $start
     * @param  bool  $allowShort
     * @return bool
     */
    protected function validateDomain(Cursor $cursor, int $start, bool $allowShort = true): bool
    {
        if (! ctype_alnum((string) $cursor->getCharacter($start))) {
            return false;
        }

        $dot = 0;

        for ($i = $start + 3, $j = $cursor->getLength() - 1; $i < $j; $i++) {
            $character = (string) $cursor->getCharacter($i);

            if ($character === '.') {
                $dot++;
            } elseif (! ctype_alnum($character) && $character !== '-') {
                break;
            }
        }

        return $dot >= ($allowShort ? 0 : 2);
    }

    /**
     * Check for whitespace character.
     *
     * @param  string  $character
     * @return bool
     */
    protected function isWhitespace(string $character): bool
    {
        $ord = mb_ord($character);

        if ($ord >= 9 && $ord <= 13) {
            return true;
        }

        if (in_array($ord, [32, 160, 5760, 8239, 8287, 12288])) {
            return true;
        }

        return $ord >= 8192 && $ord <= 8202;
    }
}
