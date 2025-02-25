<?php

declare(strict_types=1);

namespace OsiemSiedem\Autolink\Contracts;

interface Element
{
    /**
     * Get the title.
     */
    public function getTitle(): string;

    /**
     * Set the title.
     */
    public function setTitle(string $title): self;
}
