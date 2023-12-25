<?php

namespace App\Jobs;

use App\Services\AggregateTransactionsAction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Symfony\Component\Console\Output\ConsoleOutput;

class AggregateTransactionsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct()
    {
        //
    }

    public function handle(): void
    {
        $now = now();
        $start = now()->subRealDay()->startOfDay();
        $end = now()->subRealDay()->endOfDay();
        $out = new ConsoleOutput();

        (new AggregateTransactionsAction())($start, $end);

        $out->writeln($now->toDateTimeString() . ' - Aggregated transactions from: ' . $start . ' to ' . $end);

        $out->writeln('#####');
    }
}
