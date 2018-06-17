<?php

declare(strict_types=1);

namespace OsiemSiedem\Tests\Autolink\Parsers;

use OsiemSiedem\Autolink\Cursor;
use OsiemSiedem\Tests\Autolink\TestCase;
use OsiemSiedem\Autolink\Parsers\UrlParser;

final class UrlParserTest extends TestCase
{
    public function testParse(): void
    {
        $cursor = new Cursor('http://');

        $cursor->next(4);

        $parser = new UrlParser;

        $this->assertNull($parser->parse($cursor));
    }

    public function testDomainName(): void
    {
        $cursor = new Cursor('http://@');

        $cursor->next(4);

        $parser = new UrlParser;

        $this->assertNull($parser->parse($cursor));
    }

    public function testInvalidProtocol(): void
    {
        $cursor = new Cursor('xyz://example.com');

        $cursor->next(3);

        $parser = new UrlParser;

        $this->assertNull($parser->parse($cursor));
    }
}
