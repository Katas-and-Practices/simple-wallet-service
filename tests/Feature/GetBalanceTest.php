<?php

namespace Tests\Feature;

use App\Models\Transaction;
use App\Services\GetBalanceAction;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class GetBalanceTest extends TestCase
{
    use DatabaseMigrations;

    private GetBalanceAction $getBalanceAction;

    public function setUp(): void
    {
        parent::setUp();

        $this->getBalanceAction = new GetBalanceAction();
    }

    public function test_returns_zero_having_no_transactions(): void
    {
        $result = ($this->getBalanceAction)(1);

        $this->assertSame(0, $result->balance);
    }

    public function test_returns_zero_for_non_existing_user(): void
    {
        Transaction::factory()->create([
            'user_id' => 10,
        ]);

        $result = ($this->getBalanceAction)(20);

        $this->assertSame(0, $result->balance);
    }

    public function test_returns_balance_having_one_transaction(): void
    {
        $transaction = Transaction::factory()->create();

        $result = ($this->getBalanceAction)($transaction->user_id);

        $this->assertSame($transaction->amount, $result->balance);
    }

    public function test_returns_balance_having_three_transactions(): void
    {
        $transactions = Transaction::factory()->count(3)->create([
            'user_id' => 1,
        ]);
        $expectedSum = array_sum(array_column($transactions->toArray(), 'amount'));

        $result = ($this->getBalanceAction)(1);

        $this->assertSame($expectedSum, $result->balance);
    }

    public function test_returns_balance_having_multiple_transactions_from_multiple_users(): void
    {
        $userId1 = 1;
        $userId2 = 2;
        $userId3 = 3;

        Transaction::factory()->count(3)->create([
            'user_id' => $userId1,
        ]);
        $transactionsOfUserTwo = Transaction::factory()->count(3)->create([
            'user_id' => $userId2,
        ]);
        Transaction::factory()->count(3)->create([
            'user_id' => $userId3,
        ]);

        $expectedSum = array_sum(array_column($transactionsOfUserTwo->toArray(), 'amount'));

        $result = ($this->getBalanceAction)($userId2);

        $this->assertSame($expectedSum, $result->balance);
    }
}
