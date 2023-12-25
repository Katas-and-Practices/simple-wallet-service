<?php

namespace Tests\Feature\Actions;

use App\Models\Transaction;
use App\Services\AddMoneyAction;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class AddMoneyTest extends TestCase
{
    use DatabaseMigrations;

    public function test_transaction_is_added_successfully(): void
    {
        $inputData = (object)[
            'user_id' => 5,
            'amount' => 9561,
        ];
        $action = new AddMoneyAction();

        $result = ($action)($inputData);

        $this->assertIsString($result->reference_id);
        $this->assertNotEmpty($result->reference_id);
        $this->assertDatabaseHas(Transaction::class, [
            'id' => 1,
            'user_id' => 5,
            'amount' => 9561,
        ]);
    }
}
