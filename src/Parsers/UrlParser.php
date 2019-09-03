<?php

declare(strict_types=1);

namespace OsiemSiedem\Autolink\Parsers;

use Illuminate\Support\Str;
use OsiemSiedem\Autolink\Cursor;
use OsiemSiedem\Autolink\Contracts\Element;
use OsiemSiedem\Autolink\Elements\UrlElement;

class UrlParser extends AbstractUrlParser
{
    /**
     * @var bool
     */
    protected $allowShort = true;

    /**
     * @var array
     */
    protected $protocols = ['http://', 'https://'];

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
     * @return \OsiemSiedem\Autolink\Contracts\Element|null
     */
    public function parse(Cursor $cursor): ?Element
    {
        $start = $cursor->getPosition();

        if ($cursor->getText($start, 3) !== '://') {
            return null;
        }

        if ( ! $this->validateDomain($cursor, $start + 3, $this->allowShort)) {
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

        $position = $this->trimMoreDelimeters($cursor, $start, $end);

        $url = $title = $cursor->getText($position['start'], $position['end'] - $position['start']);

        return new UrlElement($title, $url, $position['start'], $position['end']);
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
        $text = strtolower($cursor->getText($start, 8));

        return Str::startsWith($text, $this->protocols);
    }
}
