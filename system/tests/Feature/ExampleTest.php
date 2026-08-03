<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function testMissingHomepageReturnsNotFound(): void
    {
        $response = $this->get('/');

        $response->assertNotFound();
    }
}
