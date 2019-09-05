<?php

declare(strict_types=1);

namespace OsiemSiedem\Tests\Autolink\Elements;

use Spatie\Html\Attributes;
use OsiemSiedem\Tests\Autolink\TestCase;
use OsiemSiedem\Autolink\Contracts\Element;
use OsiemSiedem\Autolink\Elements\BaseElement;

final class BaseElementTest extends TestCase
{
    public function testInstanceOf(): void
    {
        $element = new BaseElement('http://example.com/', 'http://example.com/', 0, 0);

        $this->assertInstanceOf(Element::class, $element);
    }

    public function testTitle(): void
    {
        $element = new BaseElement('example.com', 'http://example.com', 0, 0);

        $this->assertEquals('example.com', $element->getTitle());

        $element->setTitle('example');

        $this->assertEquals('example', $element->getTitle());
    }

    public function testUrl(): void
    {
        $element = new BaseElement('example.com', 'http://example.com', 0, 0);

        $this->assertEquals('http://example.com', $element->getUrl());

        $element->setUrl('https://example.com');

        $this->assertEquals('https://example.com', $element->getUrl());
    }

    public function testAttributes(): void
    {
        $element = new BaseElement('example.com', 'http://example.com', 0, 0, ['class' => 'foo bar']);

        $attributes = $element->getAttributes();

        $this->assertInstanceOf(Attributes::class, $attributes);
    }

    public function testPosition(): void
    {
        $element = new BaseElement('example.com', 'http://example.com', 12, 54);

        $this->assertEquals(12, $element->getStart());
        $this->assertEquals(54, $element->getEnd());
    }
}
