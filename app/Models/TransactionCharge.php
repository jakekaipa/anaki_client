<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionCharge extends Model
{
    use HasFactory;
    protected $casts = [
        'transaction_id' => 'integer',
        'percent_charge' => 'decimal:16',
        'fixed_charge'   => 'decimal:16',
        'total_charge'   => 'decimal:16',
    ];
}
