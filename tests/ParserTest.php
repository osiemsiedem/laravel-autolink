<?php

declare(strict_types=1);

namespace OsiemSiedem\Tests\Autolink;

use OsiemSiedem\Autolink\Parser;
use OsiemSiedem\Autolink\Parsers\UrlParser;

final class ParserTest extends TestCase
{
    public function testIgnoredTags(): void
    {
        $parser = new Parser;
        $parser->addElementParser(new UrlParser);
        $parser->setIgnoredTags([]);

        $this->assertCount(1, $parser->parse('<b>http://example.com</b>'));

        $parser->setIgnoredTags(['a', 'b']);

        $this->assertCount(0, $parser->parse('<b>http://example.com</b>'));
    }

    public function testEof(): void
    {
        $parser = new Parser;
        $parser->setIgnoredTags(['a']);

        $this->assertCount(0, $parser->parse('<a>http://example.com'));
    }
}
