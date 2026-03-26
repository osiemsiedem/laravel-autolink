<?php

declare(strict_types=1);

namespace OsiemSiedem\Tests\Autolink;

use OsiemSiedem\Autolink\Parser;
use OsiemSiedem\Autolink\Parsers\UrlParser;

final class ParserTest extends TestCase
{
    public function test_ignored_tags(): void
    {
        $parser = new Parser;
        $parser->addElementParser(new UrlParser);
        $parser->setIgnoredTags([]);

        $this->assertCount(1, $parser->parse('<b>http://example.com</b>'));

        $parser->setIgnoredTags(['a', 'b']);

        $this->assertCount(0, $parser->parse('<b>http://example.com</b>'));
    }

    public function test_eof(): void
    {
        $parser = new Parser;
        $parser->setIgnoredTags(['a']);

        $this->assertCount(0, $parser->parse('<a>http://example.com'));
    }

    public function test_ignored_tags_require_full_tag_match(): void
    {
        $parser = new Parser;
        $parser->addElementParser(new UrlParser);
        $parser->setIgnoredTags(['a', 'pre']);

        $this->assertCount(1, $parser->parse('<preload>http://example.com</preload>'));
        $this->assertCount(1, $parser->parse('<address>http://example.com</address>'));
    }

    public function test_ignored_tags_are_case_insensitive(): void
    {
        $parser = new Parser;
        $parser->addElementParser(new UrlParser);
        $parser->setIgnoredTags(['code']);

        $this->assertCount(0, $parser->parse('<CODE>http://example.com</CODE>'));
    }
}
