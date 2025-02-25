<?php

declare(strict_types=1);

namespace OsiemSiedem\Tests\Autolink;

use Illuminate\Support\Facades\Facade;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        Facade::clearResolvedInstances();
    }
}
