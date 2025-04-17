<?php

namespace App\Models\Mongo;

class Transfer extends BaseModel
{
    protected $connection = 'mongodb';
    protected $collection = 'transfer';
    protected $primaryKey = '_id';

    protected $fillable = ['sender', 'type', 'created_at', 'from', 'to', 'amount', 'immediate'];
}

/*
  {
    _id: ObjectId('66a96932138f6f4d360b382e'),
    from: 'TEYhxNu17cAyUDgvBNoZP9b5xLXt6ixZSa',
    to: 'TEYhxNu17cAyUDgvBNoZP9b5xLXt6ixZSa',
    amount: Long('100'),
    enabled: true,
  }
*/
