<?php

namespace Tests\Feature\Controllers;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class GetBalanceControllerTest extends TestCase
{
    use DatabaseMigrations;

    public function test_get_balance_returns_success() {
        $response = $this->get( '/api/get-balance?user_id=1');

        $response->assertStatus(200);
        $response->assertJsonStructure(['balance']);
    }
}
