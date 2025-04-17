<?php

namespace App\Models\Mongo;

class Wallet extends BaseModel
{
    protected $connection = 'mongodb';
    protected $collection = 'wallets';
    protected $primaryKey = 'userId';
}

/*
    {
        _id: ObjectId('66a88ee057eba9920b2860c6'),
        userId: 'xxx@gmail.com',
        address: '...',
        privateKey: '...'
        lastUpdated: ISODate('2024-08-01T03:55:29.813Z'),
        tetherBalance: 0,
        lastTetherBalance: 0,
        trxBalance: '193.38062'
    }
*/
