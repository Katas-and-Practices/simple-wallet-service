<?php

namespace Actions;

use App\Models\Transaction;
use App\Models\TransactionAggregate;
use App\Services\AggregateTransactionsAction;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class AggregateTransactionsTest extends TestCase
{
    use DatabaseMigrations;

    private AggregateTransactionsAction $aggregateTransactionsAction;

    protected function setUp(): void
    {
        parent::setUp();

        $this->aggregateTransactionsAction = new AggregateTransactionsAction();
    }

    public function test_returns_zero_having_no_transactions(): void
    {
        $aggregated = ($this->aggregateTransactionsAction)(now(), now());

        $this->assertSame(0, $aggregated->amount);
    }

    public function test_returns_aggregated_having_single_transaction(): void
    {
        $from = now()->subDay()->startOfDay();
        $until = now()->subDay()->endOfDay();

        $transaction = Transaction::factory()->create([
            'created_at' => now()->subDay()->startOfDay(),
        ]);

        $aggregated = ($this->aggregateTransactionsAction)($from, $until);
        $expectedAggregate = $transaction->amount;

        $this->assertDatabaseHas(TransactionAggregate::class, [
            'amount' => $expectedAggregate,
            'from' => now()->subDay()->startOfDay()->toDateTimeString(),
            'until' => now()->subDay()->endOfDay()->toDateTimeString(),
        ]);
        $this->assertSame($expectedAggregate, $aggregated->amount);
    }

    public function test_returns_aggregated_having_multiple_transactions(): void
    {
        $from = now()->subDay()->startOfDay();
        $until = now()->subDay()->endOfDay();

        $transactions = Transaction::factory()->count(3)->create([
            'created_at' => now()->subDay()->startOfDay(),
        ]);

        $aggregated = ($this->aggregateTransactionsAction)($from, $until);
        $expectedAggregate = array_sum(array_column($transactions->toArray(), 'amount'));

        $this->assertDatabaseHas(TransactionAggregate::class, [
            'amount' => $expectedAggregate,
            'from' => now()->subDay()->startOfDay()->toDateTimeString(),
            'until' => now()->subDay()->endOfDay()->toDateTimeString(),
        ]);
        $this->assertSame($expectedAggregate, $aggregated->amount);
    }

    public function test_returns_zero_having_outside_of_time_range_transactions(): void
    {
        $from = now()->subDay()->startOfDay();
        $until = now()->subDay()->endOfDay();

        Transaction::factory()->count(3)->create([
            'created_at' => now()->subDay()->endOfDay()->addSecond(),
        ]);
        Transaction::factory()->count(3)->create([
            'created_at' => now()->addSecond(),
        ]);
        Transaction::factory()->count(3)->create([
            'created_at' => now()->subDay()->startOfDay()->subSecond(),
        ]);

        $aggregated = ($this->aggregateTransactionsAction)($from, $until);

        $this->assertDatabaseHas(TransactionAggregate::class, [
            'amount' => 0,
            'from' => now()->subDay()->startOfDay()->toDateTimeString(),
            'until' => now()->subDay()->endOfDay()->toDateTimeString(),
        ]);
        $this->assertSame(0, $aggregated->amount);
    }

    public function test_throws_error_having_overlapping_aggregate_time_range(): void
    {
        $from = now()->subDay()->startOfDay();
        $until = now()->subDay()->endOfDay();

        TransactionAggregate::factory()->create([
            'from' => $from,
            'until' => $until,
        ]);

        $this->expectExceptionMessage('Cannot create overlapping transaction aggregates');
        ($this->aggregateTransactionsAction)($from, $until);
    }
}
