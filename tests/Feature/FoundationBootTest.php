<?php

namespace Tests\Feature;

use Tests\TestCase;

class FoundationBootTest extends TestCase
{
    public function test_home_page_loads(): void
    {
        $response = $this->get('/');

        $response->assertOk();
    }
}
