<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /**
     * Refuse to run database test helpers against the application database.
     */
    protected function setUpTraits()
    {
        $connection = config('database.default');
        $database = config("database.connections.{$connection}.database");

        if ($connection !== 'mysql' || $database !== 'federal_manpower_testing') {
            throw new \RuntimeException(
                'Tests may only use the isolated MySQL database [federal_manpower_testing].'
            );
        }

        return parent::setUpTraits();
    }
}
