<?php

namespace App\Services;

use App\Models\Transaction;
use Carbon\Carbon;

class SumOfTransactionsAction
{
    public function __invoke(Carbon $from = null, Carbon $until = null): object
    {
        $query = Transaction::query();

        if ($from) {
            $query->where('created_at', '>=', $from);
        }

        if ($until) {
            $query->where('created_at', '<=', $until);
        }

        $sum = $query->sum('amount');

        return (object)[
            'sum' => $sum,
        ];
    }
}
