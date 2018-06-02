<?php

declare(strict_types=1);

namespace OsiemSiedem\Autolink\Contracts;

use OsiemSiedem\Autolink\Link;
use OsiemSiedem\Autolink\Cursor;

interface Parser
{
    /**
     * Get the characters.
     *
     * @return array
     */
    public function getCharacters(): array;

    /**
     * Parse the text.
     *
     * @param  \OsiemSiedem\Autolink\Cursor  $cursor
     * @return \OsiemSiedem\Autolink\Link|null
     */
    public function parse(Cursor $cursor): ?Link;
}
