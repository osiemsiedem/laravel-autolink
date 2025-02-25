<?php

declare(strict_types=1);

namespace OsiemSiedem\Tests\Autolink\Parsers;

use OsiemSiedem\Autolink\Cursor;
use OsiemSiedem\Autolink\Parsers\UrlParser;
use OsiemSiedem\Tests\Autolink\TestCase;

final class UrlParserTest extends TestCase
{
    public function test_parse(): void
    {
        $cursor = new Cursor('http://');

        $cursor->next(4);

        $parser = new UrlParser;

        $this->assertNull($parser->parse($cursor));
    }

    public function test_domain_name(): void
    {
        $cursor = new Cursor('http://@');

        $cursor->next(4);

        $parser = new UrlParser;

        $this->assertNull($parser->parse($cursor));
    }

    public function test_invalid_protocol(): void
    {
        $cursor = new Cursor('xyz://example.com');

        $cursor->next(3);

        $parser = new UrlParser;

        $this->assertNull($parser->parse($cursor));
    }
}
