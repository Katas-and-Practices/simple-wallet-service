<?php

namespace App\Services;

use App\Models\Transaction;

class GetBalanceAction
{
    public function __invoke(int $userId): object
    {
        $balance = Transaction::query()
            ->where('user_id', $userId)
            ->sum('amount');

        return (object)[
            'balance' => $balance ?? 0,
        ];
    }
}
