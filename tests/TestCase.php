<?php

declare(strict_types=1);

namespace OsiemSiedem\Tests\Autolink;

use Illuminate\Support\Facades\Facade;
use PHPUnit\Framework\TestCase as BaseTestCase;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

abstract class TestCase extends BaseTestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * Setup the test environment.
     *
     * @return void
     */
    protected function setUp()
    {
        Facade::clearResolvedInstances();
    }
}
