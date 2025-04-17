<?php

namespace App\Models\Mongo;

class Tx extends BaseModel
{
    protected $connection = 'mongodb';
    protected $collection = 'tx';
    protected $primaryKey = 'txid';
}

/*
  {
    _id: ObjectId('66a96932138f6f4d360b382e'),
    txid: 'b5a3f646c978eb5f8b2a7e497881a0b48149095110eea0aa6118f48aee0537bd',
    contract_type: 'TriggerSmartContract',
    function_name: 'transfer',
    block_number: Long('46253376'),
    timestamp: Long('1722347940000'),
    from: 'TEYhxNu17cAyUDgvBNoZP9b5xLXt6ixZSa',
    to: 'TGXfYFEhntreKkettCSzXEB6DFuRkadtGd',
    amount: '100000000'
  }
*/
