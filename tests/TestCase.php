<?php

namespace Tests;

use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use DatabaseTransactions;
    use TestAuth;

    private string $now = '2024-12-29T10:00:00.000000Z';

    /**
     * Setup environment testing.
     */
    protected function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow($this->now);
    }

    /**
     * Clean up the testing environment before the next test.
     */
    protected function tearDown(): void
    {
        parent::tearDown();
    }
}
