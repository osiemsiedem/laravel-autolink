<?php

declare(strict_types=1);

namespace OsiemSiedem\Tests\Autolink\Parsers;

use OsiemSiedem\Autolink\Contracts\Element;
use OsiemSiedem\Autolink\Cursor;
use OsiemSiedem\Autolink\Parsers\WwwParser;
use OsiemSiedem\Tests\Autolink\TestCase;

final class WwwParserTest extends TestCase
{
    public function test_parse(): void
    {
        $cursor = new Cursor('www.localhost');
        $parser = new WwwParser;

        $this->assertNull($parser->parse($cursor));
    }

    public function test_stops_parsing_at_whitespace(): void
    {
        $cursor = new Cursor("www.example.com\n");
        $parser = new WwwParser;

        $this->assertInstanceOf(Element::class, $parser->parse($cursor));
    }

    public function test_minimum_length(): void
    {
        $cursor = new Cursor('www.a');
        $parser = new WwwParser;

        $this->assertNull($parser->parse($cursor));
    }
}
