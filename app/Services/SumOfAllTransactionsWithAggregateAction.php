<?php

namespace App\Services;

use App\Models\Transaction;
use App\Models\TransactionAggregate;
use Carbon\Carbon;

class SumOfAllTransactionsWithAggregateAction
{
    public function __invoke(Carbon $until): object
    {
        $aggregatesSum = $this->getAggregatesSum($until);

        $from = (new Carbon($until))->startOfDay();
        $transactionsSum = $this->getTransactionsSum($from, $until);

        return (object)[
            'sum' => $transactionsSum + $aggregatesSum,
        ];
    }

    public function getTransactionsSum(Carbon $from, Carbon $until): mixed
    {
        return Transaction::query()
            ->where('created_at', '>=', $from)
            ->where('created_at', '<=', $until)
            ->sum('amount');
    }

    public function getAggregatesSum(Carbon $until): mixed
    {
        return TransactionAggregate::query()
            ->where('until', '<', $until)
            ->sum('amount');
    }
}
