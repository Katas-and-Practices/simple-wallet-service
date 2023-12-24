<?php

namespace App\Services;

use App\Models\Transaction;

class AddMoneyAction
{
    public function __invoke(object $inputData): int
    {
        $transaction = Transaction::query()
            ->create([
                'user_id' => $inputData->user_id,
                'amount' => $inputData->amount,
            ]);

        return $transaction->id;
    }
}
