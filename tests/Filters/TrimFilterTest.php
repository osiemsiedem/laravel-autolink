<?php

declare(strict_types=1);

namespace OsiemSiedem\Tests\Autolink\Filters;

use OsiemSiedem\Autolink\Link;
use OsiemSiedem\Tests\Autolink\TestCase;
use OsiemSiedem\Autolink\Contracts\Filter;
use OsiemSiedem\Autolink\Filters\TrimFilter;

final class TrimFilterTest extends TestCase
{
    public function testFilter(): void
    {
        $link = new Link('http://example.com/', 'http://example.com/', [], 0, 0);

        $this->assertEquals('http://example.com/', $link->getTitle());
        $this->assertEquals('http://example.com/', $link->getUrl());

        $filter = new TrimFilter;

        $this->assertInstanceOf(Filter::class, $filter);

        $link = $filter->filter($link);

        $this->assertEquals('example.com', $link->getTitle());
        $this->assertEquals('http://example.com/', $link->getUrl());
    }
}
