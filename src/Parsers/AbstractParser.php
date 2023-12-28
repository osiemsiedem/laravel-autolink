<?php

declare(strict_types=1);

namespace OsiemSiedem\Autolink\Parsers;

use Illuminate\Support\Arr;
use OsiemSiedem\Autolink\Contracts\Parser;
use OsiemSiedem\Autolink\Cursor;

abstract class AbstractParser implements Parser
{
    /**
     * Trim the delimeters.
     *
     * @param  \OsiemSiedem\Autolink\Cursor  $cursor
     * @param  int  $start
     * @param  int  $end
     * @return array|null
     */
    protected function trimDelimeters(Cursor $cursor, int $start, int $end): ?array
    {
        for ($i = $start; $i < $end; $i++) {
            $character = $cursor->getCharacter($i);

            if ($character === '<') {
                $end = $i;

                break;
            }
        }

        while ($end > $start) {
            $character = $cursor->getCharacter($end - 1);

            if (strpos('?!.,:', $character) !== false) {
                $end--;
            } elseif ($character === ';') {
                $newEnd = $end - 2;

                while ($newEnd > 0 && ctype_alnum((string) $cursor->getCharacter($newEnd))) {
                    $newEnd--;
                }

                if ($newEnd < $end - 2) {
                    if ($newEnd > 0 && $cursor->getCharacter($newEnd) === '#') {
                        $newEnd--;
                    }

                    if ($cursor->getCharacter($newEnd) === '&') {
                        $end = $newEnd;

                        continue;
                    }
                }

                $end--;

                continue;
            }

            break;
        }

        if ($end === $start) {
            return null;
        }

        $closeParenthesis = $cursor->getCharacter($end - 1);

        if ($openParenthesis = $this->getMatchingParenthesis($closeParenthesis)) {
            $opening = $closing = 0;
            $i = $start;

            while ($i < $end) {
                $character = $cursor->getCharacter($i);

                if ($character === $openParenthesis) {
                    $opening++;
                } elseif ($character === $closeParenthesis) {
                    $closing++;
                }

                $i++;
            }

            if ($openParenthesis === $closeParenthesis) {
                if ($opening > 0) {
                    $end--;
                }
            } else {
                if ($closing > $opening) {
                    $end--;
                }
            }
        }

        return [
            'start' => $start,
            'end' => $end,
        ];
    }

    /**
     * Trim the delimeters.
     *
     * @param  \OsiemSiedem\Autolink\Cursor  $cursor
     * @param  int  $start
     * @param  int  $end
     * @return array|null
     */
    protected function trimMoreDelimeters(Cursor $cursor, int $start, int $end): ?array
    {
        for ($iterations = 0; $iterations < 5; $iterations++) {
            $prevEnd = $end;

            if ($position = $this->trimDelimeters($cursor, $start, $end)) {
                $start = $position['start'];
                $end = $position['end'];
            } else {
                return null;
            }

            if ($prevEnd === $end) {
                break;
            }
        }

        return [
            'start' => $start,
            'end' => $end,
        ];
    }

    /**
     * Get the matching parenthesis.
     *
     * @param  string  $parenthesis
     * @return string|null
     */
    protected function getMatchingParenthesis(string $parenthesis): ?string
    {
        return Arr::get([
            '"' => '"',
            "'" => "'",
            ')' => '(',
            ']' => '[',
            '}' => '{',
            '）' => '（',
            '】' => '【',
            '』' => '『',
            '」' => '「',
            '》' => '《',
            '〉' => '〈',
        ], $parenthesis);
    }
}
