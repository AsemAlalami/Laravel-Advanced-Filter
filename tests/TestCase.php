<?php

namespace AsemAlalami\LaravelAdvancedFilter\Test;

use AsemAlalami\LaravelAdvancedFilter\AdvancedFilterServiceProvider;

abstract class TestCase extends \Orchestra\Testbench\TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        // additional setup
    }

    protected function getPackageProviders($app)
    {
        return [
            AdvancedFilterServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        // perform environment setup
    }
}
