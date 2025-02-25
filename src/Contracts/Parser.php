<?php

declare(strict_types=1);

namespace OsiemSiedem\Autolink\Contracts;

use OsiemSiedem\Autolink\Cursor;

interface Parser
{
    /**
     * Get the characters.
     */
    public function getCharacters(): array;

    /**
     * Parse the text.
     */
    public function parse(Cursor $cursor): ?Element;
}
