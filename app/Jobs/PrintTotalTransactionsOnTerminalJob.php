<?php

namespace App\Jobs;

use App\Services\SumOfTransactionsAction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Symfony\Component\Console\Output\ConsoleOutput;

class PrintTotalTransactionsOnTerminalJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct()
    {
        //
    }

    public function handle(): void
    {
        $now = now();
        $out = new ConsoleOutput();

        $sumOfAll = (new SumOfTransactionsAction())();
        $out->writeln($now->toDateTimeString() . ' - Sum of all transactions: ' . $sumOfAll->sum);

        $start = now()->startOfDay();
        $end = now()->endOfDay();
        $sumOfToday = (new SumOfTransactionsAction())($start, $end);
        $out->writeln($now->toDateTimeString() . ' - Sum of today\'s transactions: ' . $sumOfToday->sum);

        $out->writeln('#####');
    }
}
