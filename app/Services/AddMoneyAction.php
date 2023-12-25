<?php

namespace App\Services;

use App\Models\Transaction;
use Illuminate\Support\Str;

class AddMoneyAction
{
    public function __invoke(object $inputData): object
    {
        $transaction = Transaction::query()
            ->create([
                'user_id' => $inputData->user_id,
                'reference_id' => (Str::uuid())->toString(),
                'amount' => $inputData->amount,
            ]);

        return (object)[
            'reference_id' => $transaction->reference_id,
        ];
    }
}
