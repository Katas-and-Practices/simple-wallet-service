<?php

namespace Actions;

use App\Models\Transaction;
use App\Services\SumOfTransactionsAction;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Collection;
use Tests\TestCase;

class SumOfTransactionsTest extends TestCase
{
    use DatabaseMigrations;

    private SumOfTransactionsAction $sumOfTransactionsAction;

    protected function setUp(): void
    {
        parent::setUp();
        $this->sumOfTransactionsAction = new SumOfTransactionsAction();
    }

    public function test_returns_zero_with_no_transactions(): void
    {
        $result = ($this->sumOfTransactionsAction)();

        $this->assertSame(0, $result->sum);
    }

    public function test_returns_sum_with_multiple_transactions(): void
    {
        $transactions = Transaction::factory()->count(5)->create();

        $result = ($this->sumOfTransactionsAction)();
        $sum = array_sum(array_column($transactions->toArray(), 'amount'));

        $this->assertSame($sum, $result->sum);
    }

    public function test_returns_sum_with_date_from(): void
    {
        $olderDate = Carbon::create('2000-05-06 10:10:00');
        $fromDate = Carbon::create('2005-05-06 10:10:00');

        $this->createTransactionsWithDate($olderDate, 3);
        $transactions = $this->createTransactionsWithDate($fromDate, 3);

        $result = ($this->sumOfTransactionsAction)($fromDate);
        $sum = array_sum(array_column($transactions->toArray(), 'amount'));

        $this->assertSame($sum, $result->sum);
    }

    public function test_returns_sum_with_date_until(): void
    {
        $untilDate = Carbon::create('2000-05-06 10:10:00');
        $newerDate = Carbon::create('2005-05-06 10:10:00');

        $transactions = $this->createTransactionsWithDate($untilDate, 3);
        $this->createTransactionsWithDate($newerDate, 3);

        $result = ($this->sumOfTransactionsAction)(until: $untilDate);
        $sum = array_sum(array_column($transactions->toArray(), 'amount'));

        $this->assertSame($sum, $result->sum);
    }

    public function test_returns_sum_with_date_from_until(): void
    {
        $date1 = Carbon::create('2001-05-06 10:10:00');
        $date2 = Carbon::create('2002-05-06 10:10:00');
        $date3 = Carbon::create('2003-05-06 10:10:00');
        $date4 = Carbon::create('2004-05-06 10:10:00');

        $this->createTransactionsWithDate($date1, 3);
        $transactions2 = $this->createTransactionsWithDate($date2, 3);
        $transactions3 = $this->createTransactionsWithDate($date3, 3);
        $this->createTransactionsWithDate($date4, 3);

        $result = ($this->sumOfTransactionsAction)($date2, $date3);
        $amounts = array_merge(
            array_column($transactions2->toArray(), 'amount'),
            array_column($transactions3->toArray(), 'amount'),
        );
        $sum = array_sum($amounts);

        $this->assertSame($sum, $result->sum);
    }

    private function createTransactionsWithDate(Carbon $date, $count = 1): Collection
    {
        $transactions = Transaction::factory()->count($count)->create([
            'created_at' => $date,
        ]);

        return $transactions;
    }
}
