<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WebJornal extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts =[
        'id'           => 'integer',
        'admin_id'     => 'integer',
        'title'        => 'object',
        'details'      => 'object',
        'tags'         => 'object',
        'slug'         => 'string',
        'image'        => 'string',
        'status'       => 'integer',
    ];

    public function admin() {
        return $this->belongsTo(Admin::class);
    }
}
