<?php

declare(strict_types=1);

namespace OsiemSiedem\Tests\Autolink\Filters;

use OsiemSiedem\Autolink\Facades\Autolink;
use OsiemSiedem\Tests\Autolink\TestCase;

final class AutolinkTest extends TestCase
{
    public function test_facade(): void
    {
        Autolink::shouldReceive('convert')
            ->once()
            ->with('http://example.com')
            ->andReturn('<a href="http://example.com">http://example.com</a>');

        $this->assertEquals('<a href="http://example.com">http://example.com</a>', Autolink::convert('http://example.com'));
    }
}
