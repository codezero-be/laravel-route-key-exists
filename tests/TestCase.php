<?php

namespace CodeZero\RouteKeyExists\Tests;

use Orchestra\Testbench\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /**
     * Setup the test environment.
     */
    public function setUp()
    {
        parent::setUp();

        config()->set('app.key', str_random(32));

        $this->loadLaravelMigrations(config('database.default'));
    }
}
