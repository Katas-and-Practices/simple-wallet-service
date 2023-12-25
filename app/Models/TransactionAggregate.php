<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionAggregate extends Model
{
    use HasFactory;

    protected $fillable = [
        'amount', 'from', 'until'
    ];
}
