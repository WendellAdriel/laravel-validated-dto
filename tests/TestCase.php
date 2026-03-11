<?php

declare(strict_types=1);

namespace WendellAdriel\ValidatedDTO\Tests;

use Orchestra\Testbench\TestCase as BaseTestCase;
use WendellAdriel\ValidatedDTO\Providers\ValidatedDTOServiceProvider;

class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function getPackageProviders($app)
    {
        return [
            ValidatedDTOServiceProvider::class,
        ];
    }
}
