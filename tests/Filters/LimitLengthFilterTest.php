<?php

declare(strict_types=1);

namespace OsiemSiedem\Tests\Autolink\Filters;

use OsiemSiedem\Tests\Autolink\TestCase;
use OsiemSiedem\Autolink\Contracts\Filter;
use OsiemSiedem\Autolink\Elements\BaseElement;
use OsiemSiedem\Autolink\Filters\LimitLengthFilter;

final class LimitLengthFilterTest extends TestCase
{
    public function testFilter(): void
    {
        $element = new BaseElement('http://example.com/some/very/long/link?foo=bar', 'http://example.com/some/very/long/link?foo=bar', 0, 0);

        $this->assertEquals('http://example.com/some/very/long/link?foo=bar', $element->getTitle());
        $this->assertEquals('http://example.com/some/very/long/link?foo=bar', $element->getUrl());

        $filter = new LimitLengthFilter;

        $this->assertInstanceOf(Filter::class, $filter);

        $element = $filter->filter($element);

        $this->assertEquals('http://example.com/some/very/l...', $element->getTitle());
        $this->assertEquals('http://example.com/some/very/long/link?foo=bar', $element->getUrl());
    }
}
