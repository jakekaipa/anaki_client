<?php

namespace App\Models\Mongo;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;
use Illuminate\Support\Facades\Config;

class TrxTransfer extends Eloquent
{
    protected $connection = 'mongodb';
    protected $collection = 'trx_transfer';
}
