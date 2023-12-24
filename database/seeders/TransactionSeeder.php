<?php

namespace Database\Seeders;

use App\Models\Transaction;
use Illuminate\Database\Seeder;
use Illuminate\Foundation\Testing\Concerns\InteractsWithDatabase;

class TransactionSeeder extends Seeder
{
    use InteractsWithDatabase;
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Transaction::factory()->count(50)->create();
    }
}
