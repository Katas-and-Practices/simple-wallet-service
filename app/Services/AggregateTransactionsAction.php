<?php

namespace App\Services;

use App\Models\Transaction;
use App\Models\TransactionAggregate;
use Carbon\Carbon;

class AggregateTransactionsAction
{
    public function __invoke(Carbon $from, Carbon $until)
    {
        if ($this->doDatesOverlap($from, $until)) {
            throw new \Exception('Cannot create overlapping transaction aggregates');
        }

        $sum = $this->getSumOfTransactions($from, $until);

        $transactionAggregate = TransactionAggregate::query()
            ->create([
                'amount' => $sum,
                'from' => $from,
                'until' => $until,
            ]);

        return $transactionAggregate;
    }

    private function doDatesOverlap(Carbon $from, Carbon $until): bool
    {
        return TransactionAggregate::query()
            ->where(function ($query) use ($from) {
                return $query
                    ->where('from', '<=', $from)
                    ->where('until', '>', $from);
            })
            ->orWhere(function ($query) use ($from, $until) {
                return $query
                    ->where('from', '>=', $from)
                    ->where('from', '<', $until);
            })
            ->exists();
    }

    private function getSumOfTransactions(Carbon $from, Carbon $until): mixed
    {
        return Transaction::query()
            ->where('created_at', '>=', $from)
            ->where('created_at', '<=', $until)
            ->sum('amount');
    }
}
