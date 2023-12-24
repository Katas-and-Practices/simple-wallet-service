<?php

namespace Tests\Feature;

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

        $this->assertSame(1, $result);
        $this->assertDatabaseHas(Transaction::class, [
            'id' => 1,
            'user_id' => 5,
            'amount' => 9561,
        ]);
    }
}
