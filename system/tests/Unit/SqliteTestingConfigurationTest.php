<?php

namespace Tests\Unit;

use Tests\TestCase;

class SqliteTestingConfigurationTest extends TestCase
{
    public function testTestSuiteUsesAnIsolatedInMemoryDatabase(): void
    {
        $this->assertSame('testing', app()->environment());
        $this->assertSame('sqlite', config('database.default'));
        $this->assertSame(':memory:', config('database.connections.sqlite.database'));
    }
}
