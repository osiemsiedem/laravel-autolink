<?php

declare(strict_types=1);

namespace OsiemSiedem\Tests\Autolink\Filters;

use OsiemSiedem\Tests\Autolink\TestCase;
use OsiemSiedem\Autolink\Contracts\Filter;
use OsiemSiedem\Autolink\Filters\TrimFilter;
use OsiemSiedem\Autolink\Elements\BaseElement;
use OsiemSiedem\Autolink\Elements\EmailElement;

final class TrimFilterTest extends TestCase
{
    public function testFilter(): void
    {
        $element = new BaseElement('http://www.example.com/', 'http://www.example.com/', 0, 0);

        $this->assertEquals('http://www.example.com/', $element->getTitle());
        $this->assertEquals('http://www.example.com/', $element->getUrl());

        $filter = new TrimFilter;

        $this->assertInstanceOf(Filter::class, $filter);

        $element = $filter->filter($element);

        $this->assertEquals('example.com', $element->getTitle());
        $this->assertEquals('http://www.example.com/', $element->getUrl());
    }

    public function testIgnoreEmailElement(): void
    {
        $element = new EmailElement('www.test@example.com', 'mailto:www.test@example.com', 0, 0);

        $this->assertEquals('www.test@example.com', $element->getTitle());
        $this->assertEquals('mailto:www.test@example.com', $element->getUrl());

        $filter = new TrimFilter;

        $this->assertInstanceOf(Filter::class, $filter);

        $element = $filter->filter($element);

        $this->assertEquals('www.test@example.com', $element->getTitle());
        $this->assertEquals('mailto:www.test@example.com', $element->getUrl());
    }
}
