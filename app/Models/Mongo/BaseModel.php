<?php

namespace App\Models\Mongo;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;
use Illuminate\Support\Facades\Config;

class BaseModel extends Eloquent
{
    protected $connection = 'mongodb';
}
