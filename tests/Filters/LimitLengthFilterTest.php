<?php

declare(strict_types=1);

namespace OsiemSiedem\Tests\Autolink\Filters;

use OsiemSiedem\Autolink\Link;
use OsiemSiedem\Tests\Autolink\TestCase;
use OsiemSiedem\Autolink\Contracts\Filter;
use OsiemSiedem\Autolink\Filters\LimitLengthFilter;

final class LimitLengthFilterTest extends TestCase
{
    public function testFilter(): void
    {
        $link = new Link('http://example.com/some/very/long/link?foo=bar', 'http://example.com/some/very/long/link?foo=bar', [], 0, 0);

        $this->assertEquals('http://example.com/some/very/long/link?foo=bar', $link->getTitle());
        $this->assertEquals('http://example.com/some/very/long/link?foo=bar', $link->getUrl());

        $filter = new LimitLengthFilter;

        $this->assertInstanceOf(Filter::class, $filter);

        $link = $filter->filter($link);

        $this->assertEquals('http://example.com/some/very/l...', $link->getTitle());
        $this->assertEquals('http://example.com/some/very/long/link?foo=bar', $link->getUrl());
    }
}
