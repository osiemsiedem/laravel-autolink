<?php

declare(strict_types=1);

namespace OsiemSiedem\Tests\Autolink\Elements;

use OsiemSiedem\Autolink\Contracts\Element;
use OsiemSiedem\Autolink\Elements\BaseElement;
use OsiemSiedem\Tests\Autolink\TestCase;
use Spatie\Html\Attributes;

final class BaseElementTest extends TestCase
{
    public function test_instance_of(): void
    {
        $element = new BaseElement('http://example.com/', 'http://example.com/', 0, 0);

        $this->assertInstanceOf(Element::class, $element);
    }

    public function test_title(): void
    {
        $element = new BaseElement('example.com', 'http://example.com', 0, 0);

        $this->assertEquals('example.com', $element->getTitle());

        $element->setTitle('example');

        $this->assertEquals('example', $element->getTitle());
    }

    public function test_url(): void
    {
        $element = new BaseElement('example.com', 'http://example.com', 0, 0);

        $this->assertEquals('http://example.com', $element->getUrl());

        $element->setUrl('https://example.com');

        $this->assertEquals('https://example.com', $element->getUrl());
    }

    public function test_attributes(): void
    {
        $element = new BaseElement('example.com', 'http://example.com', 0, 0, ['class' => 'foo bar']);

        $attributes = $element->getAttributes();

        $this->assertInstanceOf(Attributes::class, $attributes);
    }

    public function test_position(): void
    {
        $element = new BaseElement('example.com', 'http://example.com', 12, 54);

        $this->assertEquals(12, $element->getStart());
        $this->assertEquals(54, $element->getEnd());
    }
}
