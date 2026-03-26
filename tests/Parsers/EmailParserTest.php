<?php

declare(strict_types=1);

namespace OsiemSiedem\Tests\Autolink\Parsers;

use OsiemSiedem\Autolink\Cursor;
use OsiemSiedem\Autolink\Parsers\EmailParser;
use OsiemSiedem\Tests\Autolink\TestCase;

final class EmailParserTest extends TestCase
{
    public function test_rejects_invalid_local_parts_with_dots(): void
    {
        $parser = new EmailParser;

        foreach (['.a@example.com', 'a..b@example.com', 'a.@example.com'] as $email) {
            $cursor = new Cursor($email);

            $cursor->next(strpos($email, '@'));

            $this->assertNull($parser->parse($cursor));
        }
    }
}
