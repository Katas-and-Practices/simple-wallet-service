<?php

namespace Tests\Feature\Controllers;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class AddMoneyControllerTest extends TestCase
{
    use DatabaseMigrations;

    public function test_add_money_returns_success() {
        $response = $this->post( '/api/add-money', [
            'user_id' => 1,
            'amount' => 50,
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['reference_id']);
    }
}
