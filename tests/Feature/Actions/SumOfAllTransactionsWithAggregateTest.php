<?php

namespace Actions;

use App\Models\Transaction;
use App\Models\TransactionAggregate;
use App\Services\SumOfAllTransactionsWithAggregateAction;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Collection;
use Tests\TestCase;

class SumOfAllTransactionsWithAggregateTest extends TestCase
{
    use DatabaseMigrations;

    private SumOfAllTransactionsWithAggregateAction $sumOfAllTransactionsWithAggregateAction;

    protected function setUp(): void
    {
        parent::setUp();
        $this->sumOfAllTransactionsWithAggregateAction = new SumOfAllTransactionsWithAggregateAction();
    }

    public function test_returns_zero_with_no_transactions(): void
    {
        $result = ($this->sumOfAllTransactionsWithAggregateAction)(now());

        $this->assertSame(0, $result->sum);
    }

    public function test_returns_sum_with_aggregate_transaction(): void
    {
        TransactionAggregate::factory()->create([
            'from' => now()->subDays(10)->startOfDay(),
            'until' => now()->subDays(10)->endOfDay(),
            'amount' => 100,
        ]);
        TransactionAggregate::factory()->create([
            'from' => now()->subDays(9)->startOfDay(),
            'until' => now()->subDays(9)->endOfDay(),
            'amount' => 300,
        ]);

        $result = ($this->sumOfAllTransactionsWithAggregateAction)(now());

        $this->assertSame(400, $result->sum);
    }

    public function test_returns_sum_with_transaction_only(): void
    {
        Transaction::factory()->create([
            'amount' => 100,
            'created_at' => now(),
        ]);

        $result = ($this->sumOfAllTransactionsWithAggregateAction)(now());

        $this->assertSame(100, $result->sum);
    }

    public function test_returns_sum_with_transaction_and_aggregate_transaction(): void
    {
        TransactionAggregate::factory()->create([
            'from' => now()->subDays()->startOfDay(),
            'until' => now()->subDays()->endOfDay(),
            'amount' => 100,
        ]);

        Transaction::factory()->create([
            'amount' => 100,
            'created_at' => now(),
        ]);
        Transaction::factory()->create([
            'amount' => 100,
            'created_at' => now()->subDays()->startOfDay(),
        ]);

        $result = ($this->sumOfAllTransactionsWithAggregateAction)(now());

        $this->assertSame(200, $result->sum);
    }
}
