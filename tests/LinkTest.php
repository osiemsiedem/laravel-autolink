<?php

declare(strict_types=1);

namespace OsiemSiedem\Tests\Autolink;

use OsiemSiedem\Autolink\Link;
use PHPUnit\Framework\TestCase;

final class LinkTest extends TestCase
{
    public function testTitle(): void
    {
        $link = new Link('example.com', 'http://example.com', [], 0, 0);

        $this->assertEquals('example.com', $link->getTitle());

        $link->setTitle('example');

        $this->assertEquals('example', $link->getTitle());
    }

    public function testUrl(): void
    {
        $link = new Link('example.com', 'http://example.com', [], 0, 0);

        $this->assertEquals('http://example.com', $link->getUrl());

        $link->setUrl('https://example.com');

        $this->assertEquals('https://example.com', $link->getUrl());
    }

    public function testAttributes(): void
    {
        $link = new Link('example.com', 'http://example.com', ['class' => 'foo bar'], 0, 0);

        $attributes = $link->getAttributes();

        $this->assertArrayHasKey('class', $attributes);
        $this->assertEquals('foo bar', $attributes['class']);

        $link->setAttributes([]);

        $this->assertArrayNotHasKey('class', $link->getAttributes());
    }

    public function testPosition(): void
    {
        $link = new Link('example.com', 'http://example.com', [], 12, 54);

        $this->assertEquals(12, $link->getStart());
        $this->assertEquals(54, $link->getEnd());
    }

    public function testToHtml(): void
    {
        $link = new Link('<b>87</b>', 'http://example.com', ['class' => 'foo bar', 'hidden' => null], 0, 0);

        $attributes = $link->getAttributes();

        $this->assertEquals('<a class="foo bar" hidden href="http://example.com">&lt;b&gt;87&lt;/b&gt;</a>', $link->toHtml());
    }
}
