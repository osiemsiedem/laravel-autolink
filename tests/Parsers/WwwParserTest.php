<?php

declare(strict_types=1);

namespace OsiemSiedem\Tests\Autolink\Parsers;

use OsiemSiedem\Autolink\Link;
use OsiemSiedem\Autolink\Cursor;
use OsiemSiedem\Tests\Autolink\TestCase;
use OsiemSiedem\Autolink\Parsers\WwwParser;

final class WwwParserTest extends TestCase
{
    public function testParse(): void
    {
        $cursor = new Cursor('www.localhost');
        $parser = new WwwParser;

        $this->assertNull($parser->parse($cursor));
    }

    public function testStopsParsingAtWhitespace(): void
    {
        $cursor = new Cursor("www.example.com\n");
        $parser = new WwwParser;

        $this->assertInstanceOf(Link::class, $parser->parse($cursor));
    }
}
